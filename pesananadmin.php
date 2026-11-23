<?php
include "proteksiSession.php";
wajib_admin();
include "koneksi.php";

$status = "menunggu";
if(isset($_GET["status"])){
    $status = $_GET["status"];
}

$jumlah = array("menunggu" => 0, "diterima" => 0, "selesai" => 0, "ditolak" => 0, "dibatalkan" => 0);
$query = "SELECT status_pesanan, COUNT(*) AS total FROM pesanan GROUP BY status_pesanan";
$hasil = $koneksi->query($query);
while($baris = mysqli_fetch_assoc($hasil)){
    $jumlah[$baris["status_pesanan"]] = $baris["total"];
}

$stmt = $koneksi->prepare("SELECT pesanan.*, users.nama, users.no_hp, mobil.nama_mobil
    FROM pesanan
    JOIN users ON pesanan.id_user = users.id_user
    JOIN mobil ON pesanan.id_mobil = mobil.id_mobil
    WHERE pesanan.status_pesanan = ?
    ORDER BY pesanan.tgl_mulai ASC");
$stmt->bind_param("s", $status);
$stmt->execute();
$hasil = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan | Rentalku</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<header class="bar">
    <a class="brand" href="admin.php">Rentalku</a>
    <nav class="nav">
        <a href="admin.php">Dashboard</a>
        <a href="mobilAdmin.php">Kelola Mobil</a>
        <a href="pesananadmin.php" class="aktif">Pesanan</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="wrap">
    <div class="kepala"><h2>Pesanan masuk</h2></div>

    <div class="tab">
        <a href="pesananadmin.php?status=menunggu" class="<?php if($status == "menunggu"){ echo "aktif"; } ?>">Menunggu<span><?php echo $jumlah["menunggu"]; ?></span></a>
        <a href="pesananadmin.php?status=diterima" class="<?php if($status == "diterima"){ echo "aktif"; } ?>">Diterima<span><?php echo $jumlah["diterima"]; ?></span></a>
        <a href="pesananadmin.php?status=selesai" class="<?php if($status == "selesai"){ echo "aktif"; } ?>">Selesai<span><?php echo $jumlah["selesai"]; ?></span></a>
        <a href="pesananadmin.php?status=ditolak" class="<?php if($status == "ditolak"){ echo "aktif"; } ?>">Ditolak<span><?php echo $jumlah["ditolak"]; ?></span></a>
        <a href="pesananadmin.php?status=dibatalkan" class="<?php if($status == "dibatalkan"){ echo "aktif"; } ?>">Dibatalkan<span><?php echo $jumlah["dibatalkan"]; ?></span></a>
    </div>

    <div class="tabel-box">
    <table>
        <tr>
            <th>No</th><th>Pelanggan</th><th>Mobil</th><th>Tanggal</th><th>Durasi</th><th>Total</th><th>Pembayaran</th><th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        while($p = mysqli_fetch_assoc($hasil)){
            $hari = round((strtotime($p["tgl_selesai"]) - strtotime($p["tgl_mulai"])) / 86400) + 1;
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td class="nama pelanggan">
                <?php echo htmlspecialchars($p["nama"]); ?>
                <small><?php echo htmlspecialchars($p["no_hp"]); ?></small>
            </td>
            <td><?php echo $p["nama_mobil"]; ?></td>
            <td><?php echo $p["tgl_mulai"]; ?> s/d <?php echo $p["tgl_selesai"]; ?></td>
            <td><?php echo $hari; ?> hari</td>
            <td>Rp <?php echo number_format($p["total_harga"], 0, ",", "."); ?></td>
            <td>
                <?php
                if($p["status_bayar"] == "lunas"){
                    echo "<span class='pill on'>Lunas</span>";
                }else if($p["status_bayar"] == "menunggu_verifikasi"){
                    echo "<span class='pill tunggu'>Menunggu verifikasi</span>";
                }else{
                    echo "<span class='pill tolak'>Belum bayar</span>";
                }
                if($p["bukti_bayar"] != ""){
                    echo "<small class='bukti'><a href='uploads/bukti/" . htmlspecialchars($p["bukti_bayar"]) . "' target='_blank'>Lihat bukti</a></small>";
                }
                if($p["status_bayar"] == "menunggu_verifikasi" && ($status == "menunggu" || $status == "diterima")){
                ?>
                    <form method="POST" action="status.php" class="form-verif">
                        <input type="hidden" name="id_pesanan" value="<?php echo $p["id_pesanan"]; ?>">
                        <input type="hidden" name="kembali" value="pesanan">
                        <button type="submit" class="mini ok" name="aksi" value="lunas">Lunas</button>
                        <button type="submit" class="mini no" name="aksi" value="tolak_bukti" onclick="return confirm('Tolak bukti pembayaran ini?')">Tolak bukti</button>
                    </form>
                <?php } ?>
            </td>
            <td class="aksi">
                <?php if($status == "menunggu"){ ?>
                    <form method="POST" action="status.php">
                        <input type="hidden" name="id_pesanan" value="<?php echo $p["id_pesanan"]; ?>">
                        <input type="hidden" name="kembali" value="pesanan">
                        <button type="submit" class="mini ok" name="aksi" value="terima">Terima</button>
                        <button type="submit" class="mini no" name="aksi" value="tolak" onclick="return confirm('Tolak pesanan ini?')">Tolak</button>
                    </form>
                <?php }else if($status == "diterima"){ ?>
                    <form method="POST" action="status.php">
                        <input type="hidden" name="id_pesanan" value="<?php echo $p["id_pesanan"]; ?>">
                        <input type="hidden" name="kembali" value="pesanan">
                        <button type="submit" class="mini biru" name="aksi" value="selesai">Tandai selesai</button>
                    </form>
                <?php }else{ ?>
                    -
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
        <?php if($no == 1){ ?>
        <tr><td class="kosong" colspan="8">Tidak ada pesanan berstatus <?php echo htmlspecialchars($status); ?>.</td></tr>
        <?php } ?>
    </table>
    </div>
</main>
</body>
</html>