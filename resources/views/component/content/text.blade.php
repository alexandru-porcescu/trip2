@include('component.inline_list', [
    'modifiers' => 'm-light m-small',
    'items' => [
        [
            'title' => $content->user->name,
            'route' => route('user.show', [$content->user])
        ],
        [
            'title' => view('component.date.relative', ['date' => $content->created_at])
        ],
        (count($content->comments) && $content->updated_at !== $content->created_at ? [
            'title' => trans('content.row.text.comment', [
                'updated_at' => view('component.date.relative', ['date' => $content->updated_at])
            ])
        ] : null),
        [
            'title' => $content->destinations->implode('name', '</li><li class="c-inline-list__item">')
        ],
        [
            'title' => $content->topics->implode('name', '</li><li class="c-inline-list__item">')
        ]
    ]
])
