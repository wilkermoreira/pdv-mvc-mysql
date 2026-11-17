<?php
declare(strict_types=1);

/** MODEL: entidade Produto com suas regras de validação. */
final class Produto
{
    public function __construct(
        public ?int $id = null,
        public string $cod = '',
        public string $nome = '',
        public ?string $categoria = null,
        public ?string $validade = null,
        public int $quantidade = 0,
        public float $precoUnitario = 0.0,
        public float $precoPacote = 0.0,
        public ?string $imagem = null,
    ) {}

    public static function fromArray(array $d): self
    {
        $categoria = trim((string) ($d['categoria'] ?? ''));
        $validade = trim((string) ($d['validade'] ?? ''));
        $imagem = trim((string) ($d['imagem'] ?? ''));
        return new self(
            isset($d['id']) && $d['id'] !== '' ? (int) $d['id'] : null,
            trim((string) ($d['cod'] ?? '')),
            trim((string) ($d['nome'] ?? '')),
            $categoria === '' ? null : $categoria,
            $validade === '' ? null : $validade,
            (int) ($d['quantidade'] ?? 0),
            (float) str_replace(',', '.', (string) ($d['preco_unitario'] ?? 0)),
            (float) str_replace(',', '.', (string) ($d['preco_pacote'] ?? 0)),
            $imagem === '' ? null : $imagem,
        );
    }

    public function validar(): ?string
    {
        if ($this->cod === '' || $this->nome === '') {
            return 'Código e nome do produto são obrigatórios.';
        }
        if ($this->quantidade < 0) {
            return 'O estoque não pode ser negativo.';
        }
        if ($this->precoUnitario < 0 || $this->precoPacote < 0) {
            return 'Os preços não podem ser negativos.';
        }
        if ($this->validade !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->validade)) {
            return 'Data de validade inválida.';
        }
        return null;
    }
}
