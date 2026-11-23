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
    case "lunas":
    case "tolak_bukti":
        $status_baru = "";
        $status_lama = "";
        break;
    default:
        header("Location: pesananadmin.php");
        exit();
}

if($aksi == "lunas" || $aksi == "tolak_bukti"){
    // Verifikasi pembayaran
    if($aksi == "lunas"){
        $stmt = $koneksi->prepare("UPDATE pesanan SET status_bayar = 'lunas'
            WHERE id_pesanan = ? AND status_bayar = 'menunggu_verifikasi'");
    }else{
        $stmt = $koneksi->prepare("UPDATE pesanan SET status_bayar = 'belum_bayar', bukti_bayar = NULL
            WHERE id_pesanan = ? AND status_bayar = 'menunggu_verifikasi'");
    }
    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
    $stmt->close();
}else{
    $stmt = $koneksi->prepare("UPDATE pesanan SET status_pesanan = ? WHERE id_pesanan = ? AND status_pesanan = ?");
    $stmt->bind_param("sis", $status_baru, $id_pesanan, $status_lama);
    $stmt->execute();
    $stmt->close();
}

if($kembali == "admin"){
    header("Location: admin.php");
}else{
    header("Location: pesananadmin.php");
}
exit();
?>