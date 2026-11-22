<?php
session_start();
if(!isset($_SESSION["status_login"]) || $_SESSION["status_login"] !== true){
    header("Location: login.php");
    exit();
}
include "koneksi.php";

$id_user = $_SESSION["id_user"];
$stmt = $koneksi->prepare("SELECT pesanan.*, mobil.nama_mobil
    FROM pesanan
    JOIN mobil ON pesanan.id_mobil = mobil.id_mobil
    WHERE pesanan.id_user = ?
    ORDER BY pesanan.id_pesanan DESC");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$hasil = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya | Rentalku</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<header class="bar">
    <a class="brand" href="index.php">Rentalku</a>
    <nav class="nav">
        <span class="halo"><?php echo $_SESSION["nama"]; ?></span>
        <a href="index.php">Katalog</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="wrap">
    <div class="kepala"><h2>Pesanan saya</h2></div>

    <?php
    if(isset($_GET["pesan"]) && $_GET["pesan"] == "sukses"){
        echo "<p class='sukses'>Pesanan berhasil dibuat. Tunggu konfirmasi dari admin.</p>";
    }
    ?>

    <div class="tabel-box">
    <table>
        <tr>
            <th>No</th><th>Mobil</th><th>Tanggal</th><th>Total</th><th>Status</th>
        </tr>
        <?php
        $no = 1;
        while($p = mysqli_fetch_assoc($hasil)){
            $status_teks  = "Menunggu konfirmasi";
            $status_warna = "tunggu";
            if($p["status_pesanan"] == "diterima"){
                $status_teks  = "Diterima";
                $status_warna = "on";
            }else if($p["status_pesanan"] == "ditolak"){
                $status_teks  = "Ditolak";
                $status_warna = "tolak";
            }else if($p["status_pesanan"] == "selesai"){
                $status_teks  = "Selesai";
                $status_warna = "off";
            }else if($p["status_pesanan"] == "dibatalkan"){
                $status_teks  = "Dibatalkan";
                $status_warna = "off";
            }
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td class="nama"><?php echo $p["nama_mobil"]; ?></td>
            <td><?php echo $p["tgl_mulai"]; ?> s/d <?php echo $p["tgl_selesai"]; ?></td>
            <td>Rp <?php echo number_format($p["total_harga"], 0, ",", "."); ?></td>
            <td><span class="pill <?php echo $status_warna; ?>"><?php echo $status_teks; ?></span></td>
        </tr>
        <?php }?>
        <?php if($no == 1){ ?>
        <tr><td class="kosong" colspan="5">Belum ada pesanan. Pilih mobil di katalog untuk mulai!</td></tr>
        <?php }?>
    </table>
    </div>
</main>
</body>
</html>