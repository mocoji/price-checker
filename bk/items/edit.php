<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    echo "商品IDが指定されていません";
    exit;
}

// 商品情報取得
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch();
if (!$item) {
    echo "商品が見つかりません";
    exit;
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_code = $_POST['item_code'];
    $item_name = $_POST['item_name'];
    $thumbnail_url = $_POST['thumbnail_url'];
    $category = $_POST['category'];

    $stmt = $pdo->prepare("UPDATE items SET item_code = ?, item_name = ?, thumbnail_url = ?, category = ? WHERE id = ?");
    $stmt->execute([$item_code, $item_name, $thumbnail_url, $category, $item_id]);

    header("Location: list.php");
    exit;
}

$pageTitle = "商品編集";
include '../layout/header.php';
?>

<h1 class="mb-4">商品情報を編集</h1>

<form method="post" class="card p-4 shadow-sm" style="max-width: 700px;">
    <div class="mb-3">
        <label for="item_code" class="form-label">商品コード</label>
        <input type="text" class="form-control" id="item_code" name="item_code" required
               value="<?= htmlspecialchars($item['item_code']) ?>">
    </div>

    <div class="mb-3">
        <label for="item_name" class="form-label">商品名</label>
        <input type="text" class="form-control" id="item_name" name="item_name" required
               value="<?= htmlspecialchars($item['item_name']) ?>">
    </div>

    <div class="mb-3">
        <label for="thumbnail_url" class="form-label">商品画像URL</label>
        <input type="text" class="form-control" id="thumbnail_url" name="thumbnail_url"
               value="<?= htmlspecialchars($item['thumbnail_url']) ?>">
        <?php if (!empty($item['thumbnail_url'])): ?>
            <img src="<?= htmlspecialchars($item['thumbnail_url']) ?>" alt="画像" class="mt-2" style="max-height: 100px;">
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <label for="category" class="form-label">カテゴリ</label>
        <input type="text" class="form-control" id="category" name="category"
               value="<?= htmlspecialchars($item['category']) ?>">
    </div>

    <div class="d-flex justify-content-between">
        <a href="_list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新する</button>
    </div>
</form>

<?php include '../layout/footer.php'; ?>
