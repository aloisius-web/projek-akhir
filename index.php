<?php
session_start();
if (!isset($_SESSION["status_login"]) || $_SESSION["status_login"] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Pelanggan</title>
</head>
<body>
    <h2>Halo, <?php echo $_SESSION["nama"]; ?>!</h2>
    <p>Selamat datang di halaman utama rental mobil.</p>
    <a href="logout.php">Logout</a>
</body>
</html>