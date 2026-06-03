<?php
require_once 'includes/auth.php';
secure_session_start();
require_once 'includes/db.php';

require_auth();

$page = $_GET['page'] ?? 'home';
$allowed_pages = ['home', 'live', 'finished', 'statistics', 'add', 'about', 'settings'];
if (!in_array($page, $allowed_pages)) $page = 'home';

auto_update_statuses();
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Overview</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous">
</head>
<body>
    <div class="layout">
        <?php include 'includes/sidebar.php'; ?>
        <main class="content">
            <?php include "pages/{$page}.php"; ?>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" integrity="sha512-Z4RJa36Kl84unoevz3wyZ0uFDMj7lfaxAs+hiqXXeZcXg/rjdYTafAXxP/hKSqW8Wwo3vwfg2V/dkuH5fKO2bw==" crossorigin="anonymous"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
