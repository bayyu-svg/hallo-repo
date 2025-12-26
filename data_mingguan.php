<?php
require_once "include/session.php";

$requiredRoles = ['admin'];
require_once "include/auth.php";
require_once "koneksi.php";

$limit  = 5;
$page   = max((int)($_GET['page'] ?? 1), 1);
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$whereSearch = '';

if ($search !== '') {
    $safe = $conn->real_escape_string($search);
    $whereSearch = "AND (t.nama LIKE '%$safe%' OR t.keterangan LIKE '%$safe%')";
}

$data = $conn->query("
    SELECT t.*, b.nama_bank
    FROM transactions t
    LEFT JOIN banks b ON t.bank_id = b.bank_id
    WHERE t.tipe = 'mingguan'
    $whereSearch
    ORDER BY t.tanggal DESC
    LIMIT $limit OFFSET $offset
");

$totalData = $conn->query("
    SELECT COUNT(*) total
    FROM transactions t
    WHERE t.tipe = 'mingguan'
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
    <title>Data Mingguan</title>
</head>

<body class="bg-gray-100">

    <?php include "include/navbar.php"; ?>

    <div class="flex min-h-screen">
        <?php include "include/sidebar.php"; ?>

        <main class="flex-1 p-8">

            <h1 class="text-2xl font-bold mb-6">
                Data <span class="text-gray-500 text-sm">Transaksi Mingguan</span>
            </h1>

            <!-- TOP BAR -->
            <div class="flex justify-between items-start mb-6">

                <div class="border rounded p-4 bg-white w-64">
                    <p class="text-sm text-gray-500">Saldo Rekening</p>
                    <p class="font-bold text-lg">
                        Rp. <?= number_format($saldo_akhir, 0, ',', '.') ?>
                    </p>
                </div>

                <div class="flex flex-col items-end gap-3">
                    <button onclick="openModal()"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                        + Tambah Transaksi
                    </button>

                    <form method="GET">
                        <input type="text" name="search"
                            value="<?= htmlspecialchars($search) ?>"
                            placeholder="Cari nama / keterangan..."
                            class="border rounded px-3 py-2 w-64">
                    </form>
                </div>

            </div>

            <!-- TABLE -->
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>No</th>
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
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td><?= htmlspecialchars($row['nama_bank']) ?></td>
                                <td>Rp. <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                <td class="flex justify-center gap-2 py-2">

                                    <a href="view_file.php?id=<?= $row['transaction_id'] ?>"
                                        class="bg-green-600 text-white px-2 py-1 rounded">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <a href="download_file.php?id=<?= $row['transaction_id'] ?>"
                                        class="bg-blue-600 text-white px-2 py-1 rounded">
                                        <i class="fa-solid fa-download"></i>
                                    </a>

                                    <button onclick="editData(<?= $row['transaction_id'] ?>)"
                                        class="bg-yellow-400 text-white px-2 py-1 rounded">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <button onclick="hapusData(<?= $row['transaction_id'] ?>)"
                                        class="bg-red-600 text-white px-2 py-1 rounded">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                </td>
                            </tr>
                        <?php endwhile; ?>

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

    <!-- MODAL -->
    <div id="modal"
        class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-xl rounded p-6">

            <h2 id="modalTitle" class="text-lg font-bold mb-4">
                Tambah Transaksi Mingguan
            </h2>

            <form id="form" enctype="multipart/form-data">

                <input type="hidden" name="transaction_id" id="transaction_id">

                <label>Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" required class="w-full border p-2 mb-2">

                <label>Nama</label>
                <input type="text" name="nama" id="nama" required class="w-full border p-2 mb-2">

                <label>Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="w-full border p-2 mb-2"></textarea>

                <label>Nominal Pengeluaran</label>
                <input type="number" name="nominal" id="nominal" required class="w-full border p-2 mb-2">

                <label>Upload File</label>
                <input type="file" name="file" class="mb-2">
                <small class="text-red-500">*pdf / excel</small>

                <label>Rekening Bank</label>
                <select name="bank_id" id="bank_id" required class="w-full border p-2 mb-4">
                    <option value="">- Pilih -</option>
                    <?php while ($b = $banks->fetch_assoc()): ?>
                        <option value="<?= $b['bank_id'] ?>"><?= $b['nama_bank'] ?></option>
                    <?php endwhile; ?>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border rounded">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                </div>

            </form>
        </div>
    </div>

    <!-- JS -->
    <script>
        function openModal() {
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('form').reset();
            document.getElementById('transaction_id').value = '';
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }

        function editData(id) {
            fetch('crud_mingguan.php?action=get&id=' + id)
                .then(r => r.json())
                .then(d => {
                    openModal();
                    document.getElementById('transaction_id').value = d.transaction_id;
                    tanggal.value = d.tanggal;
                    nama.value = d.nama;
                    keterangan.value = d.keterangan;
                    nominal.value = d.nominal;
                    bank_id.value = d.bank_id;
                });
        }

        form.addEventListener('submit', e => {
            e.preventDefault();
            const action = transaction_id.value ? 'update' : 'add';
            fetch('crud_mingguan.php?action=' + action, {
                method: 'POST',
                body: new FormData(form)
            }).then(() => location.reload());
        });

        function hapusData(id) {
            if (confirm('Hapus data ini?')) {
                fetch('crud_mingguan.php?action=delete&id=' + id)
                    .then(() => location.reload());
            }
        }
    </script>

</body>

</html>