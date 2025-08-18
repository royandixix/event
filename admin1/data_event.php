<?php
require './templates/sidebar.php';
require '../function/config.php';

// Ambil semua event + invoice
$sql = "
    SELECT e.*, i.nomor_invoice, i.total_harga, i.kode_unik, i.total_transfer, i.bank_tujuan,
           i.no_rekening, i.nama_pemilik_rekening, i.gambar_bank, i.status AS invoice_status, i.created_at AS invoice_created
    FROM event e
    LEFT JOIN invoice i ON e.id_event = i.id_event
    ORDER BY e.created_at DESC
";
$result = mysqli_query($db, $sql);

// Ambil semua slot & booking untuk modal
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
            <table id="eventTable" class="min-w-[1200px] table-auto border-collapse w-full text-sm md:text-base">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs md:text-sm">
                    <tr>
                        <th class="border-b border-gray-200 px-4 py-2 text-left">No</th>
                        <th class="border-b border-gray-200 px-4 py-2 text-left">Judul Event</th>
                        <th class="border-b border-gray-200 px-4 py-2 text-left">Tanggal</th>
                        <th class="border-b border-gray-200 px-4 py-2 text-left">Lokasi</th>
                        <th class="border-b border-gray-200 px-4 py-2 text-left">Harga</th>
                        <th class="border-b border-gray-200 px-4 py-2">Poster</th>
                        <th class="border-b border-gray-200 px-4 py-2">Invoice</th>
                        <th class="border-b border-gray-200 px-4 py-2">Nomor Invoice</th>
                        <th class="border-b border-gray-200 px-4 py-2">Total Harga</th>
                        <th class="border-b border-gray-200 px-4 py-2">Kode Unik</th>
                        <th class="border-b border-gray-200 px-4 py-2">Total Transfer</th>
                        <th class="border-b border-gray-200 px-4 py-2">Bank Tujuan</th>
                        <th class="border-b border-gray-200 px-4 py-2">No Rekening</th>
                        <th class="border-b border-gray-200 px-4 py-2">Nama Pemilik Rekening</th>
                        <th class="border-b border-gray-200 px-4 py-2">Gambar Bank</th>
                        <th class="border-b border-gray-200 px-4 py-2">Status</th>
                        <th class="border-b border-gray-200 px-4 py-2">Created At</th>
                        <th class="border-b border-gray-200 px-4 py-2">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="bg-white hover:bg-gray-50 hover:shadow-md transition-all duration-200 rounded-lg">
                                <td class="px-4 py-3"><?= $no++ ?></td>
                                <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($row['judul_event']) ?></td>
                                <td class="px-4 py-3"><?= $row['tanggal_mulai'] ?> - <?= $row['tanggal_selesai'] ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($row['lokasi_event']) ?></td>
                                <td class="px-4 py-3">Rp <?= number_format($row['harga_event'], 0, ',', '.') ?></td>

                                <td class="px-4 py-3">
                                    <?php if ($row['poster_path']): ?>
                                        <img src="../uploads/poster/<?= $row['poster_path'] ?>"
                                            class="w-20 h-24 object-cover rounded-lg shadow-sm" alt="Poster">
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Tidak ada</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3">
                                    <?php if ($row['nomor_invoice']): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">
                                            <?= htmlspecialchars($row['nomor_invoice']) ?>
                                            (Rp <?= number_format($row['total_transfer'], 0, ',', '.') ?>)
                                        </span>
                                        <span class="text-gray-500 text-sm">(<?= htmlspecialchars($row['invoice_status']) ?>)</span>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Belum ada invoice</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($row['nomor_invoice']) ?></td>
                                <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                <td><?= $row['kode_unik'] ?></td>
                                <td>Rp <?= number_format($row['total_transfer'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['bank_tujuan']) ?></td>
                                <td><?= htmlspecialchars($row['no_rekening']) ?></td>
                                <td><?= htmlspecialchars($row['nama_pemilik_rekening']) ?></td>

                                <td>
                                    <?php if ($row['gambar_bank']): ?>
                                        <img src="../uploads/bank/<?= $row['gambar_bank'] ?>"
                                            class="w-16 h-16 object-cover rounded-lg shadow-sm" alt="Bukti">
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Tidak ada</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php
                                    $statusColor = $row['invoice_status'] === 'Lunas' ? 'bg-green-100 text-green-800' : ($row['invoice_status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600');
                                    ?>
                                    <span class="px-2 py-1 rounded-full font-medium <?= $statusColor ?>">
                                        <?= htmlspecialchars($row['invoice_status']) ?>
                                    </span>
                                </td>

                                <td><?= $row['invoice_created'] ?></td>

                                <td class="px-4 py-3 flex gap-2">
                                    <button onclick="openModal('modal-<?= $row['id_event'] ?>')"
                                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                        Lihat Slot
                                    </button>
                                    <a href="edit_event.php?id=<?= $row['id_event'] ?>"
                                        class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300">Edit</a>
                                    <button onclick="hapusEvent(<?= $row['id_event'] ?>)"
                                        class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="18" class="text-center py-6 text-gray-400 italic">
                                Belum ada data.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>


    </div>

    <!-- Modal Slot -->
    <?php mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)): ?>
        <div id="modal-<?= $row['id_event'] ?>" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
            <div class="bg-white w-11/12 md:w-1/2 rounded-xl p-6 relative">
                <h2 class="text-xl font-bold mb-4">Detail Slot - <?= htmlspecialchars($row['judul_event']) ?></h2>
                <button onclick="closeModal('modal-<?= $row['id_event'] ?>')" class="absolute top-4 right-4 text-xl">&times;</button>
                <table class="w-full table-auto border-collapse text-gray-700">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">No Slot</th>
                            <th class="border px-4 py-2">Status</th>
                            <th class="border px-4 py-2">Nama Pesanan</th>
                            <th class="border px-4 py-2">Nama Tim</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($slotsData[$row['id_event']])): ?>
                            <?php foreach ($slotsData[$row['id_event']] as $slot): ?>
                                <tr>
                                    <td class="border px-4 py-2"><?= $slot['nomor_slot'] ?></td>
                                    <td class="border px-4 py-2"><?= $slot['status'] ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($slot['nama_pesanan']) ?></td>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($slot['nama_tim']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-2 text-gray-400 italic">Belum ada slot.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endwhile; ?>
</main>

<script>
    // Fungsi buka modal
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // Fungsi tutup modal
    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Fungsi hapus event
    function hapusEvent(id) {
        if (confirm("Apakah Anda yakin ingin menghapus event ini?")) {
            fetch('hapus_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id_event=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Event berhasil dihapus!');
                        // Hapus row dari tabel tanpa reload
                        const row = document.querySelector(`button[onclick="hapusEvent(${id})"]`)?.closest('tr');
                        if (row) row.remove();
                    } else {
                        alert('Gagal menghapus: ' + data.message);
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan: ' + err);
                });
        }
    }

    // Pencarian/filter tabel
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('#eventTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    }
</script>