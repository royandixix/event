<?php
require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header.php';

// --- FUNSI UPLOAD FILE ---
function uploadFile($file_input_name, $folder_name) {
    $upload_dir = __DIR__ . "/../uploads/$folder_name/";

    // Buat folder jika belum ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
        chmod($upload_dir, 0777);
    }

    // Cek apakah folder writable
    if (!is_writable($upload_dir)) {
        return [false, "Folder $upload_dir tidak bisa ditulis!"];
    }

    // Cek file di $_FILES
    if (!isset($_FILES[$file_input_name])) {
        return [false, 'File tidak dikirim.'];
    }

    $file = $_FILES[$file_input_name];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'Upload gagal. Kode error: ' . $file['error']];
    }

    $tmp_name = $file['tmp_name'];
    $original_name = basename($file['name']);
    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($ext, $allowed_ext)) {
        return [false, 'Format file tidak didukung. Gunakan JPG/PNG/GIF.'];
    }

    $new_name = $file_input_name . '_' . time() . '.' . $ext;
    $target_file = $upload_dir . $new_name;

    if (move_uploaded_file($tmp_name, $target_file)) {
        return [true, $new_name];
    } else {
        return [false, "Gagal memindahkan file ke folder $upload_dir"];
    }
}

// --- LOGIKA FORM MANAJER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_manajer'])) {
    $nama_manajer = $_POST['nama_manajer'] ?? '';
    $nama_tim = $_POST['nama_tim'] ?? '';
    $id_provinsi = $_POST['id_provinsi'] ?? '';
    $email = $_POST['email'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    $voucher = $_POST['voucher'] ?? null;

    $kelas_arr = $_POST['kelas'] ?? [];
    $warna_kendaraan_arr = $_POST['warna_kendaraan'] ?? [];
    $tipe_kendaraan_arr = $_POST['tipe_kendaraan'] ?? [];

    $foto_manajer = null;
    $error_msg = '';
    $asal_provinsi = '';

    // Ambil nama provinsi
    if (!empty($id_provinsi)) {
        $prov_query = $db->prepare("SELECT nama_provinsi FROM provinsi WHERE id_provinsi = ?");
        $prov_query->bind_param("i", $id_provinsi);
        $prov_query->execute();
        $prov_query->bind_result($asal_provinsi);
        $prov_query->fetch();
        $prov_query->close();
    }

    // Upload foto manajer ke folder 'peserta_upload' agar konsisten
    list($success, $result) = uploadFile('foto_manajer', 'foto_peserta');
    if ($success) {
        $foto_manajer = $result;
    } else {
        $error_msg = $result;
    }

    // Simpan data manajer
    if (empty($error_msg) && $foto_manajer && $nama_manajer && $nama_tim && $id_provinsi && $email && $whatsapp) {
        $stmt = $db->prepare("INSERT INTO manajer (nama_manajer, nama_tim, foto_manajer, id_provinsi, asal_provinsi, email, whatsapp, voucher) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nama_manajer, $nama_tim, $foto_manajer, $id_provinsi, $asal_provinsi, $email, $whatsapp, $voucher);
        if ($stmt->execute()) {
            $manajer_id = $stmt->insert_id;
            $stmt_kelas = $db->prepare("INSERT INTO manajer_kelas (manajer_id, kelas, warna_kendaraan, tipe_kendaraan) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < count($kelas_arr); $i++) {
                $kelas = $kelas_arr[$i];
                $warna = $warna_kendaraan_arr[$i] ?? '';
                $tipe = $tipe_kendaraan_arr[$i] ?? '';
                $stmt_kelas->bind_param("isss", $manajer_id, $kelas, $warna, $tipe);
                $stmt_kelas->execute();
            }
            $stmt_kelas->close();
            $stmt->close();
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            Swal.fire({
                icon: 'success',
                title: '✅ Pendaftaran Berhasil!',
                text: 'Data manajer berhasil dikirim.',
                confirmButtonText: 'Oke'
            }).then(()=>{ window.location.href='riwayat_manajer.php'; });
            </script>";
        } else {
            $error_msg = 'Gagal menyimpan data manajer.';
        }
    }

    if (!empty($error_msg)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '$error_msg',
            confirmButtonText: 'Tutup'
        });
        </script>";
    }
}

// --- LOGIKA FORM PESERTA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_peserta'])) {
    $nama_peserta = $_POST['nama_peserta'] ?? '';
    $nama_tim = $_POST['nama_tim'] ?? '';
    $id_provinsi = $_POST['id_provinsi'] ?? '';
    $email = $_POST['email'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    $voucher = $_POST['voucher'] ?? null;

    $kelas_arr = $_POST['kelas'] ?? [];
    $warna_kendaraan_arr = $_POST['warna_kendaraan'] ?? [];
    $tipe_kendaraan_arr = $_POST['tipe_kendaraan'] ?? [];

    $foto_peserta = null;
    $error_msg = '';
    $asal_provinsi = '';

    // Ambil nama provinsi
    if (!empty($id_provinsi)) {
        $prov_query = $db->prepare("SELECT nama_provinsi FROM provinsi WHERE id_provinsi = ?");
        $prov_query->bind_param("i", $id_provinsi);
        $prov_query->execute();
        $prov_query->bind_result($asal_provinsi);
        $prov_query->fetch();
        $prov_query->close();
    }

    // Upload foto peserta ke folder 'foto_peserta'
    list($success, $result) = uploadFile('foto_peserta', 'foto_peserta');
    if ($success) {
        $foto_peserta = $result;
    } else {
        $error_msg = $result;
    }

    // Simpan data peserta dengan asal_provinsi
    if (empty($error_msg) && $foto_peserta && $nama_peserta && $nama_tim && $id_provinsi && $email && $whatsapp) {
        $stmt = $db->prepare("INSERT INTO peserta (nama_peserta, nama_tim, asal_provinsi, foto_peserta, id_provinsi, email, whatsapp, voucher) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nama_peserta, $nama_tim, $asal_provinsi, $foto_peserta, $id_provinsi, $email, $whatsapp, $voucher);
        if ($stmt->execute()) {
            $id_peserta = $stmt->insert_id;
            $stmt_kelas = $db->prepare("INSERT INTO peserta_kelas (peserta_id, kelas, warna_kendaraan, tipe_kendaraan) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < count($kelas_arr); $i++) {
                $kelas = $kelas_arr[$i];
                $warna = $warna_kendaraan_arr[$i] ?? '';
                $tipe = $tipe_kendaraan_arr[$i] ?? '';
                $stmt_kelas->bind_param("isss", $id_peserta, $kelas, $warna, $tipe);
                $stmt_kelas->execute();
            }
            $stmt_kelas->close();
            $stmt->close();
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            Swal.fire({
                icon: 'success',
                title: '🎉 Pendaftaran Berhasil!',
                text: 'Data peserta berhasil dikirim.',
                confirmButtonText: 'Oke'
            }).then(()=>{ window.location.href='riwayat.php'; });
            </script>";
        } else {
            $error_msg = 'Gagal menyimpan data peserta.';
        }
    }

    if (!empty($error_msg)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '$error_msg',
            confirmButtonText: 'Tutup'
        });
        </script>";
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gradient-to-r from-pink-200 via-white to-white">
    <div class="bg-gradient-to-r from-pink-200 via-white to-white">
        <div class="container mx-auto px-6 py-10 max-w-3xl space-y-12">

            <form action="" method="POST" enctype="multipart/form-data" id="form-manajer"
                class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-lg space-y-8 border border-gray-100">

                <h1 class="text-3xl font-bold text-pink-800 mb-2">Form Pendaftaran Manajer</h1>
                <p class="text-gray-600">Isi formulir di bawah ini untuk mendaftar sebagai manajer.</p>

                <h2 class="text-lg font-bold text-pink-800 bg-pink-100 px-5 py-2 rounded-full inline-block shadow-sm">
                    DATA MANAJER
                </h2>
                <div class="bg-pink-50 p-6 rounded-xl space-y-4 shadow-inner">
                    <input type="text" name="nama_manajer" placeholder="Nama Manajer" data-required
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-pink-400 focus:outline-none" required />
                    <input type="text" name="nama_tim" placeholder="Nama Tim" data-required
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-pink-400 focus:outline-none" required />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Foto Manajer
                                <span class="text-xs text-gray-500">(wajib, wajah jelas)</span>
                            </label>
                            <input type="file" name="foto_manajer" data-required
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-pink-400 focus:outline-none" required />
                        </div>

                        <?php
                        $provinsi_query = $db->query("SELECT * FROM provinsi ORDER BY nama_provinsi ASC");
                        $provinsi_list = $provinsi_query->fetch_all(MYSQLI_ASSOC);
                        ?>
                        <div class="relative w-full">
                            <label for="id_provinsi_manajer" class="block text-sm font-medium mb-1 text-gray-700">Asal Provinsi</label>
                            <select name="id_provinsi" id="id_provinsi_manajer" data-required
                                class="w-full appearance-none p-3 border border-gray-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 hover:border-pink-300 transition-all duration-200"
                                required>
                                <option value="">Pilih Provinsi</option>
                                <?php foreach ($provinsi_list as $p): ?>
                                    <option value="<?= $p['id_provinsi'] ?>"><?= $p['nama_provinsi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="email" name="email" placeholder="Email" data-required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-pink-400 focus:outline-none" required />
                        <input type="text" name="whatsapp" placeholder="Nomor WhatsApp" data-required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-pink-400 focus:outline-none" required />
                    </div>
                </div>

                <h2 class="text-lg font-bold text-pink-800 bg-pink-100 px-5 py-2 rounded-full inline-block shadow-sm">
                    DATA KELAS & KENDARAAN
                </h2>
                <div id="kelas-wrapper-manajer" class="space-y-4">
                    <div class="kelas-item bg-pink-50 p-6 rounded-xl space-y-4 relative shadow-inner border border-pink-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="kelas[]" placeholder="Kelas" data-required
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-pink-400 focus:outline-none" required />
                            <input type="text" name="warna_kendaraan[]" placeholder="Warna Kendaraan" data-required
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-pink-400 focus:outline-none" required />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="text" name="tipe_kendaraan[]" placeholder="Tipe Kendaraan" data-required
                                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-pink-400 focus:outline-none" required />
                                <p class="text-xs text-gray-500 mt-1">Contoh: Fortuner, Pajero, Mio, Jupiter</p>
                            </div>
                        </div>
                        <button type="button" class="hapusKelas group absolute top-3 right-3 bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold rounded-full shadow-sm hover:shadow-md hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center gap-1">
                            <span class="transition-transform group-hover:rotate-90 duration-300">✕</span>
                            <span class="hidden sm:inline">Hapus</span>
                        </button>
                    </div>
                </div>

                <button type="button" id="tambahKelas-manajer"
                    class="group relative overflow-hidden mt-5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-500 text-black font-semibold px-6 py-2 rounded-full transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.05]">
                    <span class="relative z-10">+ Tambah Kelas</span>
                </button>

                <div class="bg-white/80 backdrop-blur-sm p-3 rounded-lg shadow-inner border border-pink-100 mt-4">
                    <input type="text" name="voucher" placeholder="Kode Voucher (opsional)"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-400 focus:outline-none placeholder-gray-400" />
                </div>

                <button type="submit" name="submit_manajer"
                    class="bg-gradient-to-r from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 text-white font-medium px-5 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg mt-4">
                    Kirim Pendaftaran Manajer
                </button>
            </form>

            <hr class="border-t-2 border-dashed border-gray-300 my-10">

            <form action="" method="POST" enctype="multipart/form-data" id="form-peserta"
                class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-lg space-y-8 border border-gray-100">

                <h1 class="text-3xl font-bold text-blue-800 mb-2">Form Pendaftaran Peserta</h1>
                <p class="text-gray-600">Isi formulir di bawah ini untuk mendaftar sebagai peserta.</p>

                <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">
                    DATA PESERTA
                </h2>
                <div class="bg-blue-50 p-6 rounded-xl space-y-4 shadow-inner">
                    <input type="text" name="nama_peserta" placeholder="Nama Peserta" data-required
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    <input type="text" name="nama_tim" placeholder="Nama Tim" data-required
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Foto Peserta
                                <span class="text-xs text-gray-500">(wajib, wajah jelas)</span>
                            </label>
                            <input type="file" name="foto_peserta" data-required
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        </div>

                        <?php
                        $provinsi_query = $db->query("SELECT * FROM provinsi ORDER BY nama_provinsi ASC");
                        $provinsi_list = $provinsi_query->fetch_all(MYSQLI_ASSOC);
                        ?>
                        <div class="relative w-full">
                            <label for="id_provinsi_peserta" class="block text-sm font-medium mb-1 text-gray-700">Asal Provinsi</label>
                            <select name="id_provinsi" id="id_provinsi_peserta" data-required
                                class="w-full appearance-none p-3 border border-gray-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 hover:border-blue-300 transition-all duration-200"
                                required>
                                <option value="">Pilih Provinsi</option>
                                <?php foreach ($provinsi_list as $p): ?>
                                    <option value="<?= $p['id_provinsi'] ?>"><?= $p['nama_provinsi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="email" name="email" placeholder="Email" data-required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        <input type="text" name="whatsapp" placeholder="Nomor WhatsApp" data-required
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                </div>

                <h2 class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">
                    DATA KELAS & KENDARAAN
                </h2>
                <div id="kelas-wrapper-peserta" class="space-y-4">
                    <div class="kelas-item bg-blue-50 p-6 rounded-xl space-y-4 relative shadow-inner border border-blue-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="kelas[]" placeholder="Kelas" data-required
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                            <input type="text" name="warna_kendaraan[]" placeholder="Warna Kendaraan" data-required
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="text" name="tipe_kendaraan[]" placeholder="Tipe Kendaraan" data-required
                                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                                <p class="text-xs text-gray-500 mt-1">Contoh: Fortuner, Pajero, Mio, Jupiter</p>
                            </div>
                        </div>
                        <button type="button" class="hapusKelas group absolute top-3 right-3 bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold rounded-full shadow-sm hover:shadow-md hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center gap-1">
                            <span class="transition-transform group-hover:rotate-90 duration-300">✕</span>
                            <span class="hidden sm:inline">Hapus</span>
                        </button>
                    </div>
                </div>

                <button type="button" id="tambahKelas-peserta"
                    class="group relative overflow-hidden mt-5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-500 text-black font-semibold px-6 py-2 rounded-full transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.05]">
                    <span class="relative z-10">+ Tambah Kelas</span>
                    <span class="absolute inset-0 bg-yellow-300 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                </button>

                <div class="bg-white/80 backdrop-blur-sm p-3 rounded-lg shadow-inner border border-blue-100 mt-4">
                    <input type="text" name="voucher" placeholder="Kode Voucher (opsional)"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none placeholder-gray-400" />
                </div>

                <button type="submit" name="submit_peserta"
                    class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium px-5 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg mt-4">
                    Kirim Pendaftaran Peserta
                </button>
            </form>
        </div>
    </div>

    <script>
        function setupForm(formId, kelasWrapperId, tambahBtnId) {
            const form = document.getElementById(formId);
            const wrapper = document.getElementById(kelasWrapperId);
            const tambahBtn = document.getElementById(tambahBtnId);

            // Tambah Kelas
            tambahBtn.addEventListener("click", () => {
                const firstItem = wrapper.querySelector(".kelas-item");
                const clone = firstItem.cloneNode(true);
                clone.querySelectorAll("input").forEach(input => input.value = "");
                wrapper.appendChild(clone);
            });

            // Hapus Kelas
            wrapper.addEventListener("click", (e) => {
                const btn = e.target.closest(".hapusKelas");
                if (btn && btn.closest(".kelas-item")) {
                    const items = wrapper.querySelectorAll(".kelas-item");
                    if (items.length > 1) {
                        btn.closest(".kelas-item").remove();
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: "Oops...",
                            text: "Minimal harus ada 1 kelas!",
                            confirmButtonText: "OK"
                        });
                    }
                }
            });

            // Validasi Form sebelum submit
            form.addEventListener("submit", (e) => {
                const requiredFields = form.querySelectorAll("input[data-required], select[data-required]");
                let emptyFields = [];
                requiredFields.forEach(field => {
                    if ((field.type === "file" && field.files.length === 0) ||
                        (field.tagName === "SELECT" && !field.value) ||
                        (field.type !== "file" && field.tagName !== "SELECT" && !field.value.trim())) {
                        let placeholderText = field.placeholder;
                        if (!placeholderText) {
                            const label = field.previousElementSibling;
                            if (label && label.tagName === 'LABEL') {
                                placeholderText = label.textContent.trim();
                            }
                        }
                        emptyFields.push(placeholderText || field.name);
                    }
                });

                if (emptyFields.length > 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: "warning",
                        title: "Form Belum Lengkap!",
                        html: `<ul style="text-align:left;">${emptyFields.map(f => `<li>${f}</li>`).join("")}</ul>`,
                        confirmButtonText: "Oke"
                    });
                }
            });
        }

        // Jalankan fungsi setup untuk setiap form
        document.addEventListener("DOMContentLoaded", () => {
            setupForm('form-manajer', 'kelas-wrapper-manajer', 'tambahKelas-manajer');
            setupForm('form-peserta', 'kelas-wrapper-peserta', 'tambahKelas-peserta');
        });
    </script>

    <?php require 'templates/footer.php'; ?>
</body>
</html>