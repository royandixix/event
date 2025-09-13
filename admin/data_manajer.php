<?php
include '../function/config.php';
include 'templates/header.php';
include 'templates/sidebar.php';

// Ambil semua manajer yang sudah LUNAS
$manajerQuery = "
    SELECT m.* 
    FROM manajer m
    INNER JOIN invoice i ON m.id_manajer = i.id_manajer
    WHERE i.status = 'lunas'
    ORDER BY m.created_at DESC
";
$manajerResult = mysqli_query($db, $manajerQuery);
$manajer = mysqli_fetch_all($manajerResult, MYSQLI_ASSOC);

// Ambil semua kelas manajer dan kelompokkan per manajer
$kelasResult = mysqli_query($db, "SELECT * FROM manajer_kelas");
$kelasData = [];
while ($kelasRow = mysqli_fetch_assoc($kelasResult)) {
    $kelasData[$kelasRow['manajer_id']][] = $kelasRow;
}
?>

<div class="content">
    <div class="container">

        <section class="py-5 px-4" style="max-width: 800px;">
            <h1 class="display-4 fw-bold text-gradient"
                style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
                Halaman Data Manajer
            </h1>
            <p class="mt-3 fs-5 text-muted" style="line-height: 1.8;">
                Menampilkan daftar manajer yang sudah lunas beserta detail kelas dan kendaraan.
            </p>
        </section>

        <div class="mb-3 d-flex gap-2 flex-wrap tombol-group">
            <a href="tambah_manajer.php" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="fas fa-plus-circle"></i> Tambah Manajer
            </a>

            <a href="cetak_manajer_pdf.php" target="_blank" class="btn btn-danger d-flex align-items-center gap-2">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>

            <a href="cetak_manajer_excel.php" target="_blank" class="btn btn-success d-flex align-items-center gap-2">
                <i class="fas fa-file-excel"></i> Cetak Excel
            </a>
        </div>

        <!-- Input Search -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama, tim, email, provinsi...">
        </div>

        <div class="table-responsive">
            <table class="table custom-table" id="manajerTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Manajer</th>
                        <th>Nama Tim</th>
                        <th>Email / WhatsApp</th>
                        <th>Asal Provinsi</th>
                        <th>Foto</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($manajer)): ?>
                        <?php $no = 1;
                        foreach ($manajer as $m): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="searchable"><?= htmlspecialchars($m['nama_manajer']) ?></td>
                                <td class="searchable"><?= htmlspecialchars($m['nama_tim']) ?></td>
                                <td class="searchable">
                                    <?= htmlspecialchars($m['email']) ?><br>
                                    <?= htmlspecialchars($m['whatsapp']) ?>
                                </td>
                                <td class="searchable"><?= htmlspecialchars($m['asal_provinsi']) ?></td>
                                <td>
                                    <?php if (!empty($m['foto_manajer'])): ?>
                                        <img src="../uploads/foto_manajer/<?= htmlspecialchars($m['foto_manajer']) ?>" alt="Foto Manajer" style="width:60px; border-radius:5px;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($kelasData[$m['id_manajer']])): ?>
                                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#kelasModal<?= $m['id_manajer'] ?>">Lihat Kelas</button>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="edit_manajer.php?id=<?= $m['id_manajer'] ?>" class="btn btn-warning btn-lg">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-danger btn-lg" onclick="confirmDelete(<?= $m['id_manajer'] ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada manajer lunas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Kelas Manajer -->
        <?php foreach ($manajer as $m):
            $manajer_id = $m['id_manajer'];
            if (!empty($kelasData[$manajer_id])): ?>
                <div class="modal fade" id="kelasModal<?= $manajer_id ?>" tabindex="-1" aria-labelledby="kelasModalLabel<?= $manajer_id ?>" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content shadow-lg rounded-4 border-0">
                            <div class="modal-header bg-gradient text-white rounded-top-4">
                                <h5 class="modal-title" id="kelasModalLabel<?= $manajer_id ?>">
                                    <i class="bi bi-card-list me-2"></i> Kelas Manajer: <?= htmlspecialchars($m['nama_manajer']) ?>
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($kelasData[$manajer_id] as $k): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($k['kelas']) ?></td>
                                                    <td><?= htmlspecialchars($k['warna_kendaraan']) ?></td>
                                                    <td><?= htmlspecialchars($k['tipe_kendaraan']) ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Manajer?',
            text: "Data manajer akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'hapus_manajer.php?id=' + id;
            }
        });
    }

    // Live Search
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('manajerTable');
        const rows = table.querySelectorAll('tbody tr');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const searchableCells = row.querySelectorAll('.searchable');
                let match = false;
                searchableCells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(filter)) match = true;
                });
                row.style.display = match ? '' : 'none';
            });
        });
    });
</script>

<?php include 'templates/footer.php'; ?>