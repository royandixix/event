<?php
require_once '../function/config.php';
require_once '../fpdf184/fpdf.php';

// Query gabungan manajer + peserta
$combinedQuery = "
    (SELECT m.id_manajer AS id, m.nama_manajer AS nama, m.nama_tim, m.email, m.whatsapp, m.foto_manajer AS foto, 
        IFNULL(p.nama_provinsi, m.asal_provinsi) AS provinsi, i.status AS status_bayar, 'manajer' AS tipe 
     FROM manajer m 
     LEFT JOIN provinsi p ON m.id_provinsi = p.id_provinsi 
     LEFT JOIN invoice i ON m.id_manajer = i.id_manajer)
    UNION ALL
    (SELECT ps.id_peserta AS id, ps.nama_peserta AS nama, ps.nama_tim, ps.email, ps.whatsapp, ps.foto_peserta AS foto, 
        IFNULL(p.nama_provinsi, ps.asal_provinsi) AS provinsi, i.status AS status_bayar, 'peserta' AS tipe 
     FROM peserta ps 
     LEFT JOIN provinsi p ON ps.id_provinsi = p.id_provinsi 
     LEFT JOIN invoice i ON ps.id_peserta = i.id_peserta)
    ORDER BY nama ASC
";
$result = $db->query($combinedQuery);

// Inisialisasi PDF
$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Daftar Pendaftar',0,1,'C');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(255,255,255);
$pdf->SetFillColor(0,123,255); // biru header
$pdf->Cell(10,10,'No',1,0,'C',true);
$pdf->Cell(50,10,'Nama',1,0,'C',true);
$pdf->Cell(50,10,'Nama Tim',1,0,'C',true);
$pdf->Cell(60,10,'Email / WhatsApp',1,0,'C',true);
$pdf->Cell(30,10,'Provinsi',1,0,'C',true);
$pdf->Cell(20,10,'Tipe',1,0,'C',true);
$pdf->Cell(20,10,'Status',1,1,'C',true);

// Isi tabel
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$no = 1;
$fill = false;

while($row = $result->fetch_assoc()) {
    // Zebra stripe
    if($fill) $pdf->SetFillColor(224,235,255);
    else $pdf->SetFillColor(255,255,255);

    $status = $row['status_bayar'] === 'lunas' ? 'Lunas' : 'Pending';

    $pdf->Cell(10,10,$no++,1,0,'C',$fill);
    $pdf->Cell(50,10,$row['nama'],1,0,'L',$fill);
    $pdf->Cell(50,10,$row['nama_tim'] ?? '-',1,0,'L',$fill);
    $pdf->Cell(60,10,$row['email'].' / '.$row['whatsapp'],1,0,'L',$fill);
    $pdf->Cell(30,10,$row['provinsi'],1,0,'C',$fill);
    $pdf->Cell(20,10,ucfirst($row['tipe']),1,0,'C',$fill);

    // Warna status
    if($status === 'Lunas') {
        $pdf->SetFillColor(198,239,206); // hijau muda
        $pdf->SetTextColor(0,97,0);      // hijau gelap
    } else {
        $pdf->SetFillColor(255,229,153); // kuning/oranye muda
        $pdf->SetTextColor(156,87,0);     // oranye gelap
    }
    $pdf->Cell(20,10,$status,1,1,'C',true);

    // Reset text color & fill untuk baris berikutnya
    $pdf->SetTextColor(0,0,0);
    $fill = !$fill;
}

// Tampilkan PDF di browser
$pdf->Output('I','Daftar_Pendaftar.pdf');
?>
