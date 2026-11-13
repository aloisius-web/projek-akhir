<?php
session_start();
include "koneksi.php";

$email         = $_POST["email"];
$password_asli = $_POST["password"];
$role          = $_POST["role"];

$stmt = $koneksi->prepare("SELECT id_user, nama, password, role FROM users WHERE email = ? AND role = ?");
$stmt->bind_param("ss", $email, $role);
$stmt->execute();
$stmt->bind_result($id_user, $nama, $password_hash, $role_db);
$stmt->fetch();
$stmt->close();

if ($password_hash && password_verify($password_asli, $password_hash)) {
    $_SESSION["status_login"] = true;
    $_SESSION["id_user"]      = $id_user;
    $_SESSION["nama"]         = $nama;
    $_SESSION["role"]         = $role_db;

    if ($role_db == "admin") {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit();
} else {
    header("Location: login.php?pesan=salah");
    exit();
}
?>