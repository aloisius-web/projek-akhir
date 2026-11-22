<?php
include "proteksiSession.php";
wajib_admin();
include "koneksi.php";

$id_pesanan = $_POST["id_pesanan"];
$aksi       = $_POST["aksi"];
$kembali    = $_POST["kembali"];

switch($aksi){
    case "terima":
        $status_baru = "diterima";
        $status_lama = "menunggu";
        break;
    case "tolak":
        $status_baru = "ditolak";
        $status_lama = "menunggu";
        break;
    case "selesai":
        $status_baru = "selesai";
        $status_lama = "diterima";
        break;
    default:
        header("Location: pesananadmin.php");
        exit();
}

$stmt = $koneksi->prepare("UPDATE pesanan SET status_pesanan = ? WHERE id_pesanan = ? AND status_pesanan = ?");
$stmt->bind_param("sis", $status_baru, $id_pesanan, $status_lama);
$stmt->execute();
$stmt->close();

if($kembali == "admin"){
    header("Location: admin.php");
}else{
    header("Location: pesananadmin.php");
}
exit();
?>