<?php
session_start();
include "koneksi.php";

if(isset($_POST["submit"])){
    $email = trim($_POST["email"]);
    $password_asli = $_POST["password"];
    $role = $_POST["role"];

    $stmt = $koneksi->prepare("SELECT id_user, nama, password, role FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $stmt->bind_result($id_user, $nama, $password_hash, $role_db);
    $stmt->fetch();
    $stmt->close();

    if($password_hash && password_verify($password_asli, $password_hash)){
        $_SESSION["status_login"] = true;
        $_SESSION["id_user"] = $id_user;
        $_SESSION["nama"] = $nama;
        $_SESSION["role"] = $role_db;

        if($role_db == "admin"){
            header("Location: admin.php");
        }else{
            header("Location: index.php");
        }
        exit();
    } else{
        header("Location: login.php?pesan=salah");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Rentalku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="halaman">
    <div class="lingkaran"></div>
    <img class="mobil" src="mobil.png" alt="">
    <p class="tagline">Rentalku Siap Menemani Perjalanan Anda</p>

    <header class="menu">
        <a class="logo" href="login.php">Rentalku</a>
        <nav class="tautan"><a href="#">Tentang</a><a href="#">Kontak</a><a href="#">FAQ</a></nav>
    </header>

    <main class="kotak">
        <h3>Selamat datang di</h3>
        <h1>Rentalku</h1>

        <?php
        if(isset($_GET["pesan"])){
            if($_GET["pesan"] == "salah"){
                echo "<div class='gagal'>Email atau password salah!</div>";
            }else if($_GET["pesan"] == "sukses"){
                echo "<div class='berhasil'>Pendaftaran berhasil, silakan login.</div>";
            }
        }
        ?>

        <form action="login.php" method="POST">
            <div class="role">
                <input type="radio" name="role" id="role-pelanggan" value="pelanggan" checked>
                <label for="role-pelanggan">Pengunjung</label>
                <input type="radio" name="role" id="role-admin" value="admin">
                <label for="role-admin">Admin</label>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="submit" class="btn">LOGIN</button>
        </form>

        <p class="switch" id="tautan-signup">Belum punya akun? <a href="signup.php">Sign Up</a></p>
    </main>
</div>

<script>
const radioPelanggan = document.querySelector("#role-pelanggan");
const radioAdmin = document.querySelector("#role-admin");
const tautanSignup = document.querySelector("#tautan-signup");

function aturTautanSignup(){
    if(radioAdmin.checked){
        tautanSignup.style.display = "none";
    }else{
        tautanSignup.style.display = "block";
    }
}

radioPelanggan.addEventListener("change", aturTautanSignup);
radioAdmin.addEventListener("change", aturTautanSignup);
aturTautanSignup();
</script>

</body>
</html>