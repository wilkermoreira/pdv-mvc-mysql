<h1><?= $produto->id ? 'Editar produto' : 'Novo produto' ?></h1>
<form method="post" action="<?= e(url('produtos/salvar')) ?>" enctype="multipart/form-data" class="card card-body">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= e($produto->id) ?>">
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Código *</label>
      <input required maxlength="40" class="form-control" name="cod" value="<?= e($produto->cod) ?>">
    </div>
    <div class="col-md-8">
      <label class="form-label">Nome *</label>
      <input required maxlength="150" class="form-control" name="nome" value="<?= e($produto->nome) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Categoria</label>
      <input maxlength="100" class="form-control" name="categoria" value="<?= e($produto->categoria) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Validade</label>
      <input type="date" class="form-control" name="validade" value="<?= e($produto->validade) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Quantidade em estoque</label>
      <input type="number" min="0" required class="form-control" name="quantidade" value="<?= (int) $produto->quantidade ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Preço unitário (R$)</label>
      <input type="number" min="0" step="0.01" required class="form-control" name="preco_unitario" value="<?= e(number_format($produto->precoUnitario, 2, '.', '')) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Preço do pacote (R$)</label>
      <input type="number" min="0" step="0.01" required class="form-control" name="preco_pacote" value="<?= e(number_format($produto->precoPacote, 2, '.', '')) ?>">
    </div>
    <div class="col-12">
      <label class="form-label">Imagem do produto (JPG, PNG, WEBP ou GIF, até 2 MB)</label>
      <?php if ($produto->imagem): ?>
        <div class="mb-2">
          <img src="<?= e(asset('imagens/' . $produto->imagem)) ?>" alt="" width="80" class="rounded">
          <span class="form-text ms-2">Imagem atual. Envie outra para substituir.</span>
        </div>
      <?php endif; ?>
      <input type="file" class="form-control" name="imagem" accept="image/jpeg,image/png,image/webp,image/gif">
    </div>
  </div>
  <div class="mt-3">
    <button class="btn btn-success">Salvar</button>
    <a class="btn btn-secondary" href="<?= e(url('produtos')) ?>">Voltar</a>
  </div>
</form>
