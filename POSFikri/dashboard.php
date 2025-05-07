<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$nama_kasir = $_SESSION['nama_kasir'];
$level = $_SESSION['level'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard POS</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="sidebar">
        <h2>POS System</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="produk.php">Produk</a>
        <a href="transaksi.php">Transaksi</a>
        <a href="laporan_harian.php">Laporan Harian</a>
        <a href="laporan_bulanan.php">Laporan Bulanan</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <span>Halo, <?php echo htmlspecialchars($nama_kasir); ?>!</span>
        </div>

        <div class="welcome-message">
            <h3>Selamat datang di sistem POS</h3>
        </div>
        
        <div class="grid">
            
        </div>
    </div>
</body>
</html>


