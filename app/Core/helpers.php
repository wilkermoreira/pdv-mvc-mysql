<?php
declare(strict_types=1);

/** Escapa texto para exibição segura em HTML (proteção contra XSS). */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Formata valor em reais. */
function money(float|int|string $value): string
{
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
}

/** Pasta base da aplicação (funciona na raiz ou em subpasta do XAMPP). */
function base_path(): string
{
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    return $dir === '.' ? '' : $dir;
}

/** Monta a URL de uma rota: url('clientes/editar', ['id' => 3]). */
function url(string $route = 'home', array $params = []): string
{
    return base_path() . '/index.php?' . http_build_query(['r' => $route] + $params);
}

/** URL de arquivos públicos (css, imagens). */
function asset(string $path): string
{
    return base_path() . '/assets/' . ltrim($path, '/');
}

/** Token CSRF da sessão. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Campo hidden com o token CSRF para colocar em todo formulário POST. */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/** Formata data/hora do banco para o padrão brasileiro. */
function data_br(?string $value, bool $hora = false): string
{
    if (!$value) {
        return '—';
    }
    return date($hora ? 'd/m/Y H:i' : 'd/m/Y', strtotime($value));
}
