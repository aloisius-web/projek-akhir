<?php
include 'proteksiSession.php';
wajib_admin();
include 'koneksi.php';
include 'fungsiUpload.php';

$error = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_kategori = (int) $_POST['id_kategori'];
    $nama        = trim($_POST['nama_mobil']);
    $transmisi   = $_POST['transmisi'];
    $kursi       = (int) $_POST['kursi'];
    $harga       = (int) $_POST['harga_harian'];
    $deskripsi   = trim($_POST['deskripsi']);

    $foto = upload_foto($_FILES['foto'], $error);

    if($error === ""){
        $stmt = $koneksi->prepare("INSERT INTO mobil (id_kategori, nama_mobil, transmisi, kursi, harga_harian, foto, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issiiss", $id_kategori, $nama, $transmisi, $kursi, $harga, $foto, $deskripsi);
        $stmt->execute();
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
    <title>Tambah Mobil | Rentalku</title>
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
    <h2>Tambah mobil</h2>
    <?php if ($error) : ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form class="form" method="POST" enctype="multipart/form-data">
        <label>Kategori
            <select name="id_kategori" required>
                <?php while ($k = $kategori->fetch_assoc()) : ?>
                    <option value="<?= $k['id_kategori'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                <?php endwhile; ?>
            </select>
        </label>
        <label>Nama mobil <input type="text" name="nama_mobil" required></label>
        <label>Transmisi
            <select name="transmisi"><option value="manual">Manual</option><option value="matic">Matic</option></select>
        </label>
        <label>Jumlah kursi <input type="number" name="kursi" min="1" required></label>
        <label>Harga per hari (Rp) <input type="number" name="harga_harian" min="0" required></label>
        <label>Deskripsi <textarea name="deskripsi" rows="3"></textarea></label>
        <label>Foto (JPG atau PNG, maks. 2 MB) <input type="file" name="foto" accept=".jpg,.jpeg,.png"></label>
        <div class="tombol">
            <button class="btn" type="submit">Simpan</button>
            <a href="mobilAdmin.php">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
