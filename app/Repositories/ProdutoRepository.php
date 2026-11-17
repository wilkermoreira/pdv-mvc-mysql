<?php
declare(strict_types=1);

/** PADRÃO REPOSITORY: isola todo o SQL de produtos. */
final class ProdutoRepository
{
    public function __construct(private PDO $db) {}

    private function hydrate(array $r): Produto
    {
        return new Produto(
            (int) $r['id'],
            $r['cod'],
            $r['nome'],
            $r['categoria'],
            $r['validade'],
            (int) $r['quantidade'],
            (float) $r['preco_unitario'],
            (float) $r['preco_pacote'],
            $r['imagem'],
        );
    }

    /** @return Produto[] */
    public function all(string $busca = ''): array
    {
        $st = $this->db->prepare(
            'SELECT * FROM produtos WHERE nome LIKE :q1 OR cod LIKE :q2 OR categoria LIKE :q3 ORDER BY nome'
        );
        $like = "%{$busca}%";
        $st->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
        return array_map([$this, 'hydrate'], $st->fetchAll());
    }

    public function find(int $id): ?Produto
    {
        $st = $this->db->prepare('SELECT * FROM produtos WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function codExiste(string $cod, ?int $ignorarId = null): bool
    {
        $st = $this->db->prepare('SELECT COUNT(*) FROM produtos WHERE cod = ? AND id <> ?');
        $st->execute([$cod, $ignorarId ?? 0]);
        return (int) $st->fetchColumn() > 0;
    }

    public function create(Produto $p): int
    {
        $st = $this->db->prepare(
            'INSERT INTO produtos (cod, nome, categoria, validade, quantidade, preco_unitario, preco_pacote, imagem)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([$p->cod, $p->nome, $p->categoria, $p->validade, $p->quantidade,
                      $p->precoUnitario, $p->precoPacote, $p->imagem]);
        return (int) $this->db->lastInsertId();
    }

    public function update(Produto $p): void
    {
        $st = $this->db->prepare(
            'UPDATE produtos SET cod = ?, nome = ?, categoria = ?, validade = ?, quantidade = ?,
                    preco_unitario = ?, preco_pacote = ?, imagem = ? WHERE id = ?'
        );
        $st->execute([$p->cod, $p->nome, $p->categoria, $p->validade, $p->quantidade,
                      $p->precoUnitario, $p->precoPacote, $p->imagem, $p->id]);
    }

    public function delete(int $id): void
    {
        $st = $this->db->prepare('DELETE FROM produtos WHERE id = ?');
        $st->execute([$id]);
    }
}
