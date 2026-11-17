<?php
declare(strict_types=1);

/** CONTROLLER: relatórios de vendas (consulta, edição do cliente, cancelamento e exclusão). */
final class RelatorioController extends Controller
{
    private VendaRepository $vendas;

    public function __construct()
    {
        $this->vendas = new VendaRepository(Database::connection());
    }

    /** READ: lista com filtros e resumo. */
    public function index(): void
    {
        $filtros = [
            'inicio'     => (string) $this->get('inicio'),
            'fim'        => (string) $this->get('fim'),
            'status'     => (string) $this->get('status'),
            'cliente_id' => (string) $this->get('cliente_id'),
        ];
        $this->render('relatorios/index', [
            'filtros'      => $filtros,
            'vendas'       => $this->vendas->all($filtros),
            'resumo'       => $this->vendas->resumo($filtros),
            'maisVendidos' => $this->vendas->maisVendidos($filtros),
            'clientes'     => (new ClienteRepository(Database::connection()))->all(),
        ]);
    }

    /** READ: detalhes de uma venda. */
    public function detalhe(): void
    {
        $venda = $this->vendas->find((int) $this->get('id'));
        if (!$venda) {
            $this->erro('Venda não encontrada.');
            $this->redirect('relatorios');
        }
        $this->render('relatorios/detalhe', [
            'venda'    => $venda,
            'clientes' => (new ClienteRepository(Database::connection()))->all(),
        ]);
    }

    /** UPDATE: troca o cliente associado à venda. */
    public function alterarCliente(): void
    {
        $id = (int) $this->post('id');
        $this->vendas->alterarCliente($id, (int) $this->post('cliente_id') ?: null);
        $this->sucesso('Cliente da venda atualizado.');
        $this->redirect('relatorios/detalhe', ['id' => $id]);
    }

    /** UPDATE: cancela a venda e devolve o estoque. */
    public function cancelar(): void
    {
        $id = (int) $this->post('id');
        try {
            (new VendaService(Database::connection()))->cancelar($id);
            $this->sucesso("Venda #{$id} cancelada e estoque devolvido.");
        } catch (DomainException $e) {
            $this->erro($e->getMessage());
        }
        $this->redirect('relatorios');
    }

    /** DELETE: remove do histórico apenas vendas já canceladas. */
    public function excluir(): void
    {
        $id = (int) $this->post('id');
        if ($this->vendas->excluirCancelada($id)) {
            $this->sucesso("Venda #{$id} excluída do histórico.");
        } else {
            $this->erro('Só é possível excluir vendas canceladas.');
        }
        $this->redirect('relatorios');
    }
}
