<?php

return [

    'paths' => [
        resource_path('views'),
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        rtrim(sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'nuh-hospital-views'
    ),

];
