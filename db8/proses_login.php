<?php
// proses_login.php
session_start(); // Mulai sesi di paling atas

require_once 'db_connection.php'; // Sertakan file koneksi database

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_input = trim($_POST['email']);
    $nim_input = trim($_POST['nim']);

    // Validasi dasar input
    if (empty($email_input) || empty($nim_input)) {
        $_SESSION['login_error_message'] = "Email dan NIM tidak boleh kosong.";
        header("Location: login.php");
        exit();
    }

    $sql = "SELECT m.id AS mahasiswa_id, m.nama AS nama_mahasiswa, m.photo AS foto_profil, m.kelompok_id, 
                   e.id AS email_id, e.nama AS email_address
            FROM mahasiswa m
            JOIN email e ON m.id = e.mahasiswa_id
            WHERE e.nama = ? AND m.nim = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $email_input, $nim_input); // "ss" karena email dan nim keduanya string (NIM bisa jadi string jika ada karakter non-numerik atau nol di depan)

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                // Pengguna ditemukan, login berhasil
                $user = $result->fetch_assoc();

                // Simpan informasi pengguna ke dalam sesi
                $_SESSION['mahasiswa_id'] = $user['mahasiswa_id'];
                $_SESSION['email_id'] = $user['email_id']; // ID dari tabel email
                $_SESSION['nama_mahasiswa'] = $user['nama_mahasiswa'];
                $_SESSION['foto_profil'] = $user['foto_profil']; // Path ke foto profil
                $_SESSION['email_address'] = $user['email_address'];
                $_SESSION['nim'] = $nim_input; // Simpan juga NIM jika perlu ditampilkan
                $_SESSION['kelompok_id'] = $user['kelompok_id'];


                // Hapus pesan error login jika ada sebelumnya
                unset($_SESSION['login_error_message']);

                // Arahkan ke halaman utama setelah login berhasil
                header("Location: ranking.php"); // Atau halaman dashboard/index aplikasi
                exit();
            } else {
                // Kombinasi email dan NIM tidak ditemukan
                $_SESSION['login_error_message'] = "Email atau NIM yang Anda masukkan salah.";
            }
        } else {
            // Error saat eksekusi query
            error_log("Login execute error: " . $stmt->error);
            $_SESSION['login_error_message'] = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti.";
        }
        $stmt->close();
    } else {
        // Error saat menyiapkan statement
        error_log("Login prepare error: " . $conn->error);
        $_SESSION['login_error_message'] = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti.";
    }

    $conn->close();
    // Jika login gagal, arahkan kembali ke halaman login
    header("Location: login.php");
    exit();
} else {
    // Jika bukan metode POST, redirect ke halaman login
    header("Location: login.php");
    exit();
}
