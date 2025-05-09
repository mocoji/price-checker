<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "ユーザー一覧";
include '../layout/header.php';

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<h1 class="mb-4">ユーザー一覧</h1>
<a href="add.php" class="btn btn-primary mb-3">＋ 新規ユーザー登録</a>

<table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>ユーザー名</th>
            <th>権限</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td>
                <!-- 編集・削除未実装であればこのまま -->
                <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-secondary">編集</a>
                <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('削除しますか？')">削除</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../layout/footer.php'; ?>
