<?php
session_start();
if(!isset($_SESSION["status_login"]) || $_SESSION["status_login"] !== true){
    header("Location: login.php");
    exit();
}
include "koneksi.php";

$id_mobil = 0;
if(isset($_GET["id"])){
    $id_mobil = $_GET["id"];
}
$tgl_mulai = "";
if(isset($_GET["tgl_mulai"])){
    $tgl_mulai = $_GET["tgl_mulai"];
}
$tgl_selesai = "";
if(isset($_GET["tgl_selesai"])){
    $tgl_selesai = $_GET["tgl_selesai"];
}

$stmt = $koneksi->prepare("SELECT mobil.*, kategori.nama_kategori
    FROM mobil
    JOIN kategori ON mobil.id_kategori = kategori.id_kategori
    WHERE mobil.id_mobil = ? AND mobil.aktif = 1");
$stmt->bind_param("i", $id_mobil);
$stmt->execute();
$hasil = $stmt->get_result();
$m = $hasil->fetch_assoc();
$stmt->close();

if(!$m){
    header("Location: index.php");
    exit();
}

$foto = "mobil.png";
if($m["foto"] != ""){
    $foto = "uploads/" . $m["foto"];
}

$hari_ini = date("Y-m-d");
$terpesan = array();
$stmt = $koneksi->prepare("SELECT tgl_mulai, tgl_selesai FROM pesanan
    WHERE id_mobil = ?
    AND status_pesanan IN('menunggu','diterima')
    AND tgl_selesai >= ?
    ORDER BY tgl_mulai ASC");
$stmt->bind_param("is", $m["id_mobil"], $hari_ini);
$stmt->execute();
$hasil = $stmt->get_result();
while($baris = mysqli_fetch_assoc($hasil)){
    $terpesan[] = $baris;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $m["nama_mobil"]; ?> | Rentalku</title>
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
    <div class="remah">
        <a href="index.php">Katalog</a> / <?php echo $m["nama_mobil"]; ?>
    </div>

    <div class="pesan-grid">
        <div class="pesan-kiri">
            <div class="pesan-foto">
                <img src="<?php echo $foto; ?>" alt="<?php echo htmlspecialchars($m["nama_mobil"]); ?>">
            </div>

            <h1 class="pesan-judul"><?php echo $m["nama_mobil"]; ?></h1>

            <dl class="spek-baris">
                <div><dt>Kategori</dt><dd><?php echo $m["nama_kategori"]; ?></dd></div>
                <div><dt>Transmisi</dt><dd><?php echo ucfirst($m["transmisi"]); ?></dd></div>
                <div><dt>Kapasitas</dt><dd><?php echo $m["kursi"]; ?> kursi</dd></div>
            </dl>

            <h3>Tentang mobil ini</h3>
            <p class="pesan-deskripsi"><?php echo $m["deskripsi"]; ?></p>
        </div>

        <aside class="pesan-panel">
            <div class="panel-harga">
                Rp <?php echo number_format($m["harga_harian"], 0, ",", "."); ?> <small>/ hari</small>
            </div>

            <?php
            if(isset($_GET["pesan"])){
                if($_GET["pesan"] == "kosong"){
                    echo "<p class='error'>Isi tanggal sewa dan tanggal kembali.</p>";
                }else if($_GET["pesan"] == "tanggal_salah"){
                    echo "<p class='error'>Tanggal kembali tidak boleh sebelum tanggal sewa.</p>";
                }else if($_GET["pesan"] == "lewat"){
                    echo "<p class='error'>Tanggal sewa tidak boleh sebelum hari ini.</p>";
                }else if($_GET["pesan"] == "bentrok"){
                    echo "<p class='error'>Mobil sudah dipesan pada tanggal tersebut. Pilih tanggal lain.</p>";
                }else if($_GET["pesan"] == "gagal"){
                    echo "<p class='error'>Pesanan gagal disimpan, coba lagi.</p>";
                }
            }
            ?>

            <form method="POST" action="prosespesan.php">
                <input type="hidden" name="id_mobil" value="<?php echo $m["id_mobil"]; ?>">

                <div class="tgl-grup">
                    <label>Tanggal sewa
                        <input type="date" name="tgl_mulai" id="tgl_mulai" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($tgl_mulai); ?>">
                    </label>
                    <label>Tanggal kembali
                        <input type="date" name="tgl_selesai" id="tgl_selesai" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($tgl_selesai); ?>">
                    </label>
                </div>

                <p class="status-tgl" id="status-tgl">Pilih tanggal sewa dan tanggal kembali.</p>

                <dl class="rincian">
                    <div><dt>Harga per hari</dt><dd>Rp <?php echo number_format($m["harga_harian"], 0, ",", "."); ?></dd></div>
                    <div><dt>Durasi sewa</dt><dd id="r-durasi">-</dd></div>
                    <div class="jumlah"><dt>Total</dt><dd id="total">Rp 0</dd></div>
                </dl>

                <button class="btn btn-penuh" type="submit" id="tombol-pesan" disabled>Pesan sekarang</button>
                <p class="catatan">Pesananmu akan menunggu konfirmasi admin.</p>
            </form>

            <?php if(count($terpesan) > 0){ ?>
            <div class="terpesan">
                <b>Sudah dipesan pada</b>
                <ul>
                    <?php for($i = 0; $i < count($terpesan); $i++){ ?>
                    <li><?php echo date("d/m/Y", strtotime($terpesan[$i]["tgl_mulai"])); ?> s/d <?php echo date("d/m/Y", strtotime($terpesan[$i]["tgl_selesai"])); ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>
        </aside>
    </div>
</main>
<script>
const perHari = <?php echo (int) $m["harga_harian"]; ?>;
const terpesan = [
<?php for($i = 0; $i < count($terpesan); $i++){ ?>
    ["<?php echo $terpesan[$i]["tgl_mulai"]; ?>", "<?php echo $terpesan[$i]["tgl_selesai"]; ?>"],
<?php } ?>
];

const tglMulai = document.querySelector("#tgl_mulai");
const tglSelesai = document.querySelector("#tgl_selesai");
const statusTgl = document.querySelector("#status-tgl");
const rDurasi = document.querySelector("#r-durasi");
const total = document.querySelector("#total");
const tombolPesan = document.querySelector("#tombol-pesan");

function tampilkanStatus(teks, jenis){
    statusTgl.innerHTML = teks;
    statusTgl.className = "status-tgl " + jenis;
}

function bentrok(mulai, selesai){
    for(let i = 0; i < terpesan.length; i++){
        if(mulai <= terpesan[i][1] && selesai >= terpesan[i][0]){
            return true;
        }
    }
    return false;
}

function hitungTotal(){
    tombolPesan.disabled = true;
    rDurasi.innerHTML = "-";
    total.innerHTML = "Rp 0";

    if(tglMulai.value === "" || tglSelesai.value === ""){
        tampilkanStatus("Pilih tanggal sewa dan tanggal kembali.", "");
        return;
    }

    const mulai = new Date(tglMulai.value);
    const selesai = new Date(tglSelesai.value);
    const hari = Math.round((selesai - mulai) / (1000 * 60 * 60 * 24)) + 1;

    if(hari < 1){
        tampilkanStatus("Tanggal kembali tidak boleh sebelum tanggal sewa.", "salah");
        return;
    }

    rDurasi.innerHTML = hari + " hari";
    total.innerHTML = "Rp " + (hari * perHari).toLocaleString("id-ID");

    if(bentrok(tglMulai.value, tglSelesai.value)){
        tampilkanStatus("Mobil sudah dipesan pada tanggal ini. Pilih tanggal lain.", "salah");
        return;
    }

    tampilkanStatus("Mobil tersedia pada tanggal ini.", "ok");
    tombolPesan.disabled = false;
}

function aturTanggalKembali(){
    if(tglMulai.value !== ""){
        tglSelesai.min = tglMulai.value;
    }
}

tglMulai.addEventListener("input", function (){
    aturTanggalKembali();
    hitungTotal();
});
tglMulai.addEventListener("change", hitungTotal);
tglSelesai.addEventListener("input", hitungTotal);
tglSelesai.addEventListener("change", hitungTotal);

aturTanggalKembali();
hitungTotal();
</script>
</body>
</html>