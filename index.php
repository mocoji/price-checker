<?php
require_once __DIR__ . '/auth.php';
require_login();
$pageTitle = "ダッシュボード";
include __DIR__ . '/layout/header.php';
?>

<h1 class="mb-4">楽天価格比較システム 管理画面</h1>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">🏪 店舗マスタ管理</h5>
                <p class="card-text">自社・競合店舗の情報を管理します。</p>
                <a href="shops/list.php" class="btn btn-outline-primary">開く</a>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">📦 商品マスタ管理</h5>
                <p class="card-text">商品情報を登録し、価格比較のベースにします。</p>
               <a href="products/add.php" class="btn btn-outline-primary">＋ 商品登録</a>
                <a href="products/list.php" class="btn btn-outline-secondary">📋 商品一覧</a><br>
            </div>
        </div>
    </div>
	
	<div class="col">
    <div class="card shadow-sm h-100">
        <div class="card-body">
            <h5 class="card-title">🧩 競合統合</h5>
            <p class="card-text">類似商品を統合して、価格比較の精度を高めます。</p>
            <a href="products/merge.php" class="btn btn-outline-primary">統合ツールを開く</a>
        </div>
    </div>
</div>

	
    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">💰 価格比較画面</h5>
                <p class="card-text">自社と競合の価格差を一覧で比較します。</p>
                <a href="shop_items/list.php" class="btn btn-outline-primary">開く</a>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">🎟 クーポン提案</h5>
                <p class="card-text">価格差に応じたクーポンを自動で提案・出力します。</p>
                <a href="generate_coupons.php" class="btn btn-outline-primary">開く</a>
            </div>
        </div>
    </div>

    <?php if (is_admin()): ?>
    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">👤 ユーザー登録</h5>
                <p class="card-text">ログインユーザーと権限を管理します。</p>
                <a href="users/add.php" class="btn btn-outline-primary">開く</a>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">📋 操作ログ</h5>
                <p class="card-text">ユーザーの操作履歴を確認できます。</p>
                <a href="logs/list.php" class="btn btn-outline-primary">開く</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col">
        <div class="card shadow-sm h-100 border-danger">
            <div class="card-body">
                <h5 class="card-title text-danger">🚪 ログアウト</h5>
                <p class="card-text">この管理画面からログアウトします。</p>
                <a href="logout.php" class="btn btn-danger">ログアウト</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
