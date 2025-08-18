<?php
// Tampilkan semua error untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Panggil file konfigurasi dan pustaka Dompdf
require_once '../function/config.php';
require_once '../vendor/autoload.php'; // Pastikan path ini benar

use Dompdf\Dompdf;
use Dompdf\Options;

// Inisialisasi Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Ambil data dari database (peserta terbaru)
$peserta_data = [];
$error_message = '';

try {
    $result = $db->query("SELECT * FROM peserta ORDER BY id_peserta DESC LIMIT 1");
    if ($result === false) {
        throw new Exception("Error saat mengambil data peserta: " . $db->error);
    }

    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $kelas_text = '';
        $kelas_query = $db->prepare("SELECT kelas, warna_kendaraan, tipe_kendaraan, nomor_polisi 
                                     FROM peserta_kelas WHERE peserta_id = ?");
        if ($kelas_query === false) {
            throw new Exception("Error saat menyiapkan query kelas: " . $db->error);
        }
        $kelas_query->bind_param("i", $row['id_peserta']);
        $kelas_query->execute();
        $kelas_result = $kelas_query->get_result();
        
        while ($k = $kelas_result->fetch_assoc()) {
            $kelas_text .= htmlspecialchars("{$k['kelas']} - {$k['warna_kendaraan']} {$k['tipe_kendaraan']} ({$k['nomor_polisi']})") . "<br>";
        }
        $kelas_query->close();

        $row['no'] = $no++;
        $row['kelas_kendaraan'] = $kelas_text ?: '-';
        $peserta_data[] = $row;
    }

} catch (Exception $e) {
    $error_message = "Terjadi kesalahan: " . $e->getMessage();
}

// Bangun konten HTML untuk PDF
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peserta Terbaru</title>
    <style>
        body { font-family: "Times New Roman", serif; font-size: 12px; color: #000; line-height: 1.6; background: #fff; }
        .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 20px; }
        .kop h2 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .kop p { margin: 2px 0; font-size: 12px; }
        .judul { text-align: center; font-size: 15px; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-decoration: underline; }
        .pembuka { margin: 15px 0; font-size: 12px; text-align: justify; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px 10px; }
        th { background: #eee; text-align: center; font-size: 12px; }
        tr:nth-child(even) { background: #fafafa; }
        td { vertical-align: top; font-size: 12px; }
        .footer { margin-top: 40px; text-align: right; font-size: 12px; }
        .ttd { margin-top: 60px; text-align: right; font-size: 12px; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>Panitia Lomba Drag Bike</h2>
        <p>Jl. Raya Motorsport No. 123, Jakarta | Telp: (021) 1234567</p>
    </div>

    <div class="judul">LAPORAN PESERTA TERBARU</div>

    <div class="pembuka">
        Dengan hormat, <br><br>
        Bersama ini kami sampaikan laporan peserta terbaru yang telah melakukan pendaftaran dalam ajang <b>Drag Bike Competition</b>. 
        Data ini merupakan hasil input terbaru dari sistem pendaftaran online kami dan dapat digunakan sebagai referensi validasi peserta. 
        Berikut adalah rincian data peserta yang dimaksud:
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>Nama Tim</th>
                <th>Provinsi</th>
                <th>Email</th>
                <th>Whatsapp</th>
                <th>Kelas & Kendaraan</th>
            </tr>
        </thead>
        <tbody>';

if (!empty($error_message)) {
    $html .= '<tr><td colspan="7" style="color: red; text-align: center;">' . $error_message . '</td></tr>';
} elseif (empty($peserta_data)) {
    $html .= '<tr><td colspan="7" style="text-align: center;">Tidak ada data peserta terbaru.</td></tr>';
} else {
    foreach ($peserta_data as $row) {
        $html .= '
        <tr>
            <td style="text-align:center;">' . $row['no'] . '</td>
            <td>' . htmlspecialchars($row['nama_peserta']) . '</td>
            <td>' . htmlspecialchars($row['nama_tim']) . '</td>
            <td>' . htmlspecialchars($row['asal_provinsi']) . '</td>
            <td>' . htmlspecialchars($row['email']) . '</td>
            <td>' . htmlspecialchars($row['whatsapp']) . '</td>
            <td>' . $row['kelas_kendaraan'] . '</td>
        </tr>';
    }
}
$html .= '
        </tbody>
    </table>

    <div class="ttd">
        Jakarta, ' . date("d M Y") . '<br><br>
        Panitia Lomba Drag Bike
        <br><br><br><br>
        (.....................................)
    </div>
</body>
</html>';

// Load HTML ke Dompdf
$dompdf->loadHtml($html);

// Render HTML menjadi PDF
$dompdf->render();

// Keluarkan output ke browser (preview di browser, bukan auto-download)
$dompdf->stream("laporan_peserta_terbaru.pdf", ["Attachment" => false]);
