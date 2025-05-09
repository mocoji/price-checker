<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$shop_item_id = $_GET['id'] ?? null;
if (!$shop_item_id) {
    die("商品IDが指定されていません。");
}

// 対象商品取得
$stmt = $pdo->prepare("
    SELECT si.*, p.name AS product_name, s.shop_name
    FROM shop_items si
    JOIN products p ON si.product_id = p.id
    JOIN shops s ON si.shop_id = s.id
    WHERE si.id = ?
");
$stmt->execute([$shop_item_id]);
$item = $stmt->fetch();

if (!$item) {
    die("商品が見つかりません。");
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price = $_POST['price'];
    $url = $_POST['url'];
    $item_code = $_POST['item_code'];

    $stmt = $pdo->prepare("
        UPDATE shop_items
        SET price = ?, url = ?, item_code = ?, last_checked = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$price, $url, $item_code, $shop_item_id]);

    header("Location: list.php");
    exit;
}

$pageTitle = "店舗別商品編集";
include '../layout/header.php';
?>

<h1 class="mb-4">店舗別商品情報の編集</h1>

<form method="post" class="card p-4 shadow-sm" style="max-width: 700px;">
    <div class="mb-3">
        <label class="form-label">商品名</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($item['product_name']) ?>" disabled>
    </div>

    <div class="mb-3">
        <label class="form-label">店舗名</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($item['shop_name']) ?>" disabled>
    </div>

    <div class="mb-3">
        <label for="item_code" class="form-label">楽天商品コード</label>
        <input type="text" name="item_code" id="item_code" class="form-control"
               value="<?= htmlspecialchars($item['item_code']) ?>">
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">価格</label>
        <input type="number" name="price" id="price" class="form-control"
               value="<?= htmlspecialchars($item['price']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="url" class="form-label">商品URL</label>
        <input type="text" name="url" id="url" class="form-control"
               value="<?= htmlspecialchars($item['url']) ?>">
    </div>

    <div class="d-flex justify-content-between">
        <a href="list.php" class="btn btn-outline-secondary">戻
