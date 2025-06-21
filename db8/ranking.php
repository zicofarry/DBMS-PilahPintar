<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['mahasiswa_id'])) {
    header("Location: login.php");
    exit();
}

$ranking_data = [];
$top_3_students = [];
$students_for_table = [];
$error_message = null;

// Langkah 1: Panggil Stored Procedure GetPersentase
$sql_ranking = "CALL GetPersentase()";
$result_ranking = $conn->query($sql_ranking);

if ($result_ranking) {
    if ($result_ranking->num_rows > 0) {
        while ($row = $result_ranking->fetch_assoc()) {
            $ranking_data[] = $row;
        }
    }
    // Membersihkan hasil dari koneksi setelah selesai
    if (is_object($result_ranking)) $result_ranking->free();
    while ($conn->more_results() && $conn->next_result()) {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    }

    // Langkah 2: Ambil Top 3 dan siapkan untuk query foto
    $top_3_raw = array_slice($ranking_data, 0, 3);
    $top_3_nims = [];
    foreach ($top_3_raw as $student) {
        if (!empty($student['nim'])) {
            $top_3_nims[] = $student['nim'];
        }
    }

    // Langkah 3: Query foto terpisah jika ada mahasiswa di Top 3
    if (!empty($top_3_nims)) {
        // Buat placeholder '?' sebanyak jumlah NIM
        $placeholders = implode(',', array_fill(0, count($top_3_nims), '?'));
        // Siapkan tipe data string 's' sebanyak jumlah NIM
        $types = str_repeat('s', count($top_3_nims));

        $sql_photos = "SELECT nim, photo FROM mahasiswa WHERE nim IN ($placeholders)";
        $stmt_photos = $conn->prepare($sql_photos);

        if ($stmt_photos) {
            $stmt_photos->bind_param($types, ...$top_3_nims);
            $stmt_photos->execute();
            $result_photos = $stmt_photos->get_result();

            $photos_map = [];
            while ($photo_row = $result_photos->fetch_assoc()) {
                $photos_map[$photo_row['nim']] = $photo_row['photo'];
            }
            $stmt_photos->close();

            // Langkah 4: Gabungkan data foto ke dalam data Top 3
            foreach ($top_3_raw as $key => $student) {
                if (isset($photos_map[$student['nim']])) {
                    $top_3_raw[$key]['photo'] = $photos_map[$student['nim']];
                } else {
                    $top_3_raw[$key]['photo'] = null; // Default jika foto tidak ditemukan
                }
            }
        }
    }
    $top_3_students = $top_3_raw; // Sekarang $top_3_students sudah berisi data foto

    $students_for_table = array_slice($ranking_data, 3);
} else {
    $error_message = "Gagal mengambil data ranking: " . $conn->error;
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking Mahasiswa - PilahPintar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/png">
    <style>
        /* Mengatur wadah untuk ketiga podium */
        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            margin-top: 30px;
            margin-bottom: 50px;
            gap: 5px;
            min-height: 300px;
            padding-top: 50px
        }

        /* Mengatur gaya dasar untuk setiap blok podium */
        .podium-place {
            background-color: #003366;
            color: #fff;
            padding: 20px 15px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            width: 180px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .2);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            position: relative;
            padding-top: 60px
        }

        /* Mengatur foto profil di dalam podium */
        .podium-place .profile-pic-podium {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #fff;
            object-fit: cover;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .3);
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%)
        }

        /* Mengatur posisi khusus untuk foto podium #1 */
        .podium-place.first .profile-pic-podium {
            object-position: center 30%
        }

        /* Mengatur tulisan nama mahasiswa di podium */
        .podium-place h3 {
            font-size: 1.1em;
            margin: 5px 0;
            color: #fff;
            font-weight: 600;
            word-wrap: break-word
        }

        /* Mengatur tulisan detail (jumlah laporan) di podium */
        .podium-place p {
            font-size: .9em;
            margin: 2px 0;
            color: #d1e0ee
        }

        /* Mengatur tulisan peringkat (1st, 2nd, 3rd) */
        .podium-place .rank-badge {
            font-size: 1.8em;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1
        }

        /* Mengatur gaya khusus untuk podium peringkat pertama */
        .podium-place.first {
            min-height: 220px;
            background-color: #007bff;
            order: 2
        }

        /* Mengatur gaya khusus untuk podium peringkat kedua */
        .podium-place.second {
            min-height: 180px;
            background-color: #0056b3;
            order: 1
        }

        /* Mengatur gaya khusus untuk podium peringkat ketiga */
        .podium-place.third {
            min-height: 150px;
            background-color: #004085;
            order: 3
        }

        /* Mengatur wadah untuk tabel peringkat 4-50 */
        .ranking-table-container {
            margin-top: 30px
        }

        /* Mengatur gaya dasar untuk tabel ranking */
        .ranking-table {
            width: 100%;
            border-collapse: collapse
        }

        /* Mengatur sel header (th) dan sel data (td) pada tabel */
        .ranking-table td,
        .ranking-table th {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left
        }

        /* Mengatur gaya khusus untuk baris header tabel */
        .ranking-table th {
            background-color: #f2f2f2;
            font-weight: 600;
            color: #333
        }

        /* Memberi warna latar berbeda untuk baris genap (efek zebra) */
        .ranking-table tr:nth-child(even) {
            background-color: #f9f9f9
        }

        /* Mengatur efek saat kursor diarahkan ke baris tabel */
        .ranking-table tr:hover {
            background-color: #f1f1f1
        }

        /* Mengatur sel yang berisi nomor peringkat di tabel */
        .ranking-table td.rank-number-table {
            text-align: center;
            font-weight: 400
        }

        /* Mengatur tulisan "Tidak ada data" */
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #777
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include_once 'sidebar.php'; ?>
        <main class="content-area">
            <div class="page-header">
                <button id="openSidebarBtn" class="hamburger-icon-header" aria-label="Buka Menu">&#9776;</button>
                <h1>Ranking Mahasiswa</h1>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (!empty($ranking_data)): ?>
                <div class="podium-container">
                    <?php if (isset($top_3_students[1])): ?>
                        <div class="podium-place second">
                            <img src="<?php echo htmlspecialchars(isset($top_3_students[1]['photo']) && !empty($top_3_students[1]['photo']) ? $top_3_students[1]['photo'] : 'img/placeholder_user.png'); ?>" alt="Foto <?php echo htmlspecialchars($top_3_students[1]['nama']); ?>" class="profile-pic-podium">
                            <span class="rank-badge">2nd</span>
                            <h3><?php echo htmlspecialchars($top_3_students[1]['nama']); ?></h3>
                            <p><?php echo htmlspecialchars($top_3_students[1]['total_laporan']); ?> Laporan</p>
                            <p>(<?php echo htmlspecialchars($top_3_students[1]['persentase_laporan']); ?>%)</p>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($top_3_students[0])): ?>
                        <div class="podium-place first">
                            <img src="<?php echo htmlspecialchars(isset($top_3_students[0]['photo']) && !empty($top_3_students[0]['photo']) ? $top_3_students[0]['photo'] : 'img/placeholder_user.png'); ?>" alt="Foto <?php echo htmlspecialchars($top_3_students[0]['nama']); ?>" class="profile-pic-podium">
                            <span class="rank-badge">1st</span>
                            <h3><?php echo htmlspecialchars($top_3_students[0]['nama']); ?></h3>
                            <p><?php echo htmlspecialchars($top_3_students[0]['total_laporan']); ?> Laporan</p>
                            <p>(<?php echo htmlspecialchars($top_3_students[0]['persentase_laporan']); ?>%)</p>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($top_3_students[2])): ?>
                        <div class="podium-place third">
                            <img src="<?php echo htmlspecialchars(isset($top_3_students[2]['photo']) && !empty($top_3_students[2]['photo']) ? $top_3_students[2]['photo'] : 'img/placeholder_user.png'); ?>" alt="Foto <?php echo htmlspecialchars($top_3_students[2]['nama']); ?>" class="profile-pic-podium">
                            <span class="rank-badge">3rd</span>
                            <h3><?php echo htmlspecialchars($top_3_students[2]['nama']); ?></h3>
                            <p><?php echo htmlspecialchars($top_3_students[2]['total_laporan']); ?> Laporan</p>
                            <p>(<?php echo htmlspecialchars($top_3_students[2]['persentase_laporan']); ?>%)</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($students_for_table)): ?>
                    <div class="ranking-table-container">
                        <h2>Peringkat Selanjutnya (4-<?php echo count($ranking_data); ?>)</h2>
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No.</th>
                                    <th style="width: 40%;">Nama Mahasiswa</th>
                                    <th style="width: 15%;">NIM</th>
                                    <th style="width: 20%; text-align: right;">Total Laporan</th>
                                    <th style="width: 20%; text-align: right;">Persentase (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $nomor_tabel = 4; ?>
                                <?php foreach ($students_for_table as $mahasiswa): ?>
                                    <tr>
                                        <td class="rank-number-table"><?php echo $nomor_tabel++; ?></td>
                                        <td><?php echo htmlspecialchars($mahasiswa['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($mahasiswa['nim']); ?></td>
                                        <td style="text-align: right;"><?php echo htmlspecialchars($mahasiswa['total_laporan']); ?></td>
                                        <td style="text-align: right;"><?php echo htmlspecialchars(number_format($mahasiswa['persentase_laporan'], 2)); ?> %</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif (count($ranking_data) > 0 && count($ranking_data) <= 3) : ?>
                    <p class="no-data">Tidak ada data untuk peringkat 4 dan seterusnya.</p>
                <?php endif; ?>

            <?php elseif (!$error_message): ?>
                <p class="no-data">Belum ada data laporan sampah untuk ditampilkan.</p>
            <?php endif; ?>
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