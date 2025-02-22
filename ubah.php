<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

$id_tugas = $_GET['id'];
$sql = "SELECT * FROM daftar_tugas WHERE id_daftar_tugas = $id_tugas";
$result = mysqli_query($conn, $sql);
$tugas = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Tugas</h2>
        <form action="update.php" method="POST">
            <input type="hidden" name="id_tugas" value="<?php echo $tugas['id_daftar_tugas']; ?>">
            <input type="text" name="nama_tugas" value="<?php echo $tugas['nama_tugas']; ?>" required>
            <button type="submit">Update</button>
        </form>
        <a href="index.php">Kembali</a>
    </div>
</body>
</html>