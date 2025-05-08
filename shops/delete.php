<?php
require '../db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM shops WHERE id = ?");
$stmt->execute([$id]);

header('Location: list.php');
exit;
?>
