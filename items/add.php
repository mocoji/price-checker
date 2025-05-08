<?php
require '../db.php';
require '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_code = $_POST['item_code'];
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $thumbnail_url = $_POST['thumbnail_url'];

    $stmt = $pdo->prepare("INSERT INTO items (item_code, item_name, category, thumbnail_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$item_code, $item_name, $category, $thumbnail_url]);
    // ← この位置にログを記録！
   log_action($pdo, '商品登録', "item_code=$item_code");
    header('Location: list.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品登録</title>
</head>
<body>
    <h1>新規商品登録</h1>
    <form method="post">
        <label>商品コード：<input type="text" name="item_code" required></label><br><br>
        <label>商品名：<input type="text" name="item_name" required></label><br><br>
        <label>カテゴリ：<input type="text" name="category"></label><br><br>
        <label>画像URL：<input type="text" name="thumbnail_url"></label><br><br>
        <button type="submit">登録</button>
        <a href="list.php">戻る</a>
    </form>
</body>
</html>
