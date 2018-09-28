<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return [
    // Admin section
    'admin' => [
        'video' => [
            'label'      => _a('Video'),
            'permission' => [
                'resource' => 'video',
            ],
            'route'      => 'admin',
            'module'     => 'video',
            'controller' => 'video',
            'action'     => 'index',
        ],

        'category' => [
            'label'      => _a('Category'),
            'permission' => [
                'resource' => 'category',
            ],
            'route'      => 'admin',
            'module'     => 'video',
            'controller' => 'category',
            'action'     => 'index',
            'params'     => [
                'type' => 'category',
            ],

            'pages' => [
                'category'        => [
                    'label'      => _a('Category'),
                    'permission' => [
                        'resource' => 'category',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'category',
                    'action'     => 'index',
                    'params'     => [
                        'type' => 'category',
                    ],
                ],
                'brand'           => [
                    'label'      => _a('Brand'),
                    'permission' => [
                        'resource' => 'category',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'category',
                    'action'     => 'index',
                    'params'     => [
                        'type' => 'brand',
                    ],
                ],
                'update-category' => [
                    'label'      => _a('New category'),
                    'permission' => [
                        'resource' => 'category',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'category',
                    'action'     => 'update',
                    'params'     => [
                        'type' => 'category',
                    ],
                ],
                'update-brand'    => [
                    'label'      => _a('New brand'),
                    'permission' => [
                        'resource' => 'category',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'category',
                    'action'     => 'update',
                    'params'     => [
                        'type' => 'brand',
                    ],
                ],
                'sync'            => [
                    'label'      => _a('Sync category'),
                    'permission' => [
                        'resource' => 'category',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'category',
                    'action'     => 'sync',
                ],
                'merge'           => [
                    'label'      => _a('Merge category'),
                    'permission' => [
                        'resource' => 'category',
                    ],
                    'route'      => 'admin',
                    'module'     => 'shop',
                    'controller' => 'category',
                    'action'     => 'merge',
                ],
            ],
        ],

        'server' => [
            'label'      => _a('Server'),
            'permission' => [
                'resource' => 'server',
            ],
            'route'      => 'admin',
            'module'     => 'video',
            'controller' => 'server',
            'action'     => 'index',
        ],

        'attribute' => [
            'label'      => _a('Attribute'),
            'permission' => [
                'resource' => 'attribute',
            ],
            'route'      => 'admin',
            'module'     => 'video',
            'controller' => 'attribute',
            'action'     => 'index',
            'pages'      => [
                'attribute' => [
                    'label'      => _a('Attribute'),
                    'permission' => [
                        'resource' => 'attribute',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'attribute',
                    'action'     => 'index',
                ],
                'position'  => [
                    'label'      => _a('Attribute position'),
                    'permission' => [
                        'resource' => 'position',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'position',
                    'action'     => 'index',
                ],
            ],
        ],

        'tools' => [
            'label'      => _a('Tools'),
            'permission' => [
                'resource' => 'tools',
            ],
            'route'      => 'admin',
            'module'     => 'video',
            'controller' => 'tools',
            'action'     => 'index',
            'pages'      => [
                'tools' => [
                    'label'      => _a('Tools'),
                    'permission' => [
                        'resource' => 'tools',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'tools',
                    'action'     => 'index',
                ],

                'sitemap' => [
                    'label'      => _a('Sitemap'),
                    'permission' => [
                        'resource' => 'tools',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'tools',
                    'action'     => 'sitemap',
                ],

                'log' => [
                    'label'      => _a('Logs'),
                    'permission' => [
                        'resource' => 'log',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'log',
                    'action'     => 'index',
                ],

                'json' => [
                    'label'      => _a('Json'),
                    'permission' => [
                        'resource' => 'json',
                    ],
                    'route'      => 'admin',
                    'module'     => 'video',
                    'controller' => 'json',
                    'action'     => 'index',
                ],
            ],
        ],
    ],
    // Front section
    'front' => [
        'category' => [
            'label'      => _a('Category list'),
            'permission' => [
                'resource' => 'public',
            ],
            'route'      => 'video',
            'module'     => 'video',
            'controller' => 'category',
        ],

        'tag' => [
            'label'      => _a('Tag list'),
            'permission' => [
                'resource' => 'public',
            ],
            'route'      => 'video',
            'module'     => 'video',
            'controller' => 'tag',
        ],

        'channel' => [
            'label'      => _a('Your channel'),
            'permission' => [
                'resource' => 'public',
            ],
            'route'      => 'video',
            'module'     => 'video',
            'controller' => 'channel',
        ],

        'submit' => [
            'label'      => _a('Submit'),
            'permission' => [
                'resource' => 'public',
            ],
            'route'      => 'video',
            'module'     => 'video',
            'controller' => 'submit',
        ],

    ],
];