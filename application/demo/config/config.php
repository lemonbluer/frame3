<?php
return [
    'debug_mode' => TRUE, // debug模式
    // 'debug_mode' => FALSE, // debug模式
    'app_online' => FALSE,
    'tunning_out_put_type' => 'JSON',
    'view_bed' => 'bed',
    'database' =>
    [
        'demo' =>
        [
            'rw_seperate' => FALSE, // 读写分离模式（主从模式），只支持一主多从
            'table_prefix' => 'f_',
            'instances' => [
                [
                    'is_master' => TRUE,
                    'type' => 'mysql',
                    'hostname' => '192.168.1.130',
                    'db_name' => 'demo',
                    'user' => 'root',
                    'password' => 'root',
                ],
                [
                    'is_master' => FALSE,
                    'type' => 'mysql',
                    'hostname' => '192.168.1.130',
                    'db_name' => 'demo',
                    'user' => 'root',
                    'password' => 'root',
                ],
            ],
        ]
        ,
    ],
];