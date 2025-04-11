<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM pengguna WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['id_pengguna'] = $user['id_pengguna'];
        $_SESSION['nama'] = $user['nama'];
        header("Location: dashboard.php");
    } else {
        echo "<script>alert('Email atau password salah!'); history.go(-1);</script>";
    }
}
?>