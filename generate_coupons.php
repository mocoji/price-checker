<?php
require 'db.php';

// 自社店舗IDを取得
$myShopStmt = $pdo->query("SELECT id FROM shops WHERE is_own_shop = 1 LIMIT 1");
$myShop = $myShopStmt->fetch();
if (!$myShop) {
    die("自社店舗が未登録です。");
}
$myShopId = $myShop['id'];

// 対象商品を取得
$items = $pdo->query("SELECT * FROM items")->fetchAll();
$coupons = [];

foreach ($items as $item) {
    // 自社価格
    $stmt = $pdo->prepare("
        SELECT price FROM shop_items
        WHERE item_id = ? AND shop_id = ? AND is_latest = 1
    ");
    $stmt->execute([$item['id'], $myShopId]);
    $own = $stmt->fetch();

    if (!$own) continue;
    $ownPrice = $own['price'];

    // 最安競合価格
    $stmt = $pdo->prepare("
        SELECT MIN(price) AS min_price FROM shop_items
        WHERE item_id = ? AND shop_id != ? AND is_latest = 1
    ");
    $stmt->execute([$item['id'], $myShopId]);
    $competitor = $stmt->fetch();

    if (!$competitor || !$competitor['min_price']) continue;
    $minCompPrice = $competitor['min_price'];

    $diff = $ownPrice - $minCompPrice;
    if ($diff <= 0) continue;

    // 差額に応じたクーポン金額提案（例：100円単位で切り下げ）
    $discount = floor($diff / 100) * 100;

    $coupons[] = [
        'coupon_name'     => $item['item_name'] . "割引",
        'coupon_code'     => strtoupper(substr(md5($item['item_code']), 0, 8)),
        'discount_amount' => $discount,
        'valid_from'      => date('Y/m/d'),
        'valid_to'        => date('Y/m/d', strtotime('+7 days')),
        'min_purchase'    => $ownPrice,
        'usage_limit'     => '1人1回'
    ];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>クーポン提案</title>
</head>
<body>
    <h1>クーポン金額の自動提案</h1>
    <a href="download_coupons.php" target="_blank">↓ CSVをダウンロード</a><br><br>
    <table border="1" cellpadding="8">
        <tr>
            <th>クーポン名</th><th>コード</th><th>割引金額</th><th>有効期間</th><th>最低購入金額</th><th>使用制限</th>
        </tr>
        <?php foreach ($coupons as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['coupon_name']) ?></td>
            <td><?= $c['coupon_code'] ?></td>
            <td><?= $c['discount_amount'] ?> 円</td>
            <td><?= $c['valid_from'] ?>〜<?= $c['valid_to'] ?></td>
            <td><?= $c['min_purchase'] ?> 円</td>
            <td><?= $c['usage_limit'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
