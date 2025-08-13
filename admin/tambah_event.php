<?php
require 'templates/header.php';
require 'templates/sidebar.php';
require '../function/config.php';

// Proses form saat disubmit
if (isset($_POST['simpan'])) {
    $judul_event     = $_POST['judul_event'];
    $deskripsi_event = $_POST['deskripsi_event'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $lokasi_event    = $_POST['lokasi_event'];

    // Upload poster
    $poster = $_FILES['poster']['name'];
    $tmp    = $_FILES['poster']['tmp_name'];

    if ($poster) {
        $folder = "../uploads/poster/";
        if (!file_exists($folder)) mkdir($folder, 0777, true);
        $nama_file = time() . "_" . basename($poster);
        move_uploaded_file($tmp, $folder . $nama_file);
    } else {
        $nama_file = null;
    }

    // Simpan event
    $stmt = $db->prepare("INSERT INTO event (judul_event, deskripsi_event, tanggal_mulai, tanggal_selesai, lokasi_event, poster_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $judul_event, $deskripsi_event, $tanggal_mulai, $tanggal_selesai, $lokasi_event, $nama_file);

    if ($stmt->execute()) {
        $id_event = $stmt->insert_id; // ambil id event terbaru

        // Simpan paddock slots
        if (!empty($_POST['nama_slot']) && is_array($_POST['nama_slot'])) {
            $slots = $_POST['nama_slot'];
            $stmt_slot = $db->prepare("INSERT INTO paddock_slot (id_event, nomor_slot) VALUES (?, ?)");
            foreach ($slots as $slot) {
                $stmt_slot->bind_param("is", $id_event, $slot);
                $stmt_slot->execute();
            }
        }

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Event dan paddock berhasil ditambahkan',
                showConfirmButton: false,
                timer: 1500
            }).then(() => window.location='data_event.php');
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Event gagal ditambahkan'
            });
        </script>";
    }
}
?>

<main class="p-6 transition-all duration-300 lg:ml-64">
    <div class="px-6 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-blue-500 rounded-t-2xl mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Tambah Event & Paddock</h1>
            <p class="text-blue-200 mt-1">Isi form berikut untuk menambahkan event dan paddock slot.</p>
        </div>
    </div>

    <div class="w-full max-w-full bg-gray-50 border border-gray-100 rounded-2xl p-6 shadow-sm">
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <!-- Event Details -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Judul Event</label>
                <input type="text" name="judul_event" class="w-full border p-2 rounded" placeholder="Judul event" required>
            </div>
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Deskripsi Event</label>
                <textarea name="deskripsi_event" rows="5" class="w-full border p-2 rounded" placeholder="Deskripsi event" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <input type="date" name="tanggal_mulai" required>
                <input type="date" name="tanggal_selesai" required>
            </div>
            <div>
                <input type="text" name="lokasi_event" placeholder="Lokasi event" required class="w-full border p-2 rounded">
            </div>
            <div>
                <input type="file" name="poster" accept="image/*">
            </div>

            <!-- Paddock Slots -->
            <div id="paddock-container">
                <label class="block mb-2 font-semibold text-gray-700">Paddock Slots</label>
                <div class="flex gap-2 mb-2 paddock-row">
                    <input type="text" name="nama_slot[]" placeholder="Nomor Slot (misal A1)" required class="border p-2 rounded">
                    <button type="button" onclick="removeRow(this)">-</button>
                </div>
            </div>
            <button type="button" onclick="addRow()">Tambah Slot +</button>

            <div class="flex justify-end gap-4 mt-4">
                <button type="submit" name="simpan" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Event</button>
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
        <input type="text" name="nama_slot[]" placeholder="Nomor Slot (misal A1)" required class="border p-2 rounded">
        <button type="button" onclick="removeRow(this)">-</button>
    `;
    container.appendChild(div);
}

function removeRow(button) {
    button.parentElement.remove();
}
</script>

<?php require 'templates/footer.php'; ?>
