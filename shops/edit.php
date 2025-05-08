<?php
require '../db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM shops WHERE id = ?");
$stmt->execute([$id]);
$shop = $stmt->fetch();

if (!$shop) {
    die('店舗が見つかりません');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_code = $_POST['shop_code'];
    $shop_name = $_POST['shop_name'];
    $is_own_shop = isset($_POST['is_own_shop']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE shops SET shop_code = ?, shop_name = ?, is_own_shop = ? WHERE id = ?");
    $stmt->execute([$shop_code, $shop_name, $is_own_shop, $id]);

    header('Location: list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>店舗編集</title>
</head>
<body>
    <h1>店舗編集</h1>
    <form method="post">
        <label>店舗コード：<input type="text" name="shop_code" value="<?= htmlspecialchars($shop['shop_code']) ?>" required></label><br><br>
        <label>店舗名：<input type="text" name="shop_name" value="<?= htmlspecialchars($shop['shop_name']) ?>"></label><br><br>
        <label><input type="checkbox" name="is_own_shop" <?= $shop['is_own_shop'] ? 'checked' : '' ?>> 自社店舗</label><br><br>
        <button type="submit">更新</button>
        <a href="list.php">戻る</a>
    </form>
</body>
</html>
