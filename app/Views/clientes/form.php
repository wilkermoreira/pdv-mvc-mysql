<h1><?= $cliente->id ? 'Editar cliente' : 'Novo cliente' ?></h1>
<form method="post" action="<?= e(url('clientes/salvar')) ?>" class="card card-body">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= e($cliente->id) ?>">
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nome *</label>
      <input required maxlength="150" class="form-control" name="nome" value="<?= e($cliente->nome) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">CPF</label>
      <input class="form-control" name="cpf" maxlength="14" placeholder="000.000.000-00" value="<?= e($cliente->cpf) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">E-mail</label>
      <input type="email" class="form-control" name="email" value="<?= e($cliente->email) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Telefone</label>
      <input class="form-control" name="telefone" value="<?= e($cliente->telefone) ?>">
    </div>
    <div class="col-12">
      <label class="form-label">Endereço</label>
      <input class="form-control" name="endereco" value="<?= e($cliente->endereco) ?>">
    </div>
  </div>
  <div class="mt-3">
    <button class="btn btn-success">Salvar</button>
    <a class="btn btn-secondary" href="<?= e(url('clientes')) ?>">Voltar</a>
  </div>
</form>
