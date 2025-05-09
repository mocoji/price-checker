<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "価格比較一覧";
include '../layout/header.php';

// GETで選択された自社店舗ID
$selectedShopId = $_GET['my_shop_id'] ?? null;

// 自社ショップ一覧を取得
$ownShops = $pdo->query("SELECT id, shop_name FROM shops WHERE is_own_shop = 1 ORDER BY id")->fetchAll();
if (!$selectedShopId && !empty($ownShops)) {
    $selectedShopId = $ownShops[0]['id'];
}

// 商品一覧取得
$products = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC, id DESC")->fetchAll();

// 価格取得関数（全店舗分）
function getLatestPrices($pdo, $productId) {
    $stmt = $pdo->prepare("
        SELECT si.id, si.price, si.shop_id, s.shop_name, s.is_own_shop
        FROM shop_items si
        JOIN shops s ON si.shop_id = s.id
        WHERE si.product_id = ? AND si.is_latest = 1
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

function getMakerName($pdo, $maker_id) {
    if (!$maker_id) return '-';
    static $cache = [];
    if (!isset($cache[$maker_id])) {
        $stmt = $pdo->prepare("SELECT name FROM makers WHERE id = ?");
        $stmt->execute([$maker_id]);
        $cache[$maker_id] = $stmt->fetchColumn() ?: '-';
    }
    return $cache[$maker_id];
}


?>

<h1 class="mb-3">最安比較（自社 vs 競合）</h1>

<!-- ✅ 自社店舗切り替え -->
<form method="get" class="mb-4 d-flex align-items-center">
    <label for="my_shop" class="me-2 fw-bold">比較基準の自社店舗：</label>
    <select name="my_shop_id" id="my_shop" class="form-select w-auto" onchange="this.form.submit()">
        <?php foreach ($ownShops as $shop): ?>
            <option value="<?= $shop['id'] ?>" <?= $selectedShopId == $shop['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($shop['shop_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<table id="priceTable" class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th style="width: 80px;">画像</th>
            <th>商品情報</th>
            <th>自社価格</th>
            <th>最安競合価格</th>
            <th>差額</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): 
        $prices = getLatestPrices($pdo, $product['id']);
        $myPrice = null;
        $myShopItemId = null;
        $minCompPrice = null;
        $myShopName = '';
        $minCompShopName = '';

		foreach ($prices as $p) {
			if ($p['shop_id'] == $selectedShopId) {
		$myPrice = $p['price'];
		$myShopItemId = $p['id'];
        $myShopName = $p['shop_name'];
    }
}

// 最安競合価格の取得（自社以外のみ）
		foreach ($prices as $p) {
			if ($p['shop_id'] != $selectedShopId && !$p['is_own_shop']) {
				if ($minCompPrice === null || $p['price'] < $minCompPrice) {
            $minCompPrice = $p['price'];
            $minCompShopName = $p['shop_name'];
        }
    }
}


        $diff = ($myPrice !== null && $minCompPrice !== null) ? $myPrice - $minCompPrice : null;
    ?>
    <tr>
        <td><img src="<?= htmlspecialchars($product['image_url']) ?>" style="height: 60px; width: auto;"></td>
        <td>
            <strong><?= htmlspecialchars($product['name']) ?></strong><br>
            <small>カテゴリ：<?= htmlspecialchars($product['category']) ?></small>
			<small>メーカー名：<?= htmlspecialchars(getMakerName($pdo, $product['maker_id'])) ?></small>
        </td>
        <td class="text-primary fw-bold">
            <?= $myPrice !== null ? "¥" . number_format($myPrice) . "<br><small>（{$myShopName}）</small>" : '-' ?>
        </td>
        <td>
            <?= $minCompPrice !== null ? "¥" . number_format($minCompPrice) . "<br><small>（{$minCompShopName}）</small>" : '-' ?>
        </td>
        <td class="<?= $diff > 0 ? 'text-danger' : ($diff < 0 ? 'text-success' : '') ?>">
            <?= $diff !== null ? (($diff > 0 ? '+' : '') . '¥' . number_format($diff)) : '-' ?>
        </td>
        <td>
            <?php if ($myShopItemId): ?>
                <a href="edit.php?id=<?= $myShopItemId ?>" class="btn btn-sm btn-outline-secondary">編集</a>
                <a href="../price_history/chart.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-info">📊 履歴</a>
            <?php else: ?>
                <span class="text-muted">自社商品未登録</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#priceTable').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/ja.json"
        },
        pageLength: 25,
        order: []  // 初期ソートなし
    });
});
</script>

<?php include '../layout/footer.php'; ?>
