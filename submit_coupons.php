<?php
require_once 'auth.php';
require_login();
$pageTitle = "クーポン確認";
include 'layout/header.php';

$coupons = $_POST['coupons'] ?? [];

if (empty($coupons)) {
    echo "<div class='alert alert-warning'>クーポン情報が送信されていません。</div>";
    include 'layout/footer.php';
    exit;
}
?>

<div class="container py-4">
    <h1 class="mb-4">送信されたクーポン内容</h1>
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>クーポン名</th>
                <th>コード</th>
                <th>割引</th>
                <th>有効期間</th>
                <th>最低購入額</th>
                <th>使用制限</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($coupons as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['coupon_name']) ?></td>
                <td><?= htmlspecialchars($c['coupon_code']) ?></td>
                <td class="text-danger fw-bold">¥<?= number_format($c['discount_amount']) ?></td>
                <td><?= htmlspecialchars($c['valid_from']) ?>〜<?= htmlspecialchars($c['valid_to']) ?></td>
                <td>¥<?= number_format($c['min_purchase']) ?></td>
                <td><?= htmlspecialchars($c['usage_limit']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-4">
        <a href="generate_coupons.php" class="btn btn-outline-secondary">← 戻って編集</a>
        <a href="download_coupons.php" class="btn btn-success" target="_blank">↓ CSVをダウンロード</a>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
