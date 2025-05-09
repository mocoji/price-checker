<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    echo "ユーザーIDが指定されていません";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    echo "ユーザーが見つかりません";
    exit;
}

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    // パスワード変更があれば処理
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, password_hash = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $password, $role, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $role, $user_id]);
    }

    header("Location: list.php");
    exit;
}

$pageTitle = "ユーザー編集";
include '../layout/header.php';
?>

<h1 class="mb-4">ユーザー編集</h1>

<form method="post" class="card p-4 shadow-sm" style="max-width: 600px;">
    <div class="mb-3">
        <label for="username" class="form-label">ユーザー名</label>
        <input type="text" class="form-control" id="username" name="username" required
               value="<?= htmlspecialchars($user['username']) ?>">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">パスワード（変更時のみ入力）</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>

    <div class="mb-4">
        <label for="role" class="form-label">権限</label>
        <select name="role" id="role" class="form-select" required>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>管理者（admin）</option>
            <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>編集者（editor）</option>
            <option value="viewer" <?= $user['role'] === 'viewer' ? 'selected' : '' ?>>閲覧のみ（viewer）</option>
        </select>
    </div>

    <div class="d-flex justify-content-between">
        <a href="list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">更新する</button>
    </div>
</form>

<?php include '../layout/footer.php'; ?>
