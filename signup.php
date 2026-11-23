<?php
include "koneksi.php";

$pesan = "";

if(isset($_POST["submit"])){
    $nama = trim($_POST["nama"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password_asli = $_POST["password"];
    $role = "pelanggan";

    $stmt = $koneksi->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($cek);
    $stmt->fetch();
    $stmt->close();

    if($nama == "" || $email == "" || $phone == "" || $password_asli == ""){
        $pesan = "Semua Kolom Wajib Diisi!";
    }else if(!is_numeric($phone)){
        $pesan = "Nomor HP Hanya Boleh Berisi Angka!";
    }else if(strlen($password_asli) < 8){
        $pesan = "Password Minimal 8 Karakter!";
    }else if($cek){
        $pesan = "Email Sudah Dipakai!";
    }else{
        $password_hash = password_hash($password_asli, PASSWORD_DEFAULT);

        $stmt = $koneksi->prepare("INSERT INTO users(nama, email, no_hp, password, role) VALUES(?,?,?,?,?)");
        $stmt->bind_param("sssss", $nama, $email, $phone, $password_hash, $role);

        if($stmt->execute()){
            header("Location: login.php?pesan=sukses");
            exit();
        }else{
            $pesan = "Pendaftaran Gagal : " . $koneksi->error;
        }
        $stmt->close();
    }
}
?>

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

        <?php if($pesan != ""){ ?>
            <div class="gagal"><?php echo $pesan; ?></div>
        <?php } ?>

        <form action="signup.php" method="POST">
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
            <button type="submit" name="submit" class="btn">SIGN UP</button>
        </form>
        <p class="switch">Sudah punya akun? <a href="login.php">Sign In</a></p>
    </main>
</div>
</body>
</html>