<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "価格マトリクス";
include '../layout/header.php';

// 自社ショップ一覧
$ownShops = $pdo->query("SELECT id, shop_name FROM shops WHERE is_own_shop = 1 ORDER BY id")->fetchAll();
$selectedShopId = $_GET['my_shop_id'] ?? null;
if (!$selectedShopId && !empty($ownShops)) {
    $selectedShopId = $ownShops[0]['id'];
}

// 競合ショップ一覧
$competitorShops = $pdo->query("SELECT id, shop_name FROM shops WHERE is_own_shop = 0 ORDER BY shop_name")->fetchAll();

// 商品一覧
$products = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC, id DESC")->fetchAll();

// 最新価格の取得（[product_id][shop_id] => price）
$priceStmt = $pdo->query("
    SELECT si.product_id, si.shop_id, si.price
    FROM shop_items si
    WHERE si.is_latest = 1
");
$priceMap = [];
foreach ($priceStmt as $row) {
    $priceMap[$row['product_id']][$row['shop_id']] = $row['price'];
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">価格マトリクス（自社 vs 競合）</h2>
    </div>

    <form method="get" class="mb-3 d-flex align-items-center">
        <label for="my_shop" class="me-2 fw-bold">自社店舗選択：</label>
        <select name="my_shop_id" id="my_shop" class="form-select w-auto" onchange="this.form.submit()">
            <?php foreach ($ownShops as $shop): ?>
                <option value="<?= $shop['id'] ?>" <?= $selectedShopId == $shop['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($shop['shop_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="table-responsive">
       <table id="priceTable" class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:80px;">画像</th>
                    <th>商品名</th>
                    <th>自社価格</th>
                    <?php foreach ($competitorShops as $cshop): ?>
                        <th><?= htmlspecialchars($cshop['shop_name']) ?></th>
                    <?php endforeach; ?>
                    <th>差額</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): 
                    $myPrice = $priceMap[$product['id']][$selectedShopId] ?? null;
                    $minCompPrice = null;

                    foreach ($competitorShops as $cshop) {
                        $price = $priceMap[$product['id']][$cshop['id']] ?? null;
                        if ($price !== null) {
                            if ($minCompPrice === null || $price < $minCompPrice) {
                                $minCompPrice = $price;
                            }
                        }
                    }

                    $diff = ($myPrice !== null && $minCompPrice !== null) ? $myPrice - $minCompPrice : null;
                ?>
                <tr>
                    <td>
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" style="height: 60px;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($product['category']) ?></small>
                    </td>
                    <td class="text-primary fw-bold">
                        <?= $myPrice !== null ? '¥' . number_format($myPrice) : '-' ?>
                    </td>
                    <?php foreach ($competitorShops as $cshop): ?>
                        <td>
                            <?php
                                $price = $priceMap[$product['id']][$cshop['id']] ?? null;
                                echo $price !== null ? '¥' . number_format($price) : '-';
                            ?>
                        </td>
                    <?php endforeach; ?>
                    <td class="<?= $diff > 0 ? 'text-danger' : ($diff < 0 ? 'text-success' : '') ?>">
                        <?= $diff !== null ? (($diff > 0 ? '+' : '') . '¥' . number_format($diff)) : '-' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

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
