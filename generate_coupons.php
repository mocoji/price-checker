<?php
require_once 'auth.php';
require_login();
require_once 'db.php';
$pageTitle = "クーポン提案";
include 'layout/header.php';

// 自社ショップIDと名前
$myShop = $pdo->query("SELECT id, shop_name FROM shops WHERE is_own_shop = 1 LIMIT 1")->fetch();
if (!$myShop) exit("自社ショップが未登録です。");
$myShopId = $myShop['id'];
$myShopName = $myShop['shop_name'];

$products = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC, id DESC")->fetchAll();
$coupons = [];

foreach ($products as $product) {
    // 自社価格・コード取得
    $stmt = $pdo->prepare("
        SELECT si.price, si.item_code
        FROM shop_items si
        WHERE si.product_id = ? AND si.shop_id = ? AND si.is_latest = 1
    ");
    $stmt->execute([$product['id'], $myShopId]);
    $own = $stmt->fetch();
    if (!$own) continue;

    $ownPrice = $own['price'];
    $itemCode = $own['item_code'];

    // 最安競合価格と店舗名を取得
    $stmt = $pdo->prepare("
        SELECT si.price, s.shop_name
        FROM shop_items si
        JOIN shops s ON si.shop_id = s.id
        WHERE si.product_id = ? AND s.is_own_shop = 0 AND si.is_latest = 1
        ORDER BY si.price ASC LIMIT 1
    ");
    $stmt->execute([$product['id']]);
    $competitor = $stmt->fetch();
    if (!$competitor) continue;

    $minCompPrice = $competitor['price'];
    $minCompShopName = $competitor['shop_name'];
    $diff = $ownPrice - $minCompPrice;
    if ($diff <= 0) continue;

    $coupons[] = [
        'coupon_name'     => $product['name'] . "割引",
        'coupon_code'     => strtoupper(substr(md5($itemCode), 0, 8)),
        'discount_amount' => '',
        'valid_from'      => date('Y-m-d'),
        'valid_to'        => date('Y-m-d', strtotime('+7 days')),
        'min_purchase'    => $ownPrice,
        'usage_limit'     => '1人1回',
        'my_shop_name'    => $myShopName,
        'comp_shop_name'  => $minCompShopName,
        'diff'            => $diff
    ];
}
?>

<h1 class="mb-4">クーポン金額の自動提案</h1>

<form method="post" action="download_coupons.php">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>クーポン名</th><th>コード</th><th>割引</th><th>自社店舗</th><th>競合店舗</th><th>差額</th><th>有効期間</th><th>最低購入額</th><th>使用制限</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($coupons as $index => $c): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($c['coupon_name']) ?>
                    <input type="hidden" name="coupons[<?= $index ?>][coupon_name]" value="<?= htmlspecialchars($c['coupon_name']) ?>">
                </td>
                <td>
                    <?= $c['coupon_code'] ?>
                    <input type="hidden" name="coupons[<?= $index ?>][coupon_code]" value="<?= $c['coupon_code'] ?>">
                </td>
                <td>
                    <input type="number" class="form-control" name="coupons[<?= $index ?>][discount_amount]" required>
                </td>
                <td><?= htmlspecialchars($c['my_shop_name']) ?></td>
                <td><?= htmlspecialchars($c['comp_shop_name']) ?></td>
                <td><?= number_format($c['diff']) ?> 円</td>
                <td>
                    <input type="date" name="coupons[<?= $index ?>][valid_from]" value="<?= $c['valid_from'] ?>">
                    〜
                    <input type="date" name="coupons[<?= $index ?>][valid_to]" value="<?= $c['valid_to'] ?>">
                </td>
                <td>
                    ¥<?= number_format($c['min_purchase']) ?>
                    <input type="hidden" name="coupons[<?= $index ?>][min_purchase]" value="<?= $c['min_purchase'] ?>">
                </td>
                <td>
                    <input type="text" class="form-control" name="coupons[<?= $index ?>][usage_limit]" value="<?= $c['usage_limit'] ?>">
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-success">📥 CSVダウンロード</button>
    </div>
</form>

<?php include 'layout/footer.php'; ?>
