<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$shop_id = $_GET['id'] ?? null;
if (!$shop_id) {
    echo "店舗IDが指定されていません";
    exit;
}

// 店舗情報取得
$stmt = $pdo->prepare("SELECT * FROM shops WHERE id = ?");
$stmt->execute([$shop_id]);
$shop = $stmt->fetch();
if (!$shop) {
    echo "店舗が見つかりません";
    exit;
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_code = $_POST['shop_code'];
    $shop_name = $_POST['shop_name'];
    $is_own_shop = isset($_POST['is_own_shop']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE shops SET shop_code = ?, shop_name = ?, is_own_shop = ? WHERE id = ?");
    $stmt->execute([$shop_code, $shop_name, $is_own_shop, $shop_id]);

    header("Location: list.php");
    exit;
}

$pageTitle = "店舗編集";
include '../layout/header.php';
?>

<h1 class="mb-4">店舗情報を編集</h1>

<form method="post" class="card p-4 shadow-sm" style="max-width: 600px;">
    <div class="mb-3">
        <label for="shop_code" class="form-label">店舗コード</label>
        <input type="text" class="form-control" id="shop_code" name="shop_code" required
               value="<?= htmlspecialchars($shop['shop_code']) ?>">
    </div>

    <div class="mb-3">
        <label for="shop_name" class="form-label">店舗名（任意）</label>
        <input type="text" class="form-control" id="shop_name" name="shop_name"
               value="<?= htmlspecialchars($shop['shop_name']) ?>">
    </div>

    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" id="is_own_shop" name="is_own_shop"
               <?= $shop['is_own_shop'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="is_own_shop">自社店舗として扱う</label>
    </div>

    <div class="d-flex justify-content-between">
        <a href="list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新する</button>
    </div>
</form>

<?php include '../layout/footer.php'; ?>
