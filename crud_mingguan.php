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
        $name = $_FILES['file']['name'];
        $tmp  = $_FILES['file']['tmp_name'];
        $path = "uploads/mingguan/" . time() . "_" . $name;
        move_uploaded_file($tmp, $path);

        $conn->query("
            INSERT INTO transaction_files
            (transaction_id,file_name,file_path,file_type)
            VALUES (
                $tid,
                '$name',
                '$path',
                '{$_FILES['file']['type']}'
            )
        ");
    }
    exit;
}

if ($action === 'update') {
    $id = $_POST['transaction_id'];

    $conn->query("
        UPDATE transactions SET
            tanggal     = '{$_POST['tanggal']}',
            nama        = '{$_POST['nama']}',
            keterangan  = '{$_POST['keterangan']}',
            nominal     = '{$_POST['nominal']}',
            bank_id     = '{$_POST['bank_id']}',
            tipe        = 'mingguan'
        WHERE transaction_id = '{$_POST['transaction_id']}'
    ");

    if (!empty($_FILES['file']['name'])) {
        $name = $_FILES['file']['name'];
        $tmp  = $_FILES['file']['tmp_name'];
        $path = "uploads/mingguan/" . time() . "_" . $name;
        move_uploaded_file($tmp, $path);

        $conn->query("
            INSERT INTO transaction_files
            (transaction_id,file_name,file_path,file_type)
            VALUES (
                $id,
                '$name',
                '$path',
                '{$_FILES['file']['type']}'
            )
        ");
    }

    $tid = $conn->insert_id;

    if (!empty($_FILES['file']['name'])) {
        $maxSize = 5 * 1024 * 1024; // 5 MB
        $allowedTypes = [
            'application/pdf',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            die("Terjadi kesalahan saat upload file");
        }

        if ($_FILES['file']['size'] > $maxSize) {
            die("Ukuran file terlalu besar. Maksimal 5MB");
        }

        if (!in_array($_FILES['file']['type'], $allowedTypes)) {
            die("Tipe file tidak diizinkan. Hanya PDF & Excel");
        }

        $name = basename($_FILES['file']['name']);
        $tmp  = $_FILES['file']['tmp_name'];

        $path = "uploads/mingguan/" . time() . "_" . $name;

        if (!move_uploaded_file($tmp, $path)) {
            die("Gagal menyimpan file ke server");
        }

        $conn->query("
        INSERT INTO transaction_files
        (transaction_id, file_name, file_path, file_type)
        VALUES (
            $tid,
            '$name',
            '$path',
            '{$_FILES['file']['type']}'
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
