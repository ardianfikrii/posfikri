<?php
session_start();
include 'koneksi.php';

if ($_POST) {
    $e = $_POST['email'];
    $p = $_POST['password'];
    $u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM akun WHERE email='$e'"));

    if ($u && $p === $u['password']) {
        $_SESSION = ['user_id'=>$u['id_user'],'nama_kasir'=>$u['nama_kasir'],'level'=>$u['level']];
        header("Location: dashboard.php"); exit;
    }
    echo "<script>alert('Email atau password salah!');location='login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h3>Login POS</h3>
        <form method="POST">
            <div class="form-group">
                <label>Email:</label>
                <input type="text" name="email" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
