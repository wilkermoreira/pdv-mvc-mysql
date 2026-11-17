<?php
declare(strict_types=1);

/** CONTROLLER: CRUD de clientes. */
final class ClienteController extends Controller
{
    private ClienteRepository $clientes;

    public function __construct()
    {
        $this->clientes = new ClienteRepository(Database::connection());
    }

    /** READ: lista e busca. */
    public function index(): void
    {
        $busca = trim((string) $this->get('q'));
        $this->render('clientes/index', [
            'clientes' => $this->clientes->all($busca),
            'busca'    => $busca,
        ]);
    }

    /** Formulário de cadastro/edição (CREATE / UPDATE). */
    public function form(): void
    {
        $id = (int) $this->get('id', 0);
        $cliente = $id ? $this->clientes->find($id) : new Cliente();
        if (!$cliente) {
            $this->erro('Cliente não encontrado.');
            $this->redirect('clientes');
        }
        // Se a validação falhou, reaproveita o que o usuário digitou.
        if (!empty($_SESSION['old'])) {
            $cliente = Cliente::fromArray($_SESSION['old'] + ['id' => $id ?: '']);
            unset($_SESSION['old']);
        }
        $this->render('clientes/form', ['cliente' => $cliente]);
    }

    /** CREATE / UPDATE: grava o cliente. */
    public function salvar(): void
    {
        $cliente = Cliente::fromArray($_POST);
        $voltar = $cliente->id ? ['clientes/editar', ['id' => $cliente->id]] : ['clientes/novo', []];

        $erro = $cliente->validar();
        if (!$erro && $cliente->cpf !== null && $this->clientes->cpfExiste($cliente->cpf, $cliente->id)) {
            $erro = 'Já existe um cliente cadastrado com este CPF.';
        }
        if ($erro) {
            $this->erro($erro);
            $_SESSION['old'] = $_POST;
            $this->redirect(...$voltar);
        }

        if ($cliente->id) {
            $this->clientes->update($cliente);
            $this->sucesso('Cliente atualizado com sucesso.');
        } else {
            $this->clientes->create($cliente);
            $this->sucesso('Cliente cadastrado com sucesso.');
        }
        $this->redirect('clientes');
    }

    /** DELETE: exclui (as vendas antigas permanecem, sem vínculo com o cliente). */
    public function excluir(): void
    {
        $this->clientes->delete((int) $this->post('id'));
        $this->sucesso('Cliente excluído.');
        $this->redirect('clientes');
    }
}
