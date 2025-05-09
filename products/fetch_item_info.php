<?php
header('Content-Type: application/json');

// 楽天APIの設定（あなたのアプリIDに変更してください）
require_once '../lib/rakuten.php'; //
$applicationId = getRakutenApplicationId(); // 

$code = $_GET['code'] ?? '';
$shopId = $_GET['shop_id'] ?? '';

// バリデーション
if (!$code || !$shopId) {
    echo json_encode(['success' => false, 'message' => 'パラメータ不足']);
    exit;
}

// 店舗コードを取得（DBから）
require_once __DIR__ . '/../db.php';

$stmt = $pdo->prepare("SELECT shop_code FROM shops WHERE id = ?");
$stmt->execute([$shopId]);
$shop = $stmt->fetch();

if (!$shop) {
    echo json_encode(['success' => false, 'message' => '店舗が見つかりません']);
    exit;
}

$shopCode = $shop['shop_code'];

// 楽天API呼び出し
$endpoint = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20220601';
$params = [
    'applicationId' => $applicationId,
    'keyword' => $code,
    'shopCode' => $shopCode,
    'hits' => 1,
];

$url = $endpoint . '?' . http_build_query($params);
$response = @file_get_contents($url);
$data = json_decode($response, true);

// エラーハンドリング
if (!$data || !isset($data['Items'][0]['Item'])) {
    echo json_encode(['success' => false, 'message' => '商品が見つかりません']);
    exit;
}

$item = $data['Items'][0]['Item'];

echo json_encode([
    'success' => true,
    'item_name' => $item['itemName'],
    'price' => $item['itemPrice'],
    'category' => $item['genreId'] ?? '未分類',
    'thumbnail_url' => $item['mediumImageUrls'][0]['imageUrl'] ?? '',
]);
