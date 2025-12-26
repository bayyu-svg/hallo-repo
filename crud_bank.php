<?php
require_once "include/session.php";
require_once "koneksi.php";

$action = $_GET['action'] ?? '';

if ($action === 'get') {
    $id = (int)$_GET['id'];
    echo json_encode(
        $conn->query("SELECT * FROM banks WHERE bank_id=$id")->fetch_assoc()
    );
    exit;
}

if ($action === 'add') {
    $conn->query("
        INSERT INTO banks
        (nama_bank, pemilik_rekening, nomor_rekening, saldo)
        VALUES (
            '{$_POST['nama_bank']}',
            '{$_POST['pemilik_rekening']}',
            '{$_POST['nomor_rekening']}',
            '{$_POST['saldo']}'
        )
    ");
    exit;
}

if ($action === 'update') {
    $id = (int)$_POST['bank_id'];
    $conn->query("
        UPDATE banks SET
        nama_bank='{$_POST['nama_bank']}',
        pemilik_rekening='{$_POST['pemilik_rekening']}',
        nomor_rekening='{$_POST['nomor_rekening']}',
        saldo='{$_POST['saldo']}'
        WHERE bank_id=$id
    ");
    exit;
}

if ($action === 'delete') {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM banks WHERE bank_id=$id");
    exit;
}
