<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/db.php';

$pageTitle = "ダッシュボード";
include __DIR__ . '/layout/header.php';

// 商品一覧取得（グラフ選択用）
$stmt = $pdo->query("SELECT id, name FROM products ORDER BY name");
$products = $stmt->fetchAll();
?>

<h1 class="mb-4">楽天価格比較システム 管理画面</h1>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">🏪 店舗マスタ管理</h5>
                <p class="card-text">自社・競合店舗の情報を管理します。</p>
                <a href="shops/list.php" class="btn btn-outline-primary">店舗登録</a>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">📦 商品マスタ管理</h5>
                <p class="card-text">商品情報を登録し、価格比較のベースにします。</p>
                <a href="products/add.php" class="btn btn-outline-primary">＋ 商品登録</a>
                <a href="products/list.php" class="btn btn-outline-secondary">📋 商品一覧</a>
            </div>
        </div>
    </div>
	
	    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">メーカーマスタ管理</h5>
                <p class="card-text">メーカーの情報を管理します</p>
                <a href="makers/add.php" class="btn btn-outline-primary">＋ メーカー登録</a>
                <a href="makers/list.php" class="btn btn-outline-secondary"> メーカー覧</a>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">🧩 競合統合</h5>
                <p class="card-text">類似商品を統合して、価格比較の精度を高めます。</p>
                <a href="products/merge.php" class="btn btn-outline-primary">統合登録</a>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">💰 価格比較画面</h5>
                <p class="card-text">自社と競合の価格差を一覧で比較します。</p>
                <a href="shop_items/list.php" class="btn btn-outline-primary">最安比較</a>
				<a href="products/price_matrix.php" class="btn btn-outline-primary">一覧比較</a>
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
                <a href="users/add.php" class="btn btn-outline-primary">登録</a>
				<a href="users/list.php" class="btn btn-outline-primary">一覧</a>
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

<!--    <div class="col">
        <div class="card shadow-sm h-100 border-danger">
            <div class="card-body">
                <h5 class="card-title text-danger">🚪 ログアウト</h5>
                <p class="card-text">この管理画面からログアウトします。</p>
                <a href="logout.php" class="btn btn-danger">ログアウト</a>
            </div>
        </div>
    </div>
</div>
-->
	
<!-- 📊 商品選択式価格履歴表示 -->
<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h5 class="card-title">📊 任意の商品で価格履歴を確認</h5>
        <form id="chartForm" class="row row-cols-lg-auto g-3 align-items-center">
            <div class="col-12">
                <label class="form-label" for="product_id">商品を選択：</label>
                <select class="form-select" name="product_id" id="product_id" required>
                    <option value="">-- 選択してください --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-outline-info">グラフを表示</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('chartForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('product_id').value;
    if (id) {
        window.location.href = 'price_history/chart.php?id=' + encodeURIComponent(id);
    }
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
