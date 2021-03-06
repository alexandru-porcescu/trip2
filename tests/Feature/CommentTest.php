<?php

use App\User;
use App\Comment;
use App\Content;
use Carbon\Carbon;
use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommentTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    protected $publicContentTypes;
    protected $privateContentTypes;

    public function setUp()
    {
        parent::setUp();

        $this->publicContentTypes = ['blog', 'buysell', 'expat', 'flight', 'forum', 'news', 'shortnews', 'travelmate'];

        $this->privateContentTypes = ['internal'];
    }

    public function test_regular_user_can_create_and_edit_comment()
    {
        // Vue: EditorComment\EditorComment conflict - BrowserKit cannot find body input element.
        $this->markTestSkipped();

        $regular_user = factory(User::class)->create();

        foreach ($this->publicContentTypes as $type) {
            $content = factory(Content::class)->create([
                'user_id' => factory(User::class)->create()->id,
                'type' => $type,
                'end_at' => Carbon::now()->addDays(30),
                'start_at' => Carbon::now()->addDays(30)
            ]);

            // Can comment

            $this->actingAs($regular_user)
                ->visit("content/$content->type/$content->id")
                ->type("Hola chicos de $content->type", 'body')
                ->press(trans('comment.create.submit.title'))
                ->seePageIs(config('sluggable.contentTypeMapping')[$content->type] . '/' . $content->slug)
                ->see("Hola chicos de $content->type")
                ->see($regular_user->name)
                ->seeInDatabase('comments', [
                    'user_id' => $regular_user->id,
                    'content_id' => $content->id,
                    'body' => "Hola chicos de $content->type",
                    'status' => 1
                ]);

            $comment = Comment::whereBody("Hola chicos de $content->type")->first();

            // Can edit own comment

            $this->actingAs($regular_user)
                ->visit("content/$content->type/$content->id")
                ->click(trans('comment.action.edit.title'))
                ->visit("comment/$comment->id/edit")
                ->type("Hola chicas de $content->type", 'body')
                ->press(trans('comment.edit.submit.title'))
                ->seePageIs(config('sluggable.contentTypeMapping')[$content->type] . '/' . $content->slug)
                ->see("Hola chicas de $content->type")
                ->seeInDatabase('comments', [
                    'user_id' => $regular_user->id,
                    'content_id' => $content->id,
                    'body' => "Hola chicas de $content->type",
                    'status' => 1
                ]);
        }
    }

    public function test_regular_user_cannot_edit_other_comments()
    {
        $regular_user = factory(User::class)->create();

        foreach ($this->publicContentTypes as $type) {
            $content = factory(Content::class)->create([
                'user_id' => factory(User::class)->create()->id,
                'type' => 'forum'
            ]);

            $comment = factory(Comment::class)->create([
                'user_id' => factory(User::class)->create()->id,
                'content_id' => $content->id
            ]);

            // Can not edit other users comments

            $response = $this->actingAs($regular_user)
                ->visit("content/$content->type/$content->id")
                ->dontSeeInElement('Comment', trans('comment.action.edit.title'))
                ->call('GET', "comment/$comment->id/edit");

            $this->assertEquals(401, $response->status());
        }
    }

    public function test_regular_user_cannot_comments_on_private_content()
    {
        $regular_user = factory(User::class)->create();

        foreach ($this->privateContentTypes as $type) {
            $content = factory(Content::class)->create([
                'user_id' => factory(User::class)->create()->id,
                'type' => $type
            ]);

            $comment = factory(Comment::class)->create([
                'user_id' => factory(User::class)->create()->id,
                'content_id' => $content->id
            ]);

            // Can not add private content comments

            $response = $this->actingAs($regular_user)->call('POST', "content/$content->type/$content->id/comment");
            $this->assertEquals(302, $response->status());

            // Can not edit private content comments

            $response = $this->actingAs($regular_user)->call('GET', "comment/$comment->id/edit");
            $this->assertEquals(401, $response->status());
        }
    }

    public function test_content_timestamp_does_not_update_when_superuser_updating_comment()
    {
        // Vue: EditorComment\EditorComment conflict - BrowserKit cannot find body input element.
        $this->markTestSkipped();

        $superuser = factory(User::class)->create(['role' => 'superuser']);

        $contentTypes = array_merge($this->publicContentTypes, $this->privateContentTypes);

        foreach ($contentTypes as $type) {
            $content = factory(Content::class)->create([
                'user_id' => $superuser->id,
                'type' => $type
            ]);

            $comment = factory(Comment::class)->create([
                'user_id' => $superuser->id,
                'content_id' => $content->id
            ]);

            $first_date = Content::find($content->id)->updated_at;

            sleep(1);

            $this->actingAs($superuser)
                ->visit("comment/$comment->id/edit")
                ->type('Hola', 'body')
                ->press(trans('comment.edit.submit.title'));

            $second_date = Content::find($content->id)->updated_at;

            $this->assertEquals($first_date->timestamp, $second_date->timestamp);
        }
    }
}
