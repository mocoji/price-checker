<?php
require_once '../auth.php';
require_login();
require_once '../db.php';
$pageTitle = "店舗登録";
include '../layout/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_code = $_POST['shop_code'];
    $shop_name = $_POST['shop_name'];
    $is_own_shop = isset($_POST['is_own_shop']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO shops (shop_code, shop_name, is_own_shop) VALUES (?, ?, ?)");
    $stmt->execute([$shop_code, $shop_name, $is_own_shop]); // ← セミコロンが必要！

    log_action($pdo, '店舗登録', "shop_code=$shop_code");

    header('Location: list.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>店舗登録</title>
</head>
<body>
    <h1>新規店舗登録</h1>
    <form method="post">
        <label>店舗コード：<input type="text" name="shop_code" required></label><br><br>
        <label>店舗名：<input type="text" name="shop_name"></label><br><br>
        <label><input type="checkbox" name="is_own_shop"> 自社店舗</label><br><br>
        <button type="submit">登録</button>
        <a href="list.php">戻る</a>
    </form>
<?php include '../layout/footer.php'; ?>