<?php
declare(strict_types=1);

/** MODEL: item de uma venda (produto, quantidade e preço no momento da compra). */
final class ItemVenda
{
    public function __construct(
        public ?int $produtoId,
        public string $nomeProduto,
        public int $quantidade,
        public float $precoUnitario,
    ) {}

    public function subtotal(): float
    {
        return round($this->precoUnitario * $this->quantidade, 2);
    }
}
