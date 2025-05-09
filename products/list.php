<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "商品一覧";
include '../layout/header.php';

// 表示ルールの取得
$mode = $_GET['mode'] ?? 'min_price'; // 自社価格表示ルール
$makerFilter = $_GET['maker_id'] ?? ''; // メーカー絞り込み

// メーカー一覧取得
$makers = $pdo->query("SELECT * FROM makers ORDER BY name ASC")->fetchAll();

// 商品一覧取得（絞り込みあり）
$where = '';
$params = [];

if ($makerFilter !== '') {
    $where = 'WHERE p.maker_id = ?';
    $params[] = $makerFilter;
}

$stmt = $pdo->prepare("SELECT p.* FROM products p $where ORDER BY sort_order ASC, id DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();

// 店舗価格取得関数
function getShopPrices($pdo, $productId) {
    $stmt = $pdo->prepare("
        SELECT si.price, s.shop_name, s.is_own_shop, si.created_at
        FROM shop_items si
        JOIN shops s ON si.shop_id = s.id
        WHERE si.product_id = ? AND si.is_latest = 1
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

// メーカー名取得関数
function getMakerName($pdo, $maker_id) {
    if (!$maker_id) return '-';
    $stmt = $pdo->prepare("SELECT name FROM makers WHERE id = ?");
    $stmt->execute([$maker_id]);
    return $stmt->fetchColumn() ?: '-';
}
?>

<h1 class="mb-4">商品一覧</h1>

<!-- 絞り込みフォーム -->
<form method="get" class="row g-2 mb-4">
    <div class="col-auto">
        <label for="maker_id" class="form-label">メーカー：</label>
        <select name="maker_id" id="maker_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- 全て --</option>
            <?php foreach ($makers as $maker): ?>
                <option value="<?= $maker['id'] ?>" <?= $makerFilter == $maker['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($maker['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <label for="mode" class="form-label">自社表示ルール：</label>
        <select name="mode" id="mode" class="form-select" onchange="this.form.submit()">
            <option value="min_price" <?= $mode === 'min_price' ? 'selected' : '' ?>>最安値</option>
            <option value="max_price" <?= $mode === 'max_price' ? 'selected' : '' ?>>最高値</option>
            <option value="first_entry" <?= $mode === 'first_entry' ? 'selected' : '' ?>>登録が早い</option>
        </select>
    </div>
</form>

<div class="d-flex justify-content-between mb-3">
    <a href="add.php" class="btn btn-primary">＋ 新規商品登録</a>
    <a href="merge.php" class="btn btn-outline-secondary">🧩 商品統合ツール</a>
</div>

<div id="sortable" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
<?php foreach ($products as $product): 
    $prices = getShopPrices($pdo, $product['id']);
    $myPrice = null;
    $myShopName = '';
    $minCompPrice = null;
    $competitorShopName = '';

    $myEntries = array_filter($prices, fn($p) => $p['is_own_shop']);
    switch ($mode) {
        case 'min_price':
            usort($myEntries, fn($a, $b) => $a['price'] <=> $b['price']);
            break;
        case 'max_price':
            usort($myEntries, fn($a, $b) => $b['price'] <=> $a['price']);
            break;
        case 'first_entry':
            usort($myEntries, fn($a, $b) => strtotime($a['created_at']) <=> strtotime($b['created_at']));
            break;
    }
    if (!empty($myEntries)) {
        $myPrice = $myEntries[0]['price'];
        $myShopName = $myEntries[0]['shop_name'];
    }

    foreach ($prices as $p) {
        if (!$p['is_own_shop']) {
            if ($minCompPrice === null || $p['price'] < $minCompPrice) {
                $minCompPrice = $p['price'];
                $competitorShopName = $p['shop_name'];
            }
        }
    }

    $diff = ($myPrice !== null && $minCompPrice !== null)
        ? $myPrice - $minCompPrice
        : null;
?>
    <div class="col" data-id="<?= $product['id'] ?>">
        <div class="card shadow-sm h-100">
            <?php if (!empty($product['image_url'])): ?>
                <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="商品画像" style="object-fit: contain; max-height: 200px;">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="card-text"><small class="text-muted">
                    カテゴリ：<?= htmlspecialchars($product['category']) ?><br>
                    メーカー：<?= htmlspecialchars(getMakerName($pdo, $product['maker_id'])) ?>
                </small></p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        自社（<?= htmlspecialchars($myShopName) ?>）：<?= $myPrice !== null ? '¥' . number_format($myPrice) : '-' ?><br>
                        競合（<?= htmlspecialchars($competitorShopName) ?>）：<?= $minCompPrice !== null ? '¥' . number_format($minCompPrice) : '-' ?><br>
                        差額：<span class="fw-bold <?= $diff > 0 ? 'text-danger' : ($diff < 0 ? 'text-success' : '') ?>">
                            <?= $diff !== null ? (($diff > 0 ? '+' : '') . '¥' . number_format($diff)) : '-' ?>
                        </span>
                    </li>
                </ul>
                <div class="mt-3 d-flex flex-wrap gap-2">
                    <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">編集</a>
                    <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('本当に削除しますか？')">削除</a>
                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal<?= $product['id'] ?>">📊 比較</button>
                    <a href="manage_competitors.php?product_id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-secondary w-100">🏷 統合競合管理</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal<?= $product['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $product['id'] ?>" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?> の価格比較</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead>
                <tr><th>店舗名</th><th>価格</th><th>区分</th></tr>
              </thead>
              <tbody>
                <?php foreach ($prices as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['shop_name']) ?></td>
                        <td><?= '¥' . number_format($p['price']) ?></td>
                        <td><?= $p['is_own_shop'] ? '自社' : '競合' ?></td>
                    </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
<?php endforeach; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
const sortable = document.getElementById('sortable');
Sortable.create(sortable, {
    animation: 150,
    handle: '.card',
    onEnd: function () {
        const order = Array.from(sortable.querySelectorAll('[data-id]'))
            .map(el => el.getAttribute('data-id'));
        fetch('save_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order })
        });
    }
});
</script>

<?php include '../layout/footer.php'; ?>
