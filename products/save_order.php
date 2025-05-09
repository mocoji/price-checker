<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    http_response_code(400);
    exit("Invalid data");
}

$stmt = $pdo->prepare("UPDATE products SET sort_order = ? WHERE id = ?");
foreach ($data as $row) {
    $stmt->execute([$row['position'], $row['id']]);
}
