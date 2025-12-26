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
    <title>Profil</title>
</head>

<body class="bg-gray-100">

    <!-- NAVBAR -->
    <?php include "include/navbar.php"; ?>

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <?php include "include/sidebar.php"; ?>

        <!-- CONTENT -->
        <main class="flex-1 p-8">

            <div class="bg-white rounded shadow p-8">

                <h1 class="text-2xl font-bold mb-6">Profil</h1>

                <div class="flex gap-10 items-start">

                    <!-- FOTO -->
                    <div class="text-center">
                        <img
                            src="<?= $data['foto'] ? $data['foto'] : 'assets/user.png' ?>"
                            class="w-40 h-40 rounded bg-gray-300 object-cover mx-auto">

                        <a href="edit_profil.php"
                            class="inline-block mt-4 px-5 py-2 border rounded hover:bg-gray-100">
                            Edit Profil
                        </a>
                    </div>

                    <!-- DATA -->
                    <div class="space-y-4 text-gray-800">
                        <div>
                            <p class="font-semibold">Nama Lengkap</p>
                            <p><?= htmlspecialchars($data['nama_lengkap']) ?></p>
                        </div>

                        <div>
                            <p class="font-semibold">NIP</p>
                            <p><?= htmlspecialchars($data['nip']) ?></p>
                        </div>

                        <div>
                            <p class="font-semibold">No HP</p>
                            <p><?= htmlspecialchars($data['no_hp']) ?></p>
                        </div>

                        <div>
                            <p class="font-semibold">Alamat</p>
                            <p><?= nl2br(htmlspecialchars($data['alamat'])) ?></p>
                        </div>

                        <div>
                            <p class="font-semibold">Role</p>
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded">
                                <?= ucfirst($data['role']) ?>
                            </span>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </div>

</body>

</html>