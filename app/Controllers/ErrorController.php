<?php
declare(strict_types=1);

final class ErrorController extends Controller
{
    public function notFound(): void
    {
        $this->render('errors/404');
    }

    public function serverError(Throwable $e): void
    {
        $debug = filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN);
        $this->render('errors/500', ['detalhe' => $debug ? $e->getMessage() : null]);
    }
}
