<?php
include "koneksi.php";

$nama     = $_POST["nama"];
$email    = $_POST["email"];
$phone    = $_POST["phone"];
$password = $_POST["password"];
$role     = "pelanggan";

if ($nama == "" || $email == "" || $phone == "" || $password == "") {
    header("Location: signup.php?pesan=kosong");
} else if (!is_numeric($phone)) {
    header("Location: signup.php?pesan=hp_salah");
} else if (strlen($password) < 8) {
    header("Location: signup.php?pesan=pw_pendek");
} else {
    $stmt = $koneksi->prepare("SELECT id_user FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id_user);
    $stmt->fetch();
    $stmt->close();

    if ($id_user) {
        header("Location: signup.php?pesan=email_ada");
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare("INSERT INTO users (nama, email, no_hp, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $email, $phone, $password_hash, $role);

        if ($stmt->execute()) {
            header("Location: login.php?pesan=sukses");
        } else {
            header("Location: signup.php?pesan=gagal");
        }
        $stmt->close();
    }
}
?>