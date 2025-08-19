<?php
include '../function/config.php';
include 'templates/header.php';
include 'templates/sidebar.php';

$error = '';

// Ambil ID peserta dari URL
if (!isset($_GET['id'])) {
    echo "<script>
        Swal.fire({ icon: 'error', title: 'Error', text: 'ID peserta tidak ditemukan.' })
        .then(() => window.location='data_peserta.php');
    </script>";
    exit;
}

$id_peserta = (int)$_GET['id'];

// Ambil data peserta dan kelas
$query = $db->prepare("
    SELECT p.*, pk.kelas, pk.warna_kendaraan, pk.tipe_kendaraan, pk.nomor_polisi
    FROM peserta p
    LEFT JOIN peserta_kelas pk ON p.id_peserta = pk.peserta_id
    WHERE p.id_peserta = ?
");
$query->bind_param("i", $id_peserta);
$query->execute();
$result = $query->get_result();
$peserta = $result->fetch_assoc();

if (!$peserta) {
    echo "<script>
        Swal.fire({ icon: 'error', title: 'Error', text: 'Peserta tidak ditemukan.' })
        .then(() => window.location='data_peserta.php');
    </script>";
    exit;
}

// Proses update
if (isset($_POST['update'])) {
    $nama_peserta    = trim($_POST['nama_peserta']);
    $email           = trim($_POST['email']);
    $no_hp           = trim($_POST['no_hp']);
    $id_event        = $_POST['id_event'];

    $kelas           = trim($_POST['kelas']);
    $warna_kendaraan = trim($_POST['warna_kendaraan']);
    $tipe_kendaraan  = trim($_POST['tipe_kendaraan']);
    $nomor_polisi    = trim($_POST['nomor_polisi']);

    if (empty($nama_peserta) || empty($email) || empty($no_hp) || empty($id_event) || empty($kelas) || empty($warna_kendaraan) || empty($tipe_kendaraan)) {
        $error = "Semua field wajib diisi (nomor polisi opsional).";
    }

    if (!$error) {
        // Update peserta
        $stmt = $db->prepare("UPDATE peserta SET nama_peserta = ?, email = ?, whatsapp = ?, id_event = ? WHERE id_peserta = ?");
        $stmt->bind_param("sssii", $nama_peserta, $email, $no_hp, $id_event, $id_peserta);
        $stmt->execute();

        // Update peserta_kelas (jika ada)
        $stmt2 = $db->prepare("
            INSERT INTO peserta_kelas (peserta_id, kelas, warna_kendaraan, tipe_kendaraan, nomor_polisi)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE kelas=VALUES(kelas), warna_kendaraan=VALUES(warna_kendaraan), tipe_kendaraan=VALUES(tipe_kendaraan), nomor_polisi=VALUES(nomor_polisi)
        ");
        $stmt2->bind_param("issss", $id_peserta, $kelas, $warna_kendaraan, $tipe_kendaraan, $nomor_polisi);
        $stmt2->execute();

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data peserta berhasil diperbarui.',
                confirmButtonText: 'OK'
            }).then(() => window.location='data_peserta.php');
        </script>";
    }

    if ($error) {
        echo "<script>
            Swal.fire({ icon: 'error', title: 'Gagal', text: '".htmlspecialchars($error)."' });
        </script>";
    }
}
?>

<div class="content">
    <div class="container" style="max-width: 800px;">
        <section class="py-5 px-4">
            <h1 class="display-4 fw-bold text-gradient" style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
                Edit Peserta
            </h1>
            <p class="mt-3 fs-5 text-muted">Ubah data peserta dan kelas/kendaraan di bawah ini.</p>
        </section>

        <form method="POST">
            <!-- Nama Peserta -->
            <div class="mb-3">
                <label class="form-label">Nama Peserta</label>
                <input type="text" class="form-control" name="nama_peserta" value="<?= htmlspecialchars($peserta['nama_peserta']); ?>" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($peserta['email']); ?>" required>
            </div>

            <!-- No HP -->
            <div class="mb-3">
                <label class="form-label">No. HP</label>
                <input type="text" class="form-control" name="no_hp" value="<?= htmlspecialchars($peserta['whatsapp']); ?>" required>
            </div>

            <!-- Pilih Event -->
            <div class="mb-3">
                <label class="form-label">Pilih Event</label>
                <select class="form-select" name="id_event" required>
                    <option value="">-- Pilih Event --</option>
                    <?php
                    $result = $db->query("SELECT id_event, judul_event FROM event ORDER BY tanggal_mulai DESC");
                    while ($row = $result->fetch_assoc()) {
                        $selected = ($row['id_event'] == $peserta['id_event']) ? 'selected' : '';
                        echo "<option value='{$row['id_event']}' $selected>{$row['judul_event']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Nama Kelas -->
            <div class="mb-3">
                <label class="form-label">Kelas</label>
                <input type="text" class="form-control" name="kelas" value="<?= htmlspecialchars($peserta['kelas']); ?>" required>
            </div>

            <!-- Warna Kendaraan -->
            <div class="mb-3">
                <label class="form-label">Warna Kendaraan</label>
                <input type="text" class="form-control" name="warna_kendaraan" value="<?= htmlspecialchars($peserta['warna_kendaraan']); ?>" required>
            </div>

            <!-- Tipe Kendaraan -->
            <div class="mb-3">
                <label class="form-label">Tipe Kendaraan</label>
                <input type="text" class="form-control" name="tipe_kendaraan" value="<?= htmlspecialchars($peserta['tipe_kendaraan']); ?>" required>
            </div>

            <!-- Nomor Polisi -->
            <div class="mb-3">
                <label class="form-label">Nomor Polisi (opsional)</label>
                <input type="text" class="form-control" name="nomor_polisi" value="<?= htmlspecialchars($peserta['nomor_polisi']); ?>">
            </div>

            <button type="submit" name="update" class="btn btn-primary">Update Peserta</button>
            <a href="data_peserta.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
