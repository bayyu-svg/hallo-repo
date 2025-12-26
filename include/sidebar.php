<?php
$foto = $_SESSION['foto'] ?? null;

$hasFoto = !empty($foto) && file_exists($foto);
?>

<aside class="w-64 bg-red-700 text-white min-h-screen flex flex-col">

    <!-- PROFILE -->
    <div class="p-6 flex items-center gap-3 border-b border-red-600">

        <?php if ($hasFoto): ?>
            <!-- FOTO PROFIL -->
            <img src="<?= $foto ?>"
                class="w-12 h-12 rounded-full object-cover border-2 border-white">
        <?php else: ?>
            <!-- INISIAL (FALLBACK) -->
            <div class="w-12 h-12 rounded-full bg-white
                        flex items-center justify-center
                        text-red-700 font-bold text-lg">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)); ?>
            </div>
        <?php endif; ?>

        <div>
            <p class="font-semibold leading-tight">
                <?= htmlspecialchars($_SESSION['username']); ?>
            </p>
            <p class="text-xs text-red-200">
                <?= ucfirst($_SESSION['role']); ?>
            </p>
        </div>
    </div>

    <!-- NAVIGATION -->
    <nav class="flex-1 p-6 space-y-2">

        <p class="text-xs text-red-200 uppercase mb-3">
            Main Navigation
        </p>

        <!-- DASHBOARD (ADMIN & MANAGER) -->
        <a href="dashboard.php"
            class="flex items-center gap-3 px-3 py-2 rounded hover:bg-red-600">
            <i class="fa-solid fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- ================= ADMIN MENU ================= -->
        <?php if ($_SESSION['role'] === 'admin'): ?>

            <a href="data_harian.php"
                class="flex items-center gap-3 px-3 py-2 rounded hover:bg-red-600">
                <i class="fa-solid fa-calendar-day w-5"></i>
                <span>Data Harian</span>
            </a>

            <a href="data_mingguan.php"
                class="flex items-center gap-3 px-3 py-2 rounded hover:bg-red-600">
                <i class="fa-solid fa-calendar-week w-5"></i>
                <span>Data Mingguan</span>
            </a>

            <a href="rekening_bank.php"
                class="flex items-center gap-3 px-3 py-2 rounded hover:bg-red-600">
                <i class="fa-solid fa-building-columns w-5"></i>
                <span>Rekening Bank</span>
            </a>

            <a href="profil.php"
                class="flex items-center gap-3 px-3 py-2 rounded hover:bg-red-600">
                <i class="fa-solid fa-user w-5"></i>
                <span>Profil</span>
            </a>

        <?php endif; ?>

        <!-- ================= MANAGER MENU ================= -->
        <?php if ($_SESSION['role'] === 'manager'): ?>

            <a href="laporan.php"
                class="flex items-center gap-3 px-3 py-2 rounded hover:bg-red-600">
                <i class="fa-solid fa-file-lines w-5"></i>
                <span>Laporan</span>
            </a>

            <a href="profil.php"
                class="flex items-center gap-3 px-3 py-2 rounded hover:bg-red-600">
                <i class="fa-solid fa-user w-5"></i>
                <span>Profil</span>
            </a>

        <?php endif; ?>

    </nav>

    <!-- LOGOUT -->
    <div class="p-6">
        <a href="logout.php"
            class="block text-center border border-white py-2 rounded
                  hover:bg-white hover:text-red-700">
            Logout
        </a>
    </div>

</aside>