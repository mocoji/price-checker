<?php
require '../db.php';
require '../auth.php';
require_login();

if (!is_admin()) {
    die('管理者専用ページです。');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);

    log_action($pdo, 'ユーザー登録', "username=$username");

    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
</head>
<body>
    <h1>新規ユーザー登録</h1>
    <a href="../index.php">← 戻る</a>
    <form method="post">
        <label>ユーザー名：<input type="text" name="username" required></label><br><br>
        <label>パスワード：<input type="password" name="password" required></label><br><br>
        <label>権限：
            <select name="role">
                <option value="viewer">viewer（閲覧専用）</option>
                <option value="editor">editor（編集可能）</option>
                <option value="admin">admin（管理者）</option>
            </select>
        </label><br><br>
        <button type="submit">登録する</button>
    </form>
</body>
</html>
