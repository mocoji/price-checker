<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    echo "商品IDが指定されていません";
    exit;
}

// 商品情報取得
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product) {
    echo "商品が見つかりません";
    exit;
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $thumbnail_url = $_POST['image_url'];
    $category = $_POST['category'];

    $stmt = $pdo->prepare("UPDATE products SET name = ?, image_url = ?, category = ? WHERE id = ?");
    $stmt->execute([$name, $thumbnail_url, $category, $product_id]);

    header("Location: ../shop_items/list.php");
    exit;
}

$pageTitle = "商品編集";
include '../layout/header.php';
?>

<h1 class="mb-4">商品情報を編集</h1>

<form method="post" class="card p-4 shadow-sm" style="max-width: 700px;">
    <div class="mb-3">
        <label for="name" class="form-label">商品名</label>
        <input type="text" class="form-control" id="name" name="name" required
               value="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <div class="mb-3">
        <label for="image_url" class="form-label">商品画像URL</label>
        <input type="text" class="form-control" id="image_url" name="image_url"
               value="<?= htmlspecialchars($product['image_url']) ?>">
        <?php if (!empty($product['image_url'])): ?>
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="画像" class="mt-2" style="max-height: 100px;">
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <label for="category" class="form-label">カテゴリ</label>
        <input type="text" class="form-control" id="category" name="category"
               value="<?= htmlspecialchars($product['category']) ?>">
    </div>

    <div class="d-flex justify-content-between">
        <a href="../shop_items/list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新する</button>
    </div>
</form>

<?php include '../layout/footer.php'; ?>
