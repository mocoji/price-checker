<?php
session_start();
require 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        header('Location: index.php');
        exit;
    } else {
        $error = 'ログイン失敗：IDまたはパスワードが違います';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"><title>ログイン</title></head>
<body>
    <h1>ログイン</h1>
    <?php if ($error): ?><p style="color:red"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <label>ユーザー名：<input type="text" name="username" required></label><br><br>
        <label>パスワード：<input type="password" name="password" required></label><br><br>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>