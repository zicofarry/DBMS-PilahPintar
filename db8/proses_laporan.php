<?php
session_start();
require_once 'db_connection.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['email_id'])) {
    $_SESSION['message'] = "Sesi Anda telah berakhir. Silakan login kembali.";
    $_SESSION['message_type'] = "error";
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email_id_user = $_SESSION['email_id'];
    $submit_date = date('Y-m-d H:i:s');

    // Asumsi lokasi diambil dari input pertama, atau default ke 1 jika tidak ada
    $lokasi_id = isset($_POST['lokasi_id'][0]) && !empty($_POST['lokasi_id'][0]) ? (int)$_POST['lokasi_id'][0] : 1;

    $keterangan_array = $_POST['keterangan'];
    $jenis_id_array = $_POST['jenis_id'];
    $quantity_array = $_POST['quantity'];
    $satuan_id_array = $_POST['satuan_id'];

    $jumlah_item = count($keterangan_array);

    if ($jumlah_item == 0) {
        $_SESSION['message'] = "Tidak ada item laporan yang disubmit.";
        $_SESSION['message_type'] = "error";
        header("Location: form_laporan.php");
        exit();
    }

    // Mulai Transaksi Database
    $conn->begin_transaction();

    try {
        // Langkah 1: Insert ke tabel master 'laporan_sampah'
        $sql_master = "INSERT INTO laporan_sampah (email_id, submit_date, lokasi_id) VALUES (?, ?, ?)";
        $stmt_master = $conn->prepare($sql_master);
        if (!$stmt_master) {
            throw new Exception("Gagal menyiapkan statement master: " . $conn->error);
        }
        $stmt_master->bind_param("isi", $email_id_user, $submit_date, $lokasi_id);
        $stmt_master->execute();

        // Ambil ID dari laporan master yang baru saja di-insert
        $laporan_id = $conn->insert_id;
        if ($laporan_id == 0) {
            throw new Exception("Gagal mendapatkan ID laporan master.");
        }
        $stmt_master->close();

        // Langkah 2: Siapkan statement untuk insert ke tabel 'detail_laporan'
        $sql_detail = "INSERT INTO detail_laporan (laporan_id, keterangan, jenis_id, quantity, satuan_id) VALUES (?, ?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);
        if (!$stmt_detail) {
            throw new Exception("Gagal menyiapkan statement detail: " . $conn->error);
        }

        $success_count = 0;

        // Loop melalui setiap item dan insert ke 'detail_laporan'
        for ($i = 0; $i < $jumlah_item; $i++) {
            $keterangan_item = trim($keterangan_array[$i]);
            $jenis_id_item = (int)$jenis_id_array[$i];
            $quantity_item = (float)$quantity_array[$i];
            $satuan_id_item = (int)$satuan_id_array[$i];

            // Validasi sederhana
            if ($jenis_id_item <= 0 || $quantity_item <= 0 || $satuan_id_item <= 0) {
                throw new Exception("Data tidak lengkap pada item #" . ($i + 1));
            }

            $stmt_detail->bind_param("isidi", $laporan_id, $keterangan_item, $jenis_id_item, $quantity_item, $satuan_id_item);
            $stmt_detail->execute();
            $success_count++;
        }
        $stmt_detail->close();

        // Jika semua berhasil, commit transaksi
        $conn->commit();
        $_SESSION['message'] = $success_count . " item laporan sampah berhasil disubmit dalam satu laporan.";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        // Jika ada kesalahan, batalkan semua perubahan (rollback)
        $conn->rollback();
        $_SESSION['message'] = "Terjadi kesalahan: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    $conn->close();
    header("Location: form_laporan.php");
    exit();
} else {
    header("Location: form_laporan.php");
    exit();
}
