<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "価格比較一覧";
include '../layout/header.php';

// 商品一覧を取得
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

// 自社ショップIDを取得
$own_shop_stmt = $pdo->query("SELECT id FROM shops WHERE is_own_shop = 1 LIMIT 1");
$own_shop_id = $own_shop_stmt->fetchColumn();

function getLatestPrices($pdo, $productId, $own_shop_id) {
    $stmt = $pdo->prepare("
        SELECT si.id, si.price, si.shop_id, s.shop_name, s.is_own_shop
        FROM shop_items si
        JOIN shops s ON si.shop_id = s.id
        WHERE si.product_id = ? AND si.is_latest = 1
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}
?>

<h1 class="mb-4">価格比較（自社 vs 比較対象の競合）</h1>

<table class="table table-bordered table-hover align-middle">
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
        $prices = getLatestPrices($pdo, $product['id'], $own_shop_id);
        $myPrice = null;
        $minCompPrice = null;
        $myShopItemId = null;

        foreach ($prices as $p) {
            if ($p['is_own_shop']) {
                $myPrice = $p['price'];
                $myShopItemId = $p['id'];
            } else {
                if ($minCompPrice === null || $p['price'] < $minCompPrice) {
                    $minCompPrice = $p['price'];
                }
            }
        }

        $diff = ($myPrice !== null && $minCompPrice !== null)
            ? $myPrice - $minCompPrice
            : null;
    ?>
    <tr>
        <td><img src="<?= htmlspecialchars($product['image_url']) ?>" style="height: 60px; width: auto;"></td>
        <td>
            <strong><?= htmlspecialchars($product['name']) ?></strong><br>
            <small>カテゴリ：<?= htmlspecialchars($product['category']) ?></small>
        </td>
        <td class="text-primary fw-bold">
            <?= $myPrice !== null ? '¥' . number_format($myPrice) : '-' ?>
        </td>
        <td>
            <?= $minCompPrice !== null ? '¥' . number_format($minCompPrice) : '-' ?>
        </td>
        <td class="<?= $diff > 0 ? 'text-danger' : ($diff < 0 ? 'text-success' : '') ?>">
            <?= $diff !== null ? (($diff > 0 ? '+' : '') . '¥' . number_format($diff)) : '-' ?>
        </td>
        <td>
            <?php if ($myShopItemId): ?>
                <a href="edit.php?id=<?= $myShopItemId ?>" class="btn btn-sm btn-outline-secondary">編集</a>
                <a href="compare_select.php?id=<?= $myShopItemId ?>" class="btn btn-sm btn-outline-primary">比較設定</a>
                <a href="../price_history/chart.php?id=<?= $myShopItemId ?>" class="btn btn-sm btn-outline-info">📊 履歴</a>
            <?php else: ?>
                <span class="text-muted">自社商品未登録</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include '../layout/footer.php'; ?>
