<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Ambil nama mahasiswa dan foto profil dari sesi login
// Default ke nilai placeholder jika sesi tidak ditemukan (seharusnya tidak terjadi jika halaman sudah dilindungi)
$nama_mahasiswa_login = isset($_SESSION['nama_mahasiswa']) ? $_SESSION['nama_mahasiswa'] : "Pengguna";
$foto_profil_login = isset($_SESSION['foto_profil']) && !empty($_SESSION['foto_profil']) ? $_SESSION['foto_profil'] : "placeholder_user.png"; // Path ke foto placeholder default

// Menentukan halaman aktif
$current_page = basename($_SERVER['SCRIPT_NAME']);

// Tautan Logout
$logout_link = "logout.php";

?>
<aside class="sidebar">
    <div class="menu-header">
        <button id="hamburgerMenu" aria-label="Toggle Menu">&#9776;</button>
        <img src="img/logo_sidebar.png" alt="Logo PilahPintar" class="sidebar-logo" style="height: 50px;">
        <span class="tab-name">PilahPintar</span>
    </div>
    <nav class="pilihan-tab">
        <ul>
            <li>
                <a href="ranking.php" class="<?php echo ($current_page == 'ranking.php') ? 'active' : ''; ?>">
                    Ranking Mahasiswa
                </a>
            </li>
            <li>
                <a href="general_statistik.php" class="<?php echo ($current_page == 'general_statistik.php') ? 'active' : ''; ?>">
                    Statistik Umum
                </a>
            </li>
            <li>
                <a href="statistik.php" class="<?php echo ($current_page == 'statistik.php') ? 'active' : ''; ?>">
                    Statistik Mahasiswa
                </a>
            </li>
            <li>
                <a href="form_laporan.php" class="<?php echo ($current_page == 'form_laporan.php' || $current_page == 'index.php') ? 'active' : ''; ?>">
                    Submit Laporan Sampah
                </a>
            </li>
            <li>
                <a href="<?php echo $logout_link; ?>" style="
                    font-weight: 700;
                    background-color: #e53e3e; /* merah */
                    color: white;
                    padding: 8px 32px;
                    border-radius: 4px;
                    text-decoration: none;
                    display: inline-block;">
                    Logout
                </a>
            </li>
        </ul>
    </nav>
    <div class="profile-mahasiswa">
        <a href="profil.php" class="profile-mahasiswa-content-link">
            <img src="<?php echo htmlspecialchars($foto_profil_login); ?>" alt="Foto Profil" class="profile-pic">
            <span class="nama-mahasiswa"><?php echo htmlspecialchars($nama_mahasiswa_login); ?></span>
        </a>
    </div>
</aside>