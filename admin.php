<?php
include 'proteksiSession.php';
wajib_admin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Rentalku</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<header class="bar">
    <a class="brand" href="admin.php">Rentalku</a>
    <nav class="nav">
        <a href="mobilAdmin.php">Data Mobil</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="wrap">
    <section class="hero">
        <div>
            <h1>Halo, <?= htmlspecialchars($_SESSION["nama"]) ?></h1>
            <p class="lead">Kelola mobil yang tampil di katalog Rentalku.</p>
            <div class="tiles">
                <a class="tile" href="mobilAdmin.php">
                    <h3>Data mobil</h3>
                    <p>Lihat, ubah, atau hapus mobil.</p>
                </a>
                <a class="tile" href="mobilTambah.php">
                    <h3>Tambah mobil</h3>
                    <p>Isi data dan unggah foto mobil baru.</p>
                </a>
            </div>
        </div>
        <img class="hero-car" src="mobil.png" alt="">
    </section>
</main>
</body>
</html>
