<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['mahasiswa_id'])) {
    header("Location: login.php");
    exit();
}

// --- Fungsi untuk membersihkan multiple results dari koneksi ---
function clear_mysql_results($conn)
{
    while ($conn->more_results() && $conn->next_result()) {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    }
}

// --- 1. Ambil data untuk "Total Sampah per Mahasiswa" ---
$total_sampah_per_mahasiswa = [];
$sql_total_sampah = "CALL GetTotalSampahPerMahasiswa()";
if ($result = $conn->query($sql_total_sampah)) {
    while ($row = $result->fetch_assoc()) {
        $total_sampah_per_mahasiswa[] = $row;
    }
    $result->free();
}
clear_mysql_results($conn);

// --- 2. Ambil data untuk "Total per Jenis Sampah per Mahasiswa" ---
$total_per_jenis = [];
$sql_jenis_per_mhs = "CALL GetTotalPerJenisPerMahasiswa()";
if ($result = $conn->query($sql_jenis_per_mhs)) {
    while ($row = $result->fetch_assoc()) {
        $total_per_jenis[] = $row;
    }
    $result->free();
}
clear_mysql_results($conn);

// --- 3. Ambil data untuk "Sampah di Akhir Pekan (Weekend)" ---
$sampah_weekend = [];
$sql_weekend = "CALL getsampahweekend()";
if ($result = $conn->query($sql_weekend)) {
    while ($row = $result->fetch_assoc()) {
        $sampah_weekend[] = $row;
    }
    $result->free();
}
clear_mysql_results($conn);

// --- 4. Ambil data untuk "Jenis Sampah Tersering per Mahasiswa" ---
$jenis_tersering = [];
$sql_jenis_tersering = "CALL GetJenisTerseringPerMahasiswa()";
if ($result = $conn->query($sql_jenis_tersering)) {
    while ($row = $result->fetch_assoc()) {
        $jenis_tersering[] = $row;
    }
    $result->free();
}
clear_mysql_results($conn);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Umum - PilahPintar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/png">
    <style>
        /* Mengatur gaya untuk setiap kotak 'kartu' statistik */
        .statistik-section {
            margin-bottom: 40px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        }

        /* Mengatur gaya untuk judul di setiap kotak */
        .statistik-section h2 {
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
            color: #001f3f;
        }

        /* Mengatur gaya dasar untuk tabel data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Mengatur gaya untuk sel header (th) dan sel data (td) */
        .data-table td,
        .data-table th {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
        }

        /* Mengatur gaya khusus untuk baris header tabel */
        .data-table th {
            background-color: #f2f2f2;
            font-weight: 600;
        }

        /* Memberi warna latar berbeda untuk baris genap (efek zebra) */
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Kelas bantuan untuk membuat teks rata kanan */
        .data-table .text-right {
            text-align: right;
        }

        /* Mengatur tulisan "Tidak ada data" */
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #777;
        }

        /* Mengatur wadah untuk tombol navigasi */
        .pagination-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: .9em;
            color: #555;
        }

        /* Mengatur gaya tombol navigasi */
        .pagination-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            transition: background-color .2s ease;
        }

        /* Mengatur gaya tombol saat mouse diarahkan ke atasnya */
        .pagination-btn:hover {
            background-color: #0056b3;
        }

        /* Mengatur gaya tombol saat tidak bisa diklik */
        .pagination-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include_once 'sidebar.php'; ?>
        <main class="content-area">
            <div class="content-wrapper">
                <div class="page-header">
                    <button id="openSidebarBtn" class="hamburger-icon-header" aria-label="Buka Menu">&#9776;</button>
                    <h1>Statistik Umum Laporan</h1>
                </div>

                <section class="statistik-section">
                    <h2>Jenis Sampah yang Paling Sering Dilaporkan</h2>
                    <?php if (!empty($jenis_tersering)): ?>
                        <table class="data-table" id="tabelJenisTersering">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Jenis Sampah Tersering</th>
                                    <th class="text-right">Jumlah Pelaporan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jenis_tersering as $data): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($data['nim']); ?></td>
                                        <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($data['jenis_sampah']); ?></td>
                                        <td class="text-right"><?php echo htmlspecialchars($data['jumlah_kemunculan']); ?> kali</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="pagination-controls" id="paginationJenisTersering"></div>
                    <?php else: ?>
                        <p class="no-data">Tidak ada data untuk ditampilkan.</p>
                    <?php endif; ?>
                </section>

                <section class="statistik-section">
                    <h2>Total Kuantitas Sampah per Mahasiswa</h2>
                    <?php if (!empty($total_sampah_per_mahasiswa)): ?>
                        <table class="data-table" id="tabelTotalSampah">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th class="text-right">Total Kuantitas Sampah</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($total_sampah_per_mahasiswa as $data): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($data['nim']); ?></td>
                                        <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                        <td class="text-right"><?php echo htmlspecialchars(number_format($data['total_sampah'])); ?></td>
                                        <td><?php echo htmlspecialchars($data['satuan']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="pagination-controls" id="paginationTotalSampah"></div>
                    <?php else: ?>
                        <p class="no-data">Tidak ada data untuk ditampilkan.</p>
                    <?php endif; ?>
                </section>

                <section class="statistik-section">
                    <h2>Rincian Jenis Sampah per Mahasiswa</h2>
                    <?php if (!empty($total_per_jenis)): ?>
                        <table class="data-table" id="tabelJenisSampah">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Jenis Sampah</th>
                                    <th class="text-right">Total Kuantitas</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($total_per_jenis as $data): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($data['nim']); ?></td>
                                        <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($data['jenis_sampah']); ?></td>
                                        <td class="text-right"><?php echo htmlspecialchars(number_format($data['total_quantity'])); ?></td>
                                        <td><?php echo htmlspecialchars($data['satuan']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="pagination-controls" id="paginationJenisSampah"></div>
                    <?php else: ?>
                        <p class="no-data">Tidak ada data untuk ditampilkan.</p>
                    <?php endif; ?>
                </section>

                <section class="statistik-section">
                    <h2>Total Sampah Terkumpul di Akhir Pekan</h2>
                    <?php if (!empty($sampah_weekend)): ?>
                        <table class="data-table" id="tabelSampahWeekend">
                            <thead>
                                <tr>
                                    <th>Jenis Sampah</th>
                                    <th class="text-right">Total Kuantitas</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sampah_weekend as $data): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($data['jenis_sampah']); ?></td>
                                        <td class="text-right"><?php echo htmlspecialchars(number_format($data['total_quantity'])); ?></td>
                                        <td><?php echo htmlspecialchars($data['satuan']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="pagination-controls" id="paginationSampahWeekend"></div>
                    <?php else: ?>
                        <p class="no-data">Tidak ada data untuk ditampilkan.</p>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function createPagination(tableId, paginationId, rowsPerPage) {
                const table = document.getElementById(tableId);
                if (!table) return;
                const paginationContainer = document.getElementById(paginationId);
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                const rows = tbody.querySelectorAll('tr');
                const numRows = rows.length;
                let currentPage = 1;
                const numPages = Math.ceil(numRows / rowsPerPage);
                if (numPages <= 1) {
                    if (paginationContainer) paginationContainer.style.display = 'none';
                    return
                }

                function showPage(page) {
                    const startIndex = (page - 1) * rowsPerPage;
                    const endIndex = startIndex + rowsPerPage;
                    rows.forEach((row, index) => {
                        row.style.display = (index >= startIndex && index < endIndex) ? '' : 'none'
                    });
                    updateControls()
                }

                function updateControls() {
                    const startRow = (currentPage - 1) * rowsPerPage + 1;
                    const endRow = Math.min(currentPage * rowsPerPage, numRows);
                    paginationContainer.innerHTML = `
                        <button class="pagination-btn" id="prevBtn_${tableId}" title="Sebelumnya">&laquo; Prev</button>
                        <span class="page-info">Menampilkan ${startRow}-${endRow} dari ${numRows}</span>
                        <button class="pagination-btn" id="nextBtn_${tableId}" title="Berikutnya">Next &raquo;</button>
                    `;
                    const prevBtn = document.getElementById(`prevBtn_${tableId}`);
                    const nextBtn = document.getElementById(`nextBtn_${tableId}`);
                    prevBtn.disabled = (currentPage === 1);
                    nextBtn.disabled = (currentPage === numPages);
                    prevBtn.addEventListener('click', () => {
                        if (currentPage > 1) {
                            currentPage--;
                            showPage(currentPage)
                        }
                    });
                    nextBtn.addEventListener('click', () => {
                        if (currentPage < numPages) {
                            currentPage++;
                            showPage(currentPage)
                        }
                    })
                }
                showPage(1)
            }
            createPagination('tabelJenisTersering', 'paginationJenisTersering', 15);
            createPagination('tabelTotalSampah', 'paginationTotalSampah', 15);
            createPagination('tabelJenisSampah', 'paginationJenisSampah', 15);
            createPagination('tabelSampahWeekend', 'paginationSampahWeekend', 15);
        });
    </script>
    <?php
    if (isset($conn)) {
        $conn->close();
    }
    ?>
</body>

</html>