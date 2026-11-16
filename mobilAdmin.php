<?php
include 'proteksiSession.php';
wajib_admin();
include 'koneksi.php';

$q = $koneksi->query("SELECT m.*, k.nama_kategori FROM mobil m JOIN kategori k ON m.id_kategori = k.id_kategori ORDER BY m.id_mobil DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mobil | Rentalku</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
<header class="bar">
    <a class="brand" href="admin.php">Rentalku</a>
    <nav class="nav">
        <a href="admin.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="wrap">
    <div class="kepala">
        <h2>Data mobil</h2>
        <a class="btn" href="mobilTambah.php">Tambah mobil</a>
    </div>

    <div class="tabel-box">
    <table>
        <tr>
            <th>No</th><th>Foto</th><th>Nama</th><th>Kategori</th>
            <th>Transmisi</th><th>Kursi</th><th>Harga / hari</th><th>Status</th><th>Aksi</th>
        </tr>
        <?php $no = 1; while ($m = $q->fetch_assoc()) : ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?php if ($m['foto']) : ?><img src="uploads/<?= htmlspecialchars($m['foto']) ?>" alt=""><?php endif; ?></td>
            <td class="nama"><?= htmlspecialchars($m['nama_mobil']) ?></td>
            <td><?= htmlspecialchars($m['nama_kategori']) ?></td>
            <td><?= htmlspecialchars(ucfirst($m['transmisi'])) ?></td>
            <td><?= (int) $m['kursi'] ?></td>
            <td>Rp <?= number_format($m['harga_harian'], 0, ',', '.') ?></td>
            <td><span class="pill <?= $m['aktif'] ? 'on' : 'off' ?>"><?= $m['aktif'] ? 'Aktif' : 'Nonaktif' ?></span></td>
            <td class="aksi">
                <a href="mobilEdit.php?id=<?= $m['id_mobil'] ?>">Edit</a>
                <a class="hapus" href="mobilHapus.php?id=<?= $m['id_mobil'] ?>" onclick="return confirm('Hapus mobil ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($no === 1) : ?>
        <tr><td class="kosong" colspan="9">Belum ada mobil. Klik "Tambah mobil" untuk memulai.</td></tr>
        <?php endif; ?>
    </table>
    </div>
</main>
</body>
</html>
