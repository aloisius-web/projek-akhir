<?php
session_start();
if (!isset($_SESSION["status_login"]) || $_SESSION["status_login"] !== true) {
    header("Location: login.php");
    exit();
}
if ($_SESSION["role"] != "admin") {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
</head>
<body>
    <h2>Selamat Datang Admin, <?php echo $_SESSION["nama"]; ?>!</h2>
    <p>Ini adalah halaman pengelolaan data rental mobil.</p>
    <a href="logout.php">Logout</a></body>
</html>