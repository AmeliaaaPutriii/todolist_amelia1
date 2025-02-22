<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tugas = $_POST['id_tugas'];
    $nama_tugas = $_POST['nama_tugas'];

    $sql = "UPDATE daftar_tugas SET nama_tugas = '$nama_tugas' WHERE id_daftar_tugas = $id_tugas";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
    } else {
        echo "Gagal memperbarui tugas!";
    }
}
?>