<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  

    $sql = "INSERT INTO pengguna (nama, email, password) VALUES ('$nama', '$email', '$password')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Email Sudah Terdaftar!'); history.go(-1);</script>";
    }
}
?>