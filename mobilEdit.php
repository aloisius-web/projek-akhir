<?php
include 'proteksiSession.php';
wajib_admin();
include 'koneksi.php';
include 'fungsiUpload.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $koneksi->prepare("SELECT * FROM mobil WHERE id_mobil = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$mobil = $stmt->get_result()->fetch_assoc();
if(!$mobil){
    die("Kelola Mobil tidak ditemukan");
}

$error = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_kategori = (int) $_POST['id_kategori'];
    $nama        = trim($_POST['nama_mobil']);
    $transmisi   = $_POST['transmisi'];
    $kursi       = (int) $_POST['kursi'];
    $harga       = (int) $_POST['harga_harian'];
    $deskripsi   = trim($_POST['deskripsi']);
    $aktif       = isset($_POST['aktif']) ? 1 : 0;
    
    $fotoBaru = upload_foto($_FILES['foto'], $error);
    $foto = $mobil['foto']; 
 
    if ($error === "") {
        if ($fotoBaru !== null) {
            if ($mobil['foto'] && file_exists("uploads/" . $mobil['foto'])) {
                unlink("uploads/" . $mobil['foto']);
            }
            $foto = $fotoBaru;
        }
        $upd = $koneksi->prepare("UPDATE mobil SET id_kategori=?, nama_mobil=?, transmisi=?, kursi=?, harga_harian=?, deskripsi=?, aktif=?, foto=? WHERE id_mobil=?");
        $upd->bind_param("issiisisi", $id_kategori, $nama, $transmisi, $kursi, $harga, $deskripsi, $aktif, $foto, $id);
        $upd->execute();
        header("Location: mobilAdmin.php");
        exit();
    }
}
$kategori = $koneksi->query("SELECT * FROM kategori ORDER BY nama_kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Mobil | Rentalku</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<header class="bar">
    <a class="brand" href="admin.php">Rentalku</a>
    <nav class="nav">
        <a href="admin.php">Dashboard</a>
        <a href="mobilAdmin.php" class="aktif">Kelola Mobil</a>
        <a href="pesananAdmin.php">Pesanan</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="wrap sempit">
    <h2>Ubah mobil</h2>
    <?php if ($error) : ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form class="form" method="POST" enctype="multipart/form-data">
        <label>Kategori
            <select name="id_kategori" required>
                <?php while ($k = $kategori->fetch_assoc()) : ?>
                    <option value="<?= $k['id_kategori'] ?>" <?= $k['id_kategori'] == $mobil['id_kategori'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_kategori']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label>
        <label>Nama mobil <input type="text" name="nama_mobil" value="<?= htmlspecialchars($mobil['nama_mobil']) ?>" required></label>
        <label>Transmisi
            <select name="transmisi">
                <option value="manual" <?= $mobil['transmisi'] === 'manual' ? 'selected' : '' ?>>Manual</option>
                <option value="matic" <?= $mobil['transmisi'] === 'matic' ? 'selected' : '' ?>>Matic</option>
            </select>
        </label>
        <label>Jumlah kursi <input type="number" name="kursi" min="1" value="<?= (int) $mobil['kursi'] ?>" required></label>
        <label>Harga per hari (Rp) <input type="number" name="harga_harian" min="0" value="<?= (int) $mobil['harga_harian'] ?>" required></label>
        <label>Deskripsi <textarea name="deskripsi" rows="3"><?= htmlspecialchars($mobil['deskripsi'] ?? '') ?></textarea></label>
        <?php if ($mobil['foto']) : ?>
            <div class="foto-lama">Foto saat ini<img src="uploads/<?= htmlspecialchars($mobil['foto']) ?>" alt=""></div>
        <?php endif; ?>
        <label>Ganti foto (kosongkan jika tidak diganti) <input type="file" name="foto" accept=".jpg,.jpeg,.png"></label>
        <label class="cek"><input type="checkbox" name="aktif" <?= $mobil['aktif'] ? 'checked' : '' ?>> Tampilkan di katalog</label>
        <div class="tombol">
            <button class="btn" type="submit">Simpan perubahan</button>
            <a href="mobilAdmin.php">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
