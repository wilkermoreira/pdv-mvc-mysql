<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="mb-0">Venda #<?= (int) $venda->id ?></h1>
  <a class="btn btn-secondary" href="<?= e(url('relatorios')) ?>">Voltar</a>
</div>

<p>
  <strong>Data:</strong> <?= e(data_br($venda->criadoEm, true)) ?> |
  <strong>Status:</strong> <?= $venda->estaConcluida() ? 'Concluída' : 'Cancelada' ?> |
  <strong>Cliente:</strong> <?= e($venda->clienteNome ?? 'Consumidor não identificado') ?>
</p>

<form method="post" action="<?= e(url('relatorios/cliente')) ?>" class="row g-2 mb-4">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int) $venda->id ?>">
  <div class="col-md-6">
    <select class="form-select" name="cliente_id">
      <option value="">Consumidor não identificado</option>
      <?php foreach ($clientes as $c): ?>
        <option value="<?= (int) $c->id ?>" <?= $venda->clienteId === $c->id ? 'selected' : '' ?>><?= e($c->nome) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3"><button class="btn btn-outline-primary w-100">Alterar cliente da venda</button></div>
</form>

<div class="table-responsive">
  <table class="table">
    <thead><tr><th>Produto</th><th>Quantidade</th><th>Preço unitário</th><th>Subtotal</th></tr></thead>
    <tbody>
    <?php foreach ($venda->itens as $i): ?>
      <tr>
        <td><?= e($i->nomeProduto) ?></td>
        <td><?= (int) $i->quantidade ?></td>
        <td><?= e(money($i->precoUnitario)) ?></td>
        <td><?= e(money($i->subtotal())) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<h4 class="text-end">Total: <?= e(money($venda->total)) ?></h4>
