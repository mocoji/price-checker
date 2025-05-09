<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$shop_id = $_POST['shop_id'] ?? null;
$item_code = $_POST['item_code'] ?? '';
$item_name = $_POST['item_name'] ?? '';
$category = $_POST['category'] ?? '';
$price = $_POST['price'] ?? null;
$image_url = $_POST['thumbnail_url'] ?? '';

if (!$shop_id || !$item_code || !$item_name || !$price) {
    die("必須項目が不足しています");
}

// STEP 1: 同じ商品名の products があるか確認
$stmt = $pdo->prepare("SELECT id FROM products WHERE name = ?");
$stmt->execute([$item_name]);
$product_id = $stmt->fetchColumn();

if (!$product_id) {
    $stmt = $pdo->prepare("INSERT INTO products (name, category, image_url) VALUES (?, ?, ?)");
    $stmt->execute([$item_name, $category, $image_url]);
    $product_id = $pdo->lastInsertId();
}

// STEP 2: shop_items に登録
$stmt = $pdo->prepare("
    INSERT INTO shop_items (product_id, shop_id, item_code, price, url, last_checked, is_latest)
    VALUES (?, ?, ?, ?, '', NOW(), 1)
");
$stmt->execute([$product_id, $shop_id, $item_code, $price]);

$shopItemId = $pdo->lastInsertId(); // ← ここでshop_items.idを取得

// ✅ STEP 3: 履歴テーブルに記録
$stmt = $pdo->prepare("
    INSERT INTO price_history (shop_item_id, price, recorded_at)
    VALUES (?, ?, NOW())
");
$stmt->execute([$shopItemId, $price]);

header("Location: ../shop_items/list.php");
exit;
