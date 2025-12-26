<?php
require_once "include/session.php";
require_once "koneksi.php";

$admin_id = $_SESSION['admin_id'];

$nama   = $_POST['nama_lengkap'];
$nip    = $_POST['nip'];
$no_hp  = $_POST['no_hp'];
$alamat = $_POST['alamat'];

/* =====================
   UPDATE DATA TANPA FOTO
===================== */
$conn->query("
    UPDATE admin SET
    nama_lengkap = '$nama',
    nip = '$nip',
    no_hp = '$no_hp',
    alamat = '$alamat'
    WHERE admin_id = $admin_id
");

/* =====================
   UPLOAD FOTO (OPSIONAL)
===================== */
if (!empty($_FILES['foto']['name'])) {

    $allowed = ['image/jpeg', 'image/jpg'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($_FILES['foto']['type'], $allowed)) {
        die("Format file tidak diizinkan");
    }

    if ($_FILES['foto']['size'] > $maxSize) {
        die("Ukuran file maksimal 2MB");
    }

    $folder = "uploads/profile/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $filename = time() . "_" . basename($_FILES['foto']['name']);
    $path = $folder . $filename;

    move_uploaded_file($_FILES['foto']['tmp_name'], $path);

    $conn->query("
        UPDATE admin SET foto = '$path'
        WHERE admin_id = $admin_id
    ");
    
    $_SESSION['foto'] = $path;
}

header("Location: profil.php");
exit;
