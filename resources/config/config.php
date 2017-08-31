<?php

use Anomaly\FilesFieldType\Support\Config\FoldersOptions;

return [
    'folders' => [
        'type'   => 'anomaly.field_type.checkboxes',
        'config' => [
            'handler' => FoldersOptions::class,
        ],
    ],
    'min'     => [
        'type'   => 'anomaly.field_type.integer',
        'config' => [
            'min' => 1,
        ],
    ],
    'max'     => [
        'type'   => 'anomaly.field_type.integer',
        'config' => [
            'min' => 1,
        ],
    ],
    'mode'    => [
        'required' => true,
        'type'     => 'anomaly.field_type.select',
        'config'   => [
            'options' => [
                'default' => 'anomaly.field_type.files::config.mode.option.default',
                'select'  => 'anomaly.field_type.files::config.mode.option.select',
                'upload'  => 'anomaly.field_type.files::config.mode.option.upload',
            ],
        ],
    ],
];
