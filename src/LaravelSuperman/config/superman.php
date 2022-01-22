<?php

return [
    'listen'               => 'http://0.0.0.0:8787',
    'transport'            => 'tcp',
    'context'              => [],
    'name'                 => 'superman',
    'count'                => cpu_count() * 2,
    'user'                 => '',
    'group'                => '',
    'pid_file'             => storage_path() . '/superman.pid',
    'stdout_file'          => storage_path() . '/logs/stdout.log',
    'log_file'             => storage_path() . '/logs/workerman.log',
    'max_request'          => 1000000,
    'max_package_size'     => 10*1024*1024
];