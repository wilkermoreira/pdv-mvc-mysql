<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

$router = new Router();

$router->get('home', 'HomeController', 'index');

// Produtos (CRUD)
$router->get('produtos',          'ProdutoController', 'index');
$router->get('produtos/novo',     'ProdutoController', 'form');
$router->get('produtos/editar',   'ProdutoController', 'form');
$router->post('produtos/salvar',  'ProdutoController', 'salvar');
$router->post('produtos/excluir', 'ProdutoController', 'excluir');

// Clientes (CRUD)
$router->get('clientes',          'ClienteController', 'index');
$router->get('clientes/novo',     'ClienteController', 'form');
$router->get('clientes/editar',   'ClienteController', 'form');
$router->post('clientes/salvar',  'ClienteController', 'salvar');
$router->post('clientes/excluir', 'ClienteController', 'excluir');

// Carrinho de vendas (CRUD do carrinho + finalizar)
$router->get('vendas',             'VendaController', 'index');
$router->post('vendas/adicionar',  'VendaController', 'adicionar');
$router->post('vendas/atualizar',  'VendaController', 'atualizar');
$router->post('vendas/remover',    'VendaController', 'remover');
$router->post('vendas/limpar',     'VendaController', 'limpar');
$router->post('vendas/finalizar',  'VendaController', 'finalizar');

// Relatórios de vendas (CRUD sobre o histórico)
$router->get('relatorios',            'RelatorioController', 'index');
$router->get('relatorios/detalhe',    'RelatorioController', 'detalhe');
$router->post('relatorios/cliente',   'RelatorioController', 'alterarCliente');
$router->post('relatorios/cancelar',  'RelatorioController', 'cancelar');
$router->post('relatorios/excluir',   'RelatorioController', 'excluir');

try {
    $route = trim((string) ($_GET['r'] ?? 'home'), '/');
    $router->dispatch($_SERVER['REQUEST_METHOD'], $route === '' ? 'home' : $route);
} catch (Throwable $e) {
    error_log(
        '[' . date('Y-m-d H:i:s') . '] ' . $e::class . ': ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL,
        3,
        dirname(__DIR__) . '/storage/logs/app.log'
    );
    http_response_code(500);
    (new ErrorController())->serverError($e);
}
