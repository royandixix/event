<?php
require_once '../function/config.php';
require_once '../fpdf184/fpdf.php';

// Ambil manajer yang sudah LUNAS
$query = "
    SELECT m.*, i.status
    FROM manajer m
    INNER JOIN invoice i ON m.id_manajer = i.id_manajer
    WHERE i.status = 'lunas'
    ORDER BY m.created_at DESC
";
$result = mysqli_query($db, $query);
if (!$result) die("Query Error: " . mysqli_error($db));

// Inisialisasi PDF
$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->SetTextColor(0,0,128);
$pdf->Cell(0,10,'Daftar Manajer Lunas',0,1,'C');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(100,149,237);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(10,10,'No',1,0,'C',true);
$pdf->Cell(50,10,'Nama Manajer',1,0,'C',true);
$pdf->Cell(50,10,'Nama Tim',1,0,'C',true);
$pdf->Cell(60,10,'Email / WhatsApp',1,0,'C',true);
$pdf->Cell(30,10,'Provinsi',1,0,'C',true);
$pdf->Cell(50,10,'Kelas',1,1,'C',true);

// Isi tabel
$pdf->SetFont('Arial','',12);
$pdf->SetTextColor(0,0,0);
$no = 1;
$fill = false;

while($row = mysqli_fetch_assoc($result)) {
    $pdf->SetFillColor(224,235,255);

    // Ambil kelas manajer
    $kelasResult = mysqli_query($db, "SELECT kelas FROM manajer_kelas WHERE manajer_id = ".$row['id_manajer']);
    $kelasArr = [];
    while($k = mysqli_fetch_assoc($kelasResult)) {
        $kelasArr[] = $k['kelas'];
    }
    $kelasStr = implode(', ', $kelasArr);

    $pdf->Cell(10,10,$no++,1,0,'C',$fill);
    $pdf->Cell(50,10,$row['nama_manajer'],1,0,'L',$fill);
    $pdf->Cell(50,10,$row['nama_tim'],1,0,'L',$fill);
    $pdf->Cell(60,10,$row['email'].' / '.$row['whatsapp'],1,0,'L',$fill);
    $pdf->Cell(30,10,$row['asal_provinsi'],1,0,'C',$fill);
    $pdf->Cell(50,10,$kelasStr,1,1,'L',$fill);

    $fill = !$fill;
}

// Tampilkan PDF di browser
$pdf->Output('I','Daftar_Manajer_Lunas.pdf');
?>
