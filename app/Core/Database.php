<?php
declare(strict_types=1);

/**
 * PADRÃO SINGLETON
 * Garante uma única conexão PDO por requisição. Quem precisa do banco chama
 * Database::connection() e recebe sempre a mesma instância.
 */
final class Database
{
    private static ?PDO $connection = null;

    private function __construct() {}
    private function __clone() {}

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $c = require dirname(__DIR__, 2) . '/config/database.php';
            $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset={$c['charset']}";
            self::$connection = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$connection;
    }
}
