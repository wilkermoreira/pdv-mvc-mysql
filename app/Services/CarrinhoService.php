<?php
declare(strict_types=1);

/**
 * PADRÃO SERVICE LAYER
 * Regras do carrinho de compras. O carrinho fica na sessão como [produto_id => quantidade]
 * e é sempre validado contra o estoque atual do banco.
 */
final class CarrinhoService
{
    private const CHAVE = 'carrinho';

    public function __construct(private ProdutoRepository $produtos) {}

    /** CREATE: adiciona um produto (somando se já estiver no carrinho). */
    public function adicionar(int $produtoId, int $quantidade): void
    {
        $produto = $this->produtos->find($produtoId);
        if (!$produto) {
            throw new DomainException('Produto não encontrado.');
        }
        if ($quantidade < 1) {
            throw new DomainException('Informe uma quantidade maior que zero.');
        }
        $novaQtd = ($_SESSION[self::CHAVE][$produtoId] ?? 0) + $quantidade;
        if ($novaQtd > $produto->quantidade) {
            throw new DomainException(
                "Estoque insuficiente de \"{$produto->nome}\" (disponível: {$produto->quantidade})."
            );
        }
        $_SESSION[self::CHAVE][$produtoId] = $novaQtd;
    }

    /**
     * UPDATE: altera várias quantidades de uma vez. Quantidade 0 remove o item.
     * @param array<int|string, int|string> $quantidades
     * @return string[] avisos (ex.: quantidade ajustada ao estoque)
     */
    public function atualizar(array $quantidades): array
    {
        $avisos = [];
        foreach ($quantidades as $id => $qtd) {
            $id = (int) $id;
            $qtd = (int) $qtd;
            if (!isset($_SESSION[self::CHAVE][$id])) {
                continue;
            }
            if ($qtd <= 0) {
                $this->remover($id);
                continue;
            }
            $produto = $this->produtos->find($id);
            if (!$produto) {
                $this->remover($id);
                continue;
            }
            if ($qtd > $produto->quantidade) {
                $qtd = $produto->quantidade;
                $avisos[] = "Quantidade de \"{$produto->nome}\" ajustada ao estoque ({$qtd}).";
            }
            $_SESSION[self::CHAVE][$id] = $qtd;
        }
        return $avisos;
    }

    /** DELETE: remove um item. */
    public function remover(int $produtoId): void
    {
        unset($_SESSION[self::CHAVE][$produtoId]);
    }

    /** DELETE: esvazia o carrinho. */
    public function limpar(): void
    {
        unset($_SESSION[self::CHAVE]);
    }

    /** @return array<int, int> produto_id => quantidade */
    public function bruto(): array
    {
        return $_SESSION[self::CHAVE] ?? [];
    }

    /**
     * READ: itens do carrinho já com os dados atuais do produto.
     * @return array<int, array{produto: Produto, quantidade: int, subtotal: float}>
     */
    public function itens(): array
    {
        $itens = [];
        foreach ($this->bruto() as $id => $qtd) {
            $produto = $this->produtos->find((int) $id);
            if (!$produto) {
                $this->remover((int) $id); // produto foi excluído enquanto estava no carrinho
                continue;
            }
            $itens[] = [
                'produto'    => $produto,
                'quantidade' => (int) $qtd,
                'subtotal'   => round($produto->precoUnitario * $qtd, 2),
            ];
        }
        return $itens;
    }

    public function total(): float
    {
        return round(array_sum(array_column($this->itens(), 'subtotal')), 2);
    }
}
