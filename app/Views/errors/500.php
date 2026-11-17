<div class="text-center py-5">
  <h1>Erro ao carregar o sistema</h1>
  <p class="text-secondary">Confira a conexão com o banco e o arquivo <code>.env</code> (veja o README).</p>
  <?php if (!empty($detalhe)): ?><p><code><?= e($detalhe) ?></code></p><?php endif; ?>
  <a class="btn btn-primary" href="<?= e(url('home')) ?>">Voltar ao início</a>
</div>
