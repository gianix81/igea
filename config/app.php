<?php

return [
    'name' => 'Igea Club Pool Manager',
    'base_path' => dirname(__DIR__),
    'base_url' => getenv('APP_URL') ?: '',
    'session_name' => 'igea_pool_manager',
    'session_timeout' => 7200,
    'timezone' => 'Europe/Rome',
    'debug' => (bool) (getenv('APP_DEBUG') ?: false),
];
