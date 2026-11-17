<?php
declare(strict_types=1);

/** CONTROLLER: carrinho de vendas. */
final class VendaController extends Controller
{
    private ProdutoRepository $produtos;
    private ClienteRepository $clientes;
    private CarrinhoService $carrinho;

    public function __construct()
    {
        $db = Database::connection();
        $this->produtos = new ProdutoRepository($db);
        $this->clientes = new ClienteRepository($db);
        $this->carrinho = new CarrinhoService($this->produtos); // injeção de dependência
    }

    /** READ: busca de produtos + itens do carrinho. */
    public function index(): void
    {
        $busca = trim((string) $this->get('q'));
        $itens = $this->carrinho->itens();
        $this->render('vendas/index', [
            'produtos' => $this->produtos->all($busca),
            'busca'    => $busca,
            'itens'    => $itens,
            'total'    => round(array_sum(array_column($itens, 'subtotal')), 2),
            'clientes' => $this->clientes->all(),
        ]);
    }

    /** CREATE: adiciona ao carrinho. */
    public function adicionar(): void
    {
        try {
            $this->carrinho->adicionar((int) $this->post('produto_id'), (int) $this->post('quantidade', 1));
            $this->sucesso('Produto adicionado ao carrinho.');
        } catch (DomainException $e) {
            $this->erro($e->getMessage());
        }
        $this->redirect('vendas', array_filter(['q' => (string) $this->post('q')]));
    }

    /** UPDATE: altera quantidades. */
    public function atualizar(): void
    {
        $avisos = $this->carrinho->atualizar((array) $this->post('qtd', []));
        if ($avisos) {
            $this->erro(implode(' ', $avisos));
        } else {
            $this->sucesso('Carrinho atualizado.');
        }
        $this->redirect('vendas');
    }

    /** DELETE: remove um item. */
    public function remover(): void
    {
        $this->carrinho->remover((int) $this->post('produto_id'));
        $this->sucesso('Item removido do carrinho.');
        $this->redirect('vendas');
    }

    /** DELETE: esvazia o carrinho. */
    public function limpar(): void
    {
        $this->carrinho->limpar();
        $this->sucesso('Carrinho esvaziado.');
        $this->redirect('vendas');
    }

    /** Fecha a venda: grava itens e baixa o estoque (VendaService). */
    public function finalizar(): void
    {
        try {
            $service = new VendaService(Database::connection());
            $clienteId = (int) $this->post('cliente_id') ?: null;
            $vendaId = $service->finalizar($clienteId, $this->carrinho->bruto());
            $this->carrinho->limpar();
            $this->sucesso("Venda #{$vendaId} finalizada com sucesso.");
            $this->redirect('relatorios/detalhe', ['id' => $vendaId]);
        } catch (DomainException $e) {
            $this->erro($e->getMessage());
        }
        $this->redirect('vendas');
    }
}
