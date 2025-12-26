<?php
require_once "include/session.php";
require_once "koneksi.php";

$transaction_id = (int)($_GET['id'] ?? 0);

if ($transaction_id <= 0) {
    die("ID transaksi tidak valid");
}

$q = $conn->query("
    SELECT * FROM transaction_files
    WHERE transaction_id = $transaction_id
    ORDER BY file_id DESC
    LIMIT 1
");

if (!$q || $q->num_rows === 0) {
    die("File tidak ditemukan");
}

$file = $q->fetch_assoc();

if (!file_exists($file['file_path'])) {
    die("File tidak ditemukan di server");
}

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"{$file['file_name']}\"");
header("Content-Length: " . filesize($file['file_path']));

readfile($file['file_path']);
exit;
