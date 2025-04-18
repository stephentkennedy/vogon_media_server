<?php

return [
    'import_video' => [
        'controller' => 'import_video',
        'ext' => 'media',
        'desc' => 'Import videos from a directory',
        'flags' => [
            'dir' => '(optional) the directory to import from. If left empty, the current working directory will be used.'
        ]
    ],
    'generate_thumbnail' => [
        'controller' => 'generate_thumbnail',
        'ext' => 'media',
        'desc' => 'Generate a thumbnails for a video directory',
        'flags' => [
            'dir' => '(optional) the directory to generate thumbnails for',
            'replace' => '(optional) if this flag is set, it will replace the existing thumbnail(s)'
        ],
    ]
];