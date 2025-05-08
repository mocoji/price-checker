<?php
require 'generate_coupons.php'; // $couponsが生成される想定

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="coupons.csv"');

$output = fopen('php://output', 'w');

// CSVヘッダー（楽天RMS用）
fputcsv($output, ['coupon_name', 'coupon_code', 'discount_amount', 'valid_from', 'valid_to', 'min_purchase', 'usage_limit']);

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
