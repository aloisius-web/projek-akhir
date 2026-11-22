<?php
session_start();
if(!isset($_SESSION["status_login"]) || $_SESSION["status_login"] !== true){
    header("Location: login.php");
    exit();
}
include "koneksi.php";

$id_user      = $_SESSION["id_user"];
$id_mobil     = $_POST["id_mobil"];
$tgl_mulai    = $_POST["tgl_mulai"];
$tgl_selesai  = $_POST["tgl_selesai"];

$stmt = $koneksi->prepare("SELECT harga_harian FROM mobil WHERE id_mobil = ? AND aktif = 1");
$stmt->bind_param("i", $id_mobil);
$stmt->execute();
$stmt->bind_result($harga_harian);
$ada_mobil = $stmt->fetch();
$stmt->close();

$kembali = "detailmobil.php?id=" . $id_mobil . "&tgl_mulai=" . $tgl_mulai . "&tgl_selesai=" . $tgl_selesai;

if(!$ada_mobil){
    header("Location: index.php");
}else if($tgl_mulai == "" || $tgl_selesai == ""){
    header("Location: " . $kembali . "&pesan=kosong");
}else if($tgl_selesai < $tgl_mulai){
    header("Location: " . $kembali . "&pesan=tanggal_salah");
}else if($tgl_mulai < date("Y-m-d")){
    header("Location: " . $kembali . "&pesan=lewat");
}else{
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM pesanan
        WHERE id_mobil = ?
        AND status_pesanan IN('menunggu','diterima')
        AND tgl_mulai <= ?
        AND tgl_selesai >= ?");
    $cek->bind_param("iss", $id_mobil, $tgl_selesai, $tgl_mulai);
    $cek->execute();
    $cek->bind_result($jumlah_bentrok);
    $cek->fetch();
    $cek->close();

    if($jumlah_bentrok > 0){
        header("Location: " . $kembali . "&pesan=bentrok");
    }else{
        $hari = round((strtotime($tgl_selesai) - strtotime($tgl_mulai)) / 86400) + 1;
        $total_harga = $hari * $harga_harian;

        $stmt = $koneksi->prepare("INSERT INTO pesanan(id_user, id_mobil, tgl_mulai, tgl_selesai, total_harga) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("iissi", $id_user, $id_mobil, $tgl_mulai, $tgl_selesai, $total_harga);

        if($stmt->execute()){
            header("Location: riwayatpesan.php?pesan=sukses");
        }else{
            header("Location: " . $kembali . "&pesan=gagal");
        }
        $stmt->close();
    }
}
?>