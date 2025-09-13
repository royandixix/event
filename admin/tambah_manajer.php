<?php
include '../function/config.php';
include 'templates/header.php';
include 'templates/sidebar.php';

$error = '';

if (isset($_POST['simpan'])) {
    // Ambil data manajer
    $nama_manajer   = trim($_POST['nama_manajer']);
    $nama_tim       = trim($_POST['nama_tim']);
    $email          = trim($_POST['email']);
    $whatsapp       = trim($_POST['whatsapp']);
    $asal_provinsi  = trim($_POST['asal_provinsi']);

    // Ambil data kelas/kendaraan
    $kelas           = trim($_POST['kelas']);
    $warna_kendaraan = trim($_POST['warna_kendaraan']);
    $tipe_kendaraan  = trim($_POST['tipe_kendaraan']);

    // Upload foto (opsional)
    $foto_manajer = '';
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
        // Insert ke tabel manajer
        $stmt = $db->prepare("INSERT INTO manajer (nama_manajer, nama_tim, email, whatsapp, asal_provinsi, foto_manajer) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nama_manajer, $nama_tim, $email, $whatsapp, $asal_provinsi, $foto_manajer);

        if ($stmt->execute()) {
            $id_manajer_baru = $db->insert_id;

            // Insert ke tabel manajer_kelas (tanpa nomor polisi)
            $stmt2 = $db->prepare("INSERT INTO manajer_kelas (manajer_id, kelas, warna_kendaraan, tipe_kendaraan) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("isss", $id_manajer_baru, $kelas, $warna_kendaraan, $tipe_kendaraan);
            $stmt2->execute();
            $stmt2->close();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Manajer Ditambahkan',
                    text: 'Data manajer berhasil disimpan.',
                    confirmButtonText: 'OK'
                }).then(() => window.location='data_manajer.php');
            </script>";
        } else {
            $error = "Gagal menambahkan manajer.";
        }
        $stmt->close();
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
                Tambah Manajer Baru
            </h1>
            <p class="mt-3 fs-5 text-muted">Isi form di bawah untuk menambahkan manajer baru.</p>
        </section>

        <form method="POST" enctype="multipart/form-data">
            <!-- Nama Manajer -->
            <div class="mb-3">
                <label class="form-label">Nama Manajer</label>
                <input type="text" class="form-control" name="nama_manajer" required>
            </div>

            <!-- Nama Tim -->
            <div class="mb-3">
                <label class="form-label">Nama Tim</label>
                <input type="text" class="form-control" name="nama_tim" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <!-- WhatsApp -->
            <div class="mb-3">
                <label class="form-label">WhatsApp</label>
                <input type="text" class="form-control" name="whatsapp" required>
            </div>

            <!-- Asal Provinsi -->
            <div class="mb-3">
                <label class="form-label">Asal Provinsi</label>
                <input type="text" class="form-control" name="asal_provinsi" required>
            </div>

            <!-- Foto Manajer -->
            <div class="mb-3">
                <label class="form-label">Foto Manajer (opsional)</label>
                <input type="file" class="form-control" name="foto_manajer" accept="image/*">
            </div>

            <!-- Nama Kelas -->
            <div class="mb-3">
                <label class="form-label">Kelas</label>
                <input type="text" class="form-control" name="kelas" required>
            </div>

            <!-- Warna Kendaraan -->
            <div class="mb-3">
                <label class="form-label">Warna Kendaraan</label>
                <input type="text" class="form-control" name="warna_kendaraan" required>
            </div>

            <!-- Tipe Kendaraan -->
            <div class="mb-3">
                <label class="form-label">Tipe Kendaraan</label>
                <input type="text" class="form-control" name="tipe_kendaraan" required>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary">Simpan Manajer</button>
            <a href="data_manajer.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
