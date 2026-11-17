<h1>Carrinho de vendas</h1>

<form method="get" class="row g-2 mb-3">
  <input type="hidden" name="r" value="vendas">
  <div class="col-md-9">
    <input class="form-control" name="q" placeholder="Buscar produto por nome, código ou categoria" value="<?= e($busca) ?>">
  </div>
  <div class="col-md-3"><button class="btn btn-outline-primary w-100">Buscar produtos</button></div>
</form>

<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead><tr><th>Código</th><th>Produto</th><th>Estoque</th><th>Preço unitário</th><th>Adicionar</th></tr></thead>
    <tbody>
    <?php foreach ($produtos as $p): ?>
      <tr>
        <td><?= e($p->cod) ?></td>
        <td><?= e($p->nome) ?></td>
        <td><?= (int) $p->quantidade ?></td>
        <td><?= e(money($p->precoUnitario)) ?></td>
        <td>
          <form method="post" action="<?= e(url('vendas/adicionar')) ?>" class="d-flex gap-2">
            <?= csrf_field() ?>
            <input type="hidden" name="produto_id" value="<?= (int) $p->id ?>">
            <input type="hidden" name="q" value="<?= e($busca) ?>">
            <input class="form-control" style="max-width:90px" type="number" min="1" max="<?= (int) $p->quantidade ?>" name="quantidade" value="1">
            <button class="btn btn-sm btn-success" <?= $p->quantidade < 1 ? 'disabled' : '' ?>>Adicionar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$produtos): ?><tr><td colspan="5">Nenhum produto encontrado.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<hr>
<h2>Itens do carrinho</h2>

<form method="post" action="<?= e(url('vendas/atualizar')) ?>" id="form-carrinho">
  <?= csrf_field() ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>Produto</th><th>Preço</th><th>Quantidade</th><th>Subtotal</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($itens as $linha): $p = $linha['produto']; ?>
        <tr>
          <td><?= e($p->nome) ?></td>
          <td><?= e(money($p->precoUnitario)) ?></td>
          <td>
            <input class="form-control" style="max-width:100px" type="number" min="0" max="<?= (int) $p->quantidade ?>"
                   name="qtd[<?= (int) $p->id ?>]" value="<?= (int) $linha['quantidade'] ?>">
          </td>
          <td><?= e(money($linha['subtotal'])) ?></td>
          <td><button type="submit" form="remover-<?= (int) $p->id ?>" class="btn btn-sm btn-outline-danger">Remover</button></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$itens): ?><tr><td colspan="5">Seu carrinho está vazio.</td></tr><?php endif; ?>
      </tbody>
      <tfoot>
        <tr><th colspan="3" class="text-end">Total</th><th><?= e(money($total)) ?></th><th></th></tr>
      </tfoot>
    </table>
  </div>
  <button class="btn btn-outline-primary" <?= $itens ? '' : 'disabled' ?>>Atualizar quantidades</button>
</form>

<?php /* Formulários de remoção ficam fora do formulário principal (HTML não permite form dentro de form). */ ?>
<?php foreach ($itens as $linha): ?>
  <form id="remover-<?= (int) $linha['produto']->id ?>" method="post" action="<?= e(url('vendas/remover')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="produto_id" value="<?= (int) $linha['produto']->id ?>">
  </form>
<?php endforeach; ?>

<?php if ($itens): ?>
  <form method="post" action="<?= e(url('vendas/limpar')) ?>" class="mt-2" onsubmit="return confirm('Esvaziar o carrinho?')">
    <?= csrf_field() ?>
    <button class="btn btn-sm btn-outline-secondary">Esvaziar carrinho</button>
  </form>
<?php endif; ?>

<form method="post" action="<?= e(url('vendas/finalizar')) ?>" class="card card-body mt-3">
  <?= csrf_field() ?>
  <h5>Finalizar venda</h5>
  <label class="form-label">Cliente (opcional)</label>
  <select class="form-select mb-3" name="cliente_id">
    <option value="">Consumidor não identificado</option>
    <?php foreach ($clientes as $c): ?>
      <option value="<?= (int) $c->id ?>"><?= e($c->nome) ?></option>
    <?php endforeach; ?>
  </select>
  <div><button class="btn btn-success" <?= $itens ? '' : 'disabled' ?>>Concluir venda — <?= e(money($total)) ?></button></div>
</form>
