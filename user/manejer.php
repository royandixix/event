<?php
require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header.php';

// Fungsi upload foto
function uploadFoto($file, $folder, $prefix)
{
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed_ext)) {
            return ['error' => 'Format tidak didukung'];
        }
        $new_name = $prefix . '_' . time() . '.' . $ext;
        $upload_dir = "../uploads/{$folder}/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
            return ['success' => $new_name];
        } else {
            return ['error' => 'Gagal upload foto'];
        }
    }
    return ['error' => 'File tidak ditemukan'];
}

// =======================
// Proses Pendaftaran Manajer
// =======================
if (isset($_POST['submit_manajer'])) {
    $foto = uploadFoto($_FILES['foto_manajer'], 'foto_manajer', 'foto_manajer');
    if (isset($foto['error'])) {
        echo "<script>alert('{$foto['error']}');</script>";
    } else {
        $stmt = $db->prepare("INSERT INTO manajer (nama_manajer, nama_tim, foto_manajer, asal_provinsi, email, whatsapp, voucher) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssss",
            $_POST['nama_manajer'],
            $_POST['nama_tim'],
            $foto['success'],
            $_POST['asal_provinsi'],
            $_POST['email'],
            $_POST['whatsapp'],
            $_POST['voucher']
        );
        if ($stmt->execute()) {
            $id_manajer = $stmt->insert_id;
            $stmt_kelas = $db->prepare("INSERT INTO manajer_kelas (manajer_id, kelas, warna_kendaraan, tipe_kendaraan) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < count($_POST['kelas_manajer']); $i++) {
                $stmt_kelas->bind_param(
                    "isss",
                    $id_manajer,
                    $_POST['kelas_manajer'][$i],
                    $_POST['warna_kendaraan_manajer'][$i],
                    $_POST['tipe_kendaraan_manajer'][$i]
                );
                $stmt_kelas->execute();
            }
            $stmt_kelas->close();
            echo "<script>alert('Pendaftaran manajer berhasil');window.location='manajer.php';</script>";
        }
    }
}

// =======================
// Proses Pendaftaran Peserta
// =======================
if (isset($_POST['submit_peserta'])) {
    $foto = uploadFoto($_FILES['foto_peserta'], 'foto_peserta', 'foto_peserta');
    if (isset($foto['error'])) {
        echo "<script>alert('{$foto['error']}');</script>";
    } else {
        $stmt = $db->prepare("INSERT INTO peserta (nama_peserta, nama_tim, foto_peserta, asal_provinsi, email, whatsapp, voucher) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssss",
            $_POST['nama_peserta'],
            $_POST['nama_tim_peserta'],
            $foto['success'],
            $_POST['asal_provinsi_peserta'],
            $_POST['email_peserta'],
            $_POST['whatsapp_peserta'],
            $_POST['voucher_peserta']
        );
        if ($stmt->execute()) {
            $id_peserta = $stmt->insert_id;
            $stmt_kelas = $db->prepare(" INSERT INTO  peserta_kelas (peserta_id, kelas, warna_kendaraan, tipe_kendaraan, nomor_polisi) VALUES (?, ?, ?, ?, ?)");
            for ($i = 0; $i < count($_POST['kelas_peserta']); $i++) {
                $stmt_kelas->bind_param(
                    "issss",
                    $id_peserta,
                    $_POST['kelas_peserta'][$i],
                    $_POST['warna_kendaraan_peserta'][$i],
                    $_POST['tipe_kendaraan_peserta'][$i],
                    $_POST['nomor_polisi_peserta'][$i]
                );
                $stmt_kelas->execute();
            }
            $stmt_kelas->close();
            echo "<script>alert('Pendaftaran peserta berhasil');window.location='peserta.php';</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Form Pendaftaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-blue-200 via-white to-white min-h-screen">
    <div class="container mx-auto px-6 py-10 max-w-3xl space-y-6">

        <!-- Form Manajer -->
        <div>
            <h1 class="text-3xl font-bold text-blue-800 mb-2">Form Pendaftaran Manajer</h1>
            <p class="text-gray-600">Isi formulir di bawah ini untuk mendaftar sebagai manajer tim.</p>
        </div>
        <form action="" method="POST" enctype="multipart/form-data"
            class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-lg space-y-8 border border-gray-100">

            <!-- DATA MANAJER -->
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">
                DATA MANAJER
            </h2>
            <div class="bg-blue-50 p-6 rounded-xl space-y-4 shadow-inner">
                <input type="text" name="nama_manajer" placeholder="Nama Manajer" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                <input type="text" name="nama_tim" placeholder="Nama Tim" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Foto Manajer
                            <span class="text-xs text-gray-500">(wajib, wajah jelas)</span>
                        </label>
                        <input type="file" name="foto_manajer" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                    <input type="text" name="asal_provinsi" placeholder="Asal Provinsi" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="email" name="email" placeholder="Email" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    <input type="text" name="whatsapp" placeholder="Nomor WhatsApp" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>
            </div>

            <!-- DATA KELAS -->
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">
                DATA KELAS & KENDARAAN
            </h2>
            <div id="kelas-wrapper-manajer" class="space-y-4">
                <div class="kelas-item bg-blue-50 p-6 rounded-xl space-y-4 relative shadow-inner border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="kelas[]" placeholder="Kelas" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        <input type="text" name="warna_kendaraan[]" placeholder="Warna Kendaraan" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input type="text" name="tipe_kendaraan[]" placeholder="Tipe Kendaraan" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                            <p class="text-xs text-gray-500 mt-1">Contoh: Fortuner, Pajero, Mio, Jupiter</p>
                        </div>
                        <div>
                            <input type="text" name="nomor_polisi[]" placeholder="Nomor Polisi" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
                            <p class="text-xs text-gray-500 mt-1">Opsional, contoh: B 1234 XYZ</p>
                        </div>
                    </div>
                    <button type="button" onclick="hapusKelas(this)" class="group absolute top-3 right-3 bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold rounded-full shadow-sm hover:shadow-md hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center gap-1">
                        <span class="transition-transform group-hover:rotate-90 duration-300">✕</span>
                        <span class="hidden sm:inline">Hapus</span>
                    </button>
                </div>
            </div>

            <!-- Tombol Tambah -->
            <button type="button" id="tambahKelasManajer" class="group relative overflow-hidden mt-5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-500 text-black font-semibold px-6 py-2 rounded-full transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.05]">
                <span class="relative z-10">+ Tambah Kelas</span>
                <span class="absolute inset-0 bg-yellow-300 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
            </button>

            <!-- Voucher -->
            <div class="bg-white/80 backdrop-blur-sm p-3 rounded-lg shadow-inner border border-blue-100 mt-4">
                <input type="text" name="voucher" placeholder="Kode Voucher (opsional)" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none placeholder-gray-400" />
            </div>

            <!-- Submit -->
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium px-5 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg mt-4">
                Kirim Pendaftaran Manajer
            </button>
        </form>

        <!-- Form Peserta -->
        <div>
            <h1 class="text-3xl font-bold text-blue-800 mb-2">Form Pendaftaran Peserta</h1>
            <p class="text-gray-600">Isi formulir di bawah ini untuk mendaftar sebagai peserta lomba.</p>
        </div>
        <form action="" method="POST" enctype="multipart/form-data"
            class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-lg space-y-8 border border-gray-100">

            <!-- DATA PESERTA -->
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">
                DATA PESERTA
            </h2>
            <div class="bg-blue-50 p-6 rounded-xl space-y-4 shadow-inner">
                <input type="text" name="nama_peserta" placeholder="Nama Peserta" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                <input type="text" name="nama_tim" placeholder="Nama Tim" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Foto Peserta
                            <span class="text-xs text-gray-500">(wajib, wajah jelas)</span>
                        </label>
                        <input type="file" name="foto_peserta" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                    <input type="text" name="asal_provinsi" placeholder="Asal Provinsi" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="email" name="email" placeholder="Email" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    <input type="text" name="whatsapp" placeholder="Nomor WhatsApp" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>
            </div>

            <!-- DATA KELAS -->
            <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">
                DATA KELAS & KENDARAAN
            </h2>
            <div id="kelas-wrapper-peserta" class="space-y-4">
                <div class="kelas-item bg-blue-50 p-6 rounded-xl space-y-4 relative shadow-inner border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="kelas[]" placeholder="Kelas" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        <input type="text" name="warna_kendaraan[]" placeholder="Warna Kendaraan" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input type="text" name="tipe_kendaraan[]" placeholder="Tipe Kendaraan" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                            <p class="text-xs text-gray-500 mt-1">Contoh: Fortuner, Pajero, Mio, Jupiter</p>
                        </div>
                        <div>
                            <input type="text" name="nomor_polisi[]" placeholder="Nomor Polisi" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
                            <p class="text-xs text-gray-500 mt-1">Opsional, contoh: B 1234 XYZ</p>
                        </div>
                    </div>
                    <button type="button" onclick="hapusKelas(this)" class="group absolute top-3 right-3 bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold rounded-full shadow-sm hover:shadow-md hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center gap-1">
                        <span class="transition-transform group-hover:rotate-90 duration-300">✕</span>
                        <span class="hidden sm:inline">Hapus</span>
                    </button>
                </div>
            </div>

            <!-- Tombol Tambah -->
            <button type="button" id="tambahKelasPeserta" class="group relative overflow-hidden mt-5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-500 text-black font-semibold px-6 py-2 rounded-full transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.05]">
                <span class="relative z-10">+ Tambah Kelas</span>
                <span class="absolute inset-0 bg-yellow-300 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
            </button>

            <!-- Voucher -->
            <div class="bg-white/80 backdrop-blur-sm p-3 rounded-lg shadow-inner border border-blue-100 mt-4">
                <input type="text" name="voucher" placeholder="Kode Voucher (opsional)" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none placeholder-gray-400" />
            </div>

            <!-- Submit -->
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium px-5 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg mt-4">
                Kirim Pendaftaran
            </button>
        </form>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- JS -->
    <script>
        function hapusKelas(button) {
            button.closest('.kelas-item').remove();
        }

        function tambahKelas(wrapperId) {
            const wrapper = document.getElementById(wrapperId);
            const kelasItem = wrapper.querySelector('.kelas-item');
            const clone = kelasItem.cloneNode(true);
            clone.querySelectorAll('input').forEach(input => input.value = '');
            wrapper.appendChild(clone);
        }
        document.getElementById('tambahKelasManajer').addEventListener('click', () => tambahKelas('kelas-wrapper-manajer'));
        document.getElementById('tambahKelasPeserta').addEventListener('click', () => tambahKelas('kelas-wrapper-peserta'));
    </script>

    <?php require 'templates/footer.php'; ?>
</body>

</html>