<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/price_checker/auth.php';
require_login();
$pageTitle = $pageTitle ?? '管理画面';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?> | 価格比較</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="/price_checker/assets/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="/price_checker/index.php">楽天価格比較</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/price_checker/shops/list.php">店舗管理</a></li>
		<li class="nav-item"><a class="nav-link" href="/price_checker/products/add.php">商品登録</a></li>
        <li class="nav-item"><a class="nav-link" href="/price_checker/products/list.php">商品一覧</a></li>
		<li class="nav-item"><a class="nav-link" href="/price_checker/products/merge.php">競合統合</a></li>    
        <li class="nav-item"><a class="nav-link" href="/price_checker/shop_items/list.php">最安比較</a></li>
		<li class="nav-item"><a class="nav-link" href="/price_checker/products/price_matrix.php">一覧比較</a></li>
		<li class="nav-item"><a class="nav-link" href="/price_checker/generate_coupons.php">クーポン提案</a></li>
        <?php if (is_admin()): ?>
          <li class="nav-item"><a class="nav-link" href="/price_checker/users/add.php">ユーザー管理</a></li>
          <li class="nav-item"><a class="nav-link" href="/price_checker/logs/list.php">操作ログ</a></li>
        <?php endif; ?>
      </ul>

      <div class="d-flex align-items-center">
        <a href="/price_checker/products/update_all_prices.php"
           class="btn btn-sm btn-outline-warning me-3"
           onclick="return confirm('すべての商品の価格を楽天APIから再取得します。本当によろしいですか？');">
           一括価格再取得
        </a>
        <span class="navbar-text text-light">
          <?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?>
          <span class="badge bg-secondary ms-1"><?= htmlspecialchars($_SESSION['user']['role'] ?? '') ?></span>
        </span>
        <a href="/price_checker/logout.php" class="btn btn-sm btn-outline-light ms-3">ログアウト</a>
      </div>
    </div>
  </div>
</nav>

<div class="container">
