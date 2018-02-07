<?php
return [
    'frame_name' => 'frame3',
    'tunning_out_put_type' => 'HTML',
    'default_app_name' => 'demo',
    'view_bed' => NULL,
    'debug_mode' => FALSE, // debug模式
    'app_online' => TRUE, // APP是否已上线
    'static_file_extension' => ['png', 'jpg', 'js', 'css', 'jpeg'], // 静态文件后缀
    'static_file_directory' => ROOT_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'static', // 默认静态文件目录
    'session_cookie_lifetime' => 259200, // 会话cookie过期时间
];