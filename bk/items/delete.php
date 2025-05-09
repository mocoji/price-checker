<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    echo "商品IDが指定されていません";
    exit;
}

// 1. price_history を先に削除
$stmt = $pdo->prepare("
    DELETE ph FROM price_history ph
    JOIN shop_items si ON ph.shop_item_id = si.id
    WHERE si.item_id = ?
");
$stmt->execute([$item_id]);

// 2. shop_items を削除
$stmt = $pdo->prepare("DELETE FROM shop_items WHERE item_id = ?");
$stmt->execute([$item_id]);

// 3. item_competitors を削除
$stmt = $pdo->prepare("DELETE FROM item_competitors WHERE item_id = ?");
$stmt->execute([$item_id]);

// 4. items を削除
$stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
$stmt->execute([$item_id]);

// ログ記録（任意）
log_action($pdo, '商品削除', "item_id=$item_id");

header('Location: list.php');
exit;
