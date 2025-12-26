<?php
require_once("koneksi.php");

$category = $_GET['category'] ?? 'all';
$q = $_GET['q'] ?? '';

$sql = "SELECT * FROM kekayaan_intelektual WHERE 1=1";

// filter kategori
if ($category !== 'all') {
    if ($category === 'personal') {
        $sql .= " AND id_kategori = 1";
    } elseif ($category === 'komunal') {
        $sql .= " AND id_kategori = 2";
    }
}

// filter keyword
if (!empty($q)) {
    $q = $conn->real_escape_string($q);
    $sql .= " AND nama_ki LIKE '%$q%'";
}

$result = $conn->query($sql);
?>
<ul>
<?php while($row = $result->fetch_assoc()): ?>
  <li><?= htmlspecialchars($row['nama_ki']); ?> (<?= $row['id_kategori']; ?>)</li>
<?php endwhile; ?>
</ul>