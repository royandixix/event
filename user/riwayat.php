<?php
require_once '../function/config.php';
require_once 'templates/navbar.php';
require_once 'templates/header_riwayat.php';

$peserta_data = [];
$error_message = '';

try {
    $sql = "
        SELECT p.*, 
               GROUP_CONCAT(CONCAT(pk.kelas, ' - ', pk.warna_kendaraan, ' ', pk.tipe_kendaraan, ' (', pk.nomor_polisi, ')') SEPARATOR ', ') AS kelas_kendaraan
        FROM peserta p
        LEFT JOIN peserta_kelas pk ON pk.peserta_id = p.id_peserta
        GROUP BY p.id_peserta
        ORDER BY p.created_at DESC
        LIMIT 1
    ";
    $result = $db->query($sql);

    if (!$result) {
        throw new Exception("Error saat mengambil data peserta: " . $db->error);
    }

    foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
        $row['kelas_kendaraan'] = $row['kelas_kendaraan'] ?: '-';
        $peserta_data[] = $row;
    }
} catch (Exception $e) {
    $error_message = "Terjadi kesalahan: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peserta Baru - Tailwind</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-indigo-50 via-white to-pink-50 min-h-screen font-sans">
    <div class="bg-gradient-to-r from-indigo-50 via-white to-pink-50 min-h-screen font-sans">
        <div class="max-w-xl mx-auto p-4">
            <?php if ($error_message): ?>
                <div class="bg-red-100/80 border border-red-300 text-red-700 rounded-xl p-6 text-center mb-4 backdrop-blur-sm">
                    <h3 class="font-semibold text-lg mb-2">❌ Terjadi Kesalahan</h3>
                    <p><?= htmlspecialchars($error_message); ?></p>
                </div>

            <?php elseif (empty($peserta_data)): ?>
                <div class="bg-white/50 rounded-xl shadow-md p-6 text-center backdrop-blur-sm">
                    <h3 class="text-gray-800 font-semibold text-lg mb-2">📋 Belum ada peserta baru</h3>
                    <p class="text-gray-500">Data peserta akan muncul setelah pendaftaran</p>
                </div>

            <?php else: ?>
                <?php foreach ($peserta_data as $row):
                    $foto_path = "../uploads/foto_peserta/" . $row['foto_peserta'];
                    $receipt_id = "PST-" . str_pad($row['id_peserta'], 3, '0', STR_PAD_LEFT);
                    $timestamp = date('d M Y (H:i)', strtotime($row['created_at'] ?? 'now'));
                    $name_parts = explode(' ', $row['nama_peserta']);
                    $initials = substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : '');
                ?>
                    <div class="bg-white/50 rounded-2xl shadow-lg overflow-hidden mb-6 transform hover:-translate-y-2 transition-transform backdrop-blur-sm">
                        <!-- Header -->
                        <div class="bg-gray-100/70 p-4 border-b border-gray-200">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 border-2 border-green-500 text-green-500 rounded-full flex items-center justify-center font-bold text-xs">✓</div>
                                <span class="font-medium text-gray-700 text-sm"><?= $receipt_id; ?></span>
                            </div>
                            <div class="text-gray-500 text-xs ml-8"><?= $timestamp; ?></div>
                            <span class="inline-block bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full mt-2 uppercase">Terdaftar</span>
                        </div>

                        <!-- Body -->
                        <div class="p-4 flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gray-200/50 flex items-center justify-center text-gray-400 font-bold">
                                <?php if ($row['foto_peserta'] && file_exists($foto_path)): ?>
                                    <img src="<?= htmlspecialchars($foto_path); ?>" alt="Foto Peserta" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= htmlspecialchars($initials); ?>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <div class="text-gray-800 font-semibold"><?= htmlspecialchars($row['nama_peserta']); ?></div>
                                <div class="text-indigo-600 italic"><?= htmlspecialchars($row['nama_tim']); ?></div>
                                <span class="inline-block bg-indigo-100/70 text-indigo-600 text-xs font-medium px-2 py-0.5 rounded mt-1"><?= htmlspecialchars($row['asal_provinsi']); ?></span>
                                <div class="text-gray-500 text-xs mt-1">📧 <?= htmlspecialchars($row['email']); ?></div>
                                <div class="text-gray-500 text-xs">📱 <?= htmlspecialchars($row['whatsapp']); ?></div>

                                <div class="border-t border-gray-200 my-4"></div>

                                <div class="text-gray-600 text-sm mb-1 flex justify-between">
                                    <span>Kelas Kendaraan</span>
                                    <span class="text-gray-800"><?= htmlspecialchars($row['kelas_kendaraan']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Tombol Kembali -->
            <div class="text-center mt-6">
                <a href="index.php"
                   class="inline-block bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800
                          text-white font-medium px-5 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    ⬅ Kembali
                </a>
            </div>
        </div>
    </div>
</body>
</html>
