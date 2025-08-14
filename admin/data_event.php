<?php
require './templates/sidebar.php';
require '../function/config.php';

// --- Ambil semua event ---
$sql = "SELECT * FROM event ORDER BY created_at DESC";
$result = mysqli_query($db, $sql);

// --- Ambil semua slot & booking untuk modal ---
$slotSql = "
    SELECT s.id_slot, s.id_event, s.nomor_slot, s.status, b.nama_pesanan, b.nama_tim
    FROM paddock_slot s
    LEFT JOIN paddock_booking b ON s.id_slot = b.slot_id
    ORDER BY s.id_event, s.nomor_slot ASC
";
$slotResult = mysqli_query($db, $slotSql);
$slotsData = [];
while ($slotRow = mysqli_fetch_assoc($slotResult)) {
    $slotsData[$slotRow['id_event']][] = $slotRow;
}

// --- Tambah Event (proses POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_event'])) {
    $judul_event = mysqli_real_escape_string($db, $_POST['judul_event']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $lokasi_event = mysqli_real_escape_string($db, $_POST['lokasi_event']);
    $harga_event = preg_replace('/\D/', '', $_POST['harga_event']); // hapus non-digit

    $sqlInsert = "INSERT INTO event (judul_event, tanggal_mulai, tanggal_selesai, lokasi_event, harga_event) 
                  VALUES ('$judul_event', '$tanggal_mulai', '$tanggal_selesai', '$lokasi_event', '$harga_event')";
    mysqli_query($db, $sqlInsert);

    header("Location: event_management.php"); // refresh halaman
    exit;
}
?>

<main class="p-6 transition-all duration-300 lg:ml-64">
    <div class="w-full max-w-full bg-gray-50 border border-gray-100">

        <!-- Header -->
        <div class="px-6 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-blue-500 rounded-t-2xl">
            <div>
                <h1 class="text-3xl font-bold text-white">Event Management</h1>
                <p class="text-blue-200 mt-1">Kelola semua event dengan mudah dan cepat.</p>
            </div>
            <div class="flex gap-2 items-center">
                <input type="text" id="searchInput" placeholder="Cari event..." class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <a href="tambah_event.php" class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white font-semibold rounded-lg shadow hover:bg-green-600 transition">
                    <i class="fas fa-plus"></i> Tambah Event
                </a>

            </div>
        </div>

        <!-- Table Event -->
        <div class="p-6 overflow-x-auto">
            <table id="eventTable" class="min-w-[900px] table-auto border-collapse text-base md:text-lg w-full">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">ID Event</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">No</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Judul Event</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Tanggal</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Lokasi</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Harga</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Poster</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-base md:text-lg">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1;
                        mysqli_data_seek($result, 0);
                        while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="bg-white hover:shadow-md transition-all duration-200 rounded-lg">
                                <td class="px-4 py-3 font-mono text-gray-600 whitespace-nowrap"><?= $row['id_event'] ?></td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap"><?= $no++ ?></td>
                                <td class="px-4 py-3 font-semibold text-gray-800"><?= htmlspecialchars($row['judul_event']) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full font-medium shadow-sm">
                                        <?= $row['tanggal_mulai'] ?> - <?= $row['tanggal_selesai'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full font-medium shadow-sm">
                                        <?= htmlspecialchars($row['lokasi_event']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full font-medium shadow-sm">
                                        Rp <?= number_format($row['harga_event'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php if ($row['poster_path']): ?>
                                        <img src="../uploads/poster/<?= $row['poster_path'] ?>" class="w-16 h-20 object-cover rounded-lg shadow-md hover:scale-105 transition-transform duration-300" alt="Poster <?= htmlspecialchars($row['judul_event']) ?>">
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Tidak ada poster</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 flex gap-2 whitespace-nowrap">
                                    <button onclick="openModal('modal-<?= $row['id_event'] ?>')" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg shadow hover:bg-blue-200 transition">
                                        Lihat Slot
                                    </button>
                                    <a href="edit_event.php?id=<?= $row['id_event'] ?>" class="px-3 py-1 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 shadow-md transition" title="Edit Event">Edit</a>
                                    <a href="hapus_event.php?id=<?= $row['id_event'] ?>" onclick="return confirm('Yakin ingin menghapus event ini?')" class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 shadow-md transition" title="Hapus Event">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-400 italic">Belum ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modals Slot & Tambah Event -->
        <?php mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)): ?>
            <div id="modal-<?= $row['id_event'] ?>" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
                <div class="bg-white w-11/12 md:w-1/2 rounded-xl p-6 relative">
                    <h2 class="text-xl font-bold mb-4">Detail Slot - <?= htmlspecialchars($row['judul_event']) ?></h2>
                    <button onclick="closeModal('modal-<?= $row['id_event'] ?>')" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800 text-xl cursor-pointer p-2 rounded-full hover:bg-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                    <table class="w-full table-auto border-collapse text-gray-700">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">No Slot</th>
                                <th class="border px-4 py-2">Status</th>
                                <th class="border px-4 py-2">Booking</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($slotsData[$row['id_event']])): ?>
                                <?php foreach ($slotsData[$row['id_event']] as $s): ?>
                                    <tr>
                                        <td class="border px-4 py-2"><?= $s['nomor_slot'] ?></td>
                                        <td class="border px-4 py-2"><?= $s['status'] ?></td>
                                        <td class="border px-4 py-2"><?= $s['nama_pesanan'] ? $s['nama_pesanan'] . ' (' . $s['nama_tim'] . ')' : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <tr>
                                        <td class="border px-4 py-2">A<?= $i ?></td>
                                        <td class="border px-4 py-2">kosong</td>
                                        <td class="border px-4 py-2">-</td>
                                    </tr>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endwhile; ?>



    </div>
</main>

<script>
    // Cari event
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        document.querySelectorAll('#eventTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });

    // Modal
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function openMain(id) {
        const registModal = document.write(getElementById)
    }

    // Format input harga (Rupiah)
    function formatRupiah(input) {
        let value = input.value.replace(/\D/g, ''); // hanya angka
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
</script>

<?php require 'templates/footer.php'; ?>