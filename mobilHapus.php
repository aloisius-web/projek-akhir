<?php
include 'koneksi.php';
include 'proteksiSession.php'; 
wajib_admin();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $koneksi->prepare("SELECT foto FROM mobil WHERE id_mobil = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$mobil = $stmt->get_result()->fetch_assoc();

if($mobil){
   try{
    $del = $koneksi->prepare("DELETE FROM mobil WHERE id_mobil = ?");
    $del->bind_param("i", $id);
    $del->execute();

    if($mobil['foto'] && file_exists("uploads/" . $mobil['foto'])){
        unlink("uploads/" . $mobil['foto']);
    }
 } catch(mysqli_sql_exception $e){
    $off = $koneksi->prepare("UPDATE mobil SET aktif = 0 WHERE id_mobil = ?");
    $off->bind_param("i", $id);
    $off->execute();
 }
}
header("Location: mobilAdmin.php");
exit();