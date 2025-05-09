<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    echo "商品IDが指定されていません";
    exit;
}

// 対象商品取得
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch();
if (!$item) {
    echo "商品が見つかりません";
    exit;
}

// 全グループ取得
$groups = $pdo->query("SELECT * FROM item_groups ORDER BY created_at DESC")->fetchAll();

// 全商品（統合元以外）
$stmt = $pdo->prepare("SELECT * FROM items WHERE id != ?");
$stmt->execute([$item_id]);
$other_items = $stmt->fetchAll();

// POST保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['group_id'] ?? null;
    $new_group_name = trim($_POST['new_group'] ?? '');
    $selected_items = $_POST['merge_items'] ?? [];

    // 新規グループ作成
    if (!$group_id && $new_group_name !== '') {
        $stmt = $pdo->prepare("INSERT INTO item_groups (group_name) VALUES (?)");
        $stmt->execute([$new_group_name]);
        $group_id = $pdo->lastInsertId();
    }

    if ($group_id) {
        // 現在の商品をグループに登録
        $pdo->prepare("INSERT IGNORE INTO item_group_links (group_id, item_id) VALUES (?, ?)")
            ->execute([$group_id, $item_id]);

        // 選ばれた商品もグループに登録
        $stmt = $pdo->prepare("INSERT IGNORE INTO item_group_links (group_id, item_id) VALUES (?, ?)");
        foreach ($selected_items as $merge_id) {
            $stmt->execute([$group_id, $merge_id]);
        }
    }

    header("Location: list.php");
    exit;
}

$pageTitle = "商品グループ統合";
include '../layout/header.php';
?>

<h1 class="mb-4">商品をグループとして統合</h1>

<div class="mb-4">
    <strong>統合元商品：</strong><br>
    <?= htmlspecialchars($item['item_name']) ?><br>
    <small>コード：<?= htmlspecialchars($item['item_code']) ?> / カテゴリ：<?= htmlspecialchars($item['category']) ?></small>
</div>
