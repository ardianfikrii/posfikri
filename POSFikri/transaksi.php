<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$nama_kasir = $_SESSION['nama_kasir'];
$tanggal_pembelian = date("Y-m-d");
$kembalian = null;
$produk = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['kode_plu'])) {
        $kode_plu = $_POST['kode_plu'];
        $query = "SELECT * FROM produk WHERE id_produk = '$kode_plu'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $produk = mysqli_fetch_assoc($result);
            $harga = $produk['harga'];
            $stok = $produk['stok'];
            $nama_produk = $produk['nama_produk'];
            $diskon = isset($_POST['diskon']) ? (int)$_POST['diskon'] : 0;
            $total_harga = $harga - ($harga * $diskon / 100);
        } else {
            $error = "Kode PLU tidak ditemukan!";
        }
    }

    if (isset($_POST['uang_diberikan']) && isset($_POST['total_harga'])) {
        $uang_diberikan = (int)$_POST['uang_diberikan'];
        $total_harga = (int)$_POST['total_harga'];

        if ($uang_diberikan >= $total_harga) {
            $kembalian = $uang_diberikan - $total_harga;
            $insert = "INSERT INTO penjualan (tanggal_penjualan, total_harga) VALUES ('$tanggal_pembelian', '$total_harga')";

            if (mysqli_query($conn, $insert)) {
                $id_penjualan = mysqli_insert_id($conn);
                $_SESSION['success_message'] = "Transaksi berhasil! ID Penjualan: $id_penjualan";

                $_SESSION['struk'] = [
                    'tanggal' => $tanggal_pembelian,
                    'kasir' => $nama_kasir,
                    'produk' => $nama_produk,
                    'harga' => $harga,
                    'diskon' => $diskon,
                    'total' => $total_harga,
                    'uang_diberikan' => $uang_diberikan,
                    'kembalian' => $kembalian
                ];

                header("Location: transaksi.php");
                exit;
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        } else {
            $error = "Uang yang diberikan kurang!";
        }
    }

    if (isset($_POST['reset'])) {
        unset($_SESSION['struk']);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - POS</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="sidebar">
        <h2>Transaksi</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="produk.php">Produk</a>
        <a href="transaksi.php">Transaksi</a>
        <a href="laporan_harian.php">Laporan Harian</a>
        <a href="laporan_bulanan.php">Laporan Bulanan</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message" id="success-message">
                <strong><?php echo $_SESSION['success_message']; ?></strong>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form method="POST">
            <label>Kode PLU Produk</label>
            <input type="text" name="kode_plu" required>
            <button type="submit">Cari Produk</button>
        </form>

        <?php if ($produk): ?>
            <p>Nama Produk: <strong><?php echo $nama_produk; ?></strong></p>
            <p>Harga: <strong>Rp <?php echo number_format($harga, 0, ',', '.'); ?></strong></p>
            <p>Stok: <strong><?php echo $stok; ?></strong></p>
            <p>Tanggal Pembelian: <strong><?php echo $tanggal_pembelian; ?></strong></p>

            <form method="POST">
                <input type="hidden" name="kode_plu" value="<?php echo $kode_plu; ?>">
                <input type="hidden" name="harga" value="<?php echo $harga; ?>">
                <label>Diskon (%)</label>
                <input type="number" name="diskon" value="<?php echo isset($_POST['diskon']) ? $_POST['diskon'] : 0; ?>" min="0" max="100">
                <button type="submit">Hitung Total</button>
            </form>

            <p>Total Harga: <strong>Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></strong></p>

            <form method="POST">
                <input type="hidden" name="kode_plu" value="<?php echo $kode_plu; ?>">
                <input type="hidden" name="total_harga" value="<?php echo $total_harga; ?>">
                <label>Uang Diberikan</label>
                <input type="number" name="uang_diberikan" required>
                <button type="submit">Bayar</button>
            </form>

            <form method="POST">
                <button type="submit" name="reset">Reset Pembelian</button>
            </form>

            <?php if (!is_null($kembalian)): ?>
                <p>Kembalian: <strong>Rp <?php echo number_format($kembalian, 0, ',', '.'); ?></strong></p>
            <?php endif; ?>
        <?php elseif (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['struk'])): ?>
            <button onclick="cetakStruk()" style="margin-top: 20px;">Cetak Struk</button>
        <?php endif; ?>
    </div>

    <script>
    setTimeout(function() {
        var message = document.getElementById("success-message");
        if (message) {
            message.style.display = "none";
        }
    }, 3000);
    </script>

<script>
    function cetakStruk() {
        var strukHTML = `
            <html>
            <head>
                <title>Struk Pembelian</title>
            </head>
            <body>
                <h2>Struk Pembelian</h2>
                <p><strong>Tanggal:</strong> <?php echo $_SESSION['struk']['tanggal']; ?></p>
                <p><strong>Kasir:</strong> <?php echo $_SESSION['struk']['kasir']; ?></p>
                <p><strong>Produk:</strong> <?php echo $_SESSION['struk']['produk']; ?></p>
                <p><strong>Harga:</strong> Rp <?php echo number_format($_SESSION['struk']['harga'], 0, ',', '.'); ?></p>
                <p><strong>Diskon:</strong> <?php echo $_SESSION['struk']['diskon']; ?>%</p>
                <p><strong>Total:</strong> Rp <?php echo number_format($_SESSION['struk']['total'], 0, ',', '.'); ?></p>
                <p><strong>Uang Diberikan:</strong> Rp <?php echo number_format($_SESSION['struk']['uang_diberikan'], 0, ',', '.'); ?></p>
                <p><strong>Kembalian:</strong> Rp <?php echo number_format($_SESSION['struk']['kembalian'], 0, ',', '.'); ?></p>
                <button onclick="window.print()">Cetak</button>
            </body>
            </html>
        `;

        var win = window.open("", "", "width=400,height=600");
        win.document.open();
        win.document.write(strukHTML);
        win.document.close();
    }

    <?php if (isset($_SESSION['struk'])): ?>
        window.onload = function() {
            cetakStruk();
        };
    <?php endif; ?>
</script>


</body>
</html>
