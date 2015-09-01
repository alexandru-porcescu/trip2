<?php

return [

    'frontpage' => [
        
        'show' => true,
        'with' => [],
        'latest' => 'created_at',
        'take' => 1

    ],

    'index' => [
    
        'with' => ['user', 'comments', 'destinations', 'topics'],
        'latest' => 'created_at',
        'paginate' => 25,
    ],

    'edit' => [

        'fields' => [
            'file' => [
                'type' => 'file',
            ],
            'title' => [
                'type' => 'text',
            ],
            'body' => [
                'type' => 'textarea',
            ],
            'url' => [
                'type' => 'url',
            ],
            'submit' => [
                'type' => 'submit',
            ]
        ],

        'validate' => [
        
            'title' => 'required',
            'url' => 'url',
        
        ],

    ]

];