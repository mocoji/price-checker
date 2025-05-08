<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?? '管理画面' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/price_checker/index.php">価格比較管理画面</a>
        <span class="text-white"><?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?>（<?= $_SESSION['user']['role'] ?? '' ?>）</span>
    </div>
</nav>
<div class="container">
