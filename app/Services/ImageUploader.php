<?php
declare(strict_types=1);

/** Upload seguro de imagens de produtos (confere tipo real, tamanho e gera nome único). */
final class ImageUploader
{
    private const TIPOS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    private const MAX_BYTES = 2 * 1024 * 1024;
    private const PREFIXO = 'upload_';

    public function __construct(private string $pasta) {}

    public static function padrao(): self
    {
        return new self(dirname(__DIR__, 2) . '/public/assets/imagens');
    }

    /** @return string|null nome do arquivo salvo, ou null se nenhum arquivo foi enviado */
    public function salvar(?array $arquivo): ?string
    {
        if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            throw new DomainException('Não foi possível enviar a imagem. Tente novamente.');
        }
        if ($arquivo['size'] > self::MAX_BYTES) {
            throw new DomainException('A imagem deve ter no máximo 2 MB.');
        }
        // getimagesize confere o conteúdo real do arquivo (não confia na extensão enviada).
        $info = @getimagesize($arquivo['tmp_name']);
        $mime = $info['mime'] ?? '';
        if (!isset(self::TIPOS[$mime])) {
            throw new DomainException('Formato de imagem inválido. Use JPG, PNG, WEBP ou GIF.');
        }
        if (!is_dir($this->pasta) && !mkdir($this->pasta, 0775, true)) {
            throw new RuntimeException('Pasta de imagens indisponível.');
        }
        $nome = self::PREFIXO . bin2hex(random_bytes(8)) . '.' . self::TIPOS[$mime];
        if (!move_uploaded_file($arquivo['tmp_name'], $this->pasta . '/' . $nome)) {
            throw new RuntimeException('Falha ao gravar a imagem no servidor.');
        }
        return $nome;
    }

    /** Remove apenas arquivos enviados pelo sistema (preserva as imagens de exemplo). */
    public function remover(?string $nome): void
    {
        if ($nome && str_starts_with($nome, self::PREFIXO) && basename($nome) === $nome) {
            $arquivo = $this->pasta . '/' . $nome;
            if (is_file($arquivo)) {
                unlink($arquivo);
            }
        }
    }
}
