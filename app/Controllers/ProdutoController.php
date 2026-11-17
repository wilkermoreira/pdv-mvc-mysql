<?php
declare(strict_types=1);

/** CONTROLLER: CRUD de produtos (necessário para alimentar o carrinho). */
final class ProdutoController extends Controller
{
    private ProdutoRepository $produtos;
    private ImageUploader $uploader;

    public function __construct()
    {
        $this->produtos = new ProdutoRepository(Database::connection());
        $this->uploader = ImageUploader::padrao();
    }

    public function index(): void
    {
        $busca = trim((string) $this->get('q'));
        $this->render('produtos/index', [
            'produtos' => $this->produtos->all($busca),
            'busca'    => $busca,
        ]);
    }

    public function form(): void
    {
        $id = (int) $this->get('id', 0);
        $produto = $id ? $this->produtos->find($id) : new Produto();
        if (!$produto) {
            $this->erro('Produto não encontrado.');
            $this->redirect('produtos');
        }
        if (!empty($_SESSION['old'])) {
            $old = $_SESSION['old'];
            unset($_SESSION['old']);
            $novo = Produto::fromArray($old + ['id' => $id ?: '']);
            $novo->imagem = $produto->imagem; // a imagem atual é preservada
            $produto = $novo;
        }
        $this->render('produtos/form', ['produto' => $produto]);
    }

    public function salvar(): void
    {
        $produto = Produto::fromArray($_POST);
        $atual = $produto->id ? $this->produtos->find($produto->id) : null;
        $voltar = $produto->id ? ['produtos/editar', ['id' => $produto->id]] : ['produtos/novo', []];

        if ($produto->id && !$atual) {
            $this->erro('Produto não encontrado.');
            $this->redirect('produtos');
        }

        $erro = $produto->validar();
        if (!$erro && $this->produtos->codExiste($produto->cod, $produto->id)) {
            $erro = 'Já existe um produto com este código.';
        }

        $novaImagem = null;
        if (!$erro) {
            try {
                $novaImagem = $this->uploader->salvar($_FILES['imagem'] ?? null);
            } catch (DomainException $e) {
                $erro = $e->getMessage();
            }
        }

        if ($erro) {
            $this->erro($erro);
            $_SESSION['old'] = $_POST;
            $this->redirect(...$voltar);
        }

        $produto->imagem = $novaImagem ?? $atual?->imagem;

        if ($produto->id) {
            $this->produtos->update($produto);
            if ($novaImagem !== null) {
                $this->uploader->remover($atual?->imagem); // apaga a imagem antiga enviada pelo sistema
            }
            $this->sucesso('Produto atualizado com sucesso.');
        } else {
            $this->produtos->create($produto);
            $this->sucesso('Produto cadastrado com sucesso.');
        }
        $this->redirect('produtos');
    }

    public function excluir(): void
    {
        $id = (int) $this->post('id');
        $produto = $this->produtos->find($id);
        if ($produto) {
            $this->produtos->delete($id);
            $this->uploader->remover($produto->imagem);
            $this->sucesso('Produto excluído.');
        }
        $this->redirect('produtos');
    }
}
