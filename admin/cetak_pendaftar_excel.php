<?php
require '../function/config.php';

// Ambil data kelas manajer dan peserta
$kelasData = ['manajer'=>[], 'peserta'=>[]];
$kelasResult = $db->query("SELECT * FROM manajer_kelas");
while($row = $kelasResult->fetch_assoc()) $kelasData['manajer'][$row['manajer_id']][] = $row;
$kelasResult = $db->query("SELECT * FROM peserta_kelas");
while($row = $kelasResult->fetch_assoc()) $kelasData['peserta'][$row['peserta_id']][] = $row;

// Query gabungan
$combinedQuery = "
    (SELECT m.id_manajer AS id, m.nama_manajer AS nama,
        COALESCE(m.nama_tim,'-') AS nama_tim,
        COALESCE(m.email,'-') AS email,
        COALESCE(m.whatsapp,'-') AS whatsapp,
        COALESCE(p.nama_provinsi,m.asal_provinsi) AS provinsi,
        COALESCE(i.status,'pending') AS status_bayar,
        'manajer' AS tipe
     FROM manajer m
     LEFT JOIN provinsi p ON m.id_provinsi=p.id_provinsi
     LEFT JOIN invoice i ON m.id_manajer=i.id_manajer)
    UNION ALL
    (SELECT ps.id_peserta AS id, ps.nama_peserta AS nama,
        COALESCE(ps.nama_tim,'-') AS nama_tim,
        COALESCE(ps.email,'-') AS email,
        COALESCE(ps.whatsapp,'-') AS whatsapp,
        COALESCE(p.nama_provinsi,ps.asal_provinsi) AS provinsi,
        COALESCE(i.status,'pending') AS status_bayar,
        'peserta' AS tipe
     FROM peserta ps
     LEFT JOIN provinsi p ON ps.id_provinsi=p.id_provinsi
     LEFT JOIN invoice i ON ps.id_peserta=i.id_peserta)
    ORDER BY nama ASC
";
$result = $db->query($combinedQuery);

// Header Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: inline; filename=Daftar_Pendaftar_Lengkap.xls");
header("Cache-Control: max-age=0");

// Style tabel
echo "<style>
table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
th { background-color: #6495ED; color: white; padding: 6px; text-align: center; }
td { padding: 6px; vertical-align: top; }
tr:nth-child(even) { background-color: #E0EBFF; }
.status-lunas { background-color: #C6EFCE; color: #006100; font-weight: bold; text-align: center; }
.status-pending { background-color: #FFE599; color: #9C5700; font-weight: bold; text-align: center; }
</style>";

// Tabel Excel
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Nama</th>
        <th>Nama Tim</th>
        <th>Email / WhatsApp</th>
        <th>Provinsi</th>
        <th>Tipe</th>
        <th>Status Bayar</th>
        <th>Kelas & Kendaraan</th>
      </tr>";

$no = 1;
while($row = $result->fetch_assoc()) {
    $statusClass = ($row['status_bayar']==='lunas') ? 'status-lunas' : 'status-pending';
    $statusText  = ($row['status_bayar']==='lunas') ? 'Lunas' : 'Pending';

    // Ambil kelas
    $kelasInfo = '';
    $kelasList = $kelasData[$row['tipe']][$row['id']] ?? [];
    if(!empty($kelasList)){
        foreach($kelasList as $k){
            $kelasInfo .= "Kelas: ".$k['kelas']." | Warna: ".$k['warna_kendaraan']." | Tipe: ".$k['tipe_kendaraan'];
            if($row['tipe']==='peserta' && !empty($k['nomor_polisi'])) $kelasInfo .= " | Nopol: ".$k['nomor_polisi'];
            $kelasInfo .= "\n";
        }
    } else $kelasInfo = '-';

    echo "<tr>";
    echo "<td>".$no++."</td>";
    echo "<td>".$row['nama']."</td>";
    echo "<td>".$row['nama_tim']."</td>";
    echo "<td>".$row['email']." / ".$row['whatsapp']."</td>";
    echo "<td>".$row['provinsi']."</td>";
    echo "<td>".ucfirst($row['tipe'])."</td>";
    echo "<td class='{$statusClass}'>".$statusText."</td>";
    echo "<td>".nl2br($kelasInfo)."</td>";
    echo "</tr>";
}
echo "</table>";
exit;
?>
