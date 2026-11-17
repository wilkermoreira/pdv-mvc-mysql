<?php
declare(strict_types=1);

/** MODEL: entidade Venda. Calcula o total a partir dos itens. */
final class Venda
{
    public const CONCLUIDA = 'concluida';
    public const CANCELADA = 'cancelada';

    /** @param ItemVenda[] $itens */
    public function __construct(
        public ?int $id = null,
        public ?int $clienteId = null,
        public ?string $clienteNome = null,
        public float $total = 0.0,
        public string $status = self::CONCLUIDA,
        public ?string $criadoEm = null,
        public array $itens = [],
    ) {}

    public static function fromRow(array $r): self
    {
        return new self(
            (int) $r['id'],
            $r['cliente_id'] !== null ? (int) $r['cliente_id'] : null,
            $r['cliente_nome'] ?? null,
            (float) $r['total'],
            $r['status'],
            $r['criado_em'],
        );
    }

    /** @param ItemVenda[] $itens */
    public static function calcularTotal(array $itens): float
    {
        $total = 0.0;
        foreach ($itens as $item) {
            $total += $item->subtotal();
        }
        return round($total, 2);
    }

    public function estaConcluida(): bool
    {
        return $this->status === self::CONCLUIDA;
    }
}
