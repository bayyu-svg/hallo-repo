<?php
require_once "include/session.php";
require_once "koneksi.php";

$admin_id = $_SESSION['admin_id'];

$data = $conn->query("
    SELECT * FROM admin WHERE admin_id = $admin_id
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <?php include "include/head.php"; ?>
    <title>Edit Profil</title>
</head>

<body class="bg-gray-100">

    <?php include "include/navbar.php"; ?>

    <div class="flex min-h-screen">
        <?php include "include/sidebar.php"; ?>

        <main class="flex-1 p-8 flex justify-center items-start">

            <!-- CARD -->
            <div class="bg-white w-full max-w-2xl rounded shadow p-8">

                <h1 class="text-2xl font-bold mb-6">Edit Profil</h1>

                <form action="update_profil.php" method="POST" enctype="multipart/form-data">

                    <!-- NAMA -->
                    <div class="mb-4">
                        <label class="font-semibold block mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap"
                            value="<?= htmlspecialchars($data['nama_lengkap']) ?>"
                            class="w-full border rounded p-2" required>
                    </div>

                    <!-- NIP -->
                    <div class="mb-4">
                        <label class="font-semibold block mb-1">NIP</label>
                        <input type="text" name="nip"
                            value="<?= htmlspecialchars($data['nip']) ?>"
                            class="w-full border rounded p-2">
                    </div>

                    <!-- NO HP -->
                    <div class="mb-4">
                        <label class="font-semibold block mb-1">No Hp</label>
                        <input type="text" name="no_hp"
                            value="<?= htmlspecialchars($data['no_hp']) ?>"
                            class="w-full border rounded p-2">
                    </div>

                    <!-- ALAMAT -->
                    <div class="mb-4">
                        <label class="font-semibold block mb-1">Alamat</label>
                        <textarea name="alamat"
                            class="w-full border rounded p-2"
                            rows="3"><?= htmlspecialchars($data['alamat']) ?></textarea>
                    </div>

                    <!-- FOTO -->
                    <div class="mb-6">
                        <label class="font-semibold block mb-1">Upload Foto</label>
                        <input type="file" name="foto" accept=".jpg,.jpeg"
                            class="block w-full border rounded p-2">
                        <small class="text-red-500">
                            File yang diperbolehkan *.jpg | Max 2MB
                        </small>
                    </div>

                    <!-- BUTTON -->
                    <div class="flex justify-end gap-3">
                        <a href="profil.php"
                            class="px-4 py-2 border rounded hover:bg-gray-100">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>

                </form>

            </div>

        </main>
    </div>

</body>

</html>