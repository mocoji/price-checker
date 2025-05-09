<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);

    header("Location: list.php");
    exit;
}

$pageTitle = "ユーザー追加";
include '../layout/header.php';
?>

<h1 class="mb-4">新規ユーザー登録</h1>

<form method="post" class="card p-4 shadow-sm" style="max-width: 600px;">
    <div class="mb-3">
        <label for="username" class="form-label">ユーザー名</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">パスワード</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <div class="mb-4">
        <label for="role" class="form-label">権限</label>
        <select name="role" id="role" class="form-select" required>
            <option value="">-- 選択してください --</option>
            <option value="admin">管理者（admin）</option>
            <option value="editor">編集者（editor）</option>
            <option value="viewer">閲覧のみ（viewer）</option>
        </select>
    </div>

    <div class="d-flex justify-content-between">
        <a href="list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">登録する</button>
    </div>
</form>

<?php include '../layout/footer.php'; ?>
