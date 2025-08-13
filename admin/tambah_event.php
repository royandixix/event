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
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $nama_file = time() . "_" . basename($poster);
        move_uploaded_file($tmp, $folder . $nama_file);
    } else {
        $nama_file = null;
    }

    // Simpan ke database
    $stmt = $db->prepare("INSERT INTO event (judul_event, deskripsi_event, tanggal_mulai, tanggal_selesai, lokasi_event, poster_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $judul_event, $deskripsi_event, $tanggal_mulai, $tanggal_selesai, $lokasi_event, $nama_file);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Event berhasil ditambahkan',
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

<!-- Konten Utama -->
<main class="p-6 transition-all duration-300 lg:ml-64">
    
    <!-- Header -->
    <div class="px-6 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-blue-500 rounded-t-2xl mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Tambah Event</h1>
            <p class="text-blue-200 mt-1">Isi form berikut untuk menambahkan event baru.</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="w-full max-w-full bg-gray-50 border border-gray-100 rounded-2xl p-6 shadow-sm">

        <form method="POST" enctype="multipart/form-data" class="space-y-6">

            <!-- Judul Event -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Judul Event</label>
                <input type="text" name="judul_event"
                    class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                    placeholder="Masukkan judul event" required>
            </div>

            <!-- Deskripsi Event -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Deskripsi Event</label>
                <textarea name="deskripsi_event" rows="5"
                    class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                    placeholder="Tuliskan deskripsi event secara detail..." required></textarea>
            </div>

            <!-- Tanggal Mulai & Selesai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai"
                        class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                        required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai"
                        class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                        required>
                </div>
            </div>

            <!-- Lokasi -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Lokasi Event</label>
                <input type="text" name="lokasi_event"
                    class="w-full border-gray-300 rounded-lg p-3 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                    placeholder="Contoh: Jakarta Convention Center" required>
            </div>

            <!-- Poster -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">Poster Event</label>
                <input type="file" name="poster" accept="image/*"
                    class="w-full border-gray-300 rounded-lg p-3 shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                <p class="text-sm text-gray-500 mt-1">Format gambar: JPG, PNG, atau JPEG</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <button type="submit" name="simpan"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md transition">
                    Simpan Event
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