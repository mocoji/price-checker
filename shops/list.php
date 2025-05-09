<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "店舗一覧";
include '../layout/header.php';

$shops = $pdo->query("SELECT * FROM shops ORDER BY sort_order ASC, id DESC")->fetchAll();
?>

<h1 class="mb-4">店舗一覧</h1>
<a href="add.php" class="btn btn-primary mb-3">＋ 新規店舗登録</a>

<div id="sortable" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
<?php foreach ($shops as $shop): ?>
    <div class="col" data-id="<?= $shop['id'] ?>">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <?= htmlspecialchars($shop['shop_name'] ?: $shop['shop_code']) ?>
                    <?= $shop['is_own_shop'] ? '<span class="badge bg-primary ms-2">自社</span>' : '' ?>
                </h5>
                <p class="card-text">
                    店舗コード：<?= htmlspecialchars($shop['shop_code']) ?><br>
                    登録日：<?= htmlspecialchars($shop['created_at']) ?>
                </p>
                <div class="d-flex justify-content-between">
                    <a href="edit.php?id=<?= $shop['id'] ?>" class="btn btn-sm btn-outline-primary">編集</a>
                    <a href="delete.php?id=<?= $shop['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('本当に削除しますか？')">削除</a>
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
        fetch('update_shop_sort_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order })
        });
    }
});
</script>

<?php include '../layout/footer.php'; ?>
