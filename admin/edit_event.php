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

        // Hapus poster lama jika ada
        if ($nama_file && file_exists($folder . $nama_file)) unlink($folder . $nama_file);

        $poster = $_FILES['poster']['name'];
        $tmp    = $_FILES['poster']['tmp_name'];
        $nama_file = time() . "_" . basename($poster);
        move_uploaded_file($tmp, $folder . $nama_file);
    }

    // Update database
    $stmt = $db->prepare("UPDATE event SET judul_event=?, deskripsi_event=?, tanggal_mulai=?, tanggal_selesai=?, lokasi_event=?, poster_path=? WHERE id_event=?");
    $stmt->bind_param("ssssssi", $judul_event, $deskripsi_event, $tanggal_mulai, $tanggal_selesai, $lokasi_event, $nama_file, $id_event);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Event berhasil diperbarui',
                showConfirmButton: false,
                timer: 1500
            }).then(() => window.location='data_event.php');
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Event gagal diperbarui'
            });
        </script>";
    }
}
?>

<!-- Konten Utama -->
<main class="p-6 transition-all duration-300 lg:ml-64">
    
    <!-- Header -->
    <div class="px-6 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-blue-500 rounded-t-2xl mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Event</h1>
            <p class="text-blue-200 mt-1">Perbarui data event di bawah ini.</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="w-full max-w-full bg-gray-50 border border-gray-100 rounded-2xl p-6 shadow-sm">

        <form method="POST" enctype="multipart/form-data" class="space-y-6">

            <!-- Judul Event -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Judul Event</label>
                <input type="text" name="judul_event" value="<?= htmlspecialchars($event['judul_event']) ?>"
                    class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                    placeholder="Masukkan judul event" required>
            </div>

            <!-- Deskripsi Event -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Deskripsi Event</label>
                <textarea name="deskripsi_event" rows="5"
                    class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                    placeholder="Tuliskan deskripsi event secara detail..." required><?= htmlspecialchars($event['deskripsi_event']) ?></textarea>
            </div>

            <!-- Tanggal Mulai & Selesai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="<?= $event['tanggal_mulai'] ?>"
                        class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                        required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="<?= $event['tanggal_selesai'] ?>"
                        class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                        required>
                </div>
            </div>

            <!-- Lokasi -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Lokasi Event</label>
                <input type="text" name="lokasi_event" value="<?= htmlspecialchars($event['lokasi_event']) ?>"
                    class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                    placeholder="Contoh: Jakarta Convention Center" required>
            </div>

            <!-- Poster -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Poster Event</label>
                <?php if ($event['poster_path'] && file_exists("../uploads/poster/" . $event['poster_path'])): ?>
                    <img src="../uploads/poster/<?= $event['poster_path'] ?>" alt="Poster" class="mb-3 w-40 h-auto rounded-lg shadow-md">
                <?php endif; ?>
                <input type="file" name="poster" accept="image/*"
                    class="w-full border-gray-300 rounded-lg p-3 shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                <p class="text-sm text-gray-500 mt-1">Upload poster baru untuk mengganti poster lama.</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <button type="submit" name="simpan"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md transition">
                    Perbarui Event
                </button>
                <a href="data_event.php"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg shadow-md transition">
                    Batal
                </a>
            </div>

        </form>

    </div>

</main>

<?php require 'templates/footer.php'; ?>
