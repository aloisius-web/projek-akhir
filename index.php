<?php
session_start();
if(!isset($_SESSION["status_login"]) || $_SESSION["status_login"] !== true){
    header("Location: login.php");
    exit();
}
include "koneksi.php";

$cari = "";
if(isset($_GET["cari"])){
    $cari = $_GET["cari"];
}

$kategori = "";
if(isset($_GET["kategori"])){
    $kategori = $_GET["kategori"];
}

$transmisi = "";
if(isset($_GET["transmisi"])){
    $transmisi = $_GET["transmisi"];
}

$harga_maks = "";
if(isset($_GET["harga_maks"])){
    $harga_maks = $_GET["harga_maks"];
}

$tgl_mulai = "";
if(isset($_GET["tgl_mulai"])){
    $tgl_mulai = $_GET["tgl_mulai"];
}

$tgl_selesai = "";
if(isset($_GET["tgl_selesai"])){
    $tgl_selesai = $_GET["tgl_selesai"];
}

$cari_nama = "%" . $cari . "%";
$cari_kategori  = "%" . $kategori . "%";
$cari_transmisi = "%" . $transmisi . "%";
$batas_harga = 100000000;
if($harga_maks != ""){
    $batas_harga = $harga_maks;
}

$cek_tanggal = false;
$pesan_tanggal = "";
$tautan_tanggal = "";
if($tgl_mulai != "" || $tgl_selesai != ""){
    if($tgl_mulai == "" || $tgl_selesai == ""){
        $pesan_tanggal = "Isi tanggal sewa dan tanggal kembali sekaligus.";
    }else if($tgl_selesai < $tgl_mulai){
        $pesan_tanggal = "Tanggal kembali tidak boleh sebelum tanggal sewa.";
    }else if($tgl_mulai < date("Y-m-d")){
        $pesan_tanggal = "Tanggal sewa tidak boleh sebelum hari ini.";
    }else{
        $cek_tanggal = true;
        $tautan_tanggal = "&tgl_mulai=" . $tgl_mulai . "&tgl_selesai=" . $tgl_selesai;
    }
}

$stmt = $koneksi->prepare("SELECT mobil.*, kategori.nama_kategori
    FROM mobil
    JOIN kategori ON mobil.id_kategori = kategori.id_kategori
    WHERE mobil.aktif = 1
    AND mobil.nama_mobil LIKE ?
    AND kategori.nama_kategori LIKE ?
    AND mobil.transmisi LIKE ?
    AND mobil.harga_harian <= ?
    ORDER BY mobil.harga_harian ASC");
$stmt->bind_param("sssi", $cari_nama, $cari_kategori, $cari_transmisi, $batas_harga);
$stmt->execute();
$hasil = $stmt->get_result();

$daftar_kategori = $koneksi->query("SELECT * FROM kategori ORDER BY nama_kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Mobil | Rentalku</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<header class="bar">
    <a class="brand" href="index.php">Rentalku</a>
    <nav class="nav">
        <span class="halo"><?php echo $_SESSION["nama"]; ?></span>
        <a href="riwayatpesan.php">Pesanan Saya</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="wrap">
    <div class="kepala"><h2>Katalog mobil</h2></div>

    <form class="filter" method="GET">
        <label>Nama mobil
            <input type="text" name="cari" value="<?php echo htmlspecialchars($cari); ?>" placeholder="mis. Avanza">
        </label>
        <label>Kategori
            <select name="kategori">
                <option value="">Semua</option>
                <?php while($k = mysqli_fetch_assoc($daftar_kategori)){ ?>
                    <option value="<?php echo $k["nama_kategori"]; ?>" <?php if($kategori == $k["nama_kategori"]){ echo "selected"; } ?>><?php echo $k["nama_kategori"]; ?></option>
                <?php } ?>
            </select>
        </label>
        <label>Transmisi
            <select name="transmisi">
                <option value="">Semua</option>
                <option value="manual" <?php if($transmisi == "manual"){ echo "selected"; } ?>>Manual</option>
                <option value="matic" <?php if($transmisi == "matic"){ echo "selected"; } ?>>Matic</option>
            </select>
        </label>
        <label>Harga maks/hari(Rp)
            <input type="number" name="harga_maks" min="0" value="<?php echo htmlspecialchars($harga_maks); ?>">
        </label>
        <label>Tanggal sewa
            <input type="date" name="tgl_mulai" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($tgl_mulai); ?>">
        </label>
        <label>Tanggal kembali
            <input type="date" name="tgl_selesai" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($tgl_selesai); ?>">
        </label>
        <button type="submit">Cari</button>
        <a href="index.php">Atur ulang</a>
    </form>

    <?php if($pesan_tanggal != ""){ ?>
        <p class="error"><?php echo $pesan_tanggal; ?></p>
    <?php } ?>
    <?php if($cek_tanggal){ ?>
        <p class="info">Menampilkan mobil yang tersedia pada <?php echo htmlspecialchars($tgl_mulai); ?> s/d <?php echo htmlspecialchars($tgl_selesai); ?>.</p>
    <?php } ?>

    <div class="grid">
    <?php
    $jumlah_tampil = 0;
    while($m = mysqli_fetch_assoc($hasil)){
        $tersedia = true;

        if($cek_tanggal){
            $cek = $koneksi->prepare("SELECT COUNT(*) FROM pesanan
                WHERE id_mobil = ?
                AND status_pesanan IN('menunggu','diterima')
                AND tgl_mulai <= ?
                AND tgl_selesai >= ?");
            $cek->bind_param("iss", $m["id_mobil"], $tgl_selesai, $tgl_mulai);
            $cek->execute();
            $cek->bind_result($jumlah_bentrok);
            $cek->fetch();
            $cek->close();

            if($jumlah_bentrok > 0){
                $tersedia = false;
            }
        }

        if($tersedia){
            $jumlah_tampil++;
            $foto = "mobil.png";
            if($m["foto"] != ""){
                $foto = "uploads/" . $m["foto"];
            }
            $link = "detailmobil.php?id=" . $m["id_mobil"] . $tautan_tanggal;
    ?>
        <div class="card">
            <a href="<?php echo htmlspecialchars($link); ?>"><img src="<?php echo $foto; ?>" alt=""></a>
            <h3><?php echo $m["nama_mobil"]; ?></h3>
            <div class="spek"><?php echo $m["nama_kategori"]; ?>, <?php echo ucfirst($m["transmisi"]); ?>, <?php echo $m["kursi"]; ?> kursi</div>
            <div class="harga">Rp <?php echo number_format($m["harga_harian"], 0, ",", "."); ?> / hari</div>
            <a class="btn" href="<?php echo htmlspecialchars($link); ?>">Pesan</a>
        </div>
    <?php
        }
    }
    ?>
    </div>

    <?php if($jumlah_tampil == 0){ ?>
        <p class="info">Tidak ada mobil yang cocok. Coba ubah atau kurangi filter.</p>
    <?php } ?>
</main>
</body>
</html>