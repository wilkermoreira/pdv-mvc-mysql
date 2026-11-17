<section class="p-4 p-md-5 bg-white rounded-3 shadow-sm">
  <h1>Bem-vindo ao Sistema de Ponto de Venda</h1>
  <p class="lead">Sistema acadêmico em PHP e MySQL, organizado em MVC, para gerenciar produtos, clientes e vendas.</p>
  <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-primary" href="<?= e(url('produtos')) ?>">Produtos</a>
    <a class="btn btn-primary" href="<?= e(url('clientes')) ?>">Clientes</a>
    <a class="btn btn-success" href="<?= e(url('vendas')) ?>">Abrir carrinho</a>
    <a class="btn btn-outline-primary" href="<?= e(url('relatorios')) ?>">Relatórios</a>
  </div>
</section>
