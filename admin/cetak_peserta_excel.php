<?php
require '../function/config.php';

// Ambil peserta lunas
$query = "
    SELECT p.*, i.status
    FROM peserta p
    INNER JOIN invoice i ON p.id_peserta = i.id_peserta
    WHERE i.status = 'lunas'
    ORDER BY p.created_at DESC
";
$result = mysqli_query($db, $query);

// Header supaya browser menampilkan Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: inline; filename=Daftar_Peserta_Lunas.xls");
header("Cache-Control: max-age=0");

// CSS Excel
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
        <th>Nama Peserta</th>
        <th>Nama Tim</th>
        <th>Email / WhatsApp</th>
        <th>Provinsi</th>
        <th>Kelas</th>
      </tr>";

$no = 1;
while($row = mysqli_fetch_assoc($result)) {
    // Ambil kelas peserta
    $kelasResult = mysqli_query($db, "SELECT kelas FROM peserta_kelas WHERE peserta_id = ".$row['id_peserta']);
    $kelasArr = [];
    while($k = mysqli_fetch_assoc($kelasResult)) {
        $kelasArr[] = $k['kelas'];
    }
    $kelasStr = implode(', ', $kelasArr);

    echo "<tr>";
    echo "<td>".$no++."</td>";
    echo "<td>".$row['nama_peserta']."</td>";
    echo "<td>".$row['nama_tim']."</td>";
    echo "<td>".$row['email']." / ".$row['whatsapp']."</td>";
    echo "<td>".$row['asal_provinsi']."</td>";
    echo "<td>".$kelasStr."</td>";
    echo "</tr>";
}
echo "</table>";
exit;
?>
