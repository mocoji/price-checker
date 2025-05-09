<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    echo "商品IDが指定されていません。";
    exit;
}

// 競合商品の取得（他店の商品）
$stmt = $pdo->prepare("SELECT * FROM items WHERE id != ?");
$stmt->execute([$item_id]);
$other_items = $stmt->fetchAll();

// 既存の比較対象
$existing_stmt = $pdo->prepare("SELECT compared_item_id FROM item_comparisons WHERE item_id = ?");
$existing_stmt->execute([$item_id]);
$existing_ids = $existing_stmt->fetchAll(PDO::FETCH_COLUMN);

// 保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['compared'] ?? [];
    // 既存削除
    $pdo->prepare("DELETE FROM item_comparisons WHERE item_id = ?")->execute([$item_id]);
    // 新規登録
    $stmt = $pdo->prepare("INSERT INTO item_comparisons (item_id, compared_item_id) VALUES (?, ?)");
    foreach ($selected as $cmp_id) {
        $stmt->execute([$item_id, $cmp_id]);
    }
    header("Location: list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>比較対象商品選択</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h3>比較対象商品を選択（ID: <?= htmlspecialchars($item_id) ?>）</h3>

    <form method="POST">
        <div class="list-group mb-3">
            <?php foreach ($other_items as $item): ?>
                <label class="list-group-item">
                    <input type="checkbox" name="compared[]" value="<?= $item['id'] ?>"
                        <?= in_array($item['id'], $existing_ids) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($item['item_name']) ?>（<?= htmlspecialchars($item['price']) ?>円）
                </label>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary">保存</button>
        <a href="_list.php" class="btn btn-secondary">戻る</a>
    </form>
</body>
</html>
