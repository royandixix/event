<?php
require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header_paddcok.php';

// Ambil data slot paddock
$slot_query = mysqli_query($db, "SELECT * FROM paddock_slot ORDER BY nomor_slot ASC");
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

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Judul -->
        <h1 class="text-3xl font-bold text-blue-800 mb-4">Form Pendaftaran Paddock</h1>

        <!-- Form -->
        <form action="proses_pendaftaran.php" method="POST" class="bg-white p-6 rounded-xl shadow-md space-y-6">
            <!-- Data Pendaftar -->
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block">
                DATA PENDAFTAR
            </h2>

            <input type="text" name="nama_pesanan" placeholder="Nama Pesanan" class="w-full p-3 border rounded-lg" required>
            <input type="text" name="nama_tim" placeholder="Nama Tim" class="w-full p-3 border rounded-lg" required>
            <input type="text" name="nomor_wa" placeholder="Nomor WhatsApp" class="w-full p-3 border rounded-lg" required>

            <!-- Denah Pit -->
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block mt-4">
                DENAH PIT
            </h2>
            <div class="bg-white p-4 rounded-xl shadow-md">
                <img src="../uploads/denah_pit.jpg" alt="Denah Pit" class="rounded-lg border mx-auto">
            </div>

            <!-- Slot Paddock -->
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block mt-4">
                PILIH SLOT PADDOCK
            </h2>
            <div class="flex flex-wrap gap-2 mt-4">
                <?php while ($slot = mysqli_fetch_assoc($slot_query)) : ?>
                    <?php if ($slot['status'] == 'terisi') : ?>
                        <button type="button" class="bg-red-500 text-white px-4 py-2 rounded cursor-not-allowed" disabled>
                            <?= htmlspecialchars($slot['nomor_slot']); ?>
                        </button>
                    <?php else : ?>
                        <button type="button" onclick="pilihSlot(<?= $slot['id_slot']; ?>, this)"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            <?= htmlspecialchars($slot['nomor_slot']); ?>
                        </button>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

            <input type="hidden" name="slot_id" id="slot_id">

            <!-- Submit -->
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
