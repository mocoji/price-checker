<?php
require 'auth.php';
require_login();
$pageTitle = "トップページ";
include 'layout/header.php';
?>

<h1 class="mb-4">楽天価格比較システム</h1>

<div class="list-group">
    <a href="shops/list.php" class="list-group-item list-group-item-action">📦 店舗マスタ管理</a>
    <a href="items/list.php" class="list-group-item list-group-item-action">🛒 商品マスタ管理</a>
    <a href="shop_items/list.php" class="list-group-item list-group-item-action">📊 価格比較画面</a>
    <a href="generate_coupons.php" class="list-group-item list-group-item-action">💸 クーポン提案</a>
    <?php if (is_admin()): ?>
        <a href="users/add.php" class="list-group-item list-group-item-action">👤 ユーザー登録</a>
        <a href="logs/list.php" class="list-group-item list-group-item-action">📝 操作ログ一覧</a>
    <?php endif; ?>
    <a href="logout.php" class="list-group-item list-group-item-action text-danger">🚪 ログアウト</a>
</div>

<?php include 'layout/footer.php'; ?>
