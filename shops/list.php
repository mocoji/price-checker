<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "店舗一覧";
include '../layout/header.php';

$stmt = $pdo->query("SELECT * FROM shops ORDER BY id DESC");
$shops = $stmt->fetchAll();
?>

<h1 class="mb-4">店舗一覧</h1>
<a href="add.php" class="btn btn-primary mb-4">＋ 新規店舗登録</a>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php foreach ($shops as $shop): ?>
    <div class="col">
        <div class="card shadow-sm h-100 border-<?= $shop['is_own_shop'] ? 'primary' : 'secondary' ?>">
            <div class="card-body">
                <h5 class="card-title">
                    <?= htmlspecialchars($shop['shop_name'] ?: '（名称未登録）') ?>
                    <?php if ($shop['is_own_shop']): ?>
                        <span class="badge bg-primary ms-2">自社</span>
                    <?php endif; ?>
                </h5>
                <p class="card-text text-muted">
                    店舗コード：<?= htmlspecialchars($shop['shop_code']) ?>
                </p>
                <div class="d-flex justify-content-between">
                    <a href="edit.php?id=<?= $shop['id'] ?>" class="btn btn-sm btn-outline-secondary">編集</a>
                    <a href="delete.php?id=<?= $shop['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('削除しますか？')">削除</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php include '../layout/footer.php';
