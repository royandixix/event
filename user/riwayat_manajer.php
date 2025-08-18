<?php
ob_start(); // menampung output sementara 
session_start();
require_once '../function/config.php';
require_once 'templates/navbar.php';
require_once 'templates/header_riwayat_manajer.php';

$riwayat_data = [];
$error_message = '';

// Ambil tipe dari URL (default 'manajer')
$type = $_GET['type'] ?? 'manajer';

try {
    if ($type === 'manajer') {
        $sql = "
            SELECT m.*, 
                   GROUP_CONCAT(DISTINCT CONCAT(mk.kelas, ' - ', mk.warna_kendaraan, ' ', mk.tipe_kendaraan, ' (', mk.nomor_polisi, ')') SEPARATOR ', ') AS kelas_kendaraan,
                   NULL AS peserta_kelas
            FROM manajer m
            LEFT JOIN manajer_kelas mk ON mk.manajer_id = m.id_manajer
            WHERE m.id_manajer = (SELECT MAX(id_manajer) FROM manajer)
            GROUP BY m.id_manajer
        ";
    } elseif ($type === 'peserta') {
        $sql = "
            SELECT p.*, 
                   GROUP_CONCAT(DISTINCT CONCAT(pk.kelas, ' - ', pk.warna_kendaraan, ' ', pk.tipe_kendaraan, ' (', pk.nomor_polisi, ')') SEPARATOR ', ') AS kelas_kendaraan,
                   NULL AS peserta_kelas
            FROM peserta p
            LEFT JOIN peserta_kelas pk ON pk.peserta_id = p.id_peserta
            WHERE p.id_peserta = (SELECT MAX(id_peserta) FROM peserta)
            GROUP BY p.id_peserta
        ";
    } else { // gabungan manajer & peserta, ambil 1 terbaru masing-masing
        $sql = "
            SELECT * FROM (
                SELECT m.id_manajer AS id, m.nama_manajer AS nama, 
                       GROUP_CONCAT(DISTINCT CONCAT(mk.kelas, ' - ', mk.warna_kendaraan, ' ', mk.tipe_kendaraan, ' (', mk.nomor_polisi, ')')) AS kelas_kendaraan,
                       NULL AS peserta_kelas,
                       m.created_at, m.foto_manajer, m.nama_tim, m.asal_provinsi, m.email, m.whatsapp, m.voucher
                FROM manajer m
                LEFT JOIN manajer_kelas mk ON mk.manajer_id = m.id_manajer
                WHERE m.id_manajer = (SELECT MAX(id_manajer) FROM manajer)
                GROUP BY m.id_manajer
                UNION
                SELECT p.id_peserta AS id, p.nama_peserta AS nama, 
                       GROUP_CONCAT(DISTINCT CONCAT(pk.kelas, ' - ', pk.warna_kendaraan, ' ', pk.tipe_kendaraan, ' (', pk.nomor_polisi, ')')) AS kelas_kendaraan,
                       NULL AS peserta_kelas,
                       p.created_at, NULL AS foto_manajer, NULL AS nama_tim, NULL AS asal_provinsi, p.email, p.whatsapp, p.voucher
                FROM peserta p
                LEFT JOIN peserta_kelas pk ON pk.peserta_id = p.id_peserta
                WHERE p.id_peserta = (SELECT MAX(id_peserta) FROM peserta)
                GROUP BY p.id_peserta
            ) AS combined
            ORDER BY created_at DESC
        ";
    }

    $result = $db->query($sql);
    if (!$result) {
        throw new Exception("Error saat mengambil data: " . $db->error);
    }

    foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
        $row['kelas_kendaraan'] = $row['kelas_kendaraan'] ?: '-';
        $riwayat_data[] = $row;
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
    <title>Riwayat <?= ucfirst($type) ?> - Tailwind</title>
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
            <?php else: ?>
                <?php foreach ($riwayat_data as $row):
                    $foto_path = "./uploads/foto_manajer/" . ($row['foto_manajer'] ?? '');

                    // ===== PERBAIKAN =====
                    $id = $row['id'] ?? ($row['id_manajer'] ?? $row['id_peserta'] ?? 0);
                    $receipt_id = strtoupper(substr($type, 0, 3)) . "-" . str_pad($id, 3, '0', STR_PAD_LEFT);

                    $timestamp = date('d M Y (H:i)', strtotime($row['created_at'] ?? 'now'));
                    $name_parts = explode(' ', $row['nama'] ?? '');
                    $initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));
                ?>
                    <div class="bg-white/50 rounded-2xl shadow-lg overflow-hidden mb-6 transform hover:-translate-y-2 transition-transform backdrop-blur-sm">
                        <div class="bg-gray-100/70 p-4 border-b border-gray-200">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 border-2 border-green-500 text-green-500 rounded-full flex items-center justify-center font-bold text-xs">✓</div>
                                <span class="font-medium text-gray-700 text-sm"><?= $receipt_id; ?></span>
                            </div>
                            <div class="text-gray-500 text-xs ml-8"><?= $timestamp; ?></div>
                            <span class="inline-block bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full mt-2 uppercase">Terdaftar</span>
                        </div>

                        <div class="p-4">
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gray-200/50 flex items-center justify-center text-gray-400 font-bold">
                                    <?php if (!empty($row['foto_manajer']) && file_exists($foto_path)): ?>
                                        <img src="<?= htmlspecialchars($foto_path); ?>" alt="Foto" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= htmlspecialchars($initials); ?>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-1">
                                    <div class="text-gray-800 font-semibold"><?= htmlspecialchars($row['nama'] ?? '-'); ?></div>
                                    <?php if ($type === 'manajer'): ?>
                                        <div class="text-indigo-600 italic"><?= htmlspecialchars($row['nama_tim'] ?: '-'); ?></div>
                                        <span class="inline-block bg-indigo-100/70 text-indigo-600 text-xs font-medium px-2 py-0.5 rounded mt-1"><?= htmlspecialchars($row['asal_provinsi'] ?: '-'); ?></span>
                                    <?php endif; ?>
                                    <div class="text-gray-500 text-xs mt-1">📧 <?= htmlspecialchars($row['email'] ?: '-'); ?></div>
                                    <div class="text-gray-500 text-xs">📱 <?= htmlspecialchars($row['whatsapp'] ?: '-'); ?></div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 my-4"></div>

                            <div class="text-gray-600 text-sm mb-1 flex justify-between">
                                <span>Kelas Kendaraan</span>
                                <span class="text-gray-800"><?= htmlspecialchars($row['kelas_kendaraan']); ?></span>
                            </div>

                            <?php if (!empty($row['peserta_kelas'])): ?>
                                <div class="text-gray-600 text-sm mb-1 flex justify-between">
                                    <span>Peserta</span>
                                    <span class="text-gray-800"><?= htmlspecialchars($row['peserta_kelas']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="text-gray-600 text-sm mb-1 flex justify-between">
                                <span>Voucher</span>
                                <span class="text-gray-800"><?= htmlspecialchars($row['voucher'] ?? '-'); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/user/js/alert/alert_manajer.js" defer></script>

<script>
    

    
    window.location.replace('index.php');
</script>


</html>