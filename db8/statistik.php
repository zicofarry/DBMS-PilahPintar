<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['mahasiswa_id'])) {
    header("Location: login.php");
    exit();
}

// Atur zona waktu
date_default_timezone_set('Asia/Jakarta');

// --- Langkah 1: Ambil daftar semua mahasiswa untuk dropdown filter ---
$mahasiswa_list = [];
$sql_mahasiswa = "SELECT id, nim, nama FROM mahasiswa ORDER BY nama ASC";
$result_mahasiswa = $conn->query($sql_mahasiswa);
if ($result_mahasiswa) {
    while ($row = $result_mahasiswa->fetch_assoc()) {
        $mahasiswa_list[] = $row;
    }
}
// Bersihkan hasil query
if (is_object($result_mahasiswa)) $result_mahasiswa->free();
while ($conn->more_results() && $conn->next_result()) {
    if ($res = $conn->store_result()) {
        $res->free();
    }
}

// --- Langkah 2: Tentukan filter yang dipilih ---
$rentang_waktu = isset($_GET['rentang']) ? $_GET['rentang'] : 'bulanan';
// Default ke mahasiswa yang sedang login jika tidak ada pilihan
$mahasiswa_terpilih_id = isset($_GET['mahasiswa_id']) ? (int)$_GET['mahasiswa_id'] : $_SESSION['mahasiswa_id'];

if ($rentang_waktu == 'mingguan') {
    $sql_data_grafik = "CALL GetSampahPerMinggu()";
    $label_waktu_prefix = "Minggu ke-";
} else {
    $sql_data_grafik = "CALL GetSampahPerBulan()";
    $label_waktu_prefix = "Bulan ke-";
}

// --- Langkah 3: Ambil SEMUA data statistik dari Stored Procedure ---
$data_grafik_semua = [];
$error_message = null;
$result_grafik = $conn->query($sql_data_grafik);

if ($result_grafik) {
    if ($result_grafik->num_rows > 0) {
        while ($row = $result_grafik->fetch_assoc()) {
            $data_grafik_semua[] = $row;
        }
    }
    if (is_object($result_grafik)) $result_grafik->free();
    while ($conn->more_results() && $conn->next_result()) {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    }
} else {
    $error_message = "Gagal mengambil data statistik: " . $conn->error;
}

// --- Langkah 4: Filter data di PHP hanya untuk mahasiswa yang dipilih ---
$data_grafik_terfilter = [];
// Dapatkan NIM dari ID mahasiswa yang dipilih untuk dicocokkan
$nim_terpilih = '';
foreach ($mahasiswa_list as $mhs) {
    if ($mhs['id'] == $mahasiswa_terpilih_id) {
        $nim_terpilih = $mhs['nim'];
        break;
    }
}

// Lakukan filter jika NIM ditemukan
if ($nim_terpilih) {
    foreach ($data_grafik_semua as $data) {
        if ($data['nim'] == $nim_terpilih) {
            $data_grafik_terfilter[] = $data;
        }
    }
}

// Siapkan data untuk JavaScript
$data_json = json_encode($data_grafik_terfilter);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Laporan - PilahPintar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/favicon.png" type="image/png">

    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>

    <style>
        /* Mengatur kotak pembungkus untuk grafik */
        .chart-container {
            width: 100%;
            max-width: 1500px;
            height: 600px;
            margin: 40px auto;
            padding: 30px;
            background-color: #f9fafb;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        /* Mengatur kotak untuk semua pilihan filter */
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Mengatur satu grup filter (label dan dropdown) */
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Mengatur tulisan label filter */
        .filter-group label {
            font-weight: 600;
            font-size: 0.9em;
        }

        /* Mengatur kotak dropdown */
        .filter-group select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
        }

        /* Mengatur tulisan "Tidak ada data" */
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #777;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include_once 'sidebar.php'; ?>
        <main class="content-area">
            <div class="page-header">
                <button id="openSidebarBtn" class="hamburger-icon-header" aria-label="Buka Menu">&#9776;</button>
                <h1>Statistik Mahasiswa Per Satuan Waktu (Minggu/Bulan)</h1>
            </div>

            <form id="filterForm" method="GET" action="statistik.php" class="filter-container">
                <div class="filter-group">
                    <label for="mahasiswaFilter">Mahasiswa:</label>
                    <select name="mahasiswa_id" id="mahasiswaFilter">
                        <?php foreach ($mahasiswa_list as $mhs): ?>
                            <option value="<?php echo $mhs['id']; ?>" <?php if ($mhs['id'] == $mahasiswa_terpilih_id) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($mhs['nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="rentangWaktu">Berdasarkan:</label>
                    <select name="rentang" id="rentangWaktu">
                        <option value="bulanan" <?php if ($rentang_waktu == 'bulanan') echo 'selected'; ?>>Bulanan</option>
                        <option value="mingguan" <?php if ($rentang_waktu == 'mingguan') echo 'selected'; ?>>Mingguan</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit" style="background-color: #007bff; padding: 8px 15px; font-size: 0.9em;">Terapkan Filter</button>
            </form>

            <?php if (isset($error_message)): ?>
                <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div id="statistikChart" class="chart-container"></div>
        </main>
    </div>

    <script src="script.js"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const dataFromPHP = <?php echo $data_json; ?>;
            var chartDom = document.getElementById('statistikChart');

            if (chartDom && dataFromPHP && dataFromPHP.length > 0) {
                var myChart = echarts.init(chartDom);
                var option;

                const labelWaktuPrefix = "<?php echo $label_waktu_prefix; ?>";

                // Olah data untuk format ECharts Stacked Bar Chart
                const legendData = [...new Set(dataFromPHP.map(item => item.jenis_sampah))];
                const xAxisData = [...new Set(dataFromPHP.map(item => labelWaktuPrefix + (item.minggu_ke || item.bulan_ke)))]
                    .sort((a, b) => parseInt(a.replace(labelWaktuPrefix, '')) - parseInt(b.replace(labelWaktuPrefix, '')));

                const seriesData = legendData.map(jenis => {
                    return {
                        name: jenis,
                        type: 'bar',
                        stack: 'Total', // Ini yang membuat batangnya bertumpuk
                        emphasis: {
                            focus: 'series'
                        },
                        data: xAxisData.map(waktuLabel => {
                            const dataPoint = dataFromPHP.find(d => {
                                const label = labelWaktuPrefix + (d.minggu_ke || d.bulan_ke);
                                return d.jenis_sampah === jenis && label === waktuLabel;
                            });
                            return dataPoint ? parseFloat(dataPoint.total_quantity) : 0;
                        })
                    };
                });

                option = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
                    legend: {
                        data: legendData,
                        bottom: 10,
                        type: 'scroll'
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '10%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        data: xAxisData
                    },
                    yAxis: {
                        type: 'value',
                        name: 'Total Kuantitas'
                    },
                    series: seriesData
                };

                option && myChart.setOption(option);
                window.addEventListener('resize', () => myChart.resize());

            } else if (chartDom) {
                chartDom.innerHTML = '<p class="no-data">Tidak ada data statistik untuk ditampilkan sesuai filter yang dipilih.</p>';
            }
        });
    </script>
    <?php
    if (isset($conn)) {
        $conn->close();
    }
    ?>
</body>

</html>