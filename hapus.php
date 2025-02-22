<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

$id_tugas = $_GET['id'];

$sql = "DELETE FROM daftar_tugas WHERE id_daftar_tugas = $id_tugas";

if (mysqli_query($conn, $sql)) {
    header("Location: index.php");
} else {
    echo "Gagal menghapus tugas!";
}
?>