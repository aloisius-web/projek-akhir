<?php
include 'proteksiSession.php';
wajib_login();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda | Rentalku</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<header class="bar">
    <a class="brand" href="index.php">Rentalku</a>
    <nav class="nav">
        <a href="Katalog.php">Katalog</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="wrap">
    <section class="hero">
        <div>
            <h1>Halo, <?= htmlspecialchars($_SESSION["nama"]) ?></h1>
            <p class="lead">Cari mobil yang cocok untuk perjalanan Anda.</p>
            <div class="tiles">
                <a class="tile" href="Katalog.php">
                    <h3>Katalog mobil</h3>
                    <p>Cari berdasarkan nama, kategori, harga, dan tanggal.</p>
                </a>
            </div>
        </div>
        <img class="hero-car" src="mobil.png" alt="">
    </section>
</main>
</body>
</html>
