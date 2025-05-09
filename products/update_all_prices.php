<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

// 楽天APIのアプリID（※自身のキーに差し替えてください）
require_once '../lib/rakuten.php'; //
$applicationId = getRakutenApplicationId(); // 

if (!$applicationId) {
    exit("楽天APIのアプリIDが設定されていません。");
}

// すべての shop_items を対象に取得
$stmt = $pdo->query("
    SELECT si.id, si.item_code, s.shop_code
    FROM shop_items si
    JOIN shops s ON si.shop_id = s.id
");
$items = $stmt->fetchAll();

$updated = 0;
$skipped = 0;

foreach ($items as $item) {
    if (empty($item['item_code']) || empty($item['shop_code'])) {
        $skipped++;
        continue;
    }

    $params = [
        'applicationId' => $applicationId,
        'keyword' => $item['item_code'],
        'shopCode' => $item['shop_code'],
        'hits' => 1
    ];

    $url = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20220601?' . http_build_query($params);
    $json = @file_get_contents($url);
    $data = json_decode($json, true);

    if (!empty($data['Items'][0]['Item'])) {
        $price = $data['Items'][0]['Item']['itemPrice'];
        $stmt = $pdo->prepare("UPDATE shop_items SET price = ?, last_checked = NOW() WHERE id = ?");
        $stmt->execute([$price, $item['id']]);
        $updated++;
    } else {
        $skipped++;
    }

    // APIレート制限対策：必要なら sleep(1);
}

header("Location: list.php");
exit;
