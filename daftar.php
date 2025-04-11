<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - To-Do List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Daftar Akun</h2>
        <form action="proses_daftar.php" method="POST">
            <label>Nama:</label>
            <input type="text" name="nama" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" class="button">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>