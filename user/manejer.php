<?php
require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header.php';

// ======================
// FORM MANAJER
// ======================
if (isset($_POST['submit_manajer'])) {
    $nama = $_POST['manajer_nama'];
    $nama_tim = $_POST['manajer_nama_tim'];
    $provinsi = $_POST['manajer_asal_provinsi'];
    $email = $_POST['manajer_email'];
    $whatsapp = $_POST['manajer_whatsapp'];
    $voucher = $_POST['manajer_voucher'] ?? '';

    // Upload Foto Manajer
    $foto = '';
    if (isset($_FILES['manajer_foto']) && $_FILES['manajer_foto']['error'] === 0) {
        $ext = pathinfo($_FILES['manajer_foto']['name'], PATHINFO_EXTENSION);

        // Tentukan folder upload baru
        $uploadDir = '../uploads/foto_manejer/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // buat folder jika belum ada
        }

        $foto = 'manajer_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['manajer_foto']['tmp_name'], $uploadDir . $foto);
    }


    // Insert ke tabel manajer
    $stmt = $db->prepare("INSERT INTO manajer (nama_manajer, nama_tim, foto_manajer, asal_provinsi, email, whatsapp, voucher, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssss", $nama, $nama_tim, $foto, $provinsi, $email, $whatsapp, $voucher);
    $stmt->execute();
    $manajer_id = $stmt->insert_id;

    // Insert Kendaraan Manajer
    if (!empty($_POST['manajer_kelas'])) {
        for ($i = 0; $i < count($_POST['manajer_kelas']); $i++) {
            $kelas = $_POST['manajer_kelas'][$i];
            $warna = $_POST['manajer_warna_kendaraan'][$i];
            $tipe = $_POST['manajer_tipe_kendaraan'][$i];
            $nomor_polisi = $_POST['manajer_nomor_polisi'][$i] ?? '';

            $stmt2 = $db->prepare("INSERT INTO manajer_kelas (manajer_id, kelas, warna_kendaraan, tipe_kendaraan, nomor_polisi) VALUES (?, ?, ?, ?, ?)");
            $stmt2->bind_param("issss", $manajer_id, $kelas, $warna, $tipe, $nomor_polisi);
            $stmt2->execute();
        }
    }

    echo "<script>alert('Pendaftaran Manajer berhasil!'); window.location='';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran Manajer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-blue-200 via-white to-white min-h-screen">
    <div class="container mx-auto px-6 py-10 max-w-3xl space-y-6">

        <h1 class="text-3xl font-bold text-blue-800 mb-2">Form Pendaftaran Manajer</h1>
        <p class="text-gray-600">Isi formulir di bawah ini untuk mendaftar sebagai manajer tim.</p>

        <form action="" method="POST" enctype="multipart/form-data"
            class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-lg space-y-8 border border-gray-100">

            <!-- DATA MANAJER -->
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">DATA MANAJER</h2>
            <div class="bg-blue-50 p-6 rounded-xl space-y-4 shadow-inner">
                <input type="text" name="manajer_nama" placeholder="Nama Manajer" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                <input type="text" name="manajer_nama_tim" placeholder="Nama Tim" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Foto Manajer
                            <span class="text-xs text-gray-500">(wajib, wajah jelas)</span>
                        </label>
                        <input type="file" name="manajer_foto" accept="image/*" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                    <input type="text" name="manajer_asal_provinsi" placeholder="Asal Provinsi" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="email" name="manajer_email" placeholder="Email" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    <input type="text" name="manajer_whatsapp" placeholder="Nomor WhatsApp" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>
            </div>

            <!-- DATA KELAS -->
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">DATA KELAS & KENDARAAN</h2>
            <div id="kelas-wrapper-manajer" class="space-y-4">
                <div class="kelas-item bg-blue-50 p-6 rounded-xl space-y-4 relative shadow-inner border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="manajer_kelas[]" placeholder="Kelas" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        <input type="text" name="manajer_warna_kendaraan[]" placeholder="Warna Kendaraan" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="manajer_tipe_kendaraan[]" placeholder="Tipe Kendaraan" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        <input type="text" name="manajer_nomor_polisi[]" placeholder="Nomor Polisi (opsional)" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
                    </div>
                    <button type="button" class="hapusKelas group absolute top-3 right-3 bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold rounded-full shadow-sm hover:shadow-md hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center gap-1">
                        <span class="transition-transform group-hover:rotate-90 duration-300">✕</span>
                        <span class="hidden sm:inline">Hapus</span>
                    </button>
                </div>
            </div>

            <button type="button" id="tambahKelasManajer" class="group relative overflow-hidden mt-5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-500 text-black font-semibold px-6 py-2 rounded-full transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.05]">
                <span class="relative z-10">+ Tambah Kelas</span>
                <span class="absolute inset-0 bg-yellow-300 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
            </button>

            <!-- Voucher -->
            <div class="bg-white/80 backdrop-blur-sm p-3 rounded-lg shadow-inner border border-blue-100 mt-4">
                <input type="text" name="manajer_voucher" placeholder="Kode Voucher (opsional)" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none placeholder-gray-400" />
            </div>

            <button type="submit" name="submit_manajer" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium px-5 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg mt-4">
                Kirim Pendaftaran Manajer
            </button>
        </form>

    </div>

    <script>
        function initKelasDynamic(wrapperId, tambahBtnId) {
            const wrapper = document.getElementById(wrapperId);
            const tambahBtn = document.getElementById(tambahBtnId);

            tambahBtn.addEventListener('click', () => {
                const firstItem = wrapper.querySelector('.kelas-item');
                const clone = firstItem.cloneNode(true);
                clone.querySelectorAll('input').forEach(input => input.value = '');
                wrapper.appendChild(clone);
                attachHapus(clone);
            });

            function attachHapus(item) {
                const btnHapus = item.querySelector('.hapusKelas');
                btnHapus.addEventListener('click', () => {
                    if (wrapper.querySelectorAll('.kelas-item').length > 1) {
                        item.remove();
                    } else {
                        alert('Minimal harus ada 1 kelas');
                    }
                });
            }

            wrapper.querySelectorAll('.kelas-item').forEach(item => attachHapus(item));
        }

        initKelasDynamic('kelas-wrapper-manajer', 'tambahKelasManajer');
    </script>
</body>

</html>

<?php require 'templates/footer.php'; ?>