<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$makers = $pdo->query("SELECT * FROM makers ORDER BY name ASC")->fetchAll();

$pageTitle = "メーカー一覧";
include '../layout/header.php';
?>

<h1 class="mb-4">メーカー一覧</h1>

<a href="add.php" class="btn btn-primary mb-3">＋ 新規メーカー登録</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>メーカー名</th>
            <th>国（任意）</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($makers as $maker): ?>
            <tr>
                <td><?= $maker['id'] ?></td>
                <td><?= htmlspecialchars($maker['name']) ?></td>
                <td><?= htmlspecialchars($maker['country']) ?></td>
                <td>
                    <a href="delete.php?id=<?= $maker['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('削除してもよろしいですか？')">削除</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../layout/footer.php'; ?>
