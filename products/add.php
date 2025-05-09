<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "商品登録";
include '../layout/header.php';

// 店舗一覧取得（プルダウン用）
$shops = $pdo->query("SELECT * FROM shops ORDER BY is_own_shop DESC, shop_name")->fetchAll();
$makers = $pdo->query("SELECT * FROM makers ORDER BY name ASC")->fetchAll();

?>

<h1 class="mb-4">商品登録</h1>

<form method="post" action="store.php" class="card shadow-sm p-4 bg-white">
	<div class="mb-3">
    <label for="maker_id" class="form-label">メーカー</label>
    <select name="maker_id" id="maker_id" class="form-select">
        <option value="">-- 選択してください --</option>
        <?php foreach ($makers as $maker): ?>
            <option value="<?= $maker['id'] ?>" <?= isset($product['maker_id']) && $product['maker_id'] == $maker['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($maker['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

    <div class="mb-3">
        <label for="shop_id" class="form-label">店舗選択</label>
        <select name="shop_id" id="shop_id" class="form-select" required>
            <option value="">-- 店舗を選択 --</option>
            <?php foreach ($shops as $shop): ?>
                <option value="<?= $shop['id'] ?>">
                    <?= htmlspecialchars($shop['shop_name'] ?: $shop['shop_code']) ?>
                    <?= $shop['is_own_shop'] ? '（自社）' : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="item_code" class="form-label">楽天商品コード</label>
        <div class="input-group">
            <input type="text" name="item_code" id="item_code" class="form-control" required>
            <button type="button" id="fetchBtn" class="btn btn-outline-secondary">商品情報取得</button>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">商品名</label>
        <input type="text" name="item_name" id="item_name" class="form-control" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">カテゴリ</label>
        <input type="text" name="category" id="category" class="form-control" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">販売価格</label>
        <input type="number" name="price" id="price" class="form-control" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">画像URL</label>
        <input type="text" name="thumbnail_url" id="thumbnail_url" class="form-control" readonly>
        <div class="mt-2">
            <img id="preview" src="" alt="画像プレビュー" style="max-height:120px;">
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">登録する</button>
    </div>
</form>

<script>
document.getElementById('fetchBtn').addEventListener('click', async () => {
    const code = document.getElementById('item_code').value;
    const shopId = document.getElementById('shop_id').value;
    if (!code || !shopId) {
        alert('店舗と商品コードを入力してください');
        return;
    }

    const res = await fetch(`fetch_item_info.php?code=${encodeURIComponent(code)}&shop_id=${shopId}`);
    const data = await res.json();

    if (data.success) {
        document.getElementById('item_name').value = data.item_name;
        document.getElementById('category').value = data.category;
        document.getElementById('price').value = data.price;
        document.getElementById('thumbnail_url').value = data.thumbnail_url;
        document.getElementById('preview').src = data.thumbnail_url;
    } else {
        alert('商品情報の取得に失敗しました');
    }
});
</script>

<?php include '../layout/footer.php'; ?>
