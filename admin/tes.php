<?php
// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include template dan konfigurasi
include '../function/config.php';
include './templates/header.php';
include './templates/sidebar.php';

// =====================
// Query Data Manajer
// =====================
$manajerQuery = "
    SELECT m.id_manajer, m.nama_manajer, m.nama_tim, m.foto_manajer, m.email, m.whatsapp, m.voucher,
           mk.kelas, mk.warna_kendaraan, mk.tipe_kendaraan, p.nama_provinsi
    FROM manajer m
    LEFT JOIN manajer_kelas mk ON m.id_manajer = mk.manajer_id
    LEFT JOIN provinsi p ON m.id_provinsi = p.id_provinsi
    ORDER BY m.id_manajer ASC
";
$manajerResult = $db->query($manajerQuery);
if (!$manajerResult) die("Query manajer gagal: " . $db->error);

// =====================
// Query Data Peserta
// =====================
$pesertaQuery = "
    SELECT ps.id_peserta, ps.nama_peserta, ps.nama_tim, ps.foto_peserta, ps.email, ps.whatsapp, ps.voucher,
           pk.kelas, pk.warna_kendaraan, pk.tipe_kendaraan, pk.nomor_polisi, p.nama_provinsi,
           inv.status as status_pembayaran
    FROM peserta ps
    LEFT JOIN peserta_kelas pk ON ps.id_peserta = pk.peserta_id
    LEFT JOIN provinsi p ON ps.id_provinsi = p.id_provinsi
    LEFT JOIN invoice inv ON ps.id_peserta = inv.id_peserta
    ORDER BY ps.id_peserta ASC
";
$pesertaResult = $db->query($pesertaQuery);
if (!$pesertaResult) die("Query peserta gagal: " . $db->error);
?>

<div class="content container py-5">

    <!-- Header Section -->
    <section class="mb-4">
        <h1 class="display-4 fw-bold text-gradient"
            style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
            Halaman Data Peserta
        </h1>
        <p class="fs-5 text-muted">
            Menampilkan daftar peserta beserta detail kelas dan kendaraan.
        </p>
    </section>

    <!-- Tombol Tambah Peserta -->
    <div class="mb-4">
        <button type="button" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> Tambah Peserta
        </button>
    </div>

    <!-- ===================== -->
    <!-- Tabel Data Manajer -->
    <!-- ===================== -->
    <h2 class="mb-3">Data Manajer</h2>
    <div class="table-responsive mb-5">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Manajer</th>
                    <th>Nama Tim</th>
                    <th>Provinsi</th>
                    <th>Email</th>
                    <th>WhatsApp</th>
                    <th>Voucher</th>
                    <th>Kelas</th>
                    <th>Warna Kendaraan</th>
                    <th>Tipe Kendaraan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $manajerResult->fetch_assoc()): ?>
                <tr class="text-center">
                    <td><?= $no++; ?></td>
                    <td>
                        <?php $foto = !empty($row['foto_manajer']) ? $row['foto_manajer'] : 'default.png'; ?>
                        <img src="../uploads/foto_manajer/<?= htmlspecialchars($foto) ?>" class="w-16 h-16 rounded-full border mx-auto">
                    </td>
                    <td><?= htmlspecialchars($row['nama_manajer'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_tim'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_provinsi'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['whatsapp'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['voucher'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($row['kelas'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['warna_kendaraan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['tipe_kendaraan'] ?? '-') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- ===================== -->
    <!-- Tabel Data Peserta -->
    <!-- ===================== -->
    <h2 class="mb-3">Data Peserta</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Tim</th>
                    <th>Provinsi</th>
                    <th>Email</th>
                    <th>WhatsApp</th>
                    <th>Voucher</th>
                    <th>Kelas</th>
                    <th>Warna Kendaraan</th>
                    <th>Tipe Kendaraan</th>
                    <th>No Polisi</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $pesertaResult->fetch_assoc()): ?>
                <tr class="text-center">
                    <td><?= $no++; ?></td>
                    <td>
                        <?php $foto = !empty($row['foto_peserta']) ? $row['foto_peserta'] : 'default.png'; ?>
                        <img src="../uploads/foto_peserta/<?= htmlspecialchars($foto) ?>" class="w-16 h-16 rounded-full border mx-auto">
                    </td>
                    <td><?= htmlspecialchars($row['nama_peserta'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_tim'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_provinsi'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['whatsapp'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['voucher'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($row['kelas'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['warna_kendaraan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['tipe_kendaraan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nomor_polisi'] ?? '-') ?></td>
                    <td>
                        <?php
                        $status = $row['status_pembayaran'] ?? 'pending';
                        $statusClass = [
                            'lunas' => 'bg-green-200 text-green-800',
                            'pending' => 'bg-yellow-200 text-yellow-800',
                            'batal' => 'bg-red-200 text-red-800'
                        ][$status] ?? 'bg-gray-200 text-gray-800';
                        ?>
                        <span class="px-2 py-1 rounded-full <?= $statusClass ?>"><?= ucfirst($status) ?></span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include '../templates/footer.php'; ?>
