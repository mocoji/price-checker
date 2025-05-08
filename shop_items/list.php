<?php
require '../db.php';

// 商品一覧と各店舗の最新価格を表示する（JOINで一括取得）
$sql = "
SELECT 
    i.id AS item_id,
    i.item_name,
    i.item_code,
    s.shop_name,
    s.is_own_shop,
    si.price
FROM shop_items si
JOIN shops s ON si.shop_id = s.id
JOIN items i ON si.item_id = i.id
WHERE si.is_latest = 1
ORDER BY i.id, s.is_own_shop DESC, s.shop_name
";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();

// グループ化（商品ごとにまとめる）
$grouped = [];
foreach ($rows as $row) {
    $grouped[$row['item_id']]['item_name'] = $row['item_name'];
    $grouped[$row['item_id']]['item_code'] = $row['item_code'];
    $grouped[$row['item_id']]['prices'][] = $row;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>価格比較</title>
</head>
<body>
    <h1>商品価格比較</h1>
    <?php foreach ($grouped as $item): ?>
        <h2><?= htmlspecialchars($item['item_name']) ?>（<?= htmlspecialchars($item['item_code']) ?>）</h2>
        <table border="1" cellpadding="8">
            <tr>
                <th>店舗名</th><th>自社</th><th>価格</th>
            </tr>
            <?php 
                $own_price = null;
                $min_competitor = PHP_INT_MAX;
                foreach ($item['prices'] as $p):
                    if ($p['is_own_shop']) $own_price = $p['price'];
                    else $min_competitor = min($min_competitor, $p['price']);
            ?>
            <tr>
                <td><?= htmlspecialchars($p['shop_name']) ?></td>
                <td><?= $p['is_own_shop'] ? '✔' : '' ?></td>
                <td><?= number_format($p['price']) ?> 円</td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php if ($own_price !== null && $min_competitor !== PHP_INT_MAX): ?>
            <p>最安競合との差額：<?= number_format($own_price - $min_competitor) ?> 円</p>
        <?php endif; ?>
        <hr>
    <?php endforeach; ?>
</body>
</html>
