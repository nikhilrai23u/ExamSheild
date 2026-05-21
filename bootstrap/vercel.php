<?php

$tmpPath = '/tmp/laravel';

$directories = [
    "{$tmpPath}/bootstrap/cache",
    "{$tmpPath}/storage/app",
    "{$tmpPath}/storage/framework/cache/data",
    "{$tmpPath}/storage/framework/sessions",
    "{$tmpPath}/storage/framework/testing",
    "{$tmpPath}/storage/framework/views",
    "{$tmpPath}/storage/logs",
];

foreach ($directories as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

$environment = [
    'APP_CONFIG_CACHE' => "{$tmpPath}/bootstrap/cache/config.php",
    'APP_EVENTS_CACHE' => "{$tmpPath}/bootstrap/cache/events.php",
    'APP_PACKAGES_CACHE' => "{$tmpPath}/bootstrap/cache/packages.php",
    'APP_ROUTES_CACHE' => "{$tmpPath}/bootstrap/cache/routes.php",
    'APP_SERVICES_CACHE' => "{$tmpPath}/bootstrap/cache/services.php",
    'LARAVEL_STORAGE_PATH' => "{$tmpPath}/storage",
    'VIEW_COMPILED_PATH' => "{$tmpPath}/storage/framework/views",
];

foreach ($environment as $key => $value) {
    $_ENV[$key] = $_SERVER[$key] = $value;
    putenv("{$key}={$value}");
}

if (getenv('LOG_CHANNEL') === false) {
    $_ENV['LOG_CHANNEL'] = $_SERVER['LOG_CHANNEL'] = 'stderr';
    putenv('LOG_CHANNEL=stderr');
}
