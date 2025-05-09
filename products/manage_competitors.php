<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$product_id = $_GET['product_id'] ?? null;
if (!$product_id) exit("商品IDが不正です");

// 商品情報取得
$product = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$product->execute([$product_id]);
$product = $product->fetch();

if (!$product) exit("商品が見つかりません");

// 競合の shop_items を取得
$stmt = $pdo->prepare("
    SELECT si.id, si.item_code, si.price, s.shop_name, s.is_own_shop
    FROM shop_items si
    JOIN shops s ON si.shop_id = s.id
    WHERE si.product_id = ?
");
$stmt->execute([$product_id]);
$competitors = $stmt->fetchAll();

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['delete_ids'] ?? [];
    if (!empty($ids)) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("DELETE FROM shop_items WHERE id IN ($in)");
        $stmt->execute($ids);
        header("Location: list.php");
        exit;
    }
}

$pageTitle = "競合商品の削除";
include '../layout/header.php';
?>

<h1 class="mb-4">競合削除：<?= htmlspecialchars($product['name']) ?></h1>

<form method="post">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>選択</th>
				<th>種類</th>
				<th>店舗</th>
				<th>商品コード</th>
				<th>価格</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($competitors as $c): ?>
            <tr>
                <td><input type="checkbox" name="delete_ids[]" value="<?= $c['id'] ?>"></td>
                <td><?= $c['is_own_shop'] ? '自社' : '競合' ?></td>
				<td><?= htmlspecialchars($c['shop_name']) ?></td>
                <td><?= htmlspecialchars($c['item_code']) ?></td>
                <td><?= number_format($c['price']) ?>円</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between">
        <a href="list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-danger"
        onclick="return confirm('選択された商品を削除します。自社商品も含まれる場合、復元できません。本当に削除しますか？')">
        選択した商品を削除
</button>

    </div>
</form>

<?php include '../layout/footer.php'; ?>
