<?php
require '../db.php';
require '../auth.php';
require_login();
if (!is_admin()) die('管理者専用ページです');

// 操作ログを取得（最新50件）
$stmt = $pdo->query("
    SELECT l.*, u.username
    FROM operation_logs l
    JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
    LIMIT 50
");
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>操作ログ一覧</title>
</head>
<body>
    <h1>操作ログ（最新50件）</h1>
    <a href="../index.php">← 戻る</a><br><br>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th><th>ユーザー</th><th>操作</th><th>対象</th><th>日時</th>
        </tr>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= $log['id'] ?></td>
            <td><?= htmlspecialchars($log['username']) ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['target']) ?></td>
            <td><?= $log['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
