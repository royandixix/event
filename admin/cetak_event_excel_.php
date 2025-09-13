<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../function/config.php';

// Ambil data event
$result = mysqli_query($db, "SELECT * FROM event ORDER BY created_at DESC");

// Header supaya browser mencoba menampilkan Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: inline; filename=Daftar_Event.xls");
header("Cache-Control: max-age=0");

// CSS untuk Excel
echo "<style>
table { border-collapse: collapse; width: 100%; }
th { background-color: #6495ED; color: white; padding: 5px; }
td { padding: 5px; }
tr:nth-child(even) { background-color: #E0EBFF; }
</style>";

// Tabel Excel
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Judul Event</th>
        <th>Deskripsi & Lokasi</th>
        <th>Tanggal</th>
        <th>Harga</th>
      </tr>";

$no = 1;
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>".$no++."</td>";
    echo "<td>".$row['judul_event']."</td>";
    echo "<td>".$row['deskripsi_event']." - ".$row['lokasi_event']."</td>";
    echo "<td>".$row['tanggal_mulai']." s/d ".$row['tanggal_selesai']."</td>";
    echo "<td>Rp ".number_format($row['harga_event'],0,',','.')."</td>";
    echo "</tr>";
}
echo "</table>";
exit;
?>
