<?php
require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header_paddcok.php';

$id_event = $_GET['id_event'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pesanan = mysqli_real_escape_string($db, $_POST['nama_pesanan']);
    $nama_tim     = mysqli_real_escape_string($db, $_POST['nama_tim']);
    $nomor_wa     = mysqli_real_escape_string($db, $_POST['nomor_wa']);
    $slot_id      = intval($_POST['slot_id']);

    if (!$slot_id) die("Slot belum dipilih!");

    // Cek slot
    $cek_slot = mysqli_query($db, "SELECT status FROM paddock_slot WHERE id_slot = $slot_id");
    $slot = mysqli_fetch_assoc($cek_slot);
    if (!$slot) die("Slot tidak ditemukan!");
    if ($slot['status'] === 'terisi') die("Maaf, slot ini sudah terisi!");

    // Insert booking
    $insert = mysqli_query($db, "
        INSERT INTO paddock_booking (slot_id, nama_pesanan, nama_tim, nomor_wa)
        VALUES ($slot_id, '$nama_pesanan', '$nama_tim', '$nomor_wa')
    ");

    if ($insert) {
        // Update slot jadi terisi
        mysqli_query($db, "UPDATE paddock_slot SET status = 'terisi' WHERE id_slot = $slot_id");

        // Ambil harga event
        $event = mysqli_fetch_assoc(mysqli_query($db, "SELECT harga_event FROM event WHERE id_event = $id_event"));
        $total_harga = $event ? (int)$event['harga_event'] : 0;

        // Generate invoice
        $nomor_invoice = "SSM/INV-PADDOCK/" . date("m/Y") . "/" . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $kode_unik = rand(100, 999);
        $total_transfer = $total_harga + $kode_unik;

        // Simpan invoice
        mysqli_query($db, "
            INSERT INTO invoice (nomor_invoice, id_event, total_harga, kode_unik, total_transfer, bank_tujuan, no_rekening, nama_pemilik_rekening, gambar_bank, slot_id)
            VALUES ('$nomor_invoice', $id_event, $total_harga, $kode_unik, $total_transfer, 'Mandiri', '1330012345678', 'manusia', '1755237799_ag-branding-logo-2.png', $slot_id)
        ");

        // Ambil invoice terbaru
        $invoice = mysqli_fetch_assoc(mysqli_query($db, "
            SELECT * FROM invoice
            WHERE slot_id = $slot_id
            ORDER BY id_invoice DESC
            LIMIT 1
        "));

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

        if ($invoice) {
            $total_harga_fmt = number_format($invoice['total_harga'], 0, ',', '.');
            $total_transfer_fmt = number_format($invoice['total_transfer'], 0, ',', '.');

            // Ambil gambar bank
            $gambar_bank = !empty($invoice['gambar_bank']) ? "../uploads/bank/{$invoice['gambar_bank']}" : '';

            $html = "<div style='text-align:center;'>";

            // Tampilkan gambar bank
            if ($gambar_bank && file_exists($gambar_bank)) {
                $html .= "<img src='{$gambar_bank}' width='200' style='margin-bottom:15px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.3);'><br>";
            }

            $html .= "
    <b>Nomor Invoice:</b> {$invoice['nomor_invoice']}<br>
    <b>Total Harga:</b> Rp {$total_harga_fmt}<br>
    <b>Kode Unik:</b> {$invoice['kode_unik']}<br>
    <b>Total Transfer:</b> Rp {$total_transfer_fmt}<br>
    <b>Bank Tujuan:</b> {$invoice['bank_tujuan']}<br>
    <b>No Rekening:</b> {$invoice['no_rekening']}<br>
    <b>Nama Pemilik Rekening:</b> {$invoice['nama_pemilik_rekening']}<br>
</div>";
        } else {
            $html = "<b>Data invoice tidak ditemukan.</b>";
        }

        echo "<script>
            Swal.fire({
                title: 'Pendaftaran Berhasil!',
                html: `$html`,
                icon: 'success',
                width: 600,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Oke, Mengerti!'
            }).then(() => {
                window.location='paddock.php?id_event=$id_event';
            });
        </script>";

        exit;
    } else {
        die('Gagal mendaftar: ' . mysqli_error($db));
    }
}


// Ambil semua slot
$slot_query = mysqli_query($db, "
    SELECT s.id_slot, s.nomor_slot, s.status, b.nama_pesanan
    FROM paddock_slot s
    LEFT JOIN paddock_booking b ON s.id_slot = b.slot_id
    WHERE s.id_event = $id_event
    ORDER BY s.nomor_slot ASC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Paddock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-r from-blue-200 via-white to-white min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <h1 class="text-3xl  text-blue-800 mb-6 text-center drop-shadow-md">Form Pendaftaran Paddock</h1>

        <form id="paddockForm" action="" method="POST" class="bg-white p-6 rounded-xl shadow-lg space-y-6 border border-gray-100">
            <!-- DATA PENDAFTAR -->
            <h2 class="text-lg  text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">
                DATA PENDAFTAR
            </h2>
            <input type="text" name="nama_pesanan" placeholder="Nama Pesanan"
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition">
            <input type="text" name="nama_tim" placeholder="Nama Tim"
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition">
            <input type="text" name="nomor_wa" placeholder="Nomor WhatsApp"
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition">

            <!-- SLOT PADDOCK -->
            <h2 class="text-lg  text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm mt-4">
                PILIH SLOT PADDOCK
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-4 mt-4">
                <?php while ($slot = mysqli_fetch_assoc($slot_query)) : ?>
                    <?php if ($slot['status'] == 'terisi') : ?>
                        <div class="bg-red-500 text-white p-4 rounded-lg text-center shadow cursor-not-allowed opacity-80"
                            title="Diambil oleh: <?= htmlspecialchars($slot['nama_pesanan']) ?>">
                            <?= htmlspecialchars($slot['nomor_slot']); ?>
                        </div>
                    <?php else: ?>
                        <div onclick="pilihSlot(<?= $slot['id_slot']; ?>, this)"
                            class="slot-item bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center shadow-md cursor-pointer transition transform hover:-translate-y-1 hover:shadow-xl">
                            <?= htmlspecialchars($slot['nomor_slot']); ?>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

            <input type="hidden" name="slot_id" id="slot_id">

            <!-- BUTTON -->
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-800 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition transform hover:-translate-y-1">
                Submit Pendaftaran
            </button>
        </form>
    </div>



    <!-- Tambahkan ini di <head> untuk icon Font Awesome -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="./js/alert/alert_paddock.js"></script>


    <?php require 'templates/footer.php'; ?>
</body>

</html>