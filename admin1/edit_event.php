<?php
require 'templates/header.php';
require 'templates/sidebar.php';
require '../function/config.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID event tidak ditemukan!'); window.location='data_event.php';</script>";
    exit;
}

$id_event = intval($_GET['id']);

// Ambil data event
$stmt = $db->prepare("SELECT * FROM event WHERE id_event=?");
$stmt->bind_param("i", $id_event);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<script>alert('Event tidak ditemukan!'); window.location='data_event.php';</script>";
    exit;
}
$event = $result->fetch_assoc();

// Ambil invoice terkait
$stmt_inv = $db->prepare("SELECT * FROM invoice WHERE id_event=? LIMIT 1");
$stmt_inv->bind_param("i", $id_event);
$stmt_inv->execute();
$invoice = $stmt_inv->get_result()->fetch_assoc();

// Ambil paddock slots
$stmt_slot = $db->prepare("SELECT * FROM paddock_slot WHERE id_event=?");
$stmt_slot->bind_param("i", $id_event);
$stmt_slot->execute();
$slots_result = $stmt_slot->get_result();
$slots = [];
while($row = $slots_result->fetch_assoc()) {
    $slots[] = $row['nomor_slot'];
}

// Jika form disubmit
if (isset($_POST['simpan'])) {
    $judul_event     = $_POST['judul_event'];
    $deskripsi_event = $_POST['deskripsi_event'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $lokasi_event    = $_POST['lokasi_event'];
    $harga_event     = preg_replace('/[^\d]/', '', $_POST['harga_event']);
    $harga_event     = ($harga_event === '') ? 0 : intval($harga_event);
    $bank_tujuan     = $_POST['bank_tujuan'];
    $no_rekening     = $_POST['no_rekening'];
    $nama_rekening   = $_POST['nama_pemilik'];

    // Upload poster baru
    $poster = $_FILES['poster']['name'];
    $tmp_poster = $_FILES['poster']['tmp_name'];
    $nama_file_poster = $event['poster_path'];

    if ($poster) {
        $folder_poster = "../uploads/poster/";
        if (!file_exists($folder_poster)) mkdir($folder_poster, 0777, true);
        $nama_file_poster = time() . "_" . basename($poster);
        if (!move_uploaded_file($tmp_poster, $folder_poster . $nama_file_poster)) {
            echo "<script>alert('Gagal upload poster!');</script>";
            $nama_file_poster = $event['poster_path'];
        }
    }

    // Update tabel event
    $stmt = $db->prepare("UPDATE event SET judul_event=?, deskripsi_event=?, tanggal_mulai=?, tanggal_selesai=?, lokasi_event=?, poster_path=?, harga_event=? WHERE id_event=?");
    $stmt->bind_param("ssssssii", $judul_event, $deskripsi_event, $tanggal_mulai, $tanggal_selesai, $lokasi_event, $nama_file_poster, $harga_event, $id_event);
    $stmt->execute();

    // Update paddock slots: hapus dulu yg lama, insert yg baru
    $db->query("DELETE FROM paddock_slot WHERE id_event=$id_event");
    if (!empty($_POST['nama_slot']) && is_array($_POST['nama_slot'])) {
        $stmt_slot = $db->prepare("INSERT INTO paddock_slot (id_event, nomor_slot) VALUES (?, ?)");
        foreach ($_POST['nama_slot'] as $slot) {
            $slot_clean = trim($slot);
            if ($slot_clean != '') {
                $stmt_slot->bind_param("is", $id_event, $slot_clean);
                $stmt_slot->execute();
            }
        }
    }

    // Upload logo bank baru
    $gambar_bank = $_FILES['gambar_bank']['name'];
    $tmp_gambar_bank = $_FILES['gambar_bank']['tmp_name'];
    $nama_file_bank = $invoice['gambar_bank'];

    if ($gambar_bank) {
        $folder_bank = "../uploads/bank/";
        if (!file_exists($folder_bank)) mkdir($folder_bank, 0777, true);
        $nama_file_bank = time() . "_" . basename($gambar_bank);
        if (!move_uploaded_file($tmp_gambar_bank, $folder_bank . $nama_file_bank)) {
            echo "<script>alert('Gagal upload logo bank!');</script>";
            $nama_file_bank = $invoice['gambar_bank'];
        }
    }

    // Update invoice
    $kode_unik = $invoice['kode_unik'];
    $total_transfer = $harga_event + $kode_unik;
    $stmt_inv_upd = $db->prepare("UPDATE invoice SET total_harga=?, total_transfer=?, bank_tujuan=?, no_rekening=?, nama_pemilik_rekening=?, gambar_bank=? WHERE id_invoice=?");
    $stmt_inv_upd->bind_param("iissssi", $harga_event, $total_transfer, $bank_tujuan, $no_rekening, $nama_rekening, $nama_file_bank, $invoice['id_invoice']);
    $stmt_inv_upd->execute();

    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Event & Invoice Diperbarui',
            confirmButtonText: 'OK'
        }).then(() => window.location='data_event.php');
    </script>";
}
?>

<main class="p-6 transition-all duration-300 lg:ml-64">
<div class="px-6 py-6 bg-blue-500 rounded-t-2xl mb-6">
    <h1 class="text-3xl font-bold text-white">Edit Event & Paddock</h1>
    <p class="text-blue-200 mt-1">Perbarui data event dan paddock slot.</p>
</div>

<div class="bg-gray-50 border p-6 rounded-2xl shadow-sm">
<form method="POST" enctype="multipart/form-data" class="space-y-6">
    <div>
        <label class="block mb-2 font-semibold">Judul Event</label>
        <input type="text" name="judul_event" value="<?= htmlspecialchars($event['judul_event']) ?>" required class="w-full border p-2 rounded">
    </div>

    <div>
        <label class="block mb-2 font-semibold">Deskripsi Event</label>
        <textarea name="deskripsi_event" rows="5" required class="w-full border p-2 rounded"><?= htmlspecialchars($event['deskripsi_event']) ?></textarea>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <input type="date" name="tanggal_mulai" value="<?= $event['tanggal_mulai'] ?>" required class="border p-2 rounded">
        <input type="date" name="tanggal_selesai" value="<?= $event['tanggal_selesai'] ?>" required class="border p-2 rounded">
    </div>

    <div>
        <input type="text" name="lokasi_event" value="<?= htmlspecialchars($event['lokasi_event']) ?>" required class="w-full border p-2 rounded">
    </div>

    <div>
        <label class="block mb-2 font-semibold">Upload Poster Event</label>
        <input type="file" name="poster" accept="image/*" class="w-full border p-2 rounded">
        <?php if ($event['poster_path']): ?>
            <img src="../uploads/poster/<?= $event['poster_path'] ?>" alt="Poster" style="max-width:200px; margin-top:10px;">
        <?php endif; ?>
    </div>

    <div>
        <label class="block mb-2 font-semibold">Harga Event</label>
        <input type="text" id="harga_event" name="harga_event" value="<?= number_format($event['harga_event'],0,',','.') ?>" required class="w-full border p-2 rounded">
    </div>

    <hr class="my-4">

    <div>
        <label class="block mb-2 font-semibold">Pilih Bank Tujuan</label>
        <select name="bank_tujuan" required class="w-full border p-2 rounded">
            <option value="">-- Pilih Bank --</option>
            <?php
            $banks = ['BCA','Mandiri','BRI','BNI'];
            foreach($banks as $b){
                $sel = ($invoice['bank_tujuan']==$b)?'selected':'';
                echo "<option value='$b' $sel>$b</option>";
            }
            ?>
        </select>
    </div>

    <div>
        <label class="block mb-2 font-semibold">Nomor Rekening</label>
        <input type="text" name="no_rekening" value="<?= htmlspecialchars($invoice['no_rekening']) ?>" required class="w-full border p-2 rounded">
    </div>

    <div>
        <label class="block mb-2 font-semibold">Nama Pemilik Rekening</label>
        <input type="text" name="nama_pemilik" value="<?= htmlspecialchars($invoice['nama_pemilik_rekening']) ?>" required class="w-full border p-2 rounded">
    </div>

    <div>
        <label class="block mb-2 font-semibold">Upload Logo Bank</label>
        <input type="file" name="gambar_bank" accept="image/*" class="w-full border p-2 rounded">
        <?php if ($invoice['gambar_bank']): ?>
            <img src="../uploads/bank/<?= $invoice['gambar_bank'] ?>" alt="Logo Bank" style="max-width:200px; margin-top:10px;">
        <?php endif; ?>
    </div>

    <div id="paddock-container">
        <label class="block mb-2 font-semibold">Paddock Slots</label>
        <?php foreach($slots as $slot): ?>
        <div class="flex gap-2 mb-2 paddock-row">
            <input type="text" name="nama_slot[]" value="<?= htmlspecialchars($slot) ?>" required class="border p-2 rounded w-full">
            <button type="button" onclick="removeRow(this)" class="bg-red-500 text-white px-2 rounded">-</button>
        </div>
        <?php endforeach; ?>
        <?php if(empty($slots)): ?>
        <div class="flex gap-2 mb-2 paddock-row">
            <input type="text" name="nama_slot[]" placeholder="Nomor Slot (misal A1)" required class="border p-2 rounded w-full">
            <button type="button" onclick="removeRow(this)" class="bg-red-500 text-white px-2 rounded">-</button>
        </div>
        <?php endif; ?>
    </div>
    <button type="button" onclick="addRow()" class="bg-green-500 text-white px-4 py-1 rounded">Tambah Slot +</button>

    <div class="flex justify-end gap-4 mt-4">
        <button type="submit" name="simpan" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
        <a href="data_event.php" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
    </div>
</form>
</div>
</main>

<script>
// Tambah/remove paddock slot
function addRow() {
    const container = document.getElementById('paddock-container');
    const row = document.createElement('div');
    row.className = 'flex gap-2 mb-2 paddock-row';
    row.innerHTML = `<input type="text" name="nama_slot[]" placeholder="Nomor Slot" required class="border p-2 rounded w-full">
                     <button type="button" onclick="removeRow(this)" class="bg-red-500 text-white px-2 rounded">-</button>`;
    container.appendChild(row);
}

function removeRow(btn) {
    btn.parentElement.remove();
}

// Format harga input (Rp xxx.xxx)
const hargaInput = document.getElementById('harga_event');
hargaInput.addEventListener('input', function(e){
    let value = this.value.replace(/\D/g,'');
    this.value = new Intl.NumberFormat('id-ID').format(value);
});
</script>
