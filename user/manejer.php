<?php

ob_start(); // menampung output sementara
session_start();
require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header.php';

// ======================
// FORM MANAJER
// ======================
if (isset($_POST['submit_manajer'])) {
    $nama      = $_POST['manajer_nama'] ?? '';
    $nama_tim  = $_POST['manajer_nama_tim'] ?? '';
    $provinsi  = $_POST['manajer_asal_provinsi'] ?? '';
    $email     = $_POST['manajer_email'] ?? '';
    $whatsapp  = $_POST['manajer_whatsapp'] ?? '';
    $voucher   = $_POST['manajer_voucher'] ?? '';
    $error_msg = '';

    // Upload Foto Manajer
    $foto = null;
    if (isset($_FILES['manajer_foto']) && $_FILES['manajer_foto']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['manajer_foto']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed_ext)) {
            $uploadDir = '../uploads/foto_manajer/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $foto = 'manajer_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['manajer_foto']['tmp_name'], $uploadDir . $foto);
        } else {
            $error_msg = 'Format foto tidak didukung. Gunakan JPG/PNG/GIF.';
        }
    } else {
        $error_msg = 'Foto manajer wajib diupload.';
    }

    if (empty($error_msg)) {
        $stmt = $db->prepare("INSERT INTO manajer 
            (nama_manajer, nama_tim, foto_manajer, asal_provinsi, email, whatsapp, voucher, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssssss", $nama, $nama_tim, $foto, $provinsi, $email, $whatsapp, $voucher);
        if ($stmt->execute()) {
            $manajer_id = $stmt->insert_id;

            // Insert Kendaraan Manajer
            if (!empty($_POST['manajer_kelas'])) {
                $stmt2 = $db->prepare("INSERT INTO manajer_kelas 
                    (manajer_id, kelas, warna_kendaraan, tipe_kendaraan, nomor_polisi) 
                    VALUES (?, ?, ?, ?, ?)");
                for ($i = 0; $i < count($_POST['manajer_kelas']); $i++) {
                    $kelas = $_POST['manajer_kelas'][$i];
                    $warna = $_POST['manajer_warna_kendaraan'][$i];
                    $tipe  = $_POST['manajer_tipe_kendaraan'][$i];
                    $nopol = $_POST['manajer_nomor_polisi'][$i] ?? '';
                    $stmt2->bind_param("issss", $manajer_id, $kelas, $warna, $tipe, $nopol);
                    $stmt2->execute();
                }
                $stmt2->close();
            }

            $_SESSION['swal'] = [
                'icon' => 'success',
                'title' => '✅ Pendaftaran Manajer Berhasil',
                'text' => 'Data manajer sudah tersimpan.',
                'redirect' => 'riwayat_manajer.php'
            ];
        } else {
            $_SESSION['swal'] = [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'Gagal simpan data manajer.'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Error!',
            'text' => $error_msg
        ];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// ======================
// FORM PESERTA
// ======================
if (isset($_POST['submit_peserta'])) {
    $nama_peserta = $_POST['nama_peserta'] ?? '';
    $nama_tim     = $_POST['nama_tim'] ?? '';
    $asal_prov    = $_POST['asal_provinsi'] ?? '';
    $email        = $_POST['email'] ?? '';
    $whatsapp     = $_POST['whatsapp'] ?? '';
    $voucher      = $_POST['voucher'] ?? '';
    $error_msg    = '';

    $kelas_arr    = $_POST['kelas'] ?? [];
    $warna_arr    = $_POST['warna_kendaraan'] ?? [];
    $tipe_arr     = $_POST['tipe_kendaraan'] ?? [];
    $nopol_arr    = $_POST['nomor_polisi'] ?? [];

    // Upload Foto Peserta
    $foto_peserta = null;
    if (isset($_FILES['foto_peserta']) && $_FILES['foto_peserta']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto_peserta']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed_ext)) {
            $upload_dir = '../uploads/foto_peserta/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $foto_peserta = 'peserta_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['foto_peserta']['tmp_name'], $upload_dir . $foto_peserta);
        } else {
            $error_msg = 'Format foto peserta tidak didukung.';
        }
    } else {
        $error_msg = 'Foto peserta wajib diupload.';
    }

    if (empty($error_msg) && $foto_peserta && $nama_peserta && $nama_tim && $asal_prov && $email && $whatsapp) {
        $stmt = $db->prepare("INSERT INTO peserta 
            (nama_peserta, nama_tim, foto_peserta, asal_provinsi, email, whatsapp, voucher) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nama_peserta, $nama_tim, $foto_peserta, $asal_prov, $email, $whatsapp, $voucher);

        if ($stmt->execute()) {
            $id_peserta = $stmt->insert_id;

            $stmt2 = $db->prepare("INSERT INTO peserta_kelas 
                (peserta_id, kelas, warna_kendaraan, tipe_kendaraan, nomor_polisi) 
                VALUES (?, ?, ?, ?, ?)");
            for ($i = 0; $i < count($kelas_arr); $i++) {
                $kelas  = $kelas_arr[$i];
                $warna  = $warna_arr[$i] ?? '';
                $tipe   = $tipe_arr[$i] ?? '';
                $nopol  = $nopol_arr[$i] ?? '';
                $stmt2->bind_param("issss", $id_peserta, $kelas, $warna, $tipe, $nopol);
                $stmt2->execute();
            }
            $stmt2->close();

            $_SESSION['swal'] = [
                'icon' => 'success',
                'title' => '🎉 Pendaftaran Peserta Berhasil',
                'text' => 'Data peserta sudah tersimpan.',
                'redirect' => 'riwayat_manajer.php'
            ];
        } else {
            $_SESSION['swal'] = [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'Gagal simpan data peserta.'
            ];
        }
        $stmt->close();
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Error!',
            'text' => $error_msg
        ];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// ======================
// SWEETALERT SESSION
// ======================
if (!empty($_SESSION['swal'])) {
    $swal = $_SESSION['swal'];
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            icon: '{$swal['icon']}',
            title: '{$swal['title']}',
            text: '{$swal['text']}',
            confirmButtonColor: '#2563eb'
        }).then(() => {
            " . (isset($swal['redirect']) ? "window.location.href='{$swal['redirect']}';" : "") . "
        });
    </script>";
    unset($_SESSION['swal']);
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

        <!-- FORM MANAJER -->
        <form action="" method="POST" enctype="multipart/form-data"
            class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-lg space-y-8 border border-gray-100">

            <!-- DATA MANAJER -->
            <h2
                class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">DATA
                MANAJER</h2>
            <div class="bg-blue-50 p-6 rounded-xl space-y-4 shadow-inner">
                <input type="text" name="manajer_nama" placeholder="Nama Manajer"
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                <input type="text" name="manajer_nama_tim" placeholder="Nama Tim"
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Foto Manajer
                            <span class="text-xs text-gray-500">(wajib, wajah jelas)</span>
                        </label>
                        <input type="file" name="manajer_foto" accept="image/*"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                            required />
                    </div>
                    <input type="text" name="manajer_asal_provinsi" placeholder="Asal Provinsi"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="email" name="manajer_email" placeholder="Email"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    <input type="text" name="manajer_whatsapp" placeholder="Nomor WhatsApp"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>
            </div>

            <!-- DATA KELAS & KENDARAAN MANAJER -->
            <h2
                class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">DATA
                KELAS & KENDARAAN</h2>
            <div id="kelas-wrapper-manajer" class="space-y-4">
                <div class="kelas-item bg-blue-50 p-6 rounded-xl space-y-4 relative shadow-inner border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="manajer_kelas[]" placeholder="Kelas"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        <input type="text" name="manajer_warna_kendaraan[]" placeholder="Warna Kendaraan"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="manajer_tipe_kendaraan[]" placeholder="Tipe Kendaraan"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        <input type="text" name="manajer_nomor_polisi[]" placeholder="Nomor Polisi (opsional)"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
                    </div>
                    <button type="button"
                        class="hapusKelas group absolute top-3 right-3 bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold rounded-full shadow-sm hover:shadow-md hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center gap-1">
                        <span class="transition-transform group-hover:rotate-90 duration-300">✕</span>
                        <span class="hidden sm:inline">Hapus</span>
                    </button>
                </div>
            </div>

            <button type="button" id="tambahKelasManajer"
                class="group relative overflow-hidden mt-5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-500 text-black font-semibold px-6 py-2 rounded-full transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.05]">
                <span class="relative z-10">+ Tambah Kelas</span>
                <span class="absolute inset-0 bg-yellow-300 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
            </button>

            <div class="bg-white/80 backdrop-blur-sm p-3 rounded-lg shadow-inner border border-blue-100 mt-4">
                <input type="text" name="manajer_voucher" placeholder="Kode Voucher (opsional)"
                    class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none placeholder-gray-400" />
            </div>

            <button type="submit" name="submit_manajer"
                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium px-5 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg mt-4">
                Kirim Pendaftaran Manajer
            </button>
        </form>

        <!-- FORM PESERTA -->
        <form action="" method="POST" enctype="multipart/form-data"
            class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-lg space-y-8 border border-gray-100">

            <h2
                class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">DATA
                PESERTA</h2>
            <div class="bg-blue-50 p-6 rounded-xl space-y-4 shadow-inner">
                <input type="text" name="nama_peserta" placeholder="Nama Peserta"
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                <input type="text" name="nama_tim" placeholder="Nama Tim"
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Foto Peserta
                            <span class="text-xs text-gray-500">(wajib, wajah jelas)</span>
                        </label>
                        <input type="file" name="foto_peserta"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                    <input type="text" name="asal_provinsi" placeholder="Asal Provinsi"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="email" name="email" placeholder="Email"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    <input type="text" name="whatsapp" placeholder="Nomor WhatsApp"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                </div>
            </div>

            <!-- DATA KELAS & KENDARAAN PESERTA -->
            <h2
                class="text-lg font-bold text-blue-800 bg-blue-100 px-5 py-2 rounded-full inline-block shadow-sm">DATA
                KELAS & KENDARAAN</h2>
            <div id="kelas-wrapper" class="space-y-4">
                <div class="kelas-item bg-blue-50 p-6 rounded-xl space-y-4 relative shadow-inner border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="kelas[]" placeholder="Kelas"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                        <input type="text" name="warna_kendaraan[]" placeholder="Warna Kendaraan"
                            class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input type="text" name="tipe_kendaraan[]" placeholder="Tipe Kendaraan"
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
                            <p class="text-xs text-gray-500 mt-1">Contoh: Fortuner, Pajero, Mio, Jupiter</p>
                        </div>
                        <div>
                            <input type="text" name="nomor_polisi[]" placeholder="Nomor Polisi"
                                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
                            <p class="text-xs text-gray-500 mt-1">Opsional, contoh: B 1234 XYZ</p>
                        </div>
                    </div>
                    <button type="button"
                        class="hapusKelas group absolute top-3 right-3 bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold rounded-full shadow-sm hover:shadow-md hover:bg-red-500 hover:text-white transition-all duration-300 flex items-center gap-1">
                        <span class="transition-transform group-hover:rotate-90 duration-300">✕</span>
                        <span class="hidden sm:inline">Hapus</span>
                    </button>

                </div>
            </div>

            <button type="button" id="tambahKelas"
                class="group relative overflow-hidden mt-5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-500 text-black font-semibold px-6 py-2 rounded-full transition-all duration-300 shadow-md hover:shadow-lg hover:scale-[1.05]">
                <span class="relative z-10">+ Tambah Kelas</span>
                <span class="absolute inset-0 bg-yellow-300 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
            </button>

            <div class="bg-white/80 backdrop-blur-sm p-3 rounded-lg shadow-inner border border-blue-100 mt-4">
                <input type="text" name="voucher" placeholder="Kode Voucher (opsional)"
                    class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none placeholder-gray-400" />
            </div>

            <button type="submit"
                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium px-5 py-2 rounded-lg transition duration-200 shadow-md hover:shadow-lg mt-4">
                Kirim Pendaftaran
            </button>
        </form>

    </div>



</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="./js/alert/alert_manajer.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

  /**
   * Inisialisasi dynamic kelas
   */
  function initKelasDynamic(wrapperId, tambahBtnId) {
    const wrapper = document.getElementById(wrapperId);
    const tambahBtn = document.getElementById(tambahBtnId);

    // Tambah kelas
    tambahBtn.addEventListener("click", () => {
      const firstItem = wrapper.querySelector(".kelas-item");
      const clone = firstItem.cloneNode(true);
      clone.querySelectorAll("input").forEach(input => input.value = "");
      wrapper.appendChild(clone);
    });

    // Hapus kelas dengan validasi minimal 1 item
    wrapper.addEventListener("click", (e) => {
      if (e.target.closest(".hapusKelas") || e.target.closest("button")) {
        const items = wrapper.querySelectorAll(".kelas-item");
        const itemToRemove = e.target.closest(".kelas-item");

        if (items.length > 1) {
          itemToRemove.remove();
        } else {
          Swal.fire({
            icon: "warning",
            title: "Oops...",
            text: "Minimal harus ada 1 kelas!",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
          });
        }
      }
    });
  }

  initKelasDynamic("kelas-wrapper", "tambahKelas");

  /**
   * Setup validasi form
   */
  function setupValidation(formSelector) {
    const form = document.querySelector(formSelector);
    const submitBtn = form.querySelector('button[type="submit"]');

    submitBtn.addEventListener("click", (e) => {
      e.preventDefault();

      const requiredFields = form.querySelectorAll("input[required], select[required]");
      let emptyFields = [];

      requiredFields.forEach(field => {
        if ((field.type === "file" && field.files.length === 0) || 
            (field.type !== "file" && !field.value.trim())) {
          emptyFields.push(field.placeholder || field.name);
        }
      });

      if (emptyFields.length > 0) {
        Swal.fire({
          icon: "warning",
          title: "Form Belum Lengkap!",
          html: `<ul style="text-align:left;">${emptyFields.map(f => `<li>${f}</li>`).join("")}</ul>`,
          confirmButtonText: "Oke",
        });
      } else {
        form.submit(); // Submit kalau semua field lengkap
      }
    });
  }

  setupValidation('form'); // Terapkan ke form peserta

});


</script>


</html>

<?php require 'templates/footer.php'; ?>