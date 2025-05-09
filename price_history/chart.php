<?php
require '../db.php';

// 商品ID指定（GETパラメータで ?id=●）
$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    die("商品IDが必要です。");
}

// 商品名取得
$stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product) die("商品が見つかりません");

// 店舗ごとに履歴データを取得
$stmt = $pdo->prepare("
    SELECT ph.price, ph.recorded_at, s.shop_name
    FROM price_history ph
    JOIN shop_items si ON ph.shop_item_id = si.id
    JOIN shops s ON si.shop_id = s.id
    WHERE si.product_id = ?
    ORDER BY s.shop_name, ph.recorded_at
");
$stmt->execute([$product_id]);
$rows = $stmt->fetchAll();

// 店舗別にグループ化
$data = [];
foreach ($rows as $row) {
    $shop = $row['shop_name'];
    $time = $row['recorded_at'];
    $price = $row['price'];
    $data[$shop]['labels'][] = $time;
    $data[$shop]['data'][] = $price;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>価格履歴グラフ</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>価格履歴：<?= htmlspecialchars($product['name']) ?></h1>
    <a href="../shop_items/list.php">← 商品一覧に戻る</a>
    <canvas id="priceChart" width="900" height="400"></canvas>

    <script>
        const ctx = document.getElementById('priceChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_values(reset($data)['labels'] ?? [])) ?>,
                datasets: [
                    <?php foreach ($data as $shopName => $shopData): ?>
                    {
                        label: <?= json_encode($shopName) ?>,
                        data: <?= json_encode($shopData['data']) ?>,
                        borderWidth: 2,
                        fill: false,
                        tension: 0.2
                    },
                    <?php endforeach; ?>
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: '店舗別価格推移'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: '価格 (円)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: '日付'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
