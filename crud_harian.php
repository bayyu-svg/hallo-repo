<?php
require_once "include/session.php";
require_once "koneksi.php";

$action = $_GET['action'] ?? '';

if ($action === 'get') {
    $id = $_GET['id'];
    $q = $conn->query("SELECT * FROM transactions WHERE transaction_id=$id");
    echo json_encode($q->fetch_assoc());
    exit;
}

if ($action === 'add') {
    $conn->query("
        INSERT INTO transactions (tanggal,nama,keterangan,nominal,bank_id,tipe)
        VALUES (
            '{$_POST['tanggal']}',
            '{$_POST['nama']}',
            '{$_POST['keterangan']}',
            '{$_POST['nominal']}',
            '{$_POST['bank_id']}',
            'harian'
        )
    ");
    exit;
}

if ($action === 'update') {
    $conn->query("
        UPDATE transactions SET
        tanggal='{$_POST['tanggal']}',
        nama='{$_POST['nama']}',
        keterangan='{$_POST['keterangan']}',
        nominal='{$_POST['nominal']}',
        bank_id='{$_POST['bank_id']}',
        tipe ='harian'
        WHERE transaction_id='{$_POST['transaction_id']}'
    ");
    exit;
}

if ($action === 'delete') {
    $id = (int)$_GET['id'];

    // 1. HAPUS FILE TERKAIT (JIKA ADA)
    $q = $conn->query("
        SELECT file_path FROM transaction_files
        WHERE transaction_id = $id
    ");

    while ($f = $q->fetch_assoc()) {
        if (!empty($f['file_path']) && file_exists($f['file_path'])) {
            unlink($f['file_path']); // hapus file fisik
        }
    }

    // 2. HAPUS DATA FILE DI DB
    $conn->query("
        DELETE FROM transaction_files
        WHERE transaction_id = $id
    ");

    // 3. BARU HAPUS TRANSAKSI
    $conn->query("
        DELETE FROM transactions
        WHERE transaction_id = $id
        AND tipe = 'harian'
    ");

    echo json_encode(['status' => 'ok']);
    exit;
}
