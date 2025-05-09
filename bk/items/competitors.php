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

// 競合店舗一覧
$shops = $pdo->query("SELECT * FROM shops WHERE is_own_shop = 0 ORDER BY shop_name")->fetchAll();

// 現在の紐付け取得
$current = $pdo->prepare("SELECT competitor_shop_id FROM item_competitors WHERE item_id = ?");
$current->execute([$item_id]);
$current_ids = array_column($current->fetchAll(), 'competitor_shop_id');

// 保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['competitors'] ?? [];

    // 一旦削除
    $pdo->prepare("DELETE FROM item_competitors WHERE item_id = ?")->execute([$item_id]);

    // 新規登録
    $stmt = $pdo->prepare("INSERT INTO item_competitors (item_id, competitor_shop_id) VALUES (?, ?)");
    foreach ($selected as $shop_id) {
        $stmt->execute([$item_id, $shop_id]);
    }

    header("Location: list.php");
    exit;
}

$pageTitle = "比較対象設定";
include '../layout/header.php';
?>

<h1 class="mb-4">商品別 比較対象設定</h1>

<div class="mb-3">
    <h5><?= htmlspecialchars($item['item_name']) ?></h5>
    <div class="text-muted">
        商品コード：<?= htmlspecialchars($item['item_code']) ?>　
        カテゴリ：<?= htmlspecialchars($item['category']) ?>
    </div>
</div>

<form method="post" class="card p-4 shadow-sm">
    <h6 class="mb-3">比較対象とする競合店舗を選択：</h6>
    <?php foreach ($shops as $shop): ?>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="competitors[]" id="shop<?= $shop['id'] ?>"
                   value="<?= $shop['id'] ?>" <?= in_array($shop['id'], $current_ids) ? 'checked' : '' ?>>
            <label class="form-check-label" for="shop<?= $shop['id'] ?>">
                <?= htmlspecialchars($shop['shop_name'] ?: $shop['shop_code']) ?>
            </label>
        </div>
    <?php endforeach; ?>

    <div class="d-flex justify-content-between mt-4">
        <a href="_list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">保存する</button>
    </div>
</form>


