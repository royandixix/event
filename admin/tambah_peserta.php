<?php
include '../function/config.php';
include 'templates/header.php';
include 'templates/sidebar.php';

$error = '';

if (isset($_POST['simpan'])) {
    // Ambil data peserta
    $nama_peserta    = trim($_POST['nama_peserta']);
    $email           = trim($_POST['email']);
    $no_hp           = trim($_POST['no_hp']);
    $id_event        = $_POST['id_event'];

    // Ambil data kelas/kendaraan
    $kelas           = trim($_POST['kelas']);
    $warna_kendaraan = trim($_POST['warna_kendaraan']);
    $tipe_kendaraan  = trim($_POST['tipe_kendaraan']);

    // Validasi
    if (empty($nama_peserta) || empty($email) || empty($no_hp) || empty($id_event) || empty($kelas) || empty($warna_kendaraan) || empty($tipe_kendaraan)) {
        $error = "Semua field wajib diisi.";
    }

    if (!$error) {
        // Insert ke tabel peserta
        $stmt = $db->prepare("INSERT INTO peserta (nama_peserta, email, whatsapp, id_event) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nama_peserta, $email, $no_hp, $id_event);

        if ($stmt->execute()) {
            $id_peserta_baru = $db->insert_id;

            // Insert ke tabel peserta_kelas tanpa nomor polisi
            $stmt2 = $db->prepare("INSERT INTO peserta_kelas (peserta_id, kelas, warna_kendaraan, tipe_kendaraan) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("isss", $id_peserta_baru, $kelas, $warna_kendaraan, $tipe_kendaraan);
            $stmt2->execute();
            $stmt2->close();

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Peserta Ditambahkan',
                    text: 'Peserta berhasil ditambahkan ke event dan kelas.',
                    confirmButtonText: 'OK'
                }).then(() => window.location='data_peserta.php');
            </script>";
        } else {
            $error = "Gagal menambahkan peserta.";
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
                Tambah Peserta Baru
            </h1>
            <p class="mt-3 fs-5 text-muted">Isi form di bawah untuk menambahkan peserta baru.</p>
        </section>

        <form method="POST">
            <!-- Nama Peserta -->
            <div class="mb-3">
                <label class="form-label">Nama Peserta</label>
                <input type="text" class="form-control" name="nama_peserta" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <!-- No HP -->
            <div class="mb-3">
                <label class="form-label">No. HP</label>
                <input type="text" class="form-control" name="no_hp" required>
            </div>

            <!-- Pilih Event -->
            <div class="mb-3">
                <label class="form-label">Pilih Event</label>
                <select class="form-select" name="id_event" required>
                    <option value="">-- Pilih Event --</option>
                    <?php
                    $result = $db->query("SELECT id_event, judul_event FROM event ORDER BY tanggal_mulai DESC");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_event']}'>{$row['judul_event']}</option>";
                    }
                    ?>
                </select>
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

          

            <button type="submit" name="simpan" class="btn btn-primary">Simpan Peserta</button>
            <a href="data_peserta.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
