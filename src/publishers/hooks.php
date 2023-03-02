<?php

return [
    'plugins_folder' => app_path('Plugins'),
    'table' => 'plugins',
    'hook_tags' => [
        'filter',
        'init_plugin_activation',
        'init_plugin_deactivation',
        'info_notice',
    ],
];
