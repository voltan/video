<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

/**
 * @author Somayeh Karami <somayeh.karami@gmail.com>
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return [
    // Admin section
    'admin' => [
        [
            'title'      => _a('Video'),
            'controller' => 'video',
            'permission' => 'video',
        ],
        [
            'title'      => _a('Category'),
            'controller' => 'category',
            'permission' => 'category',
        ],
        [
            'title'      => _a('Server'),
            'controller' => 'server',
            'permission' => 'server',
        ],
        [
            'label'      => _a('Attribute'),
            'controller' => 'attribute',
            'permission' => 'attribute',
        ],
        [
            'label'      => _a('Logs'),
            'controller' => 'log',
            'permission' => 'log',
        ],
        [
            'label'      => _a('Tools'),
            'controller' => 'tools',
            'permission' => 'tools',
        ],
        [
            'title'      => _a('Json output'),
            'controller' => 'json',
            'permission' => 'json',
        ],
    ],
    // Front section
    'front' => [
        [
            'title'      => _a('Index'),
            'controller' => 'index',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Category'),
            'controller' => 'category',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Category list'),
            'controller' => 'category',
            'action'     => 'list',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Tag'),
            'controller' => 'tag',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Tag list'),
            'controller' => 'tag',
            'action'     => 'list',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Watch video'),
            'controller' => 'watch',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Channel'),
            'controller' => 'channel',
            'permission' => 'channel',
            'block'      => 1,
        ],
        [
            'title'      => _a('Submit'),
            'controller' => 'submit',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'label'      => _a('Json output'),
            'controller' => 'json',
            'permission' => 'public',
            'block'      => 0,
        ],
    ],
];