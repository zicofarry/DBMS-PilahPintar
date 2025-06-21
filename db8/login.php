<?php
// login.php
session_start();

// Jika pengguna sudah login, arahkan ke halaman utama
if (isset($_SESSION['mahasiswa_id'])) {
    header("Location: form_laporan.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PilahPintar</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/favicon.png" type="image/png">

    <style>
        /* Mengatur seluruh halaman login */
        body.login-page {
            position: relative;
            /* Diperlukan untuk background ::before */
            overflow: hidden;
            /* Mencegah scrollbar jika blur keluar batas */
            display: flex;
            flex-direction: column;
            /* Menyusun judul dan kotak login secara vertikal */
            justify-content: center;
            /* Memusatkan semua konten secara vertikal */
            align-items: center;
            /* Memusatkan semua konten secara horizontal */
            min-height: 100vh;
            /* Tinggi minimal seukuran layar */
            background-color: #000;
            /* Warna dasar jika background blur gagal */
            font-family: 'Poppins', sans-serif;
            /* Pastikan halaman ini pakai Poppins */
        }

        /* Membuat lapisan background gradasi blur di belakang semua konten */
        body.login-page::before {
            content: "";
            position: absolute;
            top: -50px;
            left: -50px;
            right: -50px;
            bottom: -50px;
            z-index: -1;
            /* Meletakkan lapisan ini di paling belakang */
            background:
                radial-gradient(circle at 20% 30%, rgba(0, 120, 200, 0.6) 0%, transparent 40%),
                radial-gradient(ellipse at 70% 60%, rgba(0, 50, 100, 0.8) 0%, transparent 50%),
                radial-gradient(circle at 80% 85%, rgba(0, 10, 30, 0.6) 0%, transparent 35%),
                #000c1a;
            filter: blur(25px);
            /* Memberi efek blur */
        }

        /* Mengatur gaya untuk judul aplikasi "PilahPintar" */
        .app-title-login {
            font-size: 3.5em;
            font-weight: 700;
            /* Tebal */
            margin-bottom: 30px;
            background: linear-gradient(135deg, #28a745, #1e90ff, #ffffff, #064c97);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            /* Menerapkan background hanya pada teks */
            background-clip: text;
            -webkit-text-fill-color: transparent;
            /* Membuat warna teks transparan */
            text-fill-color: transparent;
            animation: waveyGradient 10s ease-in-out infinite alternate;
            /* Menerapkan animasi gradasi */
            transition: transform 0.3s ease, text-shadow 0.3s ease;
        }

        /* Mengatur efek saat kursor diarahkan ke judul "PilahPintar" */
        .app-title-login:hover {
            transform: scale(1.05);
            /* Sedikit membesar */
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
            /* Memberi efek glow */
        }

        /* Mendefinisikan animasi pergerakan gradasi untuk efek "wavey" */
        @keyframes waveyGradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Mengatur kotak utama untuk formulir login */
        .login-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            /* Sudut melengkung */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            /* Efek bayangan */
            width: 100%;
            max-width: 400px;
            z-index: 1;
            position: relative;
        }

        /* Mengatur judul "Login Mahasiswa" di dalam kotak */
        .login-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 1.8em;
            font-weight: 600;
        }

        /* Mengatur grup yang berisi label dan input field */
        .login-container .form-group {
            margin-bottom: 18px;
        }

        /* Mengatur gaya tulisan label (seperti "Email UPI:") */
        .login-container .form-group label {
            font-weight: 500;
            font-size: 0.95em;
            display: block;
            margin-bottom: 6px;
        }

        /* Mengatur gaya kotak input untuk email dan teks */
        .login-container .form-group input[type="email"],
        .login-container .form-group input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            font-size: 1em;
            border: 1px solid #ced4da;
            border-radius: 6px;
            box-sizing: border-box;
        }

        /* Mengatur efek saat kotak input sedang aktif (di-klik) */
        .login-container .form-group input[type="email"]:focus,
        .login-container .form-group input[type="text"]:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Mengatur gaya tombol "Login" */
        .login-container .btn-submit {
            font-weight: 600;
            width: 100%;
            padding: 12px;
            font-size: 1.1em;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        /* Mengatur efek saat kursor diarahkan ke tombol "Login" */
        .login-container .btn-submit:hover {
            background-color: #0056b3;
        }

        /* Mengatur gaya untuk pesan error */
        .login-container .message {
            text-align: center;
        }
    </style>
</head>

<body class="login-page">

    <div class="app-title-login">PilahPintar</div>

    <div class="login-container">
        <h1>Login Mahasiswa</h1>

        <?php
        // Menampilkan pesan error login jika ada
        if (isset($_SESSION['login_error_message'])) {
            echo "<div class=\"message error\">" . htmlspecialchars($_SESSION['login_error_message']) . "</div>";
            unset($_SESSION['login_error_message']);
        }
        ?>

        <form action="proses_login.php" method="POST" id="formLogin">
            <div class="form-group">
                <label for="email">Email UPI:</label>
                <input type="email" id="email" name="email" required placeholder="Contoh: namaanda@upi.edu">
            </div>
            <div class="form-group">
                <label for="nim">NIM:</label>
                <input type="text" id="nim" name="nim" required placeholder="Masukkan NIM Anda">
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>
    </div>
</body>

</html>