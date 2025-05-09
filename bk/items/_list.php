<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "商品一覧";
include '../layout/header.php';

$items = $pdo->query("SELECT * FROM items ORDER BY id DESC")->fetchAll();

function getShopPrices($pdo, $itemId) {
    $stmt = $pdo->prepare("
        SELECT si.price, s.shop_name
        FROM shop_items si
        JOIN shops s ON si.shop_id = s.id
        WHERE si.item_id = ? AND si.is_latest = 1
    ");
    $stmt->execute([$itemId]);
    return $stmt->fetchAll();
}
?>
<h1 class="mb-4">商品一覧</h1>
<a href="add.php" class="btn btn-primary mb-4">＋ 新規商品登録</a>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
<?php foreach ($items as $item): 
    $prices = getShopPrices($pdo, $item['id']);
?>
    <div class="col">
        <div class="card shadow-sm h-100">
            <?php if (!empty($item['thumbnail_url'])): ?>
                <img src="<?= htmlspecialchars($item['thumbnail_url']) ?>" class="card-img-top" alt="商品画像" style="object-fit: contain; max-height: 200px;">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($item['item_name']) ?></h5>
                <ul class="list-group list-group-flush">
                    <?php foreach ($prices as $price): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($price['shop_name']) ?>：<?= number_format($price['price']) ?>円
                    </li>
					<li><a href="compare_select.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">比較対象設定</a>
					</li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">編集</a>
                    <a href="delete.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('本当に削除しますか？')">削除</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php include '../layout/footer.php'; ?>
