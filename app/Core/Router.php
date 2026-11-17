<?php
declare(strict_types=1);

/**
 * PADRÃO FRONT CONTROLLER
 * Todas as requisições entram por public/index.php e são despachadas por esta
 * classe para o Controller/método correto, de acordo com a tabela de rotas.
 * Também valida o token CSRF de toda requisição POST.
 */
final class Router
{
    /** @var array<string, array{0: string, 1: string}> */
    private array $routes = [];

    public function get(string $route, string $controller, string $method): void
    {
        $this->routes['GET ' . $route] = [$controller, $method];
    }

    public function post(string $route, string $controller, string $method): void
    {
        $this->routes['POST ' . $route] = [$controller, $method];
    }

    public function dispatch(string $httpMethod, string $route): void
    {
        $key = $httpMethod . ' ' . $route;
        if (!isset($this->routes[$key])) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        if ($httpMethod === 'POST' && !hash_equals(csrf_token(), (string) ($_POST['_token'] ?? ''))) {
            $_SESSION['erro'] = 'Sessão expirada. Repita a operação.';
            header('Location: ' . url('home'));
            exit;
        }

        [$class, $method] = $this->routes[$key];
        (new $class())->$method();
    }
}
