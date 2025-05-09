<?php
require_once '../auth.php';
require_login();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $country = $_POST['country'] ?? '';

    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO makers (name, country) VALUES (?, ?)");
        $stmt->execute([$name, $country]);
        header("Location: list.php");
        exit;
    } else {
        $error = "メーカー名は必須です";
    }
}

$pageTitle = "メーカー登録";
include '../layout/header.php';
?>

<h1 class="mb-4">新規メーカー登録</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="post" class="card p-4 shadow-sm" style="max-width: 600px;">
    <div class="mb-3">
        <label for="name" class="form-label">メーカー名</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="country" class="form-label">国（任意）</label>
        <input type="text" name="country" id="country" class="form-control">
    </div>

    <div class="d-flex justify-content-between">
        <a href="list.php" class="btn btn-outline-secondary">戻る</a>
        <button type="submit" class="btn btn-primary">登録する</button>
    </div>
</form>

<?php include '../layout/footer.php'; ?>
