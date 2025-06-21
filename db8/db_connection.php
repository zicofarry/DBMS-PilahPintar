<?php

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "db8";

// Membuat koneksi ke database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Jika koneksi gagal, tampilkan pesan error
    // ini berguna untuk debugging.
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
