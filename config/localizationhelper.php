<?php

return [

    //directory for laravel root
    'base_path' => base_path("resources/lang"),

    // for module
    'debug_auto_create_lang' => [

        'active' => true,

        // module direcotry must be as lower case
        'parent_dir_module' => env('DEBUG_AUTO_CREATE_LANG_PARENT_DIR_MODULE', base_path('../Modules')),

        'prefix_dir_module' => env('DEBUG_AUTO_CREATE_LANG_PREFIX_DIR_MODULE', ''),

        // First character of directory module to uppercase
        'dir_module_ucfirst' => true,

        // in this language files content it will not replace
        'secure_override_language_files' => ['pl']
    ]
];