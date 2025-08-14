<?php
require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header_paddcok.php';

// Ambil ID event dari GET atau default 1
$id_event = $_GET['id_event'] ?? 1;

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pesanan = mysqli_real_escape_string($db, $_POST['nama_pesanan']);
    $nama_tim     = mysqli_real_escape_string($db, $_POST['nama_tim']);
    $nomor_wa     = mysqli_real_escape_string($db, $_POST['nomor_wa']);
    $slot_id      = intval($_POST['slot_id']);

    if (!$slot_id) {
        die("Slot belum dipilih!");
    }

    // Cek status slot
    $cek_slot = mysqli_query($db, "SELECT status FROM paddock_slot WHERE id_slot = $slot_id");
    $slot     = mysqli_fetch_assoc($cek_slot);

    if (!$slot) {
        die("Slot tidak ditemukan!");
    }

    if ($slot['status'] === 'terisi') {
        die("Maaf, slot ini sudah terisi!");
    }

    // Simpan ke paddock_booking
    $insert = mysqli_query($db, "
        INSERT INTO paddock_booking (slot_id, nama_pesanan, nama_tim, nomor_wa)
        VALUES ($slot_id, '$nama_pesanan', '$nama_tim', '$nomor_wa')
    ");

    if ($insert) {
        mysqli_query($db, "UPDATE paddock_slot SET status = 'terisi' WHERE id_slot = $slot_id");
        echo "<script>alert('Pendaftaran berhasil!'); window.location='pendaftaran.php?id_event=$id_event';</script>";
        exit;
    } else {
        die("Gagal mendaftar: " . mysqli_error($db));
    }
}

// Ambil semua slot untuk event ini
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
</head>
<body class="bg-gradient-to-r from-blue-200 via-white to-white min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <h1 class="text-3xl font-bold text-blue-800 mb-4">Form Pendaftaran Paddock</h1>

        <form action="" method="POST" class="bg-white p-6 rounded-xl shadow-md space-y-6">
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block">DATA PENDAFTAR</h2>
            <input type="text" name="nama_pesanan" placeholder="Nama Pesanan" class="w-full p-3 border rounded-lg" required>
            <input type="text" name="nama_tim" placeholder="Nama Tim" class="w-full p-3 border rounded-lg" required>
            <input type="text" name="nomor_wa" placeholder="Nomor WhatsApp" class="w-full p-3 border rounded-lg" required>

            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block mt-4">PILIH SLOT PADDOCK</h2>
            <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-4 mt-4">
                <?php while ($slot = mysqli_fetch_assoc($slot_query)) : ?>
                    <?php if ($slot['status'] == 'terisi') : ?>
                        <div class="bg-red-500 text-white p-4 rounded-lg text-center shadow cursor-not-allowed"
                             title="Diambil oleh: <?= htmlspecialchars($slot['nama_pesanan']) ?>">
                            <?= htmlspecialchars($slot['nomor_slot']); ?>
                        </div>
                    <?php else: ?>
                        <div onclick="pilihSlot(<?= $slot['id_slot']; ?>, this)"
                             class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center shadow cursor-pointer">
                            <?= htmlspecialchars($slot['nomor_slot']); ?>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

            <input type="hidden" name="slot_id" id="slot_id">
            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-medium px-6 py-3 rounded-lg mt-4">
                Submit Pendaftaran
            </button>
        </form>
    </div>

    <script>
        function pilihSlot(id, el) {
            document.querySelectorAll('.bg-blue-500').forEach(btn => btn.classList.remove('ring-4', 'ring-yellow-300'));
            el.classList.add('ring-4', 'ring-yellow-300');
            document.getElementById('slot_id').value = id;
        }
    </script>

    <?php require 'templates/footer.php'; ?>
</body>
</html>
