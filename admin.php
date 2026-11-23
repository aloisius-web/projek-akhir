<?php
include "proteksiSession.php";
wajib_admin();
include "koneksi.php";

$pesanan = array("menunggu" => 0, "diterima" => 0);
$query = "SELECT status_pesanan, COUNT(*) AS total FROM pesanan GROUP BY status_pesanan";
$hasil = $koneksi->query($query);
while($baris = mysqli_fetch_assoc($hasil)){
    $pesanan[$baris["status_pesanan"]] = $baris["total"];
}

$mobil_aktif    = 0;
$mobil_nonaktif = 0;
$query = "SELECT aktif, COUNT(*) AS total FROM mobil GROUP BY aktif";
$hasil = $koneksi->query($query);
while($baris = mysqli_fetch_assoc($hasil)){
    if($baris["aktif"] == 1){
        $mobil_aktif = $baris["total"];
    }else{
        $mobil_nonaktif = $baris["total"];
    }
}

$query = "SELECT pesanan.id_pesanan, pesanan.tgl_mulai, pesanan.tgl_selesai, pesanan.total_harga, users.nama, mobil.nama_mobil
    FROM pesanan
    JOIN users ON pesanan.id_user = users.id_user
    JOIN mobil ON pesanan.id_mobil = mobil.id_mobil
    WHERE pesanan.status_pesanan = 'menunggu'
    ORDER BY pesanan.tgl_mulai ASC
    LIMIT 5";
$antrean = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Rentalku</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<header class="bar">
    <a class="brand" href="admin.php">Rentalku</a>
    <nav class="nav">
        <a href="admin.php" class="aktif">Dashboard</a>
        <a href="mobilAdmin.php">Kelola Mobil</a>
        <a href="pesananadmin.php">Pesanan</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="wrap">
    <h1>Halo, <?php echo htmlspecialchars($_SESSION["nama"]); ?></h1>
    <p class="lead">Ringkasan Rentalku hari ini.</p>

    <div class="stat-row">
        <a class="stat sorot" href="pesananadmin.php?status=menunggu">
            <small>Menunggu konfirmasi</small><b><?php echo $pesanan["menunggu"]; ?></b>
        </a>
        <a class="stat" href="pesananadmin.php?status=diterima">
            <small>Pesanan diterima</small><b><?php echo $pesanan["diterima"]; ?></b>
        </a>
        <a class="stat" href="mobilAdmin.php">
            <small>Mobil aktif</small><b><?php echo $mobil_aktif; ?></b>
        </a>
        <a class="stat" href="mobilAdmin.php">
            <small>Mobil nonaktif</small><b><?php echo $mobil_nonaktif; ?></b>
        </a>
    </div>

    <div class="dua">
            <section class="panel">
        <div class="panel-kepala">
            <h3>Pesanan menunggu konfirmasi</h3>
            <a href="pesananAdmin.php">Lihat semua</a>
        </div>
        <ul class="antrean">
            <?php
            $jumlah_antrean = 0;
            while($p = mysqli_fetch_assoc($antrean)){
                $jumlah_antrean++;
            ?>
            <li>
                <div>
                    <b><?php echo htmlspecialchars($p["nama"]); ?></b>
                    <span>
                        <?php echo $p["nama_mobil"]; ?> &middot;
                        <?php echo $p["tgl_mulai"]; ?> s/d <?php echo $p["tgl_selesai"]; ?> &middot;
                        Rp <?php echo number_format($p["total_harga"], 0, ",", "."); ?>
                    </span>
                </div>
                <form class="antrean-aksi" method="POST" action="status.php">
                    <input type="hidden" name="id_pesanan" value="<?php echo $p["id_pesanan"]; ?>">
                    <input type="hidden" name="kembali" value="admin">
                    <button type="submit" class="mini ok" name="aksi" value="terima">Terima</button>
                    <button type="submit" class="mini no" name="aksi" value="tolak" onclick="return confirm('Tolak pesanan ini?')">Tolak</button>
                </form>
            </li>
            <?php } ?>
        </ul>
        <?php if($jumlah_antrean == 0){ ?>
            <p class="kosong">Tidak ada pesanan yang menunggu. Semua beres!</p>
        <?php } ?>
    </section>
</main>
</body>
</html>