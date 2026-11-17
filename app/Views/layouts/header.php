<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PDV - Sistema de Vendas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(asset('style.css')) ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= e(url('home')) ?>">PDV Faculdade</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <div class="navbar-nav">
        <a class="nav-link" href="<?= e(url('produtos')) ?>">Produtos</a>
        <a class="nav-link" href="<?= e(url('clientes')) ?>">Clientes</a>
        <a class="nav-link" href="<?= e(url('vendas')) ?>">Carrinho de vendas</a>
        <a class="nav-link" href="<?= e(url('relatorios')) ?>">Relatórios</a>
      </div>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if (!empty($_SESSION['sucesso'])): ?>
  <div class="alert alert-success"><?= e($_SESSION['sucesso']) ?></div>
<?php unset($_SESSION['sucesso']); endif; ?>
<?php if (!empty($_SESSION['erro'])): ?>
  <div class="alert alert-danger"><?= e($_SESSION['erro']) ?></div>
<?php unset($_SESSION['erro']); endif; ?>
