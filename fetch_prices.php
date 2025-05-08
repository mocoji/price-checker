<?php
require 'db.php';

$applicationId = '1076284524592798370'; // ←置き換えてください
$endpoint = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20220601';

// 商品と店舗を取得
$items = $pdo->query("SELECT * FROM items")->fetchAll();
$shops = $pdo->query("SELECT * FROM shops")->fetchAll();

foreach ($items as $item) {
    foreach ($shops as $shop) {
        // 楽天API呼び出し
        $params = [
            'applicationId' => $applicationId,
            'keyword' => $item['item_code'],
            'shopCode' => $shop['shop_code'],
            'hits' => 1,
        ];
        $url = $endpoint . '?' . http_build_query($params);
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (!isset($data['Items'][0]['Item'])) continue;
        $itemData = $data['Items'][0]['Item'];
        $price = $itemData['itemPrice'];

        // 旧レコードの is_latest を 0 に更新
        $stmt = $pdo->prepare("UPDATE shop_items SET is_latest = 0 WHERE shop_id = ? AND item_id = ?");
        $stmt->execute([$shop['id'], $item['id']]);

        // 新しい価格を登録
        $stmt = $pdo->prepare("
            INSERT INTO shop_items (shop_id, item_id, price, is_latest)
            VALUES (?, ?, ?, 1)
        ");
        $stmt->execute([$shop['id'], $item['id'], $price]);
        $shop_item_id = $pdo->lastInsertId();

        // 前回価格を取得して異なる場合のみ履歴保存
        $stmt = $pdo->prepare("
            SELECT price FROM price_history
            WHERE shop_item_id = ?
            ORDER BY recorded_at DESC LIMIT 1
        ");
        $stmt->execute([$shop_item_id]);
        $last = $stmt->fetch();

        if (!$last || $last['price'] != $price) {
            $stmt = $pdo->prepare("
                INSERT INTO price_history (shop_item_id, price)
                VALUES (?, ?)
            ");
            $stmt->execute([$shop_item_id, $price]);
        }

        // 1秒スリープ（API制限対策）
        sleep(1);
    }
}

echo "価格取得完了\n";
