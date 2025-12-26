Nyoman Arya Agni Sanjaya - 220010167
Disini saya akan menjelaskan bagian yang saya buat. Pertama ada file dosen.php apa yang dimna itu file untuk tampilan dosen, terdapat require_once("koneksi.php"); dimana kita menggabungkan file konkesi ke dalam file dosen.php, pada code ini <?php include("include/head.php"); ?> kita akan memanggil file head.php yg berada di dalam folder include, pada code ini <?php include("include/navbar.php"); ?>, <?php include("include/sidebar.php"); ?>, <?php include("include/footer.php"); ?>, <?php include("include/script.php"); ?> sama seperti sebelumnya kita memanggil file yang ada di folder include. pada tampilannya terdapat table dan diatasnya ada button tambah data, pada table disini saya membuat no, nama lengkap, alamat, no. telepon, e-mail, jurusan, almamater, dan action yang dimna saya mengambil contohnya dari sion. 
<?php
$no = 1;
$query = "SELECT * FROM dosen";
$query = mysqli_query($conn, $query);
while ($row = mysqli_fetch_array($query)) :
?>
pada code ini saya memnaggil data dosen dari database dan melakukan loop menggunakan while
dan untuk contoh isi datanya saya panggil dengan <?= $row['nama_lengkap']; ?>
dan pada code file dosen.php saya membuat modal untuk melakukan tambahdata, edit, dan delete yang dimna untuk edit dan delete nya saya taruk di bagian perulangan while nya agar id yang di panggil sesuai dan tidak lupa saya menambhakan <input type="hidden" name="id" value="<?= $row['id'] ?>"> agar id yg kita pilih sesuai.

Pada file dosen_crud.php disini akan melakukan request POST yang dimana pada opsi tambah code terebut akan mengirimkan data yang kita input ke database melalui query yang sudah kita buat, pada opsi update disni akan mengupdate data-data yang sudah kita edit, pada opsi hapus akan menghapus data sesuai id yang kita pencet pada file dosen.php

Kadek Angga Dwiastra - 220010177
Disini saya akan menjelaskan bagian yang saya buat. Pertama ada file fakultas.php apa yang dimna itu file untuk tampilan fakultas, terdapat require_once("koneksi.php"); dimana kita menggabungkan file konkesi ke dalam file fakultas.php, pada code ini <?php include("include/head.php"); ?> kita akan memanggil file head.php yg berada di dalam folder include, pada code ini <?php include("include/navbar.php"); ?>, <?php include("include/sidebar.php"); ?>, <?php include("include/footer.php"); ?>, <?php include("include/script.php"); ?> sama seperti sebelumnya kita memanggil file yang ada di folder include. pada tampilannya terdapat table dan diatasnya ada button tambah data, pada table disini saya membuat no, nama fakultas, dan action.
<?php
$no = 1;
$query = "SELECT * FROM fakultas";
$query = mysqli_query($conn, $query);
while ($row = mysqli_fetch_array($query)) :
?>
pada code ini saya memnaggil data fakultas dari database dan melakukan loop menggunakan while
dan untuk contoh isi datanya saya panggil dengan <?= $row['nama_fakultas']; ?>
dan pada code file fakultas.php saya membuat menu samping untuk melakukan tambah data, edit, dan delete yang dimna untuk edit dan delete nya saya taruk di bagian perulangan while nya agar id yang di panggil sesuai dan tidak lupa saya menambhakan <input type="hidden" name="id" value="<?= $row['id'] ?>"> agar id yg kita pilih sesuai.

Pada file fakultas_crud.php disini akan melakukan request POST yang dimana pada opsi tambah code terebut akan mengirimkan data yang kita input ke database melalui query yang sudah kita buat, pada opsi update disni akan mengupdate data-data yang sudah kita edit, pada opsi hapus akan menghapus data sesuai id yang kita pencet pada file fakultas.php
Ibnu Dzumirrotin - 220010178