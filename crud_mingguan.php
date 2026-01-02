<?php
require_once "include/session.php";
require_once "koneksi.php";

$action = $_GET['action'] ?? '';

if ($action === 'get') {
    $id = $_GET['id'];
    echo json_encode(
        $conn->query("SELECT * FROM transactions WHERE transaction_id=$id")
            ->fetch_assoc()
    );
    exit;
}

if ($action === 'add') {

    $conn->query("
        INSERT INTO transactions
        (tanggal, nama, keterangan, nominal, bank_id, tipe)
        VALUES (
            '{$_POST['tanggal']}',
            '{$_POST['nama']}',
            '{$_POST['keterangan']}',
            '{$_POST['nominal']}',
            '{$_POST['bank_id']}',
            'mingguan'
        )
    ");

    $tid = $conn->insert_id;

    if (!empty($_FILES['file']['name'])) {

        $allowedExt = ['pdf', 'xls', 'xlsx'];
        $maxSize = 5 * 1024 * 1024;

        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            die("Tipe file tidak diizinkan (PDF / Excel)");
        }

        if ($_FILES['file']['size'] > $maxSize) {
            die("Ukuran file maksimal 5MB");
        }

        $name = time() . "_" . basename($_FILES['file']['name']);
        $path = "uploads/mingguan/" . $name;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
            die("Gagal upload file");
        }

        $conn->query("
            INSERT INTO transaction_files
            (transaction_id, file_name, file_path, file_type)
            VALUES (
                $tid,
                '$name',
                '$path',
                '$ext'
            )
        ");
    }

    exit;
}

if ($action === 'update') {

    $id = $_POST['transaction_id'];

    $conn->query("
        UPDATE transactions SET
            tanggal    = '{$_POST['tanggal']}',
            nama       = '{$_POST['nama']}',
            keterangan = '{$_POST['keterangan']}',
            nominal    = '{$_POST['nominal']}',
            bank_id    = '{$_POST['bank_id']}',
            tipe       = 'mingguan'
        WHERE transaction_id = $id
    ");

    if (!empty($_FILES['file']['name'])) {

        $allowedExt = ['pdf', 'xls', 'xlsx'];
        $maxSize = 5 * 1024 * 1024;

        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            die("Tipe file tidak diizinkan");
        }

        if ($_FILES['file']['size'] > $maxSize) {
            die("File terlalu besar");
        }

        $name = time() . "_" . basename($_FILES['file']['name']);
        $path = "uploads/mingguan/" . $name;

        move_uploaded_file($_FILES['file']['tmp_name'], $path);

        // hapus file lama (opsional tapi recommended)
        $conn->query("DELETE FROM transaction_files WHERE transaction_id = $id");

        $conn->query("
            INSERT INTO transaction_files
            (transaction_id, file_name, file_path, file_type)
            VALUES (
                $id,
                '$name',
                '$path',
                '$ext'
            )
        ");
    }

    exit;
}

if ($action === 'delete') {
    $id = $_GET['id'];
    $conn->query("DELETE FROM transaction_files WHERE transaction_id=$id");
    $conn->query("DELETE FROM transactions WHERE transaction_id=$id");
    exit;
}
