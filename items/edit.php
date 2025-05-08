<?php
require '../db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    die('商品が見つかりません');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_code = $_POST['item_code'];
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $thumbnail_url = $_POST['thumbnail_url'];

    $stmt = $pdo->prepare("UPDATE items SET item_code = ?, item_name = ?, category = ?, thumbnail_url = ? WHERE id = ?");
    $stmt->execute([$item_code, $item_name, $category, $thumbnail_url, $id]);

    header('Location: list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品編集</title>
</head>
<body>
    <h1>商品編集</h1>
    <form method="post">
        <label>商品コード：<input type="text" name="item_code" value="<?= htmlspecialchars($item['item_code']) ?>" required></label><br><br>
        <label>商品名：<input type="text" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required></label><br><br>
        <label>カテゴリ：<input type="text" name="category" value="<?= htmlspecialchars($item['category']) ?>"></label><br><br>
        <label>画像URL：<input type="text" name="thumbnail_url" value="<?= htmlspecialchars($item['thumbnail_url']) ?>"></label><br><br>
        <button type="submit">更新</button>
        <a href="list.php">戻る</a>
    </form>
</body>
</html>
