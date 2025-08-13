<?php
require './templates/sidebar.php';
require '../function/config.php';

// Ambil data event + slot + booking
$sql = "
    SELECT e.id_event, e.judul_event, e.tanggal_mulai, e.tanggal_selesai, e.lokasi_event, e.poster_path,
           s.id_slot, s.nomor_slot, s.status AS slot_status,
           b.id_booking, b.nama_pesanan, b.nama_tim, b.nomor_wa
    FROM event e
    LEFT JOIN paddock_slot s ON e.id_event = s.id_event
    LEFT JOIN paddock_booking b ON s.id_slot = b.slot_id
    ORDER BY e.created_at DESC, s.nomor_slot ASC
";
$result = mysqli_query($db, $sql);
?>

<!-- Include Font Awesome di head jika belum ada -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main class="p-6 transition-all duration-300 lg:ml-64">
    <div class="w-full max-w-full bg-gray-50 border border-gray-100">

        <!-- Header -->
        <div class="px-6 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-blue-500 rounded-t-2xl">
            <div>
                <h1 class="text-3xl font-bold text-white">Event Management</h1>
                <p class="text-blue-200 mt-1">Kelola semua event dengan mudah dan cepat.</p>
            </div>

            <div class="flex gap-2 items-center">
                <!-- Search Input -->
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari event..."
                    class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">

                <!-- Tambah Event Button -->
                <a
                    href="tambah_event.php"
                    class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white font-semibold rounded-lg shadow hover:bg-green-600 transition">
                    <i class="fas fa-plus"></i> Tambah Event
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="p-6 overflow-x-auto">
            <table id="eventTable" class="min-w-[900px] table-auto border-collapse text-base md:text-lg w-full">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">ID Event</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">No</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Judul Event</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Tanggal</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Lokasi</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Poster</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Slot</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Status Slot</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Booking</th>
                        <th class="border-b px-4 py-2 text-left font-medium whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-base md:text-lg">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
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
                                    <?php if ($row['poster_path']): ?>
                                        <img
                                            src="../uploads/poster/<?= $row['poster_path'] ?>"
                                            class="w-16 h-20 object-cover rounded-lg shadow-md hover:scale-105 transition-transform duration-300"
                                            alt="Poster <?= htmlspecialchars($row['judul_event']) ?>">
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Tidak ada poster</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                    <?= $row['nomor_slot'] ?? '-' ?>
                                </td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                    <?= $row['slot_status'] ?? '-' ?>
                                </td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                    <?php if ($row['id_booking']): ?>
                                        <?= htmlspecialchars($row['nama_pesanan']) ?> (<?= htmlspecialchars($row['nama_tim']) ?>)
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 flex gap-2 whitespace-nowrap">
                                    <a
                                        href="edit_event.php?id=<?= $row['id_event'] ?>"
                                        class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 shadow-md transition-all duration-200"
                                        title="Edit Event">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                    <a
                                        href="hapus_event.php?id=<?= $row['id_event'] ?>"
                                        onclick="return confirm('Yakin ingin menghapus event ini?')"
                                        class="flex items-center justify-center w-10 h-10 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 shadow-md transition-all duration-200"
                                        title="Hapus Event">
                                        <i class="fas fa-trash-alt text-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-6 text-gray-400 italic">Belum ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<!-- Script Search Table -->
<script>
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        document.querySelectorAll('#eventTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
</script>

<?php require 'templates/footer.php'; ?>
