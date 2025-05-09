<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order']) || !is_array($data['order'])) {
    http_response_code(400);
    exit("Invalid data");
}

$stmt = $pdo->prepare("UPDATE shops SET sort_order = ? WHERE id = ?");
foreach ($data['order'] as $position => $id) {
    $stmt->execute([$position, $id]);
}

http_response_code(200);
echo "OK";
