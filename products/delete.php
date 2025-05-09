<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    die("商品IDが指定されていません。");
}

// shop_items を先に削除（または論理削除）
$stmt = $pdo->prepare("DELETE FROM shop_items WHERE product_id = ?");
$stmt->execute([$product_id]);

// products テーブルからも削除
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$product_id]);

header("Location: ../shop_items/list.php");
exit;
