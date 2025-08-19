<?php
include '../function/config.php';
include 'templates/header.php';
include 'templates/sidebar.php';

// Ambil semua peserta
$peserta = mysqli_fetch_all(
    mysqli_query($db, "SELECT * FROM peserta ORDER BY created_at DESC"),
    MYSQLI_ASSOC
);

// Ambil semua kelas peserta dan kelompokkan per peserta
$kelasResult = mysqli_query($db, "SELECT * FROM peserta_kelas");
$kelasData = [];
while ($kelasRow = mysqli_fetch_assoc($kelasResult)) {
    $kelasData[$kelasRow['peserta_id']][] = $kelasRow;
}
?>

<div class="content">
    <div class="container">

        <!-- Header Section -->
        <section class="py-5 px-4" style="max-width: 800px;">
            <h1 class="display-4 fw-bold text-gradient"
                style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
                Halaman Data Peserta
            </h1>
            <p class="mt-3 fs-5 text-muted" style="line-height: 1.8;">
                Menampilkan daftar peserta beserta detail kelas dan kendaraan.
            </p>
        </section>

        <!-- Tombol Tambah Peserta -->
        <div class="mb-3">
            <a href="tambah_peserta.php">
            <button type="button" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle me-2"></i> Tambah Peserta
            </button>
            </a>
        </div>

        <!-- Tabel Peserta -->
        <div class="table-responsive">
    <table class="table custom-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>Nama Tim</th>
                <th>Email / WhatsApp</th>
                <th>Asal Provinsi</th>
                <th>Foto</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($peserta)): ?>
                <?php $no = 1; foreach ($peserta as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($p['nama_peserta']) ?></td>
                        <td><?= htmlspecialchars($p['nama_tim']) ?></td>
                        <td>
                            <?= htmlspecialchars($p['email']) ?><br>
                            <?= htmlspecialchars($p['whatsapp']) ?>
                        </td>
                        <td><?= htmlspecialchars($p['asal_provinsi']) ?></td>
                        <td>
                            <?php if (!empty($p['foto_peserta'])): ?>
                                <img src="../uploads/foto_peserta/<?= htmlspecialchars($p['foto_peserta']) ?>" alt="Foto Peserta" style="width:60px; border-radius:5px;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($kelasData[$p['id_peserta']])): ?>
                                <button class="btn btn-info " data-bs-toggle="modal" data-bs-target="#kelasModal<?= $p['id_peserta'] ?>">Lihat Kelas</button>
                            <?php else: ?>
                                <span class="text-muted">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="edit_peserta.php?id=<?= $p['id_peserta'] ?>" class="btn btn-warning btn-lg">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-lg" onclick="confirmDelete(<?= $p['id_peserta'] ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data peserta</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Kelas Peserta -->
<?php foreach ($peserta as $p):
    $peserta_id = $p['id_peserta'];
    if (!empty($kelasData[$peserta_id])): ?>
        <div class="modal fade" id="kelasModal<?= $peserta_id ?>" tabindex="-1" aria-labelledby="kelasModalLabel<?= $peserta_id ?>" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 border-0">
                    <div class="modal-header bg-gradient text-white rounded-top-4">
                        <h5 class="modal-title" id="kelasModalLabel<?= $peserta_id ?>">
                            <i class="bi bi-card-list me-2"></i> Kelas Peserta: <?= htmlspecialchars($p['nama_peserta']) ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Warna Kendaraan</th>
                                        <th>Tipe Kendaraan</th>
                                        <th>Nomor Polisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kelasData[$peserta_id] as $k): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($k['kelas']) ?></td>
                                            <td><?= htmlspecialchars($k['warna_kendaraan']) ?></td>
                                            <td><?= htmlspecialchars($k['tipe_kendaraan']) ?></td>
                                            <td><?= htmlspecialchars($k['nomor_polisi']) ?></td>
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
<?php endif; endforeach; ?>


    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Peserta?',
            text: "Data peserta akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'hapus_peserta.php?id=' + id;
            }
        });
    }
</script>

<?php include 'templates/footer.php'; ?>
d