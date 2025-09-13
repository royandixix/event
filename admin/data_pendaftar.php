<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../function/config.php';

// ====== PROSES POST (UPDATE/DELETE) ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // UPDATE STATUS
    if ($action === 'update_status') {
        $id     = intval($_POST['id']);
        $tipe   = $_POST['tipe'];
        $status = $_POST['status'];

        $column = ($tipe === 'manajer') ? 'id_manajer' : 'id_peserta';
        $id_event = 3;

        $checkInvoice = $db->query("SELECT * FROM invoice WHERE $column = $id");
        $invoiceExists = $checkInvoice->num_rows > 0;

        if (!$invoiceExists) {
            $nomor_invoice = 'SSM/INV-PADDOCK/' . date('m/Y') . '/' . mt_rand(100000, 999999);
            $query = "INSERT INTO invoice 
                ($column, id_event, status, nomor_invoice, total_harga, kode_unik, total_transfer, bank_tujuan, no_rekening, nama_pemilik_rekening, slot_id) 
                VALUES (?, ?, ?, ?, 0, 0, 0, '', '', '', 0)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iiss", $id, $id_event, $status, $nomor_invoice);
        } else {
            $query = "UPDATE invoice SET status = ? WHERE $column = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("si", $status, $id);
        }

        if ($stmt->execute()) {
            $_SESSION['flash'] = ['status' => 'success', 'message' => "Status berhasil diubah menjadi $status"];
        } else {
            $_SESSION['flash'] = ['status' => 'error', 'message' => "Gagal update status: " . $stmt->error];
        }
        header("Location: data_pendaftar.php");
        exit;
    }

    // DELETE DATA
    if ($action === 'delete_data') {
        $id = intval($_POST['id']);
        $tipe = strtolower($_POST['tipe']);
        $table = ($tipe === 'manajer') ? 'manajer' : 'peserta';
        $id_column = ($tipe === 'manajer') ? 'id_manajer' : 'id_peserta';
        $kelas_table = ($tipe === 'manajer') ? 'manajer_kelas' : 'peserta_kelas';
        $kelas_column = ($tipe === 'manajer') ? 'manajer_id' : 'peserta_id';

        $db->begin_transaction();
        try {
            $stmt = $db->prepare("DELETE FROM invoice WHERE $id_column = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $stmt = $db->prepare("DELETE FROM $kelas_table WHERE $kelas_column = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $fotoColumn = ($tipe === 'manajer') ? "foto_manajer" : "foto_peserta";
            $fotoQuery = $db->query("SELECT $fotoColumn AS foto FROM $table WHERE $id_column = $id");
            $foto = $fotoQuery->fetch_assoc()['foto'] ?? null;

            $stmt = $db->prepare("DELETE FROM $table WHERE $id_column = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            if ($foto) {
                $filePath = "../uploads/foto_{$tipe}/" . $foto;
                if (file_exists($filePath)) unlink($filePath);
            }

            $db->commit();
            $_SESSION['flash'] = ['status' => 'success', 'message' => "Data berhasil dihapus."];
        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['flash'] = ['status' => 'error', 'message' => "Gagal hapus data: " . $e->getMessage()];
        }

        header("Location: data_pendaftar.php");
        exit;
    }
}

// ====== TEMPLATE HEADER ======
include 'templates/header.php';
include 'templates/sidebar.php';

// ====== AMBIL DATA ======
$combinedQuery = "
    (SELECT m.id_manajer AS id, m.nama_manajer AS nama, m.nama_tim, m.email, m.whatsapp, m.foto_manajer AS foto, 
        IFNULL(p.nama_provinsi, m.asal_provinsi) AS nama_provinsi, i.status AS status_bayar, i.bukti_transfer, i.id_invoice,
        'manajer' AS tipe 
     FROM manajer m 
     LEFT JOIN provinsi p ON m.id_provinsi = p.id_provinsi 
     LEFT JOIN invoice i ON m.id_manajer = i.id_manajer)
    UNION ALL
    (SELECT ps.id_peserta AS id, ps.nama_peserta AS nama, ps.nama_tim, ps.email, ps.whatsapp, ps.foto_peserta AS foto, 
        IFNULL(p.nama_provinsi, ps.asal_provinsi) AS nama_provinsi, i.status AS status_bayar, i.bukti_transfer, i.id_invoice,
        'peserta' AS tipe 
     FROM peserta ps 
     LEFT JOIN provinsi p ON ps.id_provinsi = p.id_provinsi 
     LEFT JOIN invoice i ON ps.id_peserta = i.id_peserta)
    ORDER BY nama ASC
";
$result = $db->query($combinedQuery);
$daftar = $result->fetch_all(MYSQLI_ASSOC);

// Ambil data kelas
$kelasData = [];
$manajerKelasQuery = $db->query("SELECT * FROM manajer_kelas");
while ($row = $manajerKelasQuery->fetch_assoc()) $kelasData['manajer'][$row['manajer_id']][] = $row;
$pesertaKelasQuery = $db->query("SELECT * FROM peserta_kelas");
while ($row = $pesertaKelasQuery->fetch_assoc()) $kelasData['peserta'][$row['peserta_id']][] = $row;
?>

<div class="content">
    <div class="container">

        <?php if (!empty($_SESSION['flash'])): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    title: "<?= $_SESSION['flash']['status'] === 'success' ? 'Sukses!' : 'Error!' ?>",
                    text: "<?= $_SESSION['flash']['message'] ?>",
                    icon: "<?= $_SESSION['flash']['status'] ?>",
                    timer: 2000,
                    showConfirmButton: false
                });
            </script>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <section class="py-5 px-4" style="max-width: 800px;">
            <h1 class="display-4 fw-bold text-gradient"
                style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
                Halaman Data Pendaftar
            </h1>
            <p class="mt-3 fs-5 text-muted">Menampilkan daftar manajer dan peserta beserta detailnya.</p>
        </section>

        <div class="mb-3 d-flex gap-2 flex-wrap tombol-group">
            <a href="cetak_manajer_pdf.php" target="_blank" class="btn btn-danger d-flex align-items-center gap-2">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>

            <a href="cetak_manajer_excel.php" target="_blank" class="btn btn-success d-flex align-items-center gap-2">
                <i class="fas fa-file-excel"></i> Cetak Excel
            </a>
        </div>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama, tim, email, provinsi...">
        </div>

        <div class="table-responsive">
            <table class="table custom-table" id="pendaftarTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Nama Tim</th>
                        <th>Email / WhatsApp</th>
                        <th>Asal Provinsi</th>
                        <th>Foto</th>
                        <th>Kelas</th>
                        <th>Tipe</th>
                        <th>Status Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($daftar)) : ?>
                        <?php foreach ($daftar as $index => $d) : ?>
                            <tr>
                                <td class="searchable"><?= $index + 1 ?></td>
                                <td class="searchable"><?= htmlspecialchars($d['nama']) ?></td>
                                <td class="searchable"><?= $d['nama_tim'] ? htmlspecialchars($d['nama_tim']) : '-' ?></td>
                                <td class="searchable"><?= htmlspecialchars($d['email']) ?><br><?= htmlspecialchars($d['whatsapp']) ?></td>
                                <td class="searchable"><?= htmlspecialchars($d['nama_provinsi']) ?></td>
                                <td>
                                    <img src="../uploads/<?= $d['tipe'] == 'manajer' ? 'foto_manajer' : 'foto_peserta' ?>/<?= htmlspecialchars($d['foto']) ?>"
                                        alt="Foto" class="img-thumbnail" style="width:60px; height:60px; object-fit: cover;">
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#kelasModal<?= $d['tipe'] . $d['id'] ?>">Lihat Kelas</button>
                                </td>
                                <td><?= ucfirst($d['tipe']) ?></td>
                                <td>
                                    <button class="btn <?= $d['status_bayar'] === 'lunas' ? 'btn-success' : 'btn-warning' ?> btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#buktiModal<?= $d['tipe'] . $d['id'] ?>">
                                        <?= $d['status_bayar'] === 'lunas' ? 'Lunas' : 'Pending' ?>
                                    </button>
                                </td>
                                <td>
                                    <form method="POST" class="delete-form d-inline">
                                        <input type="hidden" name="action" value="delete_data">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <input type="hidden" name="tipe" value="<?= $d['tipe'] ?>">
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Kelas -->
                            <div class="modal fade" id="kelasModal<?= $d['tipe'] . $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Kelas <?= ucfirst($d['tipe']) ?>: <?= htmlspecialchars($d['nama']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php $kelasList = $kelasData[$d['tipe']][$d['id']] ?? []; ?>
                                            <?php if (!empty($kelasList)) : ?>
                                                <ul class="list-unstyled">
                                                    <?php foreach ($kelasList as $k) : ?>
                                                        <li class="mb-2 p-2 rounded" style="background-color: #f8f9fa;">
                                                            <strong>Kelas:</strong> <?= htmlspecialchars($k['kelas']) ?><br>
                                                            <strong>Warna:</strong> <?= htmlspecialchars($k['warna_kendaraan']) ?><br>
                                                            <strong>Tipe:</strong> <?= htmlspecialchars($k['tipe_kendaraan']) ?>
                                                            <?php if ($d['tipe'] == 'peserta' && !empty($k['nomor_polisi'])) : ?>
                                                                <br><strong>Nopol:</strong> <?= htmlspecialchars($k['nomor_polisi']) ?>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-muted">Belum ada data kelas</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Bukti -->
                            <div class="modal fade" id="buktiModal<?= $d['tipe'] . $d['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Bukti Transfer - <?= htmlspecialchars($d['nama']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <?php if (!empty($d['bukti_transfer'])): ?>
                                                <img src="../uploads/bukti/<?= htmlspecialchars($d['bukti_transfer']) ?>" class="img-fluid rounded">
                                            <?php else: ?>
                                                <p class="text-muted">Belum upload bukti transfer</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                                <input type="hidden" name="tipe" value="<?= $d['tipe'] ?>">
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="btn btn-warning">Pending</button>
                                            </form>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                                <input type="hidden" name="tipe" value="<?= $d['tipe'] ?>">
                                                <input type="hidden" name="status" value="lunas">
                                                <button type="submit" class="btn btn-success">Lunas</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data pendaftar</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Konfirmasi hapus
        document.querySelectorAll(".delete-form").forEach(function(form) {
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin mau hapus data ini?',
                    text: "Data akan dihapus permanen dan tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Pencarian
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('pendaftarTable');
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