<?php
require '../db.php';

$stmt = $pdo->query("SELECT * FROM shops ORDER BY id DESC");
$shops = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>店舗一覧</title>
</head>
<body>
    <h1>店舗一覧</h1>
    <a href="add.php">＋ 新規店舗登録</a>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th><th>コード</th><th>名称</th><th>自社</th><th>操作</th>
        </tr>
        <?php foreach ($shops as $shop): ?>
        <tr>
            <td><?= htmlspecialchars($shop['id']) ?></td>
            <td><?= htmlspecialchars($shop['shop_code']) ?></td>
            <td><?= htmlspecialchars($shop['shop_name']) ?></td>
            <td><?= $shop['is_own_shop'] ? '✔' : '' ?></td>
            <td>
                <a href="edit.php?id=<?= $shop['id'] ?>">編集</a>
                <a href="delete.php?id=<?= $shop['id'] ?>" onclick="return confirm('削除しますか？')">削除</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
