<?php
require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header.php';
require 'templates/sub.php';

// Ambil ID event dari URL
$id_event = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Query data event
$eventQuery = $db->prepare("SELECT * FROM event WHERE id_event = ?");
$eventQuery->bind_param("i", $id_event);
$eventQuery->execute();
$result = $eventQuery->get_result();
$event = $result->fetch_assoc();

// Jika event tidak ditemukan
if (!$event) {
    echo "<div class='p-6 text-red-600 font-bold'>Event tidak ditemukan atau sudah dihapus.</div>";
    require './templates/footer.php';
    exit;
}

// Data event
$judul_event     = htmlspecialchars($event['judul_event'] ?? 'Judul tidak tersedia');
$poster_file     = $event['poster_path'] ?? '';
$poster = (!empty($poster_file) && file_exists("../uploads/poster/" . $poster_file))
    ? "../uploads/poster/" . htmlspecialchars(basename($poster_file))
    : "https://via.placeholder.com/600x800?text=No+Image";

$tanggal_mulai   = !empty($event['tanggal_mulai']) ? date("d M Y", strtotime($event['tanggal_mulai'])) : '-';
$tanggal_selesai = !empty($event['tanggal_selesai']) ? date("d M Y", strtotime($event['tanggal_selesai'])) : '-';
$lokasi_event    = htmlspecialchars($event['lokasi_event'] ?? '-');
$deskripsi_event = htmlspecialchars($event['deskripsi_event'] ?? '-');

// Ambil kategori lomba
$kelasQuery = $db->prepare("
    SELECT DISTINCT kelas 
    FROM peserta_kelas 
    JOIN peserta ON peserta.id_peserta = peserta_kelas.peserta_id
");
$kelasQuery->execute();
$kategoriLomba = $kelasQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Harga pendaftaran
$hargaPendaftaran = [
    ['periode' => '01 Agu 2025 s/d 15 Agu 2025', 'harga' => 250000],
    ['periode' => '16 Agu 2025 s/d 20 Agu 2025', 'harga' => 300000],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_event ?> - Detail Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>

<!-- HANYA background utama yang pink -->

<body class="bg-gradient-to-r from-pink-200 via-white to-white">
    <div class="bg-gradient-to-r from-pink-200 via-white to-white">
        <div class="py-16 px-4 lg:px-16 max-w-6xl mx-auto grid sm:grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Kartu Event -->
            <div class="bg-white rounded-xl shadow-lg border overflow-hidden hover:shadow-xl transition">
                <img src="<?= $poster ?>" alt="Poster Event" class="w-full h-[32rem] object-cover">
                <div class="p-4 space-y-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i data-lucide="calendar" class="w-4 h-4 text-pink-500"></i>
                        <span><?= $tanggal_mulai ?> - <?= $tanggal_selesai ?></span>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800"><?= $judul_event ?></h2>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i data-lucide="map-pin" class="w-4 h-4 text-pink-500"></i>
                        <span><?= $lokasi_event ?></span>
                    </div>
                    <div class="pt-3 space-y-2">
                        <button onclick="openModal('general')" class="bg-blue-600 hover:bg-blue-700 text-white w-full py-2 rounded-lg transition">Registrasi</button>
                        <button onclick="openModal('paddock')" class="bg-gray-700 hover:bg-gray-800 text-white w-full py-2 rounded-lg transition">Registrasi Paddock</button>
                    </div>
                </div>
            </div>

            <!-- Info Event -->
            <div class="space-y-6">
                <!-- Deskripsi -->
                <div class="bg-white p-5 rounded-xl shadow border">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Deskripsi Event</h3>
                    <p class="text-gray-700 text-sm leading-relaxed"><?= nl2br($deskripsi_event) ?></p>
                </div>

                <!-- Harga Tiket -->
                <div class="bg-white p-5 rounded-xl shadow border">
                    <h3 class="font-bold text-gray-800 mb-3">Harga Tiket / Pendaftaran</h3>
                    <div class="space-y-3">
                        <?php foreach ($hargaPendaftaran as $harga) : ?>
                            <div class="flex items-center justify-between border rounded-lg p-3 bg-gray-50">
                                <span class="text-sm text-gray-700"><?= $harga['periode'] ?> - Rp<?= number_format($harga['harga'], 0, ',', '.') ?></span>
                                <button onclick="openModal('general')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded-lg text-sm">Pilih</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Kategori Lomba -->
                <div class="bg-white p-5 rounded-xl shadow border">
                    <h3 class="font-bold text-gray-800 mb-3">Kategori Lomba</h3>
                    <div class="max-h-48 overflow-y-auto border rounded-lg divide-y bg-gray-50">
                        <?php if (!empty($kategoriLomba)) : ?>
                            <?php foreach ($kategoriLomba as $k) : ?>
                                <div class="p-3 text-sm text-gray-700"><?= htmlspecialchars($k['kelas'] ?? '-') ?></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-3 text-sm text-gray-500">Belum ada kategori.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Registrasi Umum -->
        <div id="modalGeneral" class="hidden fixed inset-0 bg-black/60 flex items-start justify-center z-50">
            <div class="bg-white rounded-xl shadow-lg p-6 w-80 text-center space-y-5 animate-fade-in mt-20">
                <h3 class="text-lg font-bold text-gray-800">Pilih Jenis Registrasi</h3>
                <p class="text-sm text-gray-600">Silakan pilih apakah Anda mendaftar sebagai peserta atau manajer.</p>
                <div class="flex flex-col gap-3">
                    <a href="peserta.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">Peserta</a>
                    <a href="manejer.php" class="bg-gray-700 hover:bg-gray-800 text-white py-2 rounded-lg transition">Manajer</a>
                </div>
                <button onclick="closeModal('general')" class="mt-4 text-sm text-gray-600 hover:underline">Batal</button>
            </div>
        </div>

        <!-- Modal Registrasi Paddock -->
        <div id="modalPaddock" class="hidden fixed inset-0 bg-black/60 flex items-start justify-center z-50">
            <div class="bg-white rounded-xl shadow-lg p-6 w-80 text-center space-y-5 animate-fade-in mt-20">
                <h3 class="text-lg font-bold text-gray-800">Registrasi Paddock</h3>
                <p class="text-sm text-gray-600">Anda akan diarahkan ke form pendaftaran Paddock.</p>
                <div class="flex flex-col gap-3">
                    <a href="paddock.php?id_event=<?= $id_event ?>" class="bg-gray-700 hover:bg-gray-800 text-white py-2 rounded-lg transition">Lanjutkan</a>
                </div>
                <button onclick="closeModal('paddock')" class="mt-4 text-sm text-gray-600 hover:underline">Batal</button>
            </div>
        </div>
    </div>

    <script>
        function openModal(type) {
            if (type === 'general') document.getElementById('modalGeneral').classList.remove('hidden');
            if (type === 'paddock') document.getElementById('modalPaddock').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(type) {
            if (type === 'general') document.getElementById('modalGeneral').classList.add('hidden');
            if (type === 'paddock') document.getElementById('modalPaddock').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener("DOMContentLoaded", () => {
            lucide.createIcons();
        });
    </script>

</body>

</html>
<?php require './templates/footer.php'; ?>