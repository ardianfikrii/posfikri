<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT nama_kasir, email, level FROM akun WHERE id_user = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="sidebar">
        <h2>Profile</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="produk.php">Produk</a>
        <a href="transaksi.php">Transaksi</a>
        <a href="laporan_harian.php">Laporan Harian</a>
        <a href="laporan_bulanan.php">Laporan Bulanan</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
    
    <div class="content">
        <h2>Profil Pengguna</h2>
        <table>
            <tr>
                <th>Nama :</th>
                <td><?php echo htmlspecialchars($user['nama_kasir']); ?></td>
            </tr>
            <tr>
                <th>Email :</th>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>Level :</th>
                <td><?php echo htmlspecialchars($user['level']); ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
