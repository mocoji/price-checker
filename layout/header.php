<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/price_checker/auth.php';
require_login();
$pageTitle = $pageTitle ?? '管理画面';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/price_checker/assets/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/price_checker/index.php">価格比較</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/price_checker/shops/list.php">店舗管理</a></li>
        <li class="nav-item"><a class="nav-link" href="/price_checker/products/list.php">商品管理</a></li>
        <li class="nav-item"><a class="nav-link" href="/price_checker/shop_items/list.php">価格比較</a></li>
        <li class="nav-item"><a class="nav-link" href="/price_checker/generate_coupons.php">クーポン提案</a></li>
        <?php if (is_admin()): ?>
          <li class="nav-item"><a class="nav-link" href="/price_checker/users/add.php">ユーザー管理</a></li>
          <li class="nav-item"><a class="nav-link" href="/price_checker/logs/list.php">操作ログ</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link text-danger" href="/price_checker/logout.php">ログアウト</a></li>
      </ul>

      <span class="navbar-text text-white">
        <?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?>（<?= $_SESSION['user']['role'] ?? '' ?>）
      </span>
    </div>
  </div>
</nav>

<div class="container">
