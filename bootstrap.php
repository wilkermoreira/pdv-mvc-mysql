<?php
declare(strict_types=1);

date_default_timezone_set('America/Cuiaba');
session_start();

// Carrega o .env sem depender de bibliotecas externas.
$envFile = __DIR__ . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        if (getenv($key) === false) {
            putenv($key . '=' . trim($value, " \t\"'"));
        }
    }
}

// Autoload simples: procura a classe nas pastas da aplicação.
spl_autoload_register(function (string $class): void {
    foreach (['Core', 'Models', 'Repositories', 'Services', 'Controllers'] as $folder) {
        $file = __DIR__ . '/app/' . $folder . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/app/Core/helpers.php';
