<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$tanggal_sekarang = date("Y-m");
$laporan = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bulan'])) {
    $bulan = $_POST['bulan'];
    
    // Query untuk mengambil data penjualan berdasarkan bulan
    $query = "SELECT * FROM penjualan WHERE DATE_FORMAT(tanggal_penjualan, '%Y-%m') = '$bulan'";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $laporan[] = $row;
    }

    $_SESSION['laporan'] = $laporan;
    $_SESSION['bulan'] = $bulan;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="sidebar">
        <h2>Laporan Bulanan</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="produk.php">Produk</a>
        <a href="transaksi.php">Transaksi</a>
        <a href="laporan_harian.php">Laporan Harian</a>
        <a href="laporan_bulanan.php">Laporan Bulanan</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h2>Pilih Laporan Bulanan</h2>
        <form method="POST">
            <label for="bulan">Pilih Bulan:</label>
            <input type="month" name="bulan" value="<?php echo $tanggal_sekarang; ?>" required>
            <button type="submit">Tampilkan Laporan</button>
        </form>

        <?php if (!empty($laporan)): ?>
            <h3>Laporan Bulanan: <?php echo $_SESSION['bulan']; ?></h3>
            <table border="1">
                <tr>
                    <th>ID Penjualan</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                </tr>
                <?php foreach ($laporan as $row): ?>
                <tr>
                    <td><?php echo $row['id_penjualan']; ?></td>
                    <td><?php echo $row['tanggal_penjualan']; ?></td>
                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <button onclick="cetakLaporan()">Cetak Laporan</button>
        <?php endif; ?>
    </div>

    <script>
function cetakLaporan() {
    var laporanHTML = `
        <html>
        <head>
            <title>Laporan Bulanan</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid black; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h2>Laporan Bulanan: <?php echo $_SESSION['bulan']; ?></h2>
            <table>
                <tr>
                    <th>ID Penjualan</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                </tr>
                <?php foreach ($_SESSION['laporan'] as $row): ?>
                <tr>
                    <td><?php echo $row['id_penjualan']; ?></td>
                    <td><?php echo $row['tanggal_penjualan']; ?></td>
                    <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <script>window.print();<\/script>
        </body>
        </html>
    `;

    var win = window.open("", "", "width=800,height=600");
    win.document.write(laporanHTML);
    win.document.close();
    }
    </script>
</body>
</html>
