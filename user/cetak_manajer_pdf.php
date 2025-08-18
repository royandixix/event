<?php
require_once '../function/config.php';
require_once '../vendor/autoload.php';
use Mpdf\Mpdf;

try {
    // Ambil semua data manajer
    $sql = "
    SELECT m.*, 
           GROUP_CONCAT(CONCAT(mk.kelas, ' - ', mk.warna_kendaraan, ' ', mk.tipe_kendaraan, ' (', mk.nomor_polisi, ')') SEPARATOR '<br>') AS kelas_kendaraan
    FROM manajer m
    LEFT JOIN manajer_kelas mk ON mk.manajer_id = m.id_manajer
    WHERE DATE(m.created_at) = CURDATE()
    GROUP BY m.id_manajer
    ORDER BY m.id_manajer DESC
";

    $result = $db->query($sql);

    if (!$result) {
        throw new Exception("Error mengambil data: " . $db->error);
    }

    $manajer_data = [];
    $no = 1;
    foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
        $row['kelas_kendaraan'] = $row['kelas_kendaraan'] ?: '-';
        $row['no'] = $no++;
        $manajer_data[] = $row;
    }

    // Folder temporary mPDF
    $tempDir = __DIR__ . '/tmp/mpdf';

    // Buat folder jika belum ada
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    // Pastikan writable
    if (!is_writable($tempDir)) {
        chmod($tempDir, 0777);
    }

    $mpdf = new Mpdf([
        'tempDir' => $tempDir
    ]);

    // HTML untuk PDF
    $html = '
    <html>
    <head>
        <style>
            body { font-family: "Times New Roman", serif; font-size: 12px; }
            .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 20px; }
            .kop h2 { margin: 0; font-size: 20px; text-transform: uppercase; }
            .kop p { margin: 2px 0; font-size: 12px; }
            .judul { text-align: center; font-size: 15px; font-weight: bold; margin-top: 10px; margin-bottom: 10px; text-decoration: underline; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #000; padding: 6px 8px; font-size: 12px; }
            th { background: #eee; text-align: center; }
            tr:nth-child(even) { background: #fafafa; }
            td { vertical-align: top; }
            .ttd { margin-top: 40px; text-align: right; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="kop">
            <h2>Panitia Lomba Drag Bike</h2>
            <p>Jl. Raya Motorsport No. 123, Makassar | Telp: (0411) 1234567</p>

        </div>

        <div class="judul">LAPORAN MANAGER TERBARU</div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Manajer</th>
                    <th>Tim</th>
                    <th>Provinsi</th>
                    <th>Email</th>
                    <th>Whatsapp</th>
                    <th>Voucher</th>
                    <th>Kelas & Kendaraan</th>
                </tr>
            </thead>
            <tbody>';

    if (empty($manajer_data)) {
        $html .= '<tr><td colspan="8" style="text-align:center;">Belum ada data manajer.</td></tr>';
    } else {
        foreach ($manajer_data as $row) {
            $html .= '<tr>
                <td style="text-align:center;">'.$row['no'].'</td>
                <td>'.htmlspecialchars($row['nama_manajer']).'</td>
                <td>'.htmlspecialchars($row['nama_tim']).'</td>
                <td>'.htmlspecialchars($row['asal_provinsi']).'</td>
                <td>'.htmlspecialchars($row['email']).'</td>
                <td>'.htmlspecialchars($row['whatsapp']).'</td>
                <td>'.htmlspecialchars($row['voucher'] ?: '-').'</td>
                <td>'.$row['kelas_kendaraan'].'</td>
            </tr>';
        }
    }

    $html .= '
            </tbody>
        </table>

        <div class="ttd">
            Makassar, '.date("d M Y").'<br><br>
            Panitia Lomba Drag Bike
            <br><br><br><br>
            (.....................................)
        </div>
    </body>
    </html>';

    $mpdf->WriteHTML($html);
    $mpdf->Output('laporan_manajer.pdf', 'I');

} catch (Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
