<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    echo "ユーザーIDが指定されていません";
    exit;
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

header("Location: list.php");
exit;
