<?php
include '../function/config.php';
include 'templates/header.php';
include 'templates/sidebar.php';

// Ambil semua event
$events = mysqli_fetch_all(
    mysqli_query($db, "SELECT * FROM event ORDER BY created_at DESC"),
    MYSQLI_ASSOC
);

// Ambil semua invoice dan kelompokkan per event
$invoiceResult = mysqli_query($db, "SELECT * FROM invoice ORDER BY created_at DESC");
$invoicesData = [];
while ($invRow = mysqli_fetch_assoc($invoiceResult)) {
    $invoicesData[$invRow['id_event']][] = $invRow;
}
?>

<div class="content">
    <div class="container">

        <!-- Header Section -->
        <section class="py-5 px-4" style="max-width: 800px;">
            <h1 class="display-4 fw-bold text-gradient"
                style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
                Halaman Data Event
            </h1>
            <p class="mt-3 fs-5 text-muted" style="line-height: 1.8;">
                Halaman ini menampilkan informasi lengkap mengenai berbagai event yang diselenggarakan
            </p>
        </section>

        <!-- Tombol Tambah Event -->
        <div class="mb-3">
            <a href="tambah_event.php">
            <button type="button" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle me-2"></i> Tambah Data Event
            </button>
            </a>
        </div>

        <!-- Tabel Event -->
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Event</th>
                        <th>Deskripsi & Lokasi</th>
                        <th>Tanggal</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($events)): ?>
                        <?php $no = 1;
                        foreach ($events as $event): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($event['judul_event']) ?></td>
                                <td>
                                    <?= htmlspecialchars($event['deskripsi_event']) ?><br>
                                    <small><?= htmlspecialchars($event['lokasi_event']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($event['tanggal_mulai']) ?> s/d <?= htmlspecialchars($event['tanggal_selesai']) ?></td>
                                <td>Rp <?= number_format($event['harga_event'], 0, ',', '.') ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="edit_event.php?id=<?= $event['id_event'] ?>" class="btn btn-warning btn-lg" title="Edit Event">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-danger btn-lg" title="Hapus Event" onclick="confirmDelete(<?= $event['id_event'] ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <button class="btn btn-info btn-lg" title="Lihat Detail Invoice" data-bs-toggle="modal" data-bs-target="#invoiceModal<?= $event['id_event'] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data event</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Invoice -->
        <?php foreach ($events as $event):
            $event_id = $event['id_event'];
            if (!empty($invoicesData[$event_id])): ?>
                <div class="modal fade" id="invoiceModal<?= $event_id ?>" tabindex="-1" aria-labelledby="invoiceModalLabel<?= $event_id ?>" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content shadow-lg rounded-4 border-0">
                            <div class="modal-header bg-gradient text-white rounded-top-4">
                                <h5 class="modal-title" id="invoiceModalLabel<?= $event_id ?>">
                                    <i class="bi bi-receipt me-2"></i> Invoice Event: <?= htmlspecialchars($event['judul_event']) ?>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nomor Invoice</th>
                                                <th>Total Harga</th>
                                                <th>Kode Unik</th>
                                                <th>Total Transfer</th>
                                                <th>Bank Tujuan</th>
                                                <th>No Rekening</th>
                                                <th>Nama Pemilik Rekening</th>
                                                <th>Gambar Bank</th>
                                                <th>Status</th>
                                                <th>Waktu</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($invoicesData[$event_id] as $inv): ?>
                                                <tr class="<?= strtolower($inv['status']) === 'pending' ? 'table-warning' : ($inv['status'] === 'Lunas' ? 'table-success' : '') ?>">
                                                    <td><?= htmlspecialchars($inv['nomor_invoice']) ?></td>
                                                    <td>Rp <?= number_format($inv['total_harga'], 0, ',', '.') ?></td>
                                                    <td><?= htmlspecialchars($inv['kode_unik']) ?></td>
                                                    <td>Rp <?= number_format($inv['total_transfer'], 0, ',', '.') ?></td>
                                                    <td><?= htmlspecialchars($inv['bank_tujuan']) ?></td>
                                                    <td><?= htmlspecialchars($inv['no_rekening']) ?></td>
                                                    <td><?= htmlspecialchars($inv['nama_pemilik_rekening']) ?></td>
                                                    <td>
                                                        <?php if (!empty($inv['gambar_bank'])): ?>
                                                            <img src="../uploads/bank/<?= htmlspecialchars($inv['gambar_bank']) ?>" alt="Logo Bank" style="max-width:200px; margin-top:10px;">
                                                        <?php endif; ?>

                                                    </td>


                                                    <td><?= ucfirst($inv['status']) ?></td>
                                                    <td><?= date('d F Y, H:i', strtotime($inv['created_at'])) ?></td>


                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
        <?php endif;
        endforeach; ?>

    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(btn) {
        Swal.fire({
            title: 'Hapus Event?',
            text: "Data event akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Terhapus!', 'Event berhasil dihapus.', 'success');
            }
        });
    }

    function confirmClose(btn) {
        Swal.fire({
            title: 'Tutup Modal?',
            text: "Apakah Anda yakin ingin menutup modal invoice?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, tutup',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                let modal = bootstrap.Modal.getInstance(btn.closest('.modal'));
                modal.hide();
            }
        });
    }
</script>

<!-- CSS Tombol dan Modal -->
<style>
    /* Tombol icon-only tetap keren */
    .btn-warning,
    .btn-danger,
    .btn-info {
        font-weight: 600;
        border: none;
        width: 48px;
        height: 48px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 0.5rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    /* Hover effect */
    .btn-warning:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(255, 193, 7, 0.6);
    }

    .btn-danger:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(220, 53, 69, 0.6);
    }

    .btn-info:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(13, 110, 253, 0.6);
    }

    /* Icon lebih besar */
    .btn i {
        font-size: 1.2rem;
    }

    /* Modal header gradient */
    .modal-header.bg-gradient {
        background: linear-gradient(90deg, #4facfe, #00f2fe);
    }

    /* Table hover effect */
    .table-hover tbody tr:hover {
        background-color: rgba(79, 172, 254, 0.1);
    }

    /* Table status colors */
    .table-success td {
        font-weight: 600;
        color: #155724;
    }

    .table-warning td {
        font-weight: 600;
        color: #856404;
    }
</style>
</div>
</div>
</div>

<?php include 'templates/footer.php'; ?>