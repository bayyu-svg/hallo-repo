# PROJEK UTS BC221 BACKEND WEB DEVELOPER
CRUD Mahasiswa PROJEK UTS BC221 Backend Web Developer

# AUTHOR
[Ibnu Dzumirrotin](https://github.com/ibnudz)

## Cara Instalasi
1. Clone repo ini ke forlder _htdocs_ / _www_
``` bash
git clone https://github.com/ibnudz/crud-mahasiswa.git
```
2. Import file [_database/db_kampus.sql_](database/db_kampus.sql) ke database
3. Sesuaikan file [_koneksi.php_](koneksi.php) dengan konfigurasi anda

```php
<?php
$servername = "localhost"; // HOST DATABASE
$username = "root"; // USERNSME DATABASE
$password = ""; // PASSWORD DATABASE
$dbname = "db_kampus"; //NAMA DATABASE

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// $conn->close();
?>
```
4. Aplikasi siap digunakan

## License
[MIT](https://choosealicense.com/licenses/mit/)
