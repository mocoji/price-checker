<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "操作ログ";
include '../layout/header.php';

// ログ取得（JOINでユーザー名も取得）
$stmt = $pdo->query("
    SELECT ol.*, u.username
    FROM operation_logs ol
    LEFT JOIN users u ON ol.user_id = u.id
    ORDER BY ol.created_at DESC
    LIMIT 100
");
$logs = $stmt->fetchAll();
?>

<h1 class="mb-4">操作ログ一覧</h1>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>日時</th>
            <th>ユーザー名</th>
            <th>操作</th>
            <th>対象</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= htmlspecialchars($log['created_at']) ?></td>
            <td><?= htmlspecialchars($log['username'] ?? '不明') ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['target']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php include '../layout/footer.php'; ?>
