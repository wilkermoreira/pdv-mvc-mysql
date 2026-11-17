<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Cadastro de clientes</h1>
  <a class="btn btn-primary" href="<?= e(url('clientes/novo')) ?>">Novo cliente</a>
</div>

<form method="get" class="row g-2 mb-3">
  <input type="hidden" name="r" value="clientes">
  <div class="col-md-9">
    <input class="form-control" name="q" placeholder="Buscar por nome, CPF, e-mail ou telefone" value="<?= e($busca) ?>">
  </div>
  <div class="col-md-3"><button class="btn btn-outline-primary w-100">Buscar</button></div>
</form>

<div class="table-responsive">
  <table class="table table-striped">
    <thead><tr><th>Nome</th><th>CPF</th><th>E-mail</th><th>Telefone</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($clientes as $c): ?>
      <tr>
        <td><?= e($c->nome) ?></td>
        <td><?= e($c->cpf) ?></td>
        <td><?= e($c->email) ?></td>
        <td><?= e($c->telefone) ?></td>
        <td class="d-flex gap-2">
          <a class="btn btn-sm btn-warning" href="<?= e(url('clientes/editar', ['id' => $c->id])) ?>">Editar</a>
          <form method="post" action="<?= e(url('clientes/excluir')) ?>" onsubmit="return confirm('Excluir este cliente?')">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $c->id ?>">
            <button class="btn btn-sm btn-danger">Excluir</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$clientes): ?><tr><td colspan="5">Nenhum cliente encontrado.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
