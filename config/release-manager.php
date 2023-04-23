<?php

use IBroStudio\ReleaseManager\Formatters\CompactVersionFormatter;
use IBroStudio\ReleaseManager\Formatters\FullVersionFormatter;

return [

    'app-label' => env('APP_NAME'),

    'git' => [
        'commands' => [
            'local' => [
                'version' => 'git describe --tags',
                'commit' => 'git rev-parse --verify HEAD',
            ],
            'remote' => [
                'version' => 'git ls-remote | grep tags/ | grep -v {} | cut -d / -f 3 | sort --version-sort | tail -1',
                'commit' => 'git ls-remote',
            ],
        ],
        'version-matcher' => '/^(?P<label>[v|V]*[er]*[sion]*)[\.|\s]*(?P<major>0|[1-9]\d*)\.(?P<minor>0|[1-9]\d*)\.(?P<patch>0|[1-9]\d*)(?:-(?P<prerelease>(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+(?P<buildmetadata>[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/',
        'commit-length' => 6,
    ],

    'default' => [
        'git' => [
            'repository-path' => base_path(),
        ],
        'formatter' => FullVersionFormatter::class,
    ],

    'formatters' => [
        'compact' => [
            'class' => CompactVersionFormatter::class,
            'version-label' => 'v',
            'display-app-label' => false,
            'display-last-commit' => false,
        ],
        'full' => [
            'class' => FullVersionFormatter::class,
            'version-label' => 'version',
            'display-app-label' => true,
            'display-last-commit' => true,
        ],
        CompactVersionFormatter::class => [
            'version-label' => 'v',
            'display-app-label' => false,
            'display-last-commit' => false,
        ],
        FullVersionFormatter::class => [
            'version-label' => 'version',
            'display-app-label' => true,
            'display-last-commit' => true,
        ],
    ],

    'automatically_generate_release_notes' => true,
];
