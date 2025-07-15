<?php
include 'koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah email terdaftar
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($result) === 0) {
        // Email tidak ditemukan
        $error = "Email belum terdaftar.";
    } else {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            exit();
        } else {
            $error = "Password salah.";
        }
    }
}
?>