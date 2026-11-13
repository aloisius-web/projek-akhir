<?php
$koneksi = new mysqli("localhost", "root", "", "rental");

if($koneksi->connect_error){
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>