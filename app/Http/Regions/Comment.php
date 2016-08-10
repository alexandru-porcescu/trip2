<?php

namespace App\Http\Regions;

use Illuminate\Http\Request;

class Comment
{
    public function render(Request $request, $comment)
    {
        return component('Comment')
            ->with('profile', component('ProfileImage')
                ->with('route', route('user.show', [$comment->user]))
                ->with('image', $comment->user->imagePreset('small_square'))
                ->with('rank', $comment->user->rank * 90)
            )
            ->with('meta', collect()
                ->push(component('LinkMeta')
                    ->with('title', $comment->user->name)
                    ->with('route', route('user.show', [$comment->user]))
                )
                ->push(component('LinkMeta')
                    ->with('title', $comment->created_at->diffForHumans())
                    ->with('route', route('content.show', [
                        $comment->content->type, $comment->content, '#comment-'.$comment->id,
                    ]))
                )
                ->push(component('Flag')
                    ->with('value', 1)
                    ->with('route', route('styleguide.flag'))
                    ->with('icon', 'icon-thumb-up')
                )
            )
            ->with('body', component('Body')->with('body', $comment->body));
    }
}