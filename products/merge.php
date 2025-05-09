<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$pageTitle = "商品統合ツール";

// 商品一覧を取得
$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll();

// POST 処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $merge_from = $_POST['merge_from'] ?? [];
    $merge_to = $_POST['merge_to'] ?? null;

    if (!$merge_to || empty($merge_from)) {
        $error = "統合元と統合先を選んでください。";
    } elseif (in_array($merge_to, $merge_from)) {
        $error = "統合元と統合先が重複しています。";
    } else {
        $stmt = $pdo->prepare("UPDATE shop_items SET product_id = ? WHERE product_id = ?");
        foreach ($merge_from as $from_id) {
            $stmt->execute([$merge_to, $from_id]);
        }

        // 統合元 products を削除
        $in = implode(',', array_fill(0, count($merge_from), '?'));
        $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($in)");
        $stmt->execute($merge_from);

        $success = "商品統合が完了しました。";
    }
}
?>

<?php include '../layout/header.php'; ?>

<h1 class="mb-4">商品統合ツール</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php elseif (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST" class="card p-4 shadow-sm" style="max-width: 800px;">
    <div class="mb-3">
        <label class="form-label">統合元商品（削除されます）</label>
        <select name="merge_from[]" multiple class="form-select" size="6" required>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?>（ID: <?= $p['id'] ?>）</option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Ctrl+クリックで複数選択可能</small>
    </div>

    <div class="mb-4">
        <label class="form-label">統合先商品（残す商品）</label>
        <select name="merge_to" class="form-select" required>
            <option value="">-- 統合先を選択 --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?>（ID: <?= $p['id'] ?>）</option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">統合実行</button>
</form>

<?php include '../layout/footer.php'; ?>
