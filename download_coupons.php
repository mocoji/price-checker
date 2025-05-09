<?php
session_start();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="coupons.csv"');

$coupons = $_SESSION['coupons'] ?? [];

$output = fopen('php://output', 'w');
fputcsv($output, ['クーポン名', 'コード', '割引金額', '有効期間（From）', '有効期間（To）', '最低購入金額', '使用制限']);

foreach ($coupons as $c) {
    fputcsv($output, [
        $c['coupon_name'],
        $c['coupon_code'],
        $c['discount_amount'],
        $c['valid_from'],
        $c['valid_to'],
        $c['min_purchase'],
        $c['usage_limit']
    ]);
}

fclose($output);
exit;
