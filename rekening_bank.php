<?php
require_once "include/session.php";

$requiredRoles = ['admin'];
require_once "include/auth.php";
require_once "koneksi.php";

/* ======================
   PAGINATION
====================== */
$limit  = 5;
$page   = max((int)($_GET['page'] ?? 1), 1);
$offset = ($page - 1) * $limit;

/* ======================
   SEARCH
====================== */
$search = $_GET['search'] ?? '';
$where  = '';

if ($search !== '') {
    $safe = $conn->real_escape_string($search);
    $where = "WHERE nama_bank LIKE '%$safe%' 
              OR pemilik_rekening LIKE '%$safe%' 
              OR nomor_rekening LIKE '%$safe%'";
}

/* ======================
   DATA BANK
====================== */
$data = $conn->query("
    SELECT * FROM banks
    $where
    ORDER BY bank_id DESC
    LIMIT $limit OFFSET $offset
");

$totalData = $conn->query("
    SELECT COUNT(*) total FROM banks $where
")->fetch_assoc()['total'];

$totalPage = max(ceil($totalData / $limit), 1);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <?php include "include/head.php"; ?>
    <title>Rekening Bank</title>
</head>

<body class="bg-gray-100">
    <?php include "include/navbar.php"; ?>

    <div class="flex min-h-screen">
        <?php include "include/sidebar.php"; ?>

        <main class="flex-1 p-8">

            <h1 class="text-2xl font-bold mb-6">
                Bank <span class="text-gray-500 text-sm">Data Bank</span>
            </h1>

            <!-- TOP BAR -->
            <div class="flex justify-between items-start mb-6">

                <button onclick="openModal()"
                    class="bg-blue-600 text-white px-4 py-2 rounded">
                    + Tambah Bank
                </button>

                <form method="GET">
                    <input type="text" name="search"
                        value="<?= htmlspecialchars($search) ?>"
                        placeholder="Search"
                        class="border rounded px-3 py-2 w-64">
                </form>
            </div>

            <!-- TABLE -->
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>No</th>
                            <th>Nama Bank</th>
                            <th>Pemilik Rekening</th>
                            <th>Nomor Rekening</th>
                            <th>Saldo</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offset + 1; ?>
                        <?php while ($row = $data->fetch_assoc()): ?>
                            <tr class="border-t text-center">
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_bank']) ?></td>
                                <td><?= htmlspecialchars($row['pemilik_rekening']) ?></td>
                                <td><?= htmlspecialchars($row['nomor_rekening']) ?></td>
                                <td>Rp. <?= number_format($row['saldo'], 0, ',', '.') ?></td>
                                <td class="flex justify-center gap-2 py-2">
                                    <button onclick="editData(<?= $row['bank_id'] ?>)"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <button onclick="hapusData(<?= $row['bank_id'] ?>)"
                                        class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded">
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

    <!-- ======================
   MODAL
====================== -->
    <div id="modal"
        class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-xl rounded p-6">

            <h2 id="modalTitle" class="text-lg font-bold mb-4">
                Tambah Data Bank
            </h2>

            <form id="form">
                <input type="hidden" name="bank_id" id="bank_id">

                <label>Nama Bank</label>
                <input type="text" name="nama_bank" id="nama_bank"
                    class="w-full border p-2 mb-3" required>

                <label>Pemilik Rekening</label>
                <input type="text" name="pemilik_rekening" id="pemilik_rekening"
                    class="w-full border p-2 mb-3" required>

                <label>Nomor Rekening</label>
                <input type="text" name="nomor_rekening" id="nomor_rekening"
                    class="w-full border p-2 mb-3" required>

                <label>Nominal Saldo</label>
                <input type="number" name="saldo" id="saldo"
                    class="w-full border p-2 mb-4" required>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border rounded">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function openModal() {
            modal.classList.remove('hidden');
            form.reset();
            bank_id.value = '';
            modalTitle.innerText = 'Tambah Data Bank';
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        function editData(id) {
            fetch('crud_bank.php?action=get&id=' + id)
                .then(r => r.json())
                .then(d => {
                    openModal();
                    modalTitle.innerText = 'Edit Data Bank';
                    bank_id.value = d.bank_id;
                    nama_bank.value = d.nama_bank;
                    pemilik_rekening.value = d.pemilik_rekening;
                    nomor_rekening.value = d.nomor_rekening;
                    saldo.value = d.saldo;
                });
        }

        form.addEventListener('submit', e => {
            e.preventDefault();
            const action = bank_id.value ? 'update' : 'add';

            fetch('crud_bank.php?action=' + action, {
                method: 'POST',
                body: new FormData(form)
            }).then(() => location.reload());
        });

        function hapusData(id) {
            if (confirm('Hapus data bank ini?')) {
                fetch('crud_bank.php?action=delete&id=' + id)
                    .then(() => location.reload());
            }
        }
    </script>

</body>

</html>