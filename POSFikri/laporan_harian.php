<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$tanggal_laporan = "";
$laporan = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['tanggal_laporan'])) {
        $tanggal_laporan = $_POST['tanggal_laporan'];
        $query = "SELECT * FROM penjualan WHERE tanggal_penjualan = '$tanggal_laporan'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $laporan[] = $row;
            }
        } else {
            $error = "Tidak ada laporan untuk tanggal tersebut!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian - POS</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="sidebar">
        <h2>Laporan Harian</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="produk.php">Produk</a>
        <a href="transaksi.php">Transaksi</a>
        <a href="laporan_harian.php">Laporan Harian</a>
        <a href="laporan_bulanan.php">Laporan Bulanan</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h2>Pilih Tanggal Laporan Harian</h2>
        <form method="POST">
            <label>Tanggal Laporan:</label>
            <input type="date" name="tanggal_laporan" required>
            <button type="submit">Tampilkan Laporan</button>
        </form>

        <?php if (!empty($laporan)): ?>
            <h3>Data Laporan Harian (<?php echo $tanggal_laporan; ?>)</h3>
            <table border="1">
                <tr>
                    <th>ID Penjualan</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                </tr>
                <?php foreach ($laporan as $data): ?>
                <tr>
                    <td><?php echo $data['id_penjualan']; ?></td>
                    <td><?php echo $data['tanggal_penjualan']; ?></td>
                    <td>Rp <?php echo number_format($data['total_harga'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <button onclick="cetakLaporan()">Cetak Laporan</button>
        <?php elseif (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>

    <script>
        function cetakLaporan() {
    var laporanHTML = `
        <html>
        <head>
            <title>Laporan Harian</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid black; padding: 8px; }
            </style>
        </head>
        <body>
            <h2>Laporan Harian (<?php echo $tanggal_laporan; ?>)</h2>
            <table>
                <tr>
                    <th>ID Penjualan</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                </tr>
                <?php foreach ($laporan as $data): ?>
                <tr>
                    <td><?php echo $data['id_penjualan']; ?></td>
                    <td><?php echo $data['tanggal_penjualan']; ?></td>
                    <td>Rp <?php echo number_format($data['total_harga'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p>Terima kasih!</p>
            <script>window.print();<\/script>
        </body>
        </html>
    `;

    var win = window.open("", "", "width=800,height=600");
    win.document.open();
    win.document.write(laporanHTML);
    win.document.close();
    win.focus();
    s}

    </script>
</body>
</html>
