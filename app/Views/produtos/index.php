<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Cadastro de produtos</h1>
  <a class="btn btn-primary" href="<?= e(url('produtos/novo')) ?>">Novo produto</a>
</div>

<form method="get" class="row g-2 mb-3">
  <input type="hidden" name="r" value="produtos">
  <div class="col-md-9">
    <input class="form-control" name="q" placeholder="Buscar por nome, código ou categoria" value="<?= e($busca) ?>">
  </div>
  <div class="col-md-3"><button class="btn btn-outline-primary w-100">Buscar</button></div>
</form>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr><th></th><th>Código</th><th>Produto</th><th>Categoria</th><th>Validade</th><th>Estoque</th><th>Preço unit.</th><th>Ações</th></tr>
    </thead>
    <tbody>
    <?php foreach ($produtos as $p): ?>
      <tr>
        <td>
          <?php if ($p->imagem): ?>
            <img src="<?= e(asset('imagens/' . $p->imagem)) ?>" alt="<?= e($p->nome) ?>" width="48" height="48" class="rounded object-fit-cover">
          <?php endif; ?>
        </td>
        <td><?= e($p->cod) ?></td>
        <td><?= e($p->nome) ?></td>
        <td><?= e($p->categoria) ?></td>
        <td><?= e(data_br($p->validade)) ?></td>
        <td><?= (int) $p->quantidade ?></td>
        <td><?= e(money($p->precoUnitario)) ?></td>
        <td class="d-flex gap-2">
          <a class="btn btn-sm btn-warning" href="<?= e(url('produtos/editar', ['id' => $p->id])) ?>">Editar</a>
          <form method="post" action="<?= e(url('produtos/excluir')) ?>" onsubmit="return confirm('Excluir este produto?')">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $p->id ?>">
            <button class="btn btn-sm btn-danger">Excluir</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$produtos): ?><tr><td colspan="8">Nenhum produto encontrado.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
