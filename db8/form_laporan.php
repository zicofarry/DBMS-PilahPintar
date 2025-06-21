<?php

session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['mahasiswa_id'])) {
    header("Location: login.php");
    exit();
}

$jenis_sampah_options = "";
$sql_jenis = "SELECT id, nama_jenis FROM jenis ORDER BY nama_jenis ASC";
$result_jenis = $conn->query($sql_jenis);
if ($result_jenis->num_rows > 0) {
    while ($row = $result_jenis->fetch_assoc()) {
        $jenis_sampah_options .= "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nama_jenis']) . "</option>";
    }
}

$satuan_options = "";
$sql_satuan = "SELECT id, nama_satuan FROM satuan ORDER BY nama_satuan ASC";
$result_satuan = $conn->query($sql_satuan);
if ($result_satuan->num_rows > 0) {
    while ($row = $result_satuan->fetch_assoc()) {
        $satuan_options .= "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nama_satuan']) . "</option>";
    }
}

$lokasi_sampah_options = "";
$sql_lokasi = "SELECT id, nama FROM lokasi_sampah ORDER BY nama ASC";
$result_lokasi = $conn->query($sql_lokasi);
if ($result_lokasi->num_rows > 0) {
    while ($row = $result_lokasi->fetch_assoc()) {
        $lokasi_sampah_options .= "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nama']) . "</option>";
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Laporan Sampah - PilahPintar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/png">
    <style>
        /* Mengatur gaya untuk setiap kotak item laporan */
        .report-item {
            border: 1px solid #e0e0e0;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
            position: relative;
        }

        /* Mengatur gaya untuk judul di setiap item laporan */
        .report-item h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.1em;
            color: #555;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 5px;
        }

        /* Mengatur gaya untuk tombol hapus (tombol 'x') */
        .remove-item-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-weight: bold;
            cursor: pointer;
            line-height: 25px;
            text-align: center;
        }

        /* Mengatur gaya tombol hapus saat kursor diarahkan ke atasnya */
        .remove-item-btn:hover {
            background-color: #ee5253;
        }

        /* Mengatur gaya tombol untuk menambah item laporan baru */
        .add-item-btn {
            background-color: #5cb85c;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            margin-top: 10px;
            display: inline-block;
        }

        /* Mengatur gaya tombol tambah saat kursor diarahkan ke atasnya */
        .add-item-btn:hover {
            background-color: #4cae4c;
        }
    </style>
    </style>
</head>

<body>
    <div class="container">
        <?php include_once 'sidebar.php'; ?>

        <main class="content-area">
            <div class="page-header">
                <button id="openSidebarBtn" class="hamburger-icon-header" aria-label="Buka Menu">&#9776;</button>
                <h1>Formulir Laporan Sampah</h1>
            </div>
            <?php
            if (isset($_SESSION['message'])) {
                $message_class = (isset($_SESSION['message_type']) && $_SESSION['message_type'] == 'error') ? 'message error' : 'message success';
                echo "<div class=\"" . htmlspecialchars($message_class) . "\">" . $_SESSION['message'] . "</div>";
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>

            <form action="proses_laporan.php" method="POST" id="formLaporanSampah">

                <div id="report-items-container">
                    <div class="report-item">
                        <h3>Laporan Sampah #1 <button type="button" class="remove-item-btn" style="display:none;">&times;</button></h3>
                        <div class="form-group">
                            <label for="keterangan_0">Keterangan Laporan:</label>
                            <textarea id="keterangan_0" name="keterangan[]" rows="3"  placeholder="Deskripsikan sampah..." style="width: 1000px; height: 100px;"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="jenis_id_0">Jenis Sampah:</label>
                            <select id="jenis_id_0" name="jenis_id[]" required style="width: 1000px">
                                <option value="">Pilih Jenis Sampah...</option>
                                <?php echo $jenis_sampah_options; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity_0">Jumlah Sampah:</label>
                            <input type="number" id="quantity_0" name="quantity[]" step="0.001" required placeholder="Contoh: 1.5" style="width: 1000px">
                        </div>
                        <div class="form-group">
                            <label for="satuan_id_0">Satuan:</label>
                            <select id="satuan_id_0" name="satuan_id[]" required style="width: 1000px">
                                <option value="">Pilih Satuan...</option>
                                <?php echo $satuan_options; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="lokasi_id_0">Lokasi Sampah (Opsional):</label>
                            <select id="lokasi_id_0" name="lokasi_id[]" style="width: 1000px">
                                <option value="">Pilih Lokasi...</option>
                                <?php echo $lokasi_sampah_options; ?>
                                <?php if (empty(trim(strip_tags($lokasi_sampah_options)))): ?>
                                    <option value="" disabled>Belum ada data lokasi</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="button" id="addReportItem" class="add-item-btn"><strong>+</strong> Tambah Laporan Sampah Lain</button>
                <hr style="margin: 20px 0;">
                <button type="submit" class="btn-submit">Submit Semua Laporan</button>
            </form>
        </main>
    </div>
    <script src="script.js"></script>
</body>

</html>