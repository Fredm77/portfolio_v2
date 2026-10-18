<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$projectRoot = dirname(__DIR__);

if (is_file($projectRoot . '/.env')) {
    Dotenv\Dotenv::createUnsafeImmutable($projectRoot)->safeLoad();
}

function config_env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);

    if ($value !== false) {
        return $value;
    }

    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}
