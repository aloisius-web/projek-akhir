<?php
include "proteksiSession.php";
wajib_login();
include "koneksi.php";
include "fungsiUpload.php";

if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: riwayatpesan.php");
    exit();
}

$id_user    = $_SESSION["id_user"];
$id_pesanan = (int) $_POST["id_pesanan"];

// Pastikan pesanan milik user ini dan masih aktif
$stmt = $koneksi->prepare("SELECT bukti_bayar FROM pesanan
    WHERE id_pesanan = ? AND id_user = ?
    AND status_pesanan IN('menunggu','diterima')
    AND status_bayar IN('belum_bayar','menunggu_verifikasi')");
$stmt->bind_param("ii", $id_pesanan, $id_user);
$stmt->execute();
$stmt->bind_result($bukti_lama);
$ada = $stmt->fetch();
$stmt->close();

if(!$ada){
    header("Location: riwayatpesan.php?pesan=bukti_gagal");
    exit();
}

if(!isset($_FILES["bukti"]) || $_FILES["bukti"]["error"] === UPLOAD_ERR_NO_FILE){
    header("Location: riwayatpesan.php?pesan=bukti_kosong");
    exit();
}

$error = "";
$nama_file = upload_foto($_FILES["bukti"], $error, "bukti", "bukti_");

if($nama_file === null){
    header("Location: riwayatpesan.php?pesan=bukti_error&info=" . urlencode($error));
    exit();
}

$stmt = $koneksi->prepare("UPDATE pesanan
    SET bukti_bayar = ?, metode_bayar = 'transfer', status_bayar = 'menunggu_verifikasi'
    WHERE id_pesanan = ? AND id_user = ?");
$stmt->bind_param("sii", $nama_file, $id_pesanan, $id_user);
$stmt->execute();
$stmt->close();

// Hapus bukti lama jika diganti
if($bukti_lama != "" && file_exists(__DIR__ . "/uploads/bukti/" . $bukti_lama)){
    @unlink(__DIR__ . "/uploads/bukti/" . $bukti_lama);
}

header("Location: riwayatpesan.php?pesan=bukti_sukses");
exit();
?>