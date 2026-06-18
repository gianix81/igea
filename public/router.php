<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

if (str_starts_with($path, '/api/') && str_ends_with($path, '.php')) {
    $apiRoot = realpath(__DIR__ . '/../api');
    $target = realpath($apiRoot . '/' . substr($path, 5));
    if ($target && str_starts_with($target, $apiRoot) && is_file($target)) {
        require $target;
        return true;
    }
}

$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
return true;
