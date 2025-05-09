<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM makers WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: list.php");
exit;
