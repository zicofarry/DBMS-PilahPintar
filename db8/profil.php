<?php
session_start();

if (!isset($_SESSION['mahasiswa_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connection.php';

$mahasiswa_id = $_SESSION['mahasiswa_id'];
$nama_mahasiswa = $_SESSION['nama_mahasiswa'];
$nim_mahasiswa = $_SESSION['nim'];
$foto_profil_path = $_SESSION['foto_profil'];
$email_utama = $_SESSION['email_address'];
$kelompok_id_mhs = $_SESSION['kelompok_id'];

$detail_kelompok = "Data kelompok tidak ditemukan.";
$jumlah_laporan_pribadi = 0;
$daftar_email_lain = [];

if ($kelompok_id_mhs) {
    // Query yang diperbarui untuk JOIN kelompok dengan kelas
    $sql_kelompok = "SELECT kl.nama AS nama_kelompok, kl.kelompok AS kelompok, k.nama AS nama_kelas 
                     FROM kelompok kl 
                     JOIN kelas k ON kl.kelas_id = k.id 
                     WHERE kl.id = ?";
    $stmt_kelompok = $conn->prepare($sql_kelompok);
    if ($stmt_kelompok) {
        $stmt_kelompok->bind_param("i", $kelompok_id_mhs);
        $stmt_kelompok->execute();
        $result_kelompok = $stmt_kelompok->get_result();
        if ($row_kelompok = $result_kelompok->fetch_assoc()) {
            $detail_kelompok = htmlspecialchars($row_kelompok['nama_kelompok']) . " (Kel. " . htmlspecialchars($row_kelompok['kelompok']) . " - " . htmlspecialchars($row_kelompok['nama_kelas']). ")";
        }
        $stmt_kelompok->close();
    }
}

$sql_emails = "SELECT nama FROM email WHERE mahasiswa_id = ?";
$stmt_emails = $conn->prepare($sql_emails);
if ($stmt_emails) {
    $stmt_emails->bind_param("i", $mahasiswa_id);
    $stmt_emails->execute();
    $result_emails = $stmt_emails->get_result();
    while ($row_email = $result_emails->fetch_assoc()) {
        if ($row_email['nama'] != $email_utama) {
            $daftar_email_lain[] = htmlspecialchars($row_email['nama']);
        }
    }
    $stmt_emails->close();
}

// Menghitung jumlah laporan master yang telah disubmit
$sql_jumlah_laporan = "SELECT COUNT(id) AS total_laporan FROM laporan_sampah WHERE email_id = ?";
$stmt_laporan = $conn->prepare($sql_jumlah_laporan);
if ($stmt_laporan) {
    $stmt_laporan->bind_param("i", $_SESSION['email_id']);
    $stmt_laporan->execute();
    $result_laporan = $stmt_laporan->get_result();
    if ($row_laporan = $result_laporan->fetch_assoc()) {
        $jumlah_laporan_pribadi = (int)$row_laporan['total_laporan'];
    }
    $stmt_laporan->close();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa - <?php echo htmlspecialchars($nama_mahasiswa); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/png">
    <style>
        /* Mengatur gaya untuk kotak putih utama yang membungkus semua detail profil */
        .profile-details-container {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        }

        /* Mengatur bagian header profil (area foto, nama, dan NIM) */
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        /* Mengatur foto profil utama yang besar */
        .profile-main-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 25px;
            border: 3px solid #007bff;
        }

        /* Mengatur judul nama mahasiswa (teks h2) di header profil */
        .profile-header-info h2 {
            margin: 0 0 5px;
            color: #333;
            font-size: 1.8em;
        }

        /* Mengatur tulisan NIM di bawah nama */
        .profile-header-info p {
            margin: 0;
            color: #555;
            font-size: 1.1em;
        }

        /* Mengatur layout grid untuk informasi di bawah header */
        .profile-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        /* Mengatur gaya setiap kotak item informasi (seperti Email, Kelompok, dll) */
        .info-item {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e7e7e7;
        }

        /* Mengatur gaya untuk label di setiap item info (misalnya, tulisan "Email Utama:") */
        .info-item strong {
            display: block;
            color: #007bff;
            margin-bottom: 5px;
            font-size: .9em;
            text-transform: uppercase;
        }

        /* Mengatur gaya untuk data di setiap item info */
        .info-item span {
            color: #333;
            font-size: 1em;
        }

        /* Mengatur daftar (jika ada, seperti untuk 'Email Lain') */
        .info-info-item ul {
            list-style: none;
            padding-left: 0;
            margin-top: 5px;
        }

        /* Mengatur setiap baris dalam daftar */
        .info-item ul li {
            padding: 3px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include_once 'sidebar.php'; ?>
        <main class="content-area">
            <div class="page-header">
                <button id="openSidebarBtn" class="hamburger-icon-header" aria-label="Buka Menu">&#9776;</button>
                <h1>Profil Mahasiswa</h1>
            </div>

            <div class="profile-details-container">
                <div class="profile-header">
                    <img src="<?php echo htmlspecialchars($foto_profil_path ? $foto_profil_path : 'placeholder_user.png'); ?>" alt="Foto Profil <?php echo htmlspecialchars($nama_mahasiswa); ?>" class="profile-main-pic">
                    <div class="profile-header-info">
                        <h2><?php echo htmlspecialchars($nama_mahasiswa); ?></h2>
                        <p>NIM: <?php echo htmlspecialchars($nim_mahasiswa); ?></p>
                    </div>
                </div>

                <div class="profile-info-grid">
                    <div class="info-item">
                        <strong>Email Utama:</strong>
                        <span><?php echo htmlspecialchars($email_utama); ?></span>
                    </div>

                    <?php if (!empty($daftar_email_lain)): ?>
                        <div class="info-item">
                            <strong>Email Lain:</strong>
                            <ul>
                                <?php foreach ($daftar_email_lain as $email_lain): ?>
                                    <li><span><?php echo $email_lain; ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <strong>Kelompok:</strong>
                        <span><?php echo $detail_kelompok; ?></span>
                    </div>

                    <div class="info-item">
                        <strong>Jumlah Laporan Disubmit:</strong>
                        <span><?php echo $jumlah_laporan_pribadi; ?> Laporan</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
    <?php
    if (isset($conn)) {
        $conn->close();
    }
    ?>
</body>

</html>