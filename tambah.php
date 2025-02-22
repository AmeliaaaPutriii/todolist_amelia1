<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Tambah Tugas Baru</h2>
        <form action="proses_tambah_tugas.php" method="POST">
            <input type="text" name="nama_tugas" placeholder="Masukkan nama tugas..." required>
            <button type="submit">Tambah</button>
        </form>
        <a href="daftar_tugas.php">Lihat Daftar Tugas</a>
    </div>
</body>
</html>