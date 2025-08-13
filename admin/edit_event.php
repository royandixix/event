<?php
require 'templates/header.php';
require 'templates/sidebar.php';
require '../function/config.php';

// Ambil ID event dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location='data_event.php';</script>";
    exit;
}

$id_event = $_GET['id'];

// Ambil data event dari database
$stmt = $db->prepare("SELECT * FROM event WHERE id_event = ?");
$stmt->bind_param("i", $id_event);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "<script>window.location='data_event.php';</script>";
    exit;
}

// Ambil paddock slot terkait event
$slots_result = $db->query("SELECT * FROM paddock_slot WHERE id_event = $id_event ORDER BY id_slot ASC");
$paddock_slots = $slots_result->fetch_all(MYSQLI_ASSOC);

// Proses form saat disubmit
if (isset($_POST['simpan'])) {
    $judul_event     = $_POST['judul_event'];
    $deskripsi_event = $_POST['deskripsi_event'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $lokasi_event    = $_POST['lokasi_event'];

    // Upload poster baru jika ada
    $nama_file = $event['poster_path'];
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $folder = "../uploads/poster/";
        if (!file_exists($folder)) mkdir($folder, 0777, true);
        if ($nama_file && file_exists($folder . $nama_file)) unlink($folder . $nama_file);

        $poster = $_FILES['poster']['name'];
        $tmp    = $_FILES['poster']['tmp_name'];
        $nama_file = time() . "_" . basename($poster);
        move_uploaded_file($tmp, $folder . $nama_file);
    }

    // Update event
    $stmt = $db->prepare("UPDATE event SET judul_event=?, deskripsi_event=?, tanggal_mulai=?, tanggal_selesai=?, lokasi_event=?, poster_path=? WHERE id_event=?");
    $stmt->bind_param("ssssssi", $judul_event, $deskripsi_event, $tanggal_mulai, $tanggal_selesai, $lokasi_event, $nama_file, $id_event);
    $stmt->execute();

    // Update paddock slots
    // Hapus semua slot lama
    $db->query("DELETE FROM paddock_slot WHERE id_event = $id_event");

    // Simpan slot baru dari form
    if (!empty($_POST['nama_slot']) && is_array($_POST['nama_slot'])) {
        $slots = $_POST['nama_slot'];
        $stmt_slot = $db->prepare("INSERT INTO paddock_slot (id_event, nomor_slot) VALUES (?, ?)");
        foreach ($slots as $slot) {
            $slot = trim($slot);
            if ($slot !== "") {
                $stmt_slot->bind_param("is", $id_event, $slot);
                $stmt_slot->execute();
            }
        }
    }

    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Event dan paddock berhasil diperbarui',
            showConfirmButton: false,
            timer: 1500
        }).then(() => window.location='data_event.php');
    </script>";
}
?>

<main class="p-6 transition-all duration-300 lg:ml-64">

    <div class="px-6 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-blue-500 rounded-t-2xl mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Event & Paddock</h1>
            <p class="text-blue-200 mt-1">Perbarui data event dan paddock slot di bawah ini.</p>
        </div>
    </div>

    <div class="w-full max-w-full bg-gray-50 border border-gray-100 rounded-2xl p-6 shadow-sm">
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <!-- Event Details -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Judul Event</label>
                <input type="text" name="judul_event" value="<?= htmlspecialchars($event['judul_event']) ?>" class="w-full border p-2 rounded" required>
            </div>
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Deskripsi Event</label>
                <textarea name="deskripsi_event" rows="5" class="w-full border p-2 rounded" required><?= htmlspecialchars($event['deskripsi_event']) ?></textarea>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <input type="date" name="tanggal_mulai" value="<?= $event['tanggal_mulai'] ?>" required>
                <input type="date" name="tanggal_selesai" value="<?= $event['tanggal_selesai'] ?>" required>
            </div>
            <div>
                <input type="text" name="lokasi_event" value="<?= htmlspecialchars($event['lokasi_event']) ?>" placeholder="Lokasi event" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Poster Event</label>
                <?php if ($event['poster_path'] && file_exists("../uploads/poster/" . $event['poster_path'])): ?>
                    <img src="../uploads/poster/<?= $event['poster_path'] ?>" alt="Poster" class="mb-3 w-40 h-auto rounded-lg">
                <?php endif; ?>
                <input type="file" name="poster" accept="image/*">
            </div>

            <!-- Paddock Slots -->
            <div id="paddock-container">
                <label class="block mb-2 font-semibold text-gray-700">Paddock Slots</label>
                <?php if (!empty($paddock_slots)): ?>
                    <?php foreach ($paddock_slots as $slot): ?>
                        <div class="flex gap-2 mb-2 paddock-row">
                            <input type="text" name="nama_slot[]" value="<?= htmlspecialchars($slot['nomor_slot']) ?>" placeholder="Nomor Slot" required class="border p-2 rounded">
                            <button type="button" onclick="removeRow(this)">-</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="flex gap-2 mb-2 paddock-row">
                        <input type="text" name="nama_slot[]" placeholder="Nomor Slot" required class="border p-2 rounded">
                        <button type="button" onclick="removeRow(this)">-</button>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" onclick="addRow()">Tambah Slot +</button>

            <div class="flex justify-end gap-4 mt-4">
                <button type="submit" name="simpan" class="bg-blue-600 text-white px-4 py-2 rounded">Perbarui Event</button>
                <a href="data_event.php" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
            </div>
        </form>
    </div>
</main>

<script>
function addRow() {
    const container = document.getElementById('paddock-container');
    const div = document.createElement('div');
    div.classList.add('flex', 'gap-2', 'mb-2', 'paddock-row');
    div.innerHTML = `
        <input type="text" name="nama_slot[]" placeholder="Nomor Slot" required class="border p-2 rounded">
        <button type="button" onclick="removeRow(this)">-</button>
    `;
    container.appendChild(div);
}

function removeRow(button) {
    button.parentElement.remove();
}
</script>

<?php require 'templates/footer.php'; ?>
