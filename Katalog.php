<?php
include 'proteksiSession.php';
wajib_login();
include 'koneksi.php';

function tanggal_valid($t){
    $d = DateTime::createFromFormat('Y-m-d', $t);
    return $d && $d->format('Y-m-d') === $t;
}

$q          = trim($_GET['q'] ?? '');
$kategoriId = (int) ($_GET['kategori'] ?? 0);
$transmisi  = $_GET['transmisi'] ?? '';
$hargaMaks  = (int) ($_GET['harga_maks'] ?? 0);
$mulai      = $_GET['tgl_mulai'] ?? '';
$selesai    = $_GET['tgl_selesai'] ?? '';

if (!in_array($transmisi, ['manual', 'matic'])) {
    $transmisi = '';
}

$pesanTanggal = "";
$cekTanggal   = false;
if ($mulai !== '' || $selesai !== '') {
    if ($mulai === '' || $selesai === '') {
        $pesanTanggal = "Isi tanggal mulai dan tanggal selesai sekaligus.";
    } elseif (!tanggal_valid($mulai) || !tanggal_valid($selesai)) {
        $pesanTanggal = "Format tanggal tidak valid.";
    } elseif ($selesai < $mulai) {
        $pesanTanggal = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
    } elseif ($mulai < date('Y-m-d')) {
        $pesanTanggal = "Tanggal mulai tidak boleh sebelum hari ini.";
    } else {
        $cekTanggal = true;
    }
}

$sql = "SELECT m.*, k.nama_kategori
        FROM mobil m
        JOIN kategori k ON m.id_kategori = k.id_kategori
        WHERE m.aktif = 1";
$types  = "";
$params = [];

if ($q !== '') {
    $sql .= " AND m.nama_mobil LIKE ?";
    $types .= "s";
    $params[] = "%" . $q . "%";
}
if ($kategoriId > 0) {
    $sql .= " AND m.id_kategori = ?";
    $types .= "i";
    $params[] = $kategoriId;
}
if ($transmisi !== '') {
    $sql .= " AND m.transmisi = ?";
    $types .= "s";
    $params[] = $transmisi;
}
if ($hargaMaks > 0) {
    $sql .= " AND m.harga_harian <= ?";
    $types .= "i";
    $params[] = $hargaMaks;
}
if ($cekTanggal) {
    $sql .= " AND NOT EXISTS (
                SELECT 1 FROM pesanan p
                WHERE p.id_mobil = m.id_mobil
                  AND p.status_pesanan IN ('menunggu','diterima')
                  AND p.tgl_mulai <= ?
                  AND p.tgl_selesai >= ?
              )";
    $types .= "ss";
    $params[] = $selesai;
    $params[] = $mulai;
}
$sql .= " ORDER BY m.harga_harian ASC";

$stmt = $koneksi->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$hasil = $stmt->get_result();

$daftarKategori = $koneksi->query("SELECT * FROM kategori ORDER BY nama_kategori");
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
        <span class="halo"><?= htmlspecialchars($_SESSION["nama"] ?? '') ?></span>
        <a href="index.php">Beranda</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="wrap">
    <div class="kepala"><h2>Katalog mobil</h2></div>
    <form class="filter" method="GET">
        <label>Nama mobil
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="mis. Avanza">
        </label>
        <label>Kategori
            <select name="kategori">
                <option value="0">Semua</option>
                <?php while ($k = $daftarKategori->fetch_assoc()) : ?>
                    <option value="<?= $k['id_kategori'] ?>" <?= $kategoriId == $k['id_kategori'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_kategori']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </label>
        <label>Transmisi
            <select name="transmisi">
                <option value="">Semua</option>
                <option value="manual" <?= $transmisi === 'manual' ? 'selected' : '' ?>>Manual</option>
                <option value="matic" <?= $transmisi === 'matic' ? 'selected' : '' ?>>Matic</option>
            </select>
        </label>
        <label>Harga maks/hari (Rp)
            <input type="number" name="harga_maks" min="0" value="<?= $hargaMaks > 0 ? $hargaMaks : '' ?>">
        </label>
        <label>Tanggal sewa
            <input type="date" name="tgl_mulai" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($mulai) ?>">
        </label>
        <label>Tanggal kembali
            <input type="date" name="tgl_selesai" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($selesai) ?>">
        </label>
        <button type="submit">Cari</button>
        <a href="Katalog.php">Atur ulang</a>
    </form>

    <?php if ($pesanTanggal) : ?><p class="error"><?= htmlspecialchars($pesanTanggal) ?></p><?php endif; ?>
    <?php if ($cekTanggal) : ?>
        <p class="info">Menampilkan mobil yang tersedia pada <?= htmlspecialchars($mulai) ?> s/d <?= htmlspecialchars($selesai) ?>.</p>
    <?php endif; ?>

    <?php if ($hasil->num_rows === 0) : ?>
        <p class="info">Tidak ada mobil yang cocok. Coba ubah atau kurangi filter.</p>
    <?php else : ?>
    <div class="grid">
        <?php while ($m = $hasil->fetch_assoc()) :
            $foto = $m['foto'] ? 'uploads/' . $m['foto'] : 'mobil.png'; ?>
        <div class="card">
            <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($m['nama_mobil']) ?>">
            <h3><?= htmlspecialchars($m['nama_mobil']) ?></h3>
            <div class="spek"><?= htmlspecialchars($m['nama_kategori']) ?>, <?= ucfirst($m['transmisi']) ?>, <?= (int) $m['kursi'] ?> kursi</div>
            <div class="harga">Rp <?= number_format($m['harga_harian'], 0, ',', '.') ?> / hari</div>
            <?php if ($m['deskripsi']) : ?><p><?= htmlspecialchars($m['deskripsi']) ?></p><?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</main>
</body>
</html>
