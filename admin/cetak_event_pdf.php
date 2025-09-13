<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../function/config.php';
require_once '../fpdf184/fpdf.php';

// Ambil data event
$result = mysqli_query($db, "SELECT * FROM event ORDER BY created_at DESC");
if (!$result) die("Query Error: " . mysqli_error($db));

// Inisialisasi PDF
$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',18);
$pdf->SetTextColor(0,0,128);
$pdf->Cell(0,10,'Daftar Event',0,1,'C');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(100,149,237); // Cornflower blue
$pdf->SetTextColor(255,255,255);
$pdf->Cell(10,10,'No',1,0,'C',true);
$pdf->Cell(50,10,'Judul',1,0,'C',true);
$pdf->Cell(80,10,'Deskripsi & Lokasi',1,0,'C',true);
$pdf->Cell(40,10,'Tanggal',1,0,'C',true);
$pdf->Cell(30,10,'Harga',1,1,'C',true);

// Isi tabel
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$fill = false;
$no = 1;

while($row = mysqli_fetch_assoc($result)) {
    $pdf->SetFillColor(224,235,255);
    $pdf->Cell(10,10,$no++,1,0,'C',$fill);
    $pdf->Cell(50,10,$row['judul_event'],1,0,'L',$fill);
    $pdf->Cell(80,10,substr($row['deskripsi_event'],0,30).' - '.$row['lokasi_event'],1,0,'L',$fill);
    $pdf->Cell(40,10,$row['tanggal_mulai'].' s/d '.$row['tanggal_selesai'],1,0,'C',$fill);
    $pdf->Cell(30,10,'Rp '.number_format($row['harga_event'],0,',','.'),1,1,'R',$fill);
    $fill = !$fill;
}

// Tampilkan PDF di browser
$pdf->Output('I','Daftar_Event.pdf'); // 'I' = inline
?>
