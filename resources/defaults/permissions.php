<?php

return [
    'roles' => [
        [
            'extends' => 'users',
            'name' => 'users',
            'default' => true,
            'permissions' => [
               [
                   'name' => 'projects.create',
                   'restrictions' => [
                       ['name' => 'count', 'value' => 8],
                   ]
               ],
               'editors.enable',
               'plans.view',
               'templates.view',
               'custom_domains.create',
           ]
        ],
        [
            'extends' => 'guests',
            'name' => 'guests',
            'guests' => true,
            'permissions' => [
                //
            ]
        ]
    ],
    'all' => [
        'builder' => [
            [
                'name' => 'projects.export',
                'description' => 'Allow user to export projects to their own FTP server.'
            ],
            [
                'name' => 'editors.enable',
                'description' => 'Allow user to use html,css and js code editors.'
            ],
            [
                'name' => 'projects.download',
                'description' => 'Allow user to download their project .zip file.'
            ]
        ],

        'projects' => [
            ['name' => 'projects.view', 'advanced' => true],
            [
                'name' => 'projects.create',
                'restrictions' => [
                    [
                        'name' => 'count',
                        'type' => 'number',
                        'description' => __('policies.count_description', ['resources' => 'projects']),
                    ],
                ]
            ],
            ['name' => 'projects.update', 'advanced' => true],
            ['name' => 'projects.delete', 'advanced' => true],
        ],

        'templates' => [
            ['name' => 'templates.view', 'advanced' => true],
            ['name' => 'templates.create', 'advanced' => true],
            ['name' => 'templates.update', 'advanced' => true],
            ['name' => 'templates.delete', 'advanced' => true],
        ],
    ]
];
