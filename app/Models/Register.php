<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $lokasi_id = $_POST['lokasi_id']; // Ambil lokasi dari form

    // Cek apakah email sudah terdaftar
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email sudah digunakan.";
    } else {
        // Insert data termasuk lokasi_id
        $insert = mysqli_query($conn, "INSERT INTO users (nama, email, password, lokasi_id) VALUES ('$nama', '$email', '$password', '$lokasi_id')");
        if ($insert) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Gagal daftar. Silakan coba lagi.";
        }
    }
}
?>