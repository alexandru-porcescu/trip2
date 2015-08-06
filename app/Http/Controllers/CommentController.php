<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;

class CommentController extends Controller
{

    protected $rules = [
        'body' => 'required'
    ];

    public function store(Request $request, $type, $content_id)
    {

        $this->validate($request, $this->rules);

        $fields = [
            'content_id' => $content_id,
            'status' => 1
        ];

        $comment = Auth::user()->comments()->create(array_merge($request->all(), $fields));
        
        return redirect()->route('content.show', [$type, $content_id, '#comment-' . $comment->id]);

    }

    public function edit($id)
    {

        $comment = \App\Comment::findorFail($id);

        return \View::make("pages.comment.edit")
            ->with('comment', $comment)
            ->render();

    }

    public function update(Request $request, $id)
    {

        $this->validate($request, $this->rules);

        $comment = \App\Comment::findorFail($id);

        $fields = [
            'status' => 1
        ];

        $comment->update(array_merge($request->all(), $fields));

        return redirect()->route('content.show', [$comment->content->type, $comment->content, '#comment-' . $comment->id]);

    }

    public function status($id, $status)
    {

        $comment = \App\Comment::findorFail($id);

        if ($status == 0 || $status == 1) {

            $comment->status = $status;
            $comment->save();

            return redirect()
                ->route('content.show', [$comment->content->type, $comment->content, '#comment-' . $comment->id])
                ->with('status', trans("content.status.$status.status"));
        }
        
        return back();

    }

}
