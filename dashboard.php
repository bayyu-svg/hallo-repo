<?php
session_start();
require_once "koneksi.php";
require_once "include/session.php";

$requiredRoles = ['admin', 'manager'];
require_once "include/auth.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   PENGELUARAN
========================= */

// Harian (semua bank)
$hari_ini = $conn->query("
    SELECT IFNULL(SUM(nominal),0) total
    FROM transactions
    WHERE tipe='harian'
      AND DATE(tanggal)=CURDATE()
")->fetch_assoc()['total'];

// Mingguan (semua bank)
$minggu_ini = $conn->query("
    SELECT IFNULL(SUM(nominal),0) total
    FROM transactions
    WHERE tipe='mingguan'
      AND YEARWEEK(tanggal,1)=YEARWEEK(CURDATE(),1)
")->fetch_assoc()['total'];

/* =========================
   SALDO PER BANK (INTI)
========================= */

$banks = $conn->query("
    SELECT 
        b.bank_id,
        b.nama_bank,
        b.saldo AS saldo_awal,
        IFNULL(SUM(t.nominal),0) AS total_pengeluaran,
        (b.saldo - IFNULL(SUM(t.nominal),0)) AS saldo_akhir
    FROM banks b
    LEFT JOIN transactions t ON t.bank_id = b.bank_id
    GROUP BY b.bank_id
");

/* =========================
   CALENDAR EVENTS
========================= */

$events = [];
$q = $conn->query("SELECT tanggal, nominal FROM transactions");

while ($row = $q->fetch_assoc()) {
    $events[] = [
        'title' => 'Rp. ' . number_format($row['nominal'], 0, ',', '.'),
        'start' => $row['tanggal']
    ];
}

/* =========================
   GRAFIK BULANAN
========================= */

$grafik = [];
$q4 = $conn->query("
    SELECT MONTH(tanggal) bulan, SUM(nominal) total
    FROM transactions
    WHERE YEAR(tanggal)=YEAR(CURDATE())
    GROUP BY MONTH(tanggal)
");

while ($row = $q4->fetch_assoc()) {
    $grafik[$row['bulan']] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <?php include "include/head.php"; ?>
    <title>Dashboard</title>
</head>

<body class="bg-gray-100">

    <?php include "include/navbar.php"; ?>

    <div class="flex min-h-screen">

        <?php include "include/sidebar.php"; ?>

        <main class="flex-1 p-8">

            <!-- TITLE -->
            <h1 class="text-2xl font-bold mb-8">
                Dashboard <span class="text-gray-500 text-sm">Control Panel</span>
            </h1>

            <!-- SUMMARY -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                <div class="bg-red-600 text-white p-6 rounded shadow">
                    <p class="text-lg font-bold">
                        Rp. <?= number_format($hari_ini, 0, ',', '.') ?>
                    </p>
                    <span>Pengeluaran Hari Ini</span>
                </div>

                <div class="bg-red-600 text-white p-6 rounded shadow">
                    <p class="text-lg font-bold">
                        Rp. <?= number_format($minggu_ini, 0, ',', '.') ?>
                    </p>
                    <span>Pengeluaran Minggu Ini</span>
                </div>

            </div>

            <!-- SALDO PER BANK -->
            <h2 class="text-lg font-semibold mb-4">Saldo Rekening</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <?php while ($b = $banks->fetch_assoc()): ?>
                    <div class="bg-white p-5 rounded shadow">
                        <p class="text-sm text-gray-500">Rekening</p>
                        <p class="font-semibold"><?= htmlspecialchars($b['nama_bank']) ?></p>

                        <p class="text-2xl font-bold text-green-600 mt-2">
                            Rp. <?= number_format($b['saldo_akhir'], 0, ',', '.') ?>
                        </p>

                        <p class="text-sm text-gray-600 mt-2">
                            Total pengeluaran dari rekening ini
                        </p>
                        <p class="text-sm font-semibold text-red-600">
                            Rp. <?= number_format($b['total_pengeluaran'], 0, ',', '.') ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- GRAFIK & KALENDER -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-2 bg-white p-6 rounded shadow">
                    <h3 class="font-semibold mb-4">
                        Grafik Biaya Operasional Per Bulan
                    </h3>
                    <canvas id="chart"></canvas>
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <div id="calendar"></div>
                </div>

            </div>

        </main>
    </div>

    <!-- CALENDAR -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const events = <?= json_encode($events); ?>;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 400,
                events: events
            });

            calendar.render();
        });
    </script>

    <!-- CHART -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('chart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($grafik)) ?>,
                datasets: [{
                    label: 'Pengeluaran',
                    data: <?= json_encode(array_values($grafik)) ?>,
                    backgroundColor: '#dc2626'
                }]
            }
        });
    </script>

</body>

</html>