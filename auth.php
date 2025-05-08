<?php
session_start();

function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: /price_checker/login.php');
        exit;
    }
}

function is_admin() {
    return $_SESSION['user']['role'] === 'admin';
}

function is_editor() {
    return in_array($_SESSION['user']['role'], ['admin', 'editor']);
}

function log_action($pdo, $action, $target = '') {
    if (!isset($_SESSION['user'])) return;
    $stmt = $pdo->prepare("INSERT INTO operation_logs (user_id, action, target) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user']['id'], $action, $target]);
}
