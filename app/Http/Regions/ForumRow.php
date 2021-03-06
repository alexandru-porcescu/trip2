<?php

namespace App\Http\Regions;

class ForumRow
{
    public function render($forum, $route = '')
    {
        $user = request()->user();
        $commentCount = $forum->vars()->commentCount;
        $unreadCommentCount = $forum->vars()->unreadCommentCount;
        $firstUnreadCommentId = $forum->vars()->firstUnreadCommentId;
        $route = $route ? $route : route($forum->type . '.show', [$forum->slug]);

        $append = '';

        if (in_array($forum->type, ['forum', 'expat', 'buysell', 'misc'])) {
            $last_page = ceil(($commentCount - $unreadCommentCount) / config('content.forum.paginate'));

            if ($last_page > 0) {
                $append = '?page=' . $last_page;
            }
        }

        return component('ForumRow')
            ->with('route', $route)
            ->with(
                'user',
                component('UserImage')
                    ->with('route', route('user.show', [$forum->user]))
                    ->with('image', $forum->user->vars()->imagePreset('xsmall_square'))
                    ->with('rank', $forum->user->vars()->rank)
                    ->with('size', 58)
                    ->with('border', 3)
            )
            ->with('title', $forum->vars()->title)
            ->with(
                'meta',
                component('Meta')->with(
                    'items',
                    collect()
                        ->pushWhen(
                            $user && $user->hasRole('regular') && $forum->vars()->isNew,
                            component('Tag')
                                ->is('red')
                                ->with('title', trans('content.show.isnew'))
                                ->with('route', $route)
                        )
                        ->pushWhen(
                            $user && $user->hasRole('regular') && $unreadCommentCount,
                            component('Tag')
                                ->is('red')
                                ->with(
                                    'title',
                                    trans_choice('content.show.newcomments', $unreadCommentCount, [
                                        'count' => $unreadCommentCount
                                    ])
                                )
                                ->with(
                                    'route',
                                    route('forum.show', [$forum->slug]) .
                                        ($firstUnreadCommentId ? $append . '#comment-' . $firstUnreadCommentId : '')
                                )
                        )
                        ->pushWhen($commentCount, component('Tag')->with('title', $commentCount))
                        ->pushWhen(
                            $forum->views_count >= 25,
                            component('Tag')
                                ->is('orange')
                                ->with(
                                    'title',
                                    $forum->views_count > 1
                                        ? trans('content.post.views', ['number' => $forum->views_count])
                                        : trans('content.post.view', ['number' => $forum->views_count])
                                )
                        )
                        ->push(
                            component('MetaLink')
                                ->is('cyan')
                                ->with('title', $forum->user->vars()->name)
                                ->with('route', route('user.show', [$forum->user]))
                        )
                        ->push(
                            component('MetaLink')
                                ->is('gray')
                                ->with('title', $forum->vars()->updated_at)
                        )
                        ->merge(
                            $forum->destinations->map(function ($destination) {
                                return component('Tag')
                                    ->is('orange')
                                    ->with('title', $destination->name)
                                    ->with('route', route('destination.showSlug', [$destination->slug]));
                            })
                        )
                        ->merge(
                            $forum->topics->map(function ($topic) {
                                return component('MetaLink')
                                    ->with('title', $topic->name)
                                    ->with('route', route('forum.index', ['topic' => $topic]));
                            })
                        )
                )
            );
    }
}
