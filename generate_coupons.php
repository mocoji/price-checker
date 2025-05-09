<?php
require_once 'auth.php';
require_login();
require_once 'db.php';
$pageTitle = "クーポン提案";
include 'layout/header.php';

// 自社店舗一覧取得
$ownShops = $pdo->query("SELECT id, shop_name FROM shops WHERE is_own_shop = 1 ORDER BY id")->fetchAll();
$selectedShopId = $_GET['my_shop_id'] ?? null;

if (!$selectedShopId && !empty($ownShops)) {
    $selectedShopId = $ownShops[0]['id'];
}

if (!$selectedShopId) {
    echo "<div class='alert alert-danger'>自社店舗が未登録です。</div>";
    include 'layout/footer.php';
    exit;
}

// 自社店舗名
$myShopName = '';
foreach ($ownShops as $shop) {
    if ($shop['id'] == $selectedShopId) {
        $myShopName = $shop['shop_name'];
        break;
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC, id DESC")->fetchAll();
$coupons = [];

foreach ($products as $product) {
    // 自社価格取得
    $stmt = $pdo->prepare("
        SELECT si.price, si.item_code
        FROM shop_items si
        WHERE si.product_id = ? AND si.shop_id = ? AND si.is_latest = 1
    ");
    $stmt->execute([$product['id'], $selectedShopId]);
    $own = $stmt->fetch();

    if (!$own) continue;
    $ownPrice = $own['price'];
    $itemCode = $own['item_code'];

    // 最安競合価格取得
    $stmt = $pdo->prepare("
        SELECT si.price, s.shop_name
        FROM shop_items si
        JOIN shops s ON si.shop_id = s.id
        WHERE si.product_id = ? AND s.is_own_shop = 0 AND si.is_latest = 1
        ORDER BY si.price ASC
        LIMIT 1
    ");
    $stmt->execute([$product['id']]);
    $competitor = $stmt->fetch();

    if (!$competitor || !$competitor['price']) continue;

    $minCompPrice = $competitor['price'];
    $compShopName = $competitor['shop_name'];
    $diff = $ownPrice - $minCompPrice;
    if ($diff <= 0) continue;

    $discount = floor($diff * 1.1 / 10) * 10; // 差額の110%、1の位切り捨て

    $coupons[] = [
        'coupon_name'     => $product['name'] . "割引",
        'coupon_code'     => strtoupper(substr(md5($itemCode), 0, 8)),
        'discount_amount' => $discount,
        'valid_from'      => date('Y/m/d'),
        'valid_to'        => date('Y/m/d', strtotime('+7 days')),
        'min_purchase'    => $ownPrice,
        'usage_limit'     => '1人1回',
        'my_shop_name'    => $myShopName,
        'comp_shop_name'  => $compShopName,
        'diff'            => $diff
    ];
}

$_SESSION['coupons'] = $coupons;
?>

<div class="container py-4">
    <h1 class="mb-4">クーポン金額の自動提案</h1>

    <!-- ✅ 自社店舗選択フォーム -->
<form method="get" class="mb-4 d-flex align-items-center">
    <label for="my_shop" class="me-2 fw-bold">自社店舗の選択：</label>
    <select name="my_shop_id" id="my_shop" class="form-select w-auto" onchange="this.form.submit()">
        <?php foreach ($ownShops as $shop): ?>
            <option value="<?= $shop['id'] ?>" <?= $selectedShopId == $shop['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($shop['shop_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>	
	
	
	
<form method="post" action="submit_coupons.php">
    <table id="priceTable" class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>クーポン名</th>
                <th>コード</th>
                <th>割引</th>
                <th>自社店舗</th>
                <th>競合店舗</th>
                <th>差額</th>
                <th>有効期間</th>
                <th>最低購入額</th>
                <th>使用制限</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($coupons as $i => $c): ?>
            <tr>
                <td>
                    <input type="text" name="coupons[<?= $i ?>][coupon_name]" class="form-control" value="<?= htmlspecialchars($c['coupon_name']) ?>">
                </td>
                <td>
                    <input type="text" name="coupons[<?= $i ?>][coupon_code]" class="form-control" value="<?= $c['coupon_code'] ?>" readonly>
                </td>
                <td>
                    <input type="number" name="coupons[<?= $i ?>][discount_amount]" class="form-control" value="<?= $c['discount_amount'] ?>" required>
                </td>
                <td><?= htmlspecialchars($c['my_shop_name']) ?></td>
                <td><?= htmlspecialchars($c['comp_shop_name']) ?></td>
                <td><?= number_format($c['diff']) ?>円</td>
                <td>
                    <div class="d-flex">
                        <input type="date" name="coupons[<?= $i ?>][valid_from]" class="form-control me-1" value="<?= $c['valid_from'] ?>">
                        <input type="date" name="coupons[<?= $i ?>][valid_to]" class="form-control" value="<?= $c['valid_to'] ?>">
                    </div>
                </td>
                <td>
                    <input type="number" name="coupons[<?= $i ?>][min_purchase]" class="form-control" value="<?= $c['min_purchase'] ?>" required>
                </td>
                <td>
                    <select name="coupons[<?= $i ?>][usage_limit]" class="form-select">
                        <option <?= $c['usage_limit'] === '1人1回' ? 'selected' : '' ?>>1人1回</option>
                        <option <?= $c['usage_limit'] === '1人何回でも' ? 'selected' : '' ?>>1人何回でも</option>
                        <option <?= $c['usage_limit'] === '全体で1回のみ' ? 'selected' : '' ?>>全体で1回のみ</option>
                        <option <?= $c['usage_limit'] === '無制限' ? 'selected' : '' ?>>無制限</option>
                    </select>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-end">
        <button type="submit" class="btn btn-primary">クーポン情報を保存する</button>
    </div>
</form>

    <a href="download_coupons.php" target="_blank" class="btn btn-outline-success mb-3">↓ CSVをダウンロード</a>

    <?php if (empty($coupons)): ?>
        <div class="alert alert-info">クーポンを提案できる商品が見つかりませんでした。</div>
    <?php else: ?>
   
    <?php endif; ?>
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

<?php include 'layout/footer.php'; ?>
