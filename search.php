<?php
// アプリID（楽天開発者ページで取得）
$applicationId = '1076284524592798370'; // ←書き換えてください

// 検索条件（例：商品コードや商品名の一部）
$keyword = 'c-1dayms90-2p'; // 商品識別のための文字列
$hits = 5; // 検索件数

// APIエンドポイント
$endpoint = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20220601';

// パラメータ設定
$params = [
    'applicationId' => $applicationId,
    'keyword' => $keyword,
    'hits' => $hits,
    'format' => 'json'
];

// URL生成
$url = $endpoint . '?' . http_build_query($params);

// API呼び出し
$response = file_get_contents($url);
$data = json_decode($response, true);

// 結果の表示
if (isset($data['Items'])) {
    echo "<h1>検索結果</h1>";
    foreach ($data['Items'] as $entry) {
        $item = $entry['Item'];
        echo "<p>";
        echo "商品名: " . htmlspecialchars($item['itemName']) . "<br>";
        echo "価格: " . number_format($item['itemPrice']) . "円<br>";
        echo "ショップ: " . htmlspecialchars($item['shopName']) . "<br>";
        echo "</p><hr>";
    }
} else {
    echo "検索結果がありませんでした。";
}
?>
