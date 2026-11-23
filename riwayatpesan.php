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
    if(isset($_GET["pesan"])){
        $pesan = $_GET["pesan"];
        if($pesan == "sukses"){
            echo "<p class='sukses'>Pesanan berhasil dibuat. Tunggu konfirmasi dari admin.</p>";
        }else if($pesan == "batal"){
            echo "<p class='sukses'>Pesanan berhasil dibatalkan.</p>";
        }else if($pesan == "batal_gagal"){
            echo "<p class='error'>Pesanan tidak bisa dibatalkan (sudah lunas, selesai, atau sudah diproses).</p>";
        }else if($pesan == "bukti_sukses"){
            echo "<p class='sukses'>Bukti pembayaran terkirim. Tunggu verifikasi dari admin.</p>";
        }else if($pesan == "bukti_kosong"){
            echo "<p class='error'>Pilih file bukti pembayaran terlebih dahulu.</p>";
        }else if($pesan == "bukti_error"){
            $info = isset($_GET["info"]) ? $_GET["info"] : "Upload gagal";
            echo "<p class='error'>" . htmlspecialchars($info) . "</p>";
        }else if($pesan == "bukti_gagal"){
            echo "<p class='error'>Bukti pembayaran tidak bisa diunggah untuk pesanan ini.</p>";
        }
    }
    ?>

    <div class="tabel-box">
    <table>
        <tr>
            <th>No</th><th>Mobil</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Pembayaran</th><th>Aksi</th>
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
            <td>
                <?php
                $aktif = in_array($p["status_pesanan"], array("menunggu", "diterima"));
                if($p["status_bayar"] == "lunas"){
                    echo "<span class='pill on'>Lunas</span>";
                }else if($p["status_bayar"] == "menunggu_verifikasi"){
                    echo "<span class='pill tunggu'>Menunggu verifikasi</span>";
                }else if($aktif){
                    echo "<span class='pill tolak'>Belum bayar</span>";
                }else{
                    echo "-";
                }
                if($p["bukti_bayar"] != ""){
                    echo "<small class='bukti'><a href='uploads/bukti/" . htmlspecialchars($p["bukti_bayar"]) . "' target='_blank'>Lihat bukti</a></small>";
                }
                ?>
            </td>
            <td class="aksi">
                <?php if($aktif && $p["status_bayar"] != "lunas"){ ?>
                    <form class="form-bukti" method="POST" action="uploadBukti.php" enctype="multipart/form-data">
                        <input type="hidden" name="id_pesanan" value="<?php echo $p["id_pesanan"]; ?>">
                        <input type="file" name="bukti" accept=".jpg,.jpeg,.png" required>
                        <button type="submit" class="mini biru"><?php echo $p["bukti_bayar"] != "" ? "Ganti bukti" : "Upload bukti"; ?></button>
                    </form>
                    <form method="POST" action="batalPesanan.php">
                        <input type="hidden" name="id_pesanan" value="<?php echo $p["id_pesanan"]; ?>">
                        <button type="submit" class="mini no" onclick="return confirm('Batalkan pesanan ini?')">Batalkan</button>
                    </form>
                <?php }else{ ?>
                    -
                <?php } ?>
            </td>
        </tr>
        <?php }?>
        <?php if($no == 1){ ?>
        <tr><td class="kosong" colspan="7">Belum ada pesanan. Pilih mobil di katalog untuk mulai!</td></tr>
        <?php }?>
    </table>
    </div>
</main>
</body>
</html>