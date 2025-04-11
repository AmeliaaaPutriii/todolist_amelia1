<?php
session_start();
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
    <title>Dashboard - To-Do List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Selamat datang di To Do List, <?php echo $_SESSION['nama']; ?>!</h2><br>
        <a href="todolist.php" class="button">Tambah Tugas</a>
        <p><a href="daftar_tugas.php" class="button">Lihat Daftar Tugas</a></p>
    </div>
</body>
</html>