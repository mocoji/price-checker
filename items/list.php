<?php
require '../auth.php';
require_login();
require '../db.php';

$stmt = $pdo->query("SELECT * FROM items ORDER BY id DESC");
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品一覧</title>
</head>
<body>
    <h1>商品一覧</h1>
    <a href="add.php">＋ 新規商品登録</a>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th><th>商品コード</th><th>商品名</th><th>カテゴリ</th><th>操作</th>
        </tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['id']) ?></td>
            <td><?= htmlspecialchars($item['item_code']) ?></td>
            <td><?= htmlspecialchars($item['item_name']) ?></td>
            <td><?= htmlspecialchars($item['category']) ?></td>
            <td>
                <a href="edit.php?id=<?= $item['id'] ?>">編集</a>
                <a href="delete.php?id=<?= $item['id'] ?>" onclick="return confirm('削除しますか？')">削除</a>
            </td>
			<td>
    <a href="../price_history/chart.php?id=<?= $item['id'] ?>">📊 履歴</a>
</td>

        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
