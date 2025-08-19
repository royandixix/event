<?php
include '../function/config.php';
include 'templates/header.php';
include 'templates/sidebar.php';

// Initialize error
$error = '';

if (isset($_POST['simpan'])) {
    // Ambil data dan trim
    $judul_event     = trim($_POST['judul_event']);
    $deskripsi_event = trim($_POST['deskripsi_event']);
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $lokasi_event    = trim($_POST['lokasi_event']);
    $harga_event     = preg_replace('/[^\d]/', '', $_POST['harga_event']); // format rupiah → angka
    $bank_tujuan     = trim($_POST['bank_tujuan']);
    $no_rekening     = trim($_POST['no_rekening']);
    $nama_rekening   = trim($_POST['nama_pemilik']);

    // Validasi tanggal
    if ($tanggal_mulai > $tanggal_selesai) {
        $error = "Tanggal mulai tidak boleh lebih dari tanggal selesai.";
    }

    // Upload poster event
    $nama_file_poster = null;
    if (!$error && !empty($_FILES['poster']['name'])) {
        $folder_poster = "../uploads/poster/";
        if (!file_exists($folder_poster)) mkdir($folder_poster, 0777, true);

        $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $error = "Format poster tidak diperbolehkan. Gunakan jpg, jpeg, png, gif.";
        } else {
            $nama_file_poster = time() . "_" . basename($_FILES['poster']['name']);
            move_uploaded_file($_FILES['poster']['tmp_name'], $folder_poster . $nama_file_poster);
        }
    }

    if (!$error) {
        // Insert event
        $stmt = $db->prepare("INSERT INTO event 
            (judul_event, deskripsi_event, tanggal_mulai, tanggal_selesai, lokasi_event, poster_path, harga_event) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $judul_event, $deskripsi_event, $tanggal_mulai, $tanggal_selesai, $lokasi_event, $nama_file_poster, $harga_event);

        if ($stmt->execute()) {
            $id_event = $stmt->insert_id;
            $kode_sementara = "EVT" . date('Ymd') . '-' . str_pad($id_event, 3, '0', STR_PAD_LEFT);

            // Insert paddock slots
            if (!empty($_POST['nama_slot']) && is_array($_POST['nama_slot'])) {
                $stmt_slot = $db->prepare("INSERT INTO paddock_slot (id_event, nomor_slot) VALUES (?, ?)");
                foreach ($_POST['nama_slot'] as $slot) {
                    $slot = trim($slot);
                    if ($slot != '') {
                        $stmt_slot->bind_param("is", $id_event, $slot);
                        $stmt_slot->execute();
                    }
                }
            }

            // Buat invoice
            $no_invoice = "SSM/INV-PADDOCK/" . date("m/Y") . "/" . str_pad($id_event, 6, "0", STR_PAD_LEFT);
            $kode_unik = rand(100, 999);
            $total_transfer = $harga_event + $kode_unik;

            // Upload logo bank
            $nama_file_bank = null;
            if (!empty($_FILES['gambar_bank']['name'])) {
                $folder_bank = "../uploads/bank/";
                if (!file_exists($folder_bank)) mkdir($folder_bank, 0777, true);

                $ext_bank = strtolower(pathinfo($_FILES['gambar_bank']['name'], PATHINFO_EXTENSION));
                $allowed_bank = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($ext_bank, $allowed_bank)) {
                    $nama_file_bank = time() . "_" . basename($_FILES['gambar_bank']['name']);
                    move_uploaded_file($_FILES['gambar_bank']['tmp_name'], $folder_bank . $nama_file_bank);
                }
            }

            $stmt_invoice = $db->prepare("INSERT INTO invoice 
                (nomor_invoice, id_event, total_harga, kode_unik, total_transfer, bank_tujuan, no_rekening, nama_pemilik_rekening, gambar_bank, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt_invoice->bind_param("siiisssss", $no_invoice, $id_event, $harga_event, $kode_unik, $total_transfer, $bank_tujuan, $no_rekening, $nama_rekening, $nama_file_bank);
            $stmt_invoice->execute();

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Event & Invoice Dibuat',
                    html: `
                        <p>Event berhasil dibuat dengan kode <b>{$kode_sementara}</b></p>
                        <hr>
                        <p><b>Nomor Invoice:</b> {$no_invoice}</p>
                        <p><b>Total yang harus dibayar:</b> Rp {$total_transfer}</p>
                        <p>Mohon transfer ke:</p>
                        <p><b>{$bank_tujuan} - {$no_rekening}</b></p>
                        <p>a/n {$nama_rekening}</p>
                        " . (!empty($nama_file_bank) ? "<img src='../uploads/bank/{$nama_file_bank}' alt='Logo Bank' style='max-width:200px; margin-top:10px;'>" : "") . "
                        <hr>
                        <small>Pastikan nominal transfer sesuai termasuk 3 digit terakhir.</small>
                    `,
                    confirmButtonText: 'OK'
                }).then(() => window.location='data_event.php');
            </script>";
        } else {
            $error = "Gagal menambahkan event.";
        }
    }

    if ($error) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '" . htmlspecialchars($error) . "'
            });
        </script>";
    }
}
?>

<div class="content">
    <div class="container" style="max-width: 800px;">
        <section class="py-5 px-4">
            <h1 class="display-4 fw-bold text-gradient" style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
                Tambah Event Baru
            </h1>
            <p class="mt-3 fs-5 text-muted">Isi form di bawah untuk menambahkan event baru.</p>
        </section>

        <form method="POST" enctype="multipart/form-data">
            <!-- Judul Event -->
            <div class="mb-3">
                <label class="form-label">Judul Event</label>
                <input type="text" class="form-control" name="judul_event" required>
            </div>

            <!-- Deskripsi -->
            <div class="mb-3">
                <label class="form-label">Deskripsi Event</label>
                <textarea class="form-control" name="deskripsi_event" rows="4" required></textarea>
            </div>

            <!-- Tanggal -->
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="tanggal_mulai" required>
                </div>
                <div class="col">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" name="tanggal_selesai" required>
                </div>
            </div>

            <!-- Lokasi -->
            <div class="mb-3">
                <label class="form-label">Lokasi Event</label>
                <input type="text" class="form-control" name="lokasi_event" required>
            </div>

            <!-- Harga -->
            <div class="mb-3">
                <label class="form-label">Harga Event (Rp)</label>
                <input type="text" class="form-control" name="harga_event" required>
            </div>

            <!-- Poster Event -->
            <div class="mb-3">
                <label class="form-label">Poster Event</label>
                <input type="file" class="form-control" name="poster" accept="image/*" onchange="previewPoster(event)">
                <img id="posterPreview" style="max-width:200px; margin-top:10px; display:none;">
            </div>

            <!-- Paddock Slots -->
            <div class="mb-3">
                <label class="form-label">Paddock Slots</label>
                <div id="paddockSlots">
                    <div class="slot-input mb-2 d-flex">
                        <input type="text" class="form-control me-2" name="nama_slot[]" placeholder="Slot 1" required>
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapusSlot(this)">Hapus</button>
                    </div>
                    <div class="slot-input mb-2 d-flex">
                        <input type="text" class="form-control me-2" name="nama_slot[]" placeholder="Slot 2" required>
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapusSlot(this)">Hapus</button>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" onclick="tambahSlot()">Tambah Slot</button>
                <small class="text-muted d-block mt-1">Tambah atau hapus sesuai kebutuhan.</small>
            </div>

            <hr>

            <!-- Data Bank -->
            <div class="mb-3">
                <label class="form-label">Bank Tujuan</label>
                <input type="text" class="form-control" name="bank_tujuan" required>
            </div>
            <div class="mb-3">
                <label class="form-label">No. Rekening</label>
                <input type="text" class="form-control" name="no_rekening" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Pemilik Rekening</label>
                <input type="text" class="form-control" name="nama_pemilik" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Logo Bank</label>
                <input type="file" class="form-control" name="gambar_bank" accept="image/*" onchange="previewBank(event)">
                <img id="bankPreview" style="max-width:200px; margin-top:10px; display:none;">
            </div>

            <button type="submit" name="simpan" class="btn btn-primary">Simpan Event</button>
            <a href="data_event.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<script>
    function previewPoster(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('posterPreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function previewBank(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('bankPreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }


    let slotCount = 2; // Jumlah slot awal

    function tambahSlot() {
        slotCount++;
        const container = document.getElementById('paddockSlots');
        const div = document.createElement('div');
        div.classList.add('slot-input', 'mb-2', 'd-flex');
        div.innerHTML = `
        <input type="text" class="form-control me-2" name="nama_slot[]" placeholder="Slot ${slotCount}" required>
        <button type="button" class="btn btn-danger btn-sm" onclick="hapusSlot(this)">Hapus</button>
    `;
        container.appendChild(div);
    }

    function hapusSlot(button) {
        const div = button.parentElement;
        div.remove();
    }
</script>

<?php include 'templates/footer.php'; ?>