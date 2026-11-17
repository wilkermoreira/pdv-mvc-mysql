<?php
declare(strict_types=1);

/**
 * Controller base: fornece render(), redirect() e mensagens flash
 * para todos os Controllers da aplicação.
 */
abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewsDir = dirname(__DIR__) . '/Views/';
        require $viewsDir . 'layouts/header.php';
        require $viewsDir . $view . '.php';
        require $viewsDir . 'layouts/footer.php';
    }

    protected function redirect(string $route, array $params = []): never
    {
        header('Location: ' . url($route, $params));
        exit;
    }

    protected function post(string $key, mixed $default = ''): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = ''): mixed
    {
        return $_GET[$key] ?? $default;
    }

    protected function sucesso(string $msg): void
    {
        $_SESSION['sucesso'] = $msg;
    }

    protected function erro(string $msg): void
    {
        $_SESSION['erro'] = $msg;
    }
}
