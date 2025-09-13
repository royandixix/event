<?php
include '../function/config.php';
include 'templates/header.php';
include 'templates/sidebar.php';

$error = '';

// Ambil id_manajer dari query string
$id_manajer = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id_manajer) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'ID manajer tidak valid.'
        }).then(() => window.location='data_manajer.php');
    </script>";
    exit;
}

// Ambil data manajer dari database
$stmt = $db->prepare("SELECT * FROM manajer WHERE id_manajer = ?");
$stmt->bind_param("i", $id_manajer);
$stmt->execute();
$result = $stmt->get_result();
$manajer = $result->fetch_assoc();

if (!$manajer) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Manajer tidak ditemukan.'
        }).then(() => window.location='data_manajer.php');
    </script>";
    exit;
}

// Ambil data kelas/kendaraan
$stmt2 = $db->prepare("SELECT * FROM manajer_kelas WHERE manajer_id = ?");
$stmt2->bind_param("i", $id_manajer);
$stmt2->execute();
$result2 = $stmt2->get_result();
$kelas_data = $result2->fetch_assoc();

if (isset($_POST['simpan'])) {
    // Ambil data dari form
    $nama_manajer   = trim($_POST['nama_manajer']);
    $nama_tim       = trim($_POST['nama_tim']);
    $email          = trim($_POST['email']);
    $whatsapp       = trim($_POST['whatsapp']);
    $asal_provinsi  = trim($_POST['asal_provinsi']);

    $kelas           = trim($_POST['kelas']);
    $warna_kendaraan = trim($_POST['warna_kendaraan']);
    $tipe_kendaraan  = trim($_POST['tipe_kendaraan']);

    // Upload foto (opsional)
    $foto_manajer = $manajer['foto_manajer']; // default foto lama
    if (!empty($_FILES['foto_manajer']['name'])) {
        $target_dir = "../uploads/foto_manajer/";
        $foto_manajer = time() . '_' . basename($_FILES['foto_manajer']['name']);
        move_uploaded_file($_FILES['foto_manajer']['tmp_name'], $target_dir . $foto_manajer);
    }

    // Validasi
    if (empty($nama_manajer) || empty($nama_tim) || empty($email) || empty($whatsapp) || empty($asal_provinsi) || empty($kelas) || empty($warna_kendaraan) || empty($tipe_kendaraan)) {
        $error = "Semua field wajib diisi (foto opsional).";
    }

    if (!$error) {
        // Update tabel manajer
        $stmt = $db->prepare("UPDATE manajer SET nama_manajer=?, nama_tim=?, email=?, whatsapp=?, asal_provinsi=?, foto_manajer=? WHERE id_manajer=?");
        $stmt->bind_param("ssssssi", $nama_manajer, $nama_tim, $email, $whatsapp, $asal_provinsi, $foto_manajer, $id_manajer);
        $stmt->execute();
        $stmt->close();

        // Update tabel manajer_kelas tanpa nomor polisi
        $stmt2 = $db->prepare("UPDATE manajer_kelas SET kelas=?, warna_kendaraan=?, tipe_kendaraan=? WHERE manajer_id=?");
        $stmt2->bind_param("sssi", $kelas, $warna_kendaraan, $tipe_kendaraan, $id_manajer);
        $stmt2->execute();
        $stmt2->close();

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data manajer berhasil diupdate.',
                confirmButtonText: 'OK'
            }).then(() => window.location='data_manajer.php');
        </script>";
    }

    if ($error) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '".htmlspecialchars($error)."'
            });
        </script>";
    }
}
?>

<div class="content">
    <div class="container" style="max-width: 800px;">
        <section class="py-5 px-4">
            <h1 class="display-4 fw-bold text-gradient" style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
                Edit Manajer
            </h1>
            <p class="mt-3 fs-5 text-muted">Ubah data manajer sesuai kebutuhan.</p>
        </section>

        <form method="POST" enctype="multipart/form-data">
            <!-- Nama Manajer -->
            <div class="mb-3">
                <label class="form-label">Nama Manajer</label>
                <input type="text" class="form-control" name="nama_manajer" value="<?= htmlspecialchars($manajer['nama_manajer']) ?>" required>
            </div>

            <!-- Nama Tim -->
            <div class="mb-3">
                <label class="form-label">Nama Tim</label>
                <input type="text" class="form-control" name="nama_tim" value="<?= htmlspecialchars($manajer['nama_tim']) ?>" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($manajer['email']) ?>" required>
            </div>

            <!-- WhatsApp -->
            <div class="mb-3">
                <label class="form-label">WhatsApp</label>
                <input type="text" class="form-control" name="whatsapp" value="<?= htmlspecialchars($manajer['whatsapp']) ?>" required>
            </div>

            <!-- Asal Provinsi -->
            <div class="mb-3">
                <label class="form-label">Asal Provinsi</label>
                <input type="text" class="form-control" name="asal_provinsi" value="<?= htmlspecialchars($manajer['asal_provinsi']) ?>" required>
            </div>

            <!-- Foto Manajer -->
            <div class="mb-3">
                <label class="form-label">Foto Manajer (opsional)</label>
                <input type="file" class="form-control" name="foto_manajer" accept="image/*">
                <?php if($manajer['foto_manajer']): ?>
                    <img src="../uploads/foto_manajer/<?= $manajer['foto_manajer'] ?>" alt="Foto Manajer" style="width:100px; margin-top:10px;">
                <?php endif; ?>
            </div>

            <!-- Nama Kelas -->
            <div class="mb-3">
                <label class="form-label">Kelas</label>
                <input type="text" class="form-control" name="kelas" value="<?= htmlspecialchars($kelas_data['kelas']) ?>" required>
            </div>

            <!-- Warna Kendaraan -->
            <div class="mb-3">
                <label class="form-label">Warna Kendaraan</label>
                <input type="text" class="form-control" name="warna_kendaraan" value="<?= htmlspecialchars($kelas_data['warna_kendaraan']) ?>" required>
            </div>

            <!-- Tipe Kendaraan -->
            <div class="mb-3">
                <label class="form-label">Tipe Kendaraan</label>
                <input type="text" class="form-control" name="tipe_kendaraan" value="<?= htmlspecialchars($kelas_data['tipe_kendaraan']) ?>" required>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary">Update Manajer</button>
            <a href="data_manajer.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
