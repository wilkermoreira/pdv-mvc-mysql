<?php
declare(strict_types=1);

/**
 * PADRÃO SERVICE LAYER
 * Concentra as regras de fechar e cancelar vendas. Tudo roda dentro de uma
 * transação: ou grava a venda inteira (itens + baixa de estoque) ou nada.
 */
final class VendaService
{
    public function __construct(private PDO $db) {}

    /**
     * @param array<int, int> $carrinho produto_id => quantidade
     * @return int número da venda criada
     */
    public function finalizar(?int $clienteId, array $carrinho): int
    {
        if (!$carrinho) {
            throw new DomainException('O carrinho está vazio.');
        }
        ksort($carrinho); // ordem fixa de bloqueio evita deadlock entre vendas simultâneas

        $this->db->beginTransaction();
        try {
            $busca = $this->db->prepare('SELECT id, nome, quantidade, preco_unitario FROM produtos WHERE id = ? FOR UPDATE');
            $itens = [];
            foreach ($carrinho as $produtoId => $qtd) {
                $busca->execute([(int) $produtoId]);
                $p = $busca->fetch();
                if (!$p) {
                    throw new DomainException('Um dos produtos do carrinho não existe mais.');
                }
                if ($qtd < 1 || (int) $p['quantidade'] < $qtd) {
                    throw new DomainException("Estoque insuficiente de \"{$p['nome']}\" (disponível: {$p['quantidade']}).");
                }
                $itens[] = new ItemVenda((int) $p['id'], $p['nome'], (int) $qtd, (float) $p['preco_unitario']);
            }

            $total = Venda::calcularTotal($itens);
            $this->db->prepare('INSERT INTO vendas (cliente_id, total, status) VALUES (?, ?, ?)')
                     ->execute([$clienteId, $total, Venda::CONCLUIDA]);
            $vendaId = (int) $this->db->lastInsertId();

            $insereItem = $this->db->prepare(
                'INSERT INTO venda_itens (venda_id, produto_id, nome_produto, quantidade, preco_unitario, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $baixa = $this->db->prepare('UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?');
            foreach ($itens as $i) {
                $insereItem->execute([$vendaId, $i->produtoId, $i->nomeProduto, $i->quantidade, $i->precoUnitario, $i->subtotal()]);
                $baixa->execute([$i->quantidade, $i->produtoId]);
            }

            $this->db->commit();
            return $vendaId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Cancela a venda e devolve os itens ao estoque (a venda continua no histórico). */
    public function cancelar(int $vendaId): void
    {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare('SELECT status FROM vendas WHERE id = ? FOR UPDATE');
            $st->execute([$vendaId]);
            $venda = $st->fetch();
            if (!$venda) {
                throw new DomainException('Venda não encontrada.');
            }
            if ($venda['status'] === Venda::CANCELADA) {
                throw new DomainException('Esta venda já está cancelada.');
            }

            $itens = $this->db->prepare('SELECT produto_id, quantidade FROM venda_itens WHERE venda_id = ? AND produto_id IS NOT NULL');
            $itens->execute([$vendaId]);
            $devolve = $this->db->prepare('UPDATE produtos SET quantidade = quantidade + ? WHERE id = ?');
            foreach ($itens->fetchAll() as $row) {
                $devolve->execute([$row['quantidade'], $row['produto_id']]);
            }

            $this->db->prepare('UPDATE vendas SET status = ? WHERE id = ?')->execute([Venda::CANCELADA, $vendaId]);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
