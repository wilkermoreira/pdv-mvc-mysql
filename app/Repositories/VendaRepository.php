<?php
declare(strict_types=1);

/** PADRÃO REPOSITORY: consultas de vendas e relatórios. */
final class VendaRepository
{
    public function __construct(private PDO $db) {}

    /** Monta o WHERE comum aos relatórios (período, status e cliente). */
    private function filtro(array $f, array &$params): string
    {
        $where = ' WHERE 1=1';
        if (!empty($f['inicio'])) {
            $where .= ' AND DATE(v.criado_em) >= :inicio';
            $params['inicio'] = $f['inicio'];
        }
        if (!empty($f['fim'])) {
            $where .= ' AND DATE(v.criado_em) <= :fim';
            $params['fim'] = $f['fim'];
        }
        if (!empty($f['status'])) {
            $where .= ' AND v.status = :status';
            $params['status'] = $f['status'];
        }
        if (!empty($f['cliente_id'])) {
            $where .= ' AND v.cliente_id = :cliente_id';
            $params['cliente_id'] = (int) $f['cliente_id'];
        }
        return $where;
    }

    /** @return Venda[] */
    public function all(array $filtros = []): array
    {
        $params = [];
        $sql = 'SELECT v.*, c.nome AS cliente_nome FROM vendas v
                LEFT JOIN clientes c ON c.id = v.cliente_id'
             . $this->filtro($filtros, $params) . ' ORDER BY v.criado_em DESC, v.id DESC';
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return array_map([Venda::class, 'fromRow'], $st->fetchAll());
    }

    public function find(int $id): ?Venda
    {
        $st = $this->db->prepare(
            'SELECT v.*, c.nome AS cliente_nome FROM vendas v
             LEFT JOIN clientes c ON c.id = v.cliente_id WHERE v.id = ?'
        );
        $st->execute([$id]);
        $row = $st->fetch();
        if (!$row) {
            return null;
        }
        $venda = Venda::fromRow($row);
        $venda->itens = $this->itens($id);
        return $venda;
    }

    /** @return ItemVenda[] */
    public function itens(int $vendaId): array
    {
        $st = $this->db->prepare('SELECT * FROM venda_itens WHERE venda_id = ? ORDER BY id');
        $st->execute([$vendaId]);
        return array_map(
            static fn(array $r) => new ItemVenda(
                $r['produto_id'] !== null ? (int) $r['produto_id'] : null,
                $r['nome_produto'],
                (int) $r['quantidade'],
                (float) $r['preco_unitario'],
            ),
            $st->fetchAll()
        );
    }

    public function alterarCliente(int $vendaId, ?int $clienteId): void
    {
        $st = $this->db->prepare('UPDATE vendas SET cliente_id = ? WHERE id = ?');
        $st->execute([$clienteId, $vendaId]);
    }

    /** Só vendas canceladas podem ser apagadas do histórico. */
    public function excluirCancelada(int $id): bool
    {
        $st = $this->db->prepare("DELETE FROM vendas WHERE id = ? AND status = 'cancelada'");
        $st->execute([$id]);
        return $st->rowCount() > 0;
    }

    /** Totais do período considerando apenas vendas concluídas. */
    public function resumo(array $filtros = []): array
    {
        $filtros['status'] = Venda::CONCLUIDA;
        $params = [];
        $sql = 'SELECT COUNT(*) AS qtd, COALESCE(SUM(v.total), 0) AS total FROM vendas v'
             . $this->filtro($filtros, $params);
        $st = $this->db->prepare($sql);
        $st->execute($params);
        $r = $st->fetch();
        $qtd = (int) $r['qtd'];
        $total = (float) $r['total'];
        return ['qtd' => $qtd, 'total' => $total, 'ticket' => $qtd > 0 ? round($total / $qtd, 2) : 0.0];
    }

    /** Produtos mais vendidos no período (somente vendas concluídas). */
    public function maisVendidos(array $filtros = [], int $limite = 5): array
    {
        $filtros['status'] = Venda::CONCLUIDA;
        $params = [];
        $sql = 'SELECT i.nome_produto, SUM(i.quantidade) AS qtd, SUM(i.subtotal) AS total
                FROM venda_itens i INNER JOIN vendas v ON v.id = i.venda_id'
             . $this->filtro($filtros, $params)
             . ' GROUP BY i.nome_produto ORDER BY qtd DESC, total DESC LIMIT ' . (int) $limite;
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }
}
