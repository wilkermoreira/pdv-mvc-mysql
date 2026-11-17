<?php
declare(strict_types=1);

/** MODEL: entidade Cliente com suas regras de validação. */
final class Cliente
{
    public function __construct(
        public ?int $id = null,
        public string $nome = '',
        public ?string $cpf = null,
        public ?string $email = null,
        public ?string $telefone = null,
        public ?string $endereco = null,
        public ?string $criadoEm = null,
    ) {}

    /** Cria o objeto a partir do formulário (POST) ou de uma linha do banco. */
    public static function fromArray(array $d): self
    {
        $vazioParaNull = static fn($v) => ($v = trim((string) $v)) === '' ? null : $v;
        $cpf = $vazioParaNull($d['cpf'] ?? '');
        if ($cpf !== null) {
            $digitos = preg_replace('/\D/', '', $cpf);
            if (strlen($digitos) === 11) {
                $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digitos);
            }
        }
        return new self(
            isset($d['id']) && $d['id'] !== '' ? (int) $d['id'] : null,
            trim((string) ($d['nome'] ?? '')),
            $cpf,
            $vazioParaNull($d['email'] ?? ''),
            $vazioParaNull($d['telefone'] ?? ''),
            $vazioParaNull($d['endereco'] ?? ''),
            $d['criado_em'] ?? null,
        );
    }

    /** Retorna a mensagem de erro ou null se os dados estiverem válidos. */
    public function validar(): ?string
    {
        if ($this->nome === '') {
            return 'O nome do cliente é obrigatório.';
        }
        if (strlen($this->nome) > 150) {
            return 'O nome pode ter no máximo 150 caracteres.';
        }
        if ($this->email !== null && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return 'Informe um e-mail válido.';
        }
        if ($this->cpf !== null && !self::cpfValido($this->cpf)) {
            return 'CPF inválido.';
        }
        return null;
    }

    public static function cpfValido(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * (($t + 1) - $i);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }
        return true;
    }
}
