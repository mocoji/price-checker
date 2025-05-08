<?php
$host = 'localhost';
$dbname = 'price_checker';
$user = 'root'; // 通常XAMPPはroot
$pass = '';     // パスワード未設定が多い（設定していれば入力）

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('データベース接続失敗: ' . $e->getMessage());
}
?>
