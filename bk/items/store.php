<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

// 入力値取得
$shop_id = $_POST['shop_id'] ?? null;
$item_code = $_POST['item_code'] ?? null;
$item_name = $_POST['item_name'] ?? null;
$category = $_POST['category'] ?? null;
$price = $_POST['price'] ?? null;
$thumbnail_url = $_POST['thumbnail_url'] ?? null;

if (!$shop_id || !$item_code || !$item_name || !$price) {
    die('必要な項目が不足しています');
}

// STEP1: 商品マスタに存在するか確認（名前一致でチェック）
$stmt = $pdo->prepare("SELECT id FROM products WHERE name = ?");
$stmt->execute([$item_name]);
$product_id = $stmt->fetchColumn();

// STEP2: 未登録ならproductsに追加
if (!$product_id) {
    $stmt = $pdo->prepare("INSERT INTO products (name, category, image_url) VALUES (?, ?, ?)");
    $stmt->execute([$item_name, $category, $thumbnail_url]);
    $product_id = $pdo->lastInsertId();
}

// STEP3: shop_itemsに登録
$stmt = $pdo->prepare("
    INSERT INTO shop_items (product_id, shop_id, item_code, price, url, last_checked)
    VALUES (?, ?, ?, ?, ?, NOW())
");
$item_url = ''; // 必要ならfetch_item_info.phpからitemUrlも渡す
$stmt->execute([
    $product_id,
    $shop_id,
    $item_code,
    $price,
    $item_url
]);

// 完了後リダイレクト
header("Location: list.php?success=1");
exit;
