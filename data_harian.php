<?php
require_once "include/session.php";

$requiredRoles = ['admin'];
require_once "include/auth.php";
require_once "koneksi.php";

$limit  = 5;
$page   = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$whereSearch = '';

if ($search !== '') {
    $safe = $conn->real_escape_string($search);
    $whereSearch = "
        AND (
            t.nama LIKE '%$safe%' OR
            t.keterangan LIKE '%$safe%'
        )
    ";
}

$data = $conn->query("
    SELECT t.*, b.nama_bank
    FROM transactions t
    LEFT JOIN banks b ON t.bank_id = b.bank_id
    WHERE t.tipe = 'harian'
    $whereSearch
    ORDER BY t.tanggal DESC
    LIMIT $limit OFFSET $offset
");

/* TOTAL DATA */
$totalData = $conn->query("
    SELECT COUNT(*) total
    FROM transactions t
    WHERE t.tipe = 'harian'
    $whereSearch
")->fetch_assoc()['total'];

$totalPage = max(ceil($totalData / $limit), 1);

$banks = $conn->query("SELECT * FROM banks");

$saldo_awal = $conn->query("
    SELECT IFNULL(SUM(saldo),0) total FROM banks
")->fetch_assoc()['total'];

$total_pengeluaran = $conn->query("
    SELECT IFNULL(SUM(nominal),0) total FROM transactions
")->fetch_assoc()['total'];

$saldo_akhir = $saldo_awal - $total_pengeluaran;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <?php include "include/head.php"; ?>
    <title>Data Harian</title>
</head>

<body class="bg-gray-100">

    <!-- NAVBAR -->
    <?php include "include/navbar.php"; ?>

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <?php include "include/sidebar.php"; ?>

        <!-- CONTENT -->
        <main class="flex-1 p-8">

            <!-- TITLE -->
            <h1 class="text-2xl font-bold mb-6">
                Data <span class="text-gray-500 text-sm">Harian</span>
            </h1>

            <!-- TOP BAR -->
            <div class="flex justify-between items-start mb-6">

                <!-- SALDO -->
                <div class="border rounded p-4 bg-white w-64">
                    <p class="text-sm text-gray-500">Saldo Rekening</p>
                    <p class="font-bold text-lg">
                        Rp. <?= number_format($saldo_akhir, 0, ',', '.') ?>
                    </p>
                </div>

                <!-- ACTION -->
                <div class="flex flex-col items-end gap-3">

                    <button onclick="openModal()"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                        + Tambah Transaksi
                    </button>

                    <!-- SEARCH -->
                    <form method="GET">
                        <input type="text"
                            name="search"
                            value="<?= htmlspecialchars($search) ?>"
                            placeholder="Cari data..."
                            class="border rounded px-3 py-2 w-64">
                    </form>

                </div>

            </div>

            <!-- TABLE -->
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2">No</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Keterangan</th>
                            <th>Bank</th>
                            <th>Pengeluaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offset + 1; ?>
                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr class="border-t text-center">
                                <td class="p-2"><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td><?= htmlspecialchars($row['nama_bank']) ?></td>
                                <td>Rp. <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                <td class="flex justify-center gap-2 py-2">
                                    <button onclick="editData(<?= $row['transaction_id'] ?>)"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <button onclick="hapusData(<?= $row['transaction_id'] ?>)"
                                        class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>
            <!-- PAGINATION -->
            <div class="flex justify-end mt-6 gap-1">

                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>"
                        class="px-3 py-1 border rounded">Previous</a>
                <?php endif; ?>

                <span class="px-3 py-1 bg-gray-200 rounded">
                    <?= $page ?> / <?= $totalPage ?>
                </span>

                <?php if ($page < $totalPage): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>"
                        class="px-3 py-1 border rounded">Next</a>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!--MODAL TAMBAH / EDIT-->
    <div id="modal"
        class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-xl rounded p-6">

            <h2 id="modalTitle"
                class="text-lg font-bold mb-4">
                Tambah Data Harian
            </h2>

            <form id="form">

                <input type="hidden" name="transaction_id" id="transaction_id">

                <div class="mb-3">
                    <label class="block mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal"
                        class="w-full border rounded p-2" required>
                </div>

                <div class="mb-3">
                    <label class="block mb-1">Nama</label>
                    <input type="text" name="nama" id="nama"
                        class="w-full border rounded p-2" required>
                </div>

                <div class="mb-3">
                    <label class="block mb-1">Keterangan</label>
                    <textarea name="keterangan" id="keterangan"
                        class="w-full border rounded p-2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="block mb-1">Nominal</label>
                    <input type="number" name="nominal" id="nominal"
                        class="w-full border rounded p-2" required>
                </div>

                <div class="mb-3">
                    <label class="block mb-1">Rekening Bank</label>
                    <select name="bank_id" id="bank_id"
                        class="w-full border rounded p-2" required>
                        <option value="">- Pilih -</option>
                        <?php while ($b = $banks->fetch_assoc()): ?>
                            <option value="<?= $b['bank_id'] ?>">
                                <?= $b['nama_bank'] ?>
                            </option>
                        <?php endwhile ?>
                    </select>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button"
                        onclick="closeModal()"
                        class="px-4 py-2 border rounded">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!--JAVASCRIPT-->
    <script>
        function openModal(isEdit = false) {
            document.getElementById('modal').classList.remove('hidden');

            if (!isEdit) {
                document.getElementById('form').reset();
                document.getElementById('transaction_id').value = '';
                document.getElementById('modalTitle').innerText = 'Tambah Data Harian';
            }
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }

        function editData(id) {
            fetch('crud_harian.php?action=get&id=' + id)
                .then(res => res.json())
                .then(d => {
                    openModal(true);
                    document.getElementById('modalTitle').innerText = 'Edit Data Harian';

                    document.getElementById('transaction_id').value = d.transaction_id;
                    document.getElementById('tanggal').value = d.tanggal;
                    document.getElementById('nama').value = d.nama;
                    document.getElementById('keterangan').value = d.keterangan;
                    document.getElementById('nominal').value = d.nominal;
                    document.getElementById('bank_id').value = d.bank_id;
                });
        }

        document.getElementById('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const action = document.getElementById('transaction_id').value ? 'update' : 'add';

            fetch('crud_harian.php?action=' + action, {
                method: 'POST',
                body: new FormData(this)
            }).then(() => {
                closeModal();
                location.reload();
            });
        });

        function hapusData(id) {
            if (confirm('Hapus data ini?')) {
                fetch('crud_harian.php?action=delete&id=' + id)
                    .then(() => location.reload());
            }
        }
    </script>

    <script>
        document.querySelector('input[name="search"]').addEventListener('input', function() {
            const url = new URL(window.location);
            url.searchParams.set('search', this.value);
            url.searchParams.set('page', 1);
            window.history.replaceState({}, '', url);
        });
    </script>

</body>

</html>