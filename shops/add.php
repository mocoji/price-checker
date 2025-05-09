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
    $stmt->execute([$shop_code, $shop_name, $is_own_shop]);

    log_action($pdo, '店舗登録', "shop_code=$shop_code");
    header('Location: list.php');
    exit;
}
?>

<h1 class="mb-4">新規店舗登録</h1>

<form method="post" class="card shadow-sm p-4 bg-white">
    <div class="mb-3">
        <label for="shop_code" class="form-label">楽天店舗コード <span class="text-danger">*</span></label>
        <input type="text" name="shop_code" id="shop_code" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="shop_name" class="form-label">店舗名（任意）</label>
        <input type="text" name="shop_name" id="shop_name" class="form-control">
    </div>

    <div class="form-check mb-4">
        <input type="checkbox" name="is_own_shop" id="is_own_shop" class="form-check-input">
        <label for="is_own_shop" class="form-check-label">自社店舗として登録する</label>
    </div>

    <div class="d-flex justify-content-between">
        <a href="list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">登録する</button>
    </div>
</form>

<?php include '../layout/footer.php'; ?>
