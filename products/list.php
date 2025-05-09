<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "商品一覧";
include '../layout/header.php';

$products = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC, id DESC")->fetchAll();

function getShopPrices($pdo, $productId) {
    $stmt = $pdo->prepare("
        SELECT si.price, s.shop_name
        FROM shop_items si
        JOIN shops s ON si.shop_id = s.id
        WHERE si.product_id = ? AND si.is_latest = 1
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

if (!empty($_SESSION['price_update_done'])) {
    echo "<script>alert('全商品の価格取得が完了しました');</script>";
    unset($_SESSION['price_update_done']);
}

?>
<h1 class="mb-4">商品一覧</h1>

<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="add.php" class="btn btn-primary">＋ 新規商品登録</a>
    <a href="merge.php" class="btn btn-outline-secondary">🧩 商品統合ツール</a>
    <a href="update_all_prices.php" class="btn btn-outline-warning"
       onclick="return confirm('全商品の価格を再取得します。続けますか？');">🔄 一括価格再取得</a>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="sortable">
<?php foreach ($products as $product): 
    $prices = getShopPrices($pdo, $product['id']);
?>
    <div class="col product-card" data-id="<?= $product['id'] ?>">
        <div class="card shadow-sm h-100">
            <?php if (!empty($product['image_url'])): ?>
                <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" style="object-fit: contain; max-height: 200px;">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="card-text"><small class="text-muted">カテゴリ：<?= htmlspecialchars($product['category']) ?></small></p>
                <ul class="list-group list-group-flush">
                    <?php foreach ($prices as $price): ?>
                    <li class="list-group-item"><?= htmlspecialchars($price['shop_name']) ?>：<?= number_format($price['price']) ?>円</li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">編集</a>
					<a href="manage_competitors.php?product_id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-secondary">統合競合を管理</a>
                    <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('本当に削除しますか？')">削除</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<!-- Sortable.js + 並べ替え送信 -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
const sortable = new Sortable(document.getElementById('sortable'), {
    animation: 150,
    onEnd: function () {
        const order = [];
        document.querySelectorAll('.product-card').forEach((el, index) => {
            order.push({ id: el.dataset.id, position: index + 1 });
        });

        fetch('save_order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(order)
        });
    }
});
</script>


<?php include '../layout/footer.php'; ?>
