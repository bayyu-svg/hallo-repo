<?php
require_once "include/session.php";

$requiredRoles = ['manager'];
require_once "include/auth.php";
require_once "koneksi.php";

$tgl_awal  = $_GET['tgl_awal']  ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

$data = $conn->query("
    SELECT 
        t.transaction_id,
        t.tanggal,
        t.nama,
        t.keterangan,
        t.nominal,
        f.file_id
    FROM transactions t
    LEFT JOIN transaction_files f 
        ON t.transaction_id = f.transaction_id
    WHERE DATE(t.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ORDER BY t.tanggal ASC
");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <?php include "include/head.php"; ?>
    <title>Laporan</title>
</head>

<body class="bg-gray-100">

    <?php include "include/navbar.php"; ?>

    <div class="flex min-h-screen">

        <?php include "include/sidebar.php"; ?>

        <main class="flex-1 p-8">

            <!-- TITLE -->
            <h1 class="text-2xl font-bold mb-6">
                Laporan <span class="text-gray-500 text-sm">Data Laporan</span>
            </h1>

            <!-- FILTER -->
            <div class="bg-white rounded shadow p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Filter Laporan</h2>

                <form method="GET" class="flex items-end gap-4">
                    <div>
                        <label class="block text-sm mb-1">Mulai Tanggal</label>
                        <input type="date" name="tgl_awal"
                            value="<?= $tgl_awal ?>"
                            class="border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Sampai Tanggal</label>
                        <input type="date" name="tgl_akhir"
                            value="<?= $tgl_akhir ?>"
                            class="border rounded px-3 py-2">
                    </div>

                    <button
                        class="bg-blue-600 text-white px-6 py-2 rounded">
                        TAMPILKAN
                    </button>
                </form>
            </div>

            <!-- TABLE -->
            <div class="bg-white rounded shadow overflow-x-auto">
                <h2 class="text-lg font-semibold p-4 border-b">
                    Laporan Pengeluaran
                </h2>

                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2">No</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Keterangan</th>
                            <th>Pengeluaran</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        while ($row = $data->fetch_assoc()):
                        ?>
                            <tr class="border-t text-center">
                                <td class="p-2"><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td>
                                    Rp. <?= number_format($row['nominal'], 0, ',', '.') ?>
                                </td>
                                <td class="flex justify-center gap-2 py-2">

                                    <?php if ($row['file_id']): ?>
                                        <!-- VIEW -->
                                        <a href="view_file.php?id=<?= $row['transaction_id'] ?>"
                                            class="bg-green-600 text-white px-2 py-1 rounded"
                                            title="Lihat">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <!-- DOWNLOAD -->
                                        <a href="download_file.php?id=<?= $row['transaction_id'] ?>"
                                            class="bg-blue-600 text-white px-2 py-1 rounded"
                                            title="Download">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">
                                            Tidak ada file
                                        </span>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>

</html>