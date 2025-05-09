<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$product_id = $_GET['id'] ?? null;
if (!$product_id) die("商品IDが必要です。");

$stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product) die("商品が見つかりません");

$stmt = $pdo->prepare("
    SELECT ph.price, ph.recorded_at, s.shop_name
    FROM price_history ph
    JOIN shop_items si ON ph.shop_item_id = si.id
    JOIN shops s ON si.shop_id = s.id
    JOIN (
        SELECT shop_item_id, DATE(recorded_at) as date, MAX(recorded_at) as latest_recorded
        FROM price_history
        GROUP BY shop_item_id, DATE(recorded_at)
    ) latest
    ON ph.shop_item_id = latest.shop_item_id
    AND DATE(ph.recorded_at) = latest.date
    AND ph.recorded_at = latest.latest_recorded
    WHERE si.product_id = ?
    ORDER BY ph.recorded_at
");
$stmt->execute([$product_id]);
$rows = $stmt->fetchAll();

// データを店舗別に整理
$data = [];
$all_dates = [];
foreach ($rows as $row) {
    $shop = $row['shop_name'];
    $date = $row['recorded_at'];
    $price = $row['price'];

    $data[$shop][$date] = $price;
    $all_dates[$date] = true;
}

$all_dates = array_keys($all_dates);
sort($all_dates);

// 各店舗に対して全ての日付を持たせ、なければnull
$datasets = [];
foreach ($data as $shop => $prices_by_date) {
    $dataset = [];
    foreach ($all_dates as $date) {
        $dataset[] = $prices_by_date[$date] ?? null;
    }
    $datasets[] = [
        'label' => $shop,
        'data' => $dataset,
        'borderWidth' => 2,
        'fill' => false,
        'tension' => 0.3
    ];
}

$pageTitle = "価格履歴グラフ";
include '../layout/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= htmlspecialchars($product['name']) ?> の価格履歴</h2>
        <a href="../shop_items/list.php" class="btn btn-outline-secondary">← 商品一覧に戻る</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">📈 店舗別 価格推移グラフ</h5>
            <canvas id="priceChart" height="80"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = <?= json_encode($all_dates) ?>;
    const datasets = <?= json_encode($datasets) ?>;

    const ctx = document.getElementById('priceChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                    // デフォルトの凡例クリックで表示/非表示OK
                },
                tooltip: {
                    callbacks: {
                        label: ctx => '¥' + ctx.formattedValue
                    }
                }
            },
            scales: {
                y: {
                    title: { display: true, text: '価格（円）' }
                },
                x: {
                    title: { display: true, text: '記録日時' }
                }
            }
        }
    });
</script>

<?php include '../layout/footer.php'; ?>
