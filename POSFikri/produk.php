<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if (isset($_POST['tambah'])) {
    $stmt = $conn->prepare("INSERT INTO produk (id_produk, nama_produk, harga, stok) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $_POST['id_produk'], $_POST['nama_produk'], $_POST['harga'], $_POST['stok']);
    $stmt->execute();
    header("Location: produk.php");
    exit;
}

if (isset($_POST['edit'])) {
    $stmt = $conn->prepare("UPDATE produk SET nama_produk=?, harga=?, stok=? WHERE id_produk=?");
    $stmt->bind_param("siii", $_POST['nama_produk'], $_POST['harga'], $_POST['stok'], $_POST['id_produk']);
    $stmt->execute();
    header("Location: produk.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $stmt = $conn->prepare("DELETE FROM produk WHERE id_produk=?");
    $stmt->bind_param("i", $_GET['hapus']);
    $stmt->execute();
    header("Location: produk.php");
    exit;
}

// Ambil Data Produk
$produk = $conn->query("SELECT * FROM produk");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="sidebar">
        <h2>Produk</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="produk.php">Produk</a>
        <a href="transaksi.php">Transaksi</a>
        <a href="laporan_harian.php">Laporan Harian</a>
        <a href="laporan_bulanan.php">Laporan Bulanan</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h2>Manajemen Produk</h2>

        <!-- Form Tambah Produk -->
        <form method="POST">
            <input type="number" name="id_produk" placeholder="ID Produk" required>
            <input type="text" name="nama_produk" placeholder="Nama Produk" required>
            <input type="number" name="harga" placeholder="Harga" required>
            <input type="number" name="stok" placeholder="Stok" required>
            <button type="submit" name="tambah">Tambah</button>
        </form>

        <!-- Tabel Produk -->
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $produk->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_produk'] ?></td>
                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                <td><?= $row['stok'] ?></td>
                <td>
                    <a href="produk.php?edit=<?= $row['id_produk'] ?>">Edit</a> | 
                    <a href="produk.php?hapus=<?= $row['id_produk'] ?>" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <!-- Form Edit Produk -->
        <?php if (isset($_GET['edit'])):
            $id_produk = $_GET['edit'];
            $data = $conn->query("SELECT * FROM produk WHERE id_produk=$id_produk")->fetch_assoc();
        ?>
        <h3>Edit Produk</h3>
        <form method="POST">
            <input type="hidden" name="id_produk" value="<?= $data['id_produk'] ?>">
            <input type="text" name="nama_produk" value="<?= htmlspecialchars($data['nama_produk']) ?>" required>
            <input type="number" name="harga" value="<?= $data['harga'] ?>" required>
            <input type="number" name="stok" value="<?= $data['stok'] ?>" required>
            <button type="submit" name="edit">Simpan</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
