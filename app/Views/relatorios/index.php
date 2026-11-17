<h1>Relatórios de vendas</h1>

<form method="get" class="row g-2 mb-3">
  <input type="hidden" name="r" value="relatorios">
  <div class="col-md-3">
    <label class="form-label">Data inicial</label>
    <input class="form-control" type="date" name="inicio" value="<?= e($filtros['inicio']) ?>">
  </div>
  <div class="col-md-3">
    <label class="form-label">Data final</label>
    <input class="form-control" type="date" name="fim" value="<?= e($filtros['fim']) ?>">
  </div>
  <div class="col-md-2">
    <label class="form-label">Status</label>
    <select class="form-select" name="status">
      <option value="">Todos</option>
      <option value="concluida" <?= $filtros['status'] === 'concluida' ? 'selected' : '' ?>>Concluída</option>
      <option value="cancelada" <?= $filtros['status'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
    </select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Cliente</label>
    <select class="form-select" name="cliente_id">
      <option value="">Todos</option>
      <?php foreach ($clientes as $c): ?>
        <option value="<?= (int) $c->id ?>" <?= (string) $filtros['cliente_id'] === (string) $c->id ? 'selected' : '' ?>><?= e($c->nome) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2 align-self-end">
    <button class="btn btn-primary w-100">Filtrar</button>
  </div>
</form>

<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card card-body"><small class="text-secondary">Vendas concluídas</small><strong class="fs-4"><?= (int) $resumo['qtd'] ?></strong></div></div>
  <div class="col-md-4"><div class="card card-body"><small class="text-secondary">Faturamento</small><strong class="fs-4"><?= e(money($resumo['total'])) ?></strong></div></div>
  <div class="col-md-4"><div class="card card-body"><small class="text-secondary">Ticket médio</small><strong class="fs-4"><?= e(money($resumo['ticket'])) ?></strong></div></div>
</div>

<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead><tr><th>Número</th><th>Data</th><th>Cliente</th><th>Total</th><th>Status</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($vendas as $v): ?>
      <tr>
        <td>#<?= (int) $v->id ?></td>
        <td><?= e(data_br($v->criadoEm, true)) ?></td>
        <td><?= e($v->clienteNome ?? 'Consumidor não identificado') ?></td>
        <td><?= e(money($v->total)) ?></td>
        <td><span class="badge <?= $v->estaConcluida() ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $v->estaConcluida() ? 'Concluída' : 'Cancelada' ?></span></td>
        <td class="d-flex gap-2">
          <a class="btn btn-sm btn-outline-primary" href="<?= e(url('relatorios/detalhe', ['id' => $v->id])) ?>">Detalhes</a>
          <?php if ($v->estaConcluida()): ?>
            <form method="post" action="<?= e(url('relatorios/cancelar')) ?>" onsubmit="return confirm('Cancelar esta venda e devolver os itens ao estoque?')">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $v->id ?>">
              <button class="btn btn-sm btn-warning">Cancelar</button>
            </form>
          <?php else: ?>
            <form method="post" action="<?= e(url('relatorios/excluir')) ?>" onsubmit="return confirm('Excluir esta venda do histórico?')">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $v->id ?>">
              <button class="btn btn-sm btn-danger">Excluir</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$vendas): ?><tr><td colspan="6">Nenhuma venda encontrada para os filtros informados.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php if ($maisVendidos): ?>
  <h2 class="h4 mt-4">Produtos mais vendidos</h2>
  <div class="table-responsive">
    <table class="table table-sm w-auto">
      <thead><tr><th>Produto</th><th class="text-end">Qtd.</th><th class="text-end">Total</th></tr></thead>
      <tbody>
      <?php foreach ($maisVendidos as $m): ?>
        <tr><td><?= e($m['nome_produto']) ?></td><td class="text-end"><?= (int) $m['qtd'] ?></td><td class="text-end"><?= e(money($m['total'])) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
