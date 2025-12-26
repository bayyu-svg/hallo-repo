<?php
session_start();
include "koneksi.php";
include "include/session.php";

$requiredRoles = ['admin', 'manager'];
include "include/auth.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Pengeluaran hari ini
$q1 = $conn->query("
    SELECT IFNULL(SUM(nominal),0) total
    FROM transactions
    WHERE tipe = 'harian'
      AND DATE(tanggal) = CURDATE()
");
$hari_ini = $q1->fetch_assoc()['total'];

// Pengeluaran minggu ini
$q2 = $conn->query("
    SELECT IFNULL(SUM(nominal),0) total
    FROM transactions
    WHERE tipe = 'mingguan'
      AND YEARWEEK(tanggal,1) = YEARWEEK(CURDATE(),1)
");
$minggu_ini = $q2->fetch_assoc()['total'];

// Saldo rekening
$saldo_awal = $conn->query("
    SELECT IFNULL(SUM(saldo),0) total FROM banks
")->fetch_assoc()['total'];

$total_pengeluaran = $conn->query("
    SELECT IFNULL(SUM(nominal),0) total FROM transactions
")->fetch_assoc()['total'];

$saldo = $saldo_awal - $total_pengeluaran;

// Tanggal
$events = [];
$q = $conn->query("SELECT tanggal, nominal FROM transactions");

while ($row = $q->fetch_assoc()) {
    $events[] = [
        'title' => 'Rp. ' . number_format($row['nominal'], 0, ',', '.'),
        'start' => $row['tanggal']
    ];
}

// Grafik bulanan
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

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-8">

            <!-- TITLE -->
            <h1 class="text-2xl font-bold mb-8">
                Dashboard <span class="text-gray-500 text-sm">Control Panel</span>
            </h1>

            <!-- CARD -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

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

                <div class="bg-red-600 text-white p-6 rounded shadow">
                    <p class="text-lg font-bold">
                        Rp. <?= number_format($saldo, 0, ',', '.') ?>
                    </p>
                    <span>Saldo Rekening</span>
                </div>

            </div>

            <!-- GRAFIK + KALENDER -->
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

    <!-- Tanggal -->
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