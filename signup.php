<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Rentalku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="halaman">
    <div class="lingkaran"></div>
    <img class="mobil" src="mobil.png" alt="">
    <p class="tagline">Nikmati Perjalanan Dengan Rentalku</p>

    <header class="menu">
        <a class="logo" href="login.php">Rentalku</a>
        <nav class="tautan"><a href="#">Tentang</a><a href="#">Kontak</a><a href="#">FAQ</a></nav>
    </header>

    <main class="kotak">
        <h3>Buat akun baru di</h3>
        <h1>Rentalku</h1>

        <?php
        if (isset($_GET["pesan"])) {
            if ($_GET["pesan"] == "kosong") {
                echo "<div class='gagal'>Semua kolom wajib diisi.</div>";
            } else if ($_GET["pesan"] == "hp_salah") {
                echo "<div class='gagal'>Nomor HP hanya boleh berisi angka.</div>";
            } else if ($_GET["pesan"] == "pw_pendek") {
                echo "<div class='gagal'>Password minimal 8 karakter.</div>";
            } else if ($_GET["pesan"] == "email_ada") {
                echo "<div class='gagal'>Email sudah terdaftar.</div>";
            } else if ($_GET["pesan"] == "gagal") {
                echo "<div class='gagal'>Pendaftaran gagal, coba lagi.</div>";
            }
        }
        ?>

        <form action="signup_proses.php" method="POST">
            <div class="field">
                <label for="nama">Name</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="field">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required>
            </div>
            <button type="submit" class="btn">SIGN UP</button>
        </form>
        <p class="switch">Sudah punya akun? <a href="login.php">Sign In</a></p>
    </main>
</div>
</body>
</html>