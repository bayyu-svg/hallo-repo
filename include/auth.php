<?php
// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default: hanya admin yang boleh
$allowedRoles = ['admin'];

// Kalau halaman butuh role lain, halaman tsb set variabel ini
if (isset($requiredRoles)) {
    $allowedRoles = $requiredRoles;
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    http_response_code(403);
    echo "Akses ditolak";
    exit;
}