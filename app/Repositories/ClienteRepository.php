<?php
declare(strict_types=1);

/**
 * PADRÃO REPOSITORY
 * Isola todo o SQL de clientes. Controllers e Services trabalham com objetos
 * Cliente e não precisam saber como os dados são gravados.
 */
final class ClienteRepository
{
    public function __construct(private PDO $db) {}

    /** @return Cliente[] */
    public function all(string $busca = ''): array
    {
        $sql = 'SELECT * FROM clientes';
        $params = [];
        if ($busca !== '') {
            // Com prepares nativos o mesmo :nome de parâmetro não pode se repetir.
            $sql .= ' WHERE nome LIKE :q1 OR email LIKE :q2 OR cpf LIKE :q3 OR telefone LIKE :q4';
            $params = ['q1' => "%{$busca}%", 'q2' => "%{$busca}%", 'q3' => "%{$busca}%", 'q4' => "%{$busca}%"];
        }
        $st = $this->db->prepare($sql . ' ORDER BY nome');
        $st->execute($params);
        return array_map([Cliente::class, 'fromArray'], $st->fetchAll());
    }

    public function find(int $id): ?Cliente
    {
        $st = $this->db->prepare('SELECT * FROM clientes WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ? Cliente::fromArray($row) : null;
    }

    public function cpfExiste(string $cpf, ?int $ignorarId = null): bool
    {
        $st = $this->db->prepare('SELECT COUNT(*) FROM clientes WHERE cpf = ? AND id <> ?');
        $st->execute([$cpf, $ignorarId ?? 0]);
        return (int) $st->fetchColumn() > 0;
    }

    public function create(Cliente $c): int
    {
        $st = $this->db->prepare(
            'INSERT INTO clientes (nome, cpf, email, telefone, endereco) VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([$c->nome, $c->cpf, $c->email, $c->telefone, $c->endereco]);
        return (int) $this->db->lastInsertId();
    }

    public function update(Cliente $c): void
    {
        $st = $this->db->prepare(
            'UPDATE clientes SET nome = ?, cpf = ?, email = ?, telefone = ?, endereco = ? WHERE id = ?'
        );
        $st->execute([$c->nome, $c->cpf, $c->email, $c->telefone, $c->endereco, $c->id]);
    }

    public function delete(int $id): void
    {
        $st = $this->db->prepare('DELETE FROM clientes WHERE id = ?');
        $st->execute([$id]);
    }
}
