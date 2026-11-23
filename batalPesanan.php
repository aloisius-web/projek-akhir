<?php
include "proteksiSession.php";
wajib_login();
include "koneksi.php";

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: riwayatpesan.php");
    exit();
}

$id_user    = $_SESSION["id_user"];
$id_pesanan = (int) $_POST["id_pesanan"];

// Hanya pesanan milik sendiri, belum lunas, dan masih menunggu/diterima yang bisa dibatalkan
$stmt = $koneksi->prepare("UPDATE pesanan SET status_pesanan = 'dibatalkan'
    WHERE id_pesanan = ? AND id_user = ?
    AND status_pesanan IN('menunggu','diterima')
    AND status_bayar <> 'lunas'");
$stmt->bind_param("ii", $id_pesanan, $id_user);
$stmt->execute();
$berhasil = $stmt->affected_rows > 0;
$stmt->close();

if($berhasil){
    header("Location: riwayatpesan.php?pesan=batal");
}else{
    header("Location: riwayatpesan.php?pesan=batal_gagal");
}
exit();
?>