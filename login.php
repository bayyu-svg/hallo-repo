<?php
date_default_timezone_set('Asia/Makassar');
session_start();
include_once("koneksi.php");

function authenticateAdmin($username, $password, $conn)
{
    $query = "SELECT * FROM admin WHERE username = ? LIMIT 1";
    $stmt  = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Cek password MD5
        if (md5($password) === $admin['password']) {
            return $admin;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $user = authenticateAdmin($username, $password, $conn);

    if ($user) {
        // SESSION
        $_SESSION['admin_id'] = $user['admin_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['foto']     = $user['foto'];

        // REDIRECT BERDASARKAN ROLE
        if ($user['role'] === 'admin') {
            header("Location: dashboard.php");
        } elseif ($user['role'] === 'manager') {
            header("Location: laporan.php");
        }
        exit;
    } else {
        $error_message = "Username atau password salah.";
    }

    // Log login
    $logMessage  = date('Y-m-d H:i:s') . " | Username: $username | $message" . PHP_EOL;
    file_put_contents('login_logs.txt', $logMessage, FILE_APPEND);

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- TAILWIND CSS (WAJIB) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- OPTIONAL ICON -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <title>Login</title>
</head>

<body class="antialiased">
    <main class="h-screen w-screen">
        <div class="grid grid-cols-1 md:grid-cols-2 h-full">

            <!-- LEFT : IMAGE -->
            <div class="hidden md:block bg-cover bg-center"
                style="background-image: url('assets/img/foto/gambar_telkom.jpg');">
            </div>

            <!-- RIGHT : LOGIN FORM -->
            <div class="bg-red-700 flex items-center justify-center px-6">
                <div class="w-full max-w-md text-white">

                    <!-- TITLE -->
                    <h1 class="text-3xl font-bold text-center mb-2">
                        LOG IN SYSTEM
                    </h1>
                    <p class="text-center text-sm mb-10">
                        Pencatatan dan Monitoring Biaya Operasional
                    </p>

                    <!-- ERROR MESSAGE -->
                    <?php if (!empty($error_message)): ?>
                        <div class="mb-4 p-3 text-sm text-red-800 bg-red-100 rounded">
                            <?= $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- FORM -->
                    <form method="POST" class="space-y-6">

                        <!-- USERNAME -->
                        <div>
                            <label class="block mb-2 font-medium">Username</label>
                            <input type="text" name="username"
                                class="w-full px-4 py-3 rounded-lg bg-red-500 placeholder-white
                                       text-white focus:outline-none focus:ring-2 focus:ring-white"
                                placeholder="Masukkan username" required>
                        </div>

                        <!-- PASSWORD -->
                        <div>
                            <label class="block mb-2 font-medium">Password</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-3 rounded-lg bg-red-500 placeholder-white
                                       text-white focus:outline-none focus:ring-2 focus:ring-white"
                                placeholder="Masukkan password" required>
                        </div>

                        <!-- BUTTON -->
                        <button type="submit"
                            class="w-full py-3 bg-red-200 text-red-800 font-bold
                                   rounded-lg hover:bg-red-300 transition">
                            LOGIN
                        </button>

                    </form>
                </div>
            </div>

        </div>
    </main>
</body>

</html>