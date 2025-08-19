<?php
include '../function/config.php';
include 'templates/header.php';
include 'templates/sidebar.php';

if (!isset($_GET['id'])) {
    echo "<script>
        Swal.fire({icon: 'error', title: 'Error', text: 'ID Event tidak ditemukan'}).then(() => window.location='data_event.php');
    </script>";
    exit;
}

$id_event = intval($_GET['id']);

// Ambil data event lama
$stmt = $db->prepare("SELECT * FROM event WHERE id_event=?");
$stmt->bind_param("i", $id_event);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "<script>
        Swal.fire({icon: 'error', title: 'Error', text: 'Event tidak ditemukan'}).then(() => window.location='data_event.php');
    </script>";
    exit;
}

// Ambil invoice lama
$stmt_inv = $db->prepare("SELECT * FROM invoice WHERE id_event=? LIMIT 1");
$stmt_inv->bind_param("i", $id_event);
$stmt_inv->execute();
$invoice = $stmt_inv->get_result()->fetch_assoc();

// Ambil paddock slot
$slots = [];
$stmt_slot = $db->prepare("SELECT nomor_slot FROM paddock_slot WHERE id_event=?");
$stmt_slot->bind_param("i", $id_event);
$stmt_slot->execute();
$res_slot = $stmt_slot->get_result();
while ($row = $res_slot->fetch_assoc()) {
    $slots[] = $row['nomor_slot'];
}

// Handle form submission
if (isset($_POST['simpan'])) {
    $judul_event     = $_POST['judul_event'];
    $deskripsi_event = $_POST['deskripsi_event'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $lokasi_event    = $_POST['lokasi_event'];
    $harga_event     = preg_replace('/[^\d]/', '', $_POST['harga_event']);
    $bank_tujuan     = $_POST['bank_tujuan'];
    $no_rekening     = $_POST['no_rekening'];
    $nama_rekening   = $_POST['nama_pemilik'];

    // Poster
    $nama_file_poster = $event['poster_path'];
    if (!empty($_FILES['poster']['name'])) {
        $folder_poster = "../uploads/poster/";
        if (!file_exists($folder_poster)) mkdir($folder_poster, 0777, true);
        $nama_file_poster = time() . "_" . basename($_FILES['poster']['name']);
        move_uploaded_file($_FILES['poster']['tmp_name'], $folder_poster . $nama_file_poster);
    }

    // Update event
    $stmt_upd = $db->prepare("UPDATE event SET judul_event=?, deskripsi_event=?, tanggal_mulai=?, tanggal_selesai=?, lokasi_event=?, poster_path=?, harga_event=? WHERE id_event=?");
    $stmt_upd->bind_param("sssssssi", $judul_event, $deskripsi_event, $tanggal_mulai, $tanggal_selesai, $lokasi_event, $nama_file_poster, $harga_event, $id_event);
    $stmt_upd->execute();

    // Update paddock slot
    $db->query("DELETE FROM paddock_slot WHERE id_event=$id_event");
    if (!empty($_POST['nama_slot']) && is_array($_POST['nama_slot'])) {
        $stmt_slot = $db->prepare("INSERT INTO paddock_slot (id_event, nomor_slot) VALUES (?, ?)");
        foreach ($_POST['nama_slot'] as $slot) {
            if ($slot != '') {
                $stmt_slot->bind_param("is", $id_event, $slot);
                $stmt_slot->execute();
            }
        }
    }

    // Invoice update
    $kode_unik = rand(100, 999);
    $total_transfer = $harga_event + $kode_unik;

    // Bank logo
    $nama_file_bank = $invoice['gambar_bank'];
    if (!empty($_FILES['gambar_bank']['name'])) {
        $folder_bank = "../uploads/bank/";
        if (!file_exists($folder_bank)) mkdir($folder_bank, 0777, true);
        $nama_file_bank = time() . "_" . basename($_FILES['gambar_bank']['name']);
        move_uploaded_file($_FILES['gambar_bank']['tmp_name'], $folder_bank . $nama_file_bank);
    }

    $stmt_inv_upd = $db->prepare("UPDATE invoice SET total_harga=?, kode_unik=?, total_transfer=?, bank_tujuan=?, no_rekening=?, nama_pemilik_rekening=?, gambar_bank=? WHERE id_event=?");
    $stmt_inv_upd->bind_param("iiissssi", $harga_event, $kode_unik, $total_transfer, $bank_tujuan, $no_rekening, $nama_rekening, $nama_file_bank, $id_event);
    $stmt_inv_upd->execute();

    // SweetAlert sukses
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Event & Invoice Diperbarui',
            html: `
                <p>Event berhasil diperbarui dengan kode <b>EVT" . date('Ymd') . "-" . str_pad($id_event, 3, '0', STR_PAD_LEFT) . "</b></p>
                <hr>
                <p><b>Nomor Invoice:</b> {$invoice['nomor_invoice']}</p>
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
}
?>

<div class="content">
    <div class="container" style="max-width: 800px;">
        <section class="py-5 px-4">
            <h1 class="display-4 fw-bold text-gradient" style="background: linear-gradient(90deg, #4facfe, #00f2fe); -webkit-background-clip: text; color: transparent;">
                Edit Event
            </h1>
            <p class="mt-3 fs-5 text-muted">Ubah data event sesuai kebutuhan.</p>
        </section>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Judul Event</label>
                <input type="text" class="form-control" name="judul_event" value="<?= htmlspecialchars($event['judul_event']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi Event</label>
                <textarea class="form-control" name="deskripsi_event" rows="4" required><?= htmlspecialchars($event['deskripsi_event']) ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="tanggal_mulai" value="<?= $event['tanggal_mulai'] ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" name="tanggal_selesai" value="<?= $event['tanggal_selesai'] ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Lokasi Event</label>
                <input type="text" class="form-control" name="lokasi_event" value="<?= htmlspecialchars($event['lokasi_event']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Harga Event (Rp)</label>
                <input type="text" class="form-control" name="harga_event" value="<?= $event['harga_event'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Poster Event</label>
                <input type="file" class="form-control" name="poster" accept="image/*" onchange="previewPoster(event)">
                <?php if ($event['poster_path']): ?>
                    <img id="posterPreview" src="../uploads/poster/<?= $event['poster_path'] ?>" style="max-width:200px; margin-top:10px;">
                <?php else: ?>
                    <img id="posterPreview" style="max-width:200px; margin-top:10px; display:none;">
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Paddock Slots</label>
                <div id="slotsContainer">
                    <?php foreach ($slots as $s): ?>
                        <div class="input-group mb-2 slot-row">
                            <input type="text" class="form-control" name="nama_slot[]" value="<?= htmlspecialchars($s) ?>" placeholder="Nama Slot" required>
                            <button type="button" class="btn btn-danger remove-slot">Hapus</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="addSlotBtn" class="btn btn-success btn-sm mt-2">Tambah Slot</button>
                <small class="text-muted d-block mt-1">Tambahkan atau hapus slot sesuai kebutuhan.</small>
            </div>

            <hr>

            <div class="mb-3">
                <label class="form-label">Bank Tujuan</label>
                <input type="text" class="form-control" name="bank_tujuan" value="<?= htmlspecialchars($invoice['bank_tujuan']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">No. Rekening</label>
                <input type="text" class="form-control" name="no_rekening" value="<?= htmlspecialchars($invoice['no_rekening']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Pemilik Rekening</label>
                <input type="text" class="form-control" name="nama_pemilik" value="<?= htmlspecialchars($invoice['nama_pemilik_rekening']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Logo Bank</label>
                <input type="file" class="form-control" name="gambar_bank" accept="image/*" onchange="previewBank(event)">
                <?php if ($invoice['gambar_bank']): ?>
                    <img id="bankPreview" src="../uploads/bank/<?= $invoice['gambar_bank'] ?>" style="max-width:200px; margin-top:10px;">
                <?php else: ?>
                    <img id="bankPreview" style="max-width:200px; margin-top:10px; display:none;">
                <?php endif; ?>
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



    document.getElementById('addSlotBtn').addEventListener('click', function() {
        const container = document.getElementById('slotsContainer');

        const div = document.createElement('div');
        div.className = 'input-group mb-2 slot-row';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'nama_slot[]';
        input.className = 'form-control';
        input.placeholder = 'Nama Slot';
        input.required = true;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-danger remove-slot';
        btn.textContent = 'Hapus';
        btn.addEventListener('click', function() {
            container.removeChild(div);
        });

        div.appendChild(input);
        div.appendChild(btn);
        container.appendChild(div);
    });

    // Hapus slot yang sudah ada
    document.querySelectorAll('.remove-slot').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const row = btn.closest('.slot-row');
            row.parentNode.removeChild(row);
        });
    });
</script>

<?php include 'templates/footer.php'; ?>