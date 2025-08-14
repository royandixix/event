<?php
require 'templates/header.php';
require 'templates/sidebar.php';
require '../function/config.php';

// Fungsi generate kode full event otomatis
function generateKodeFull($db) {
    $prefix = "EVT";
    $tanggal = date('Ymd'); // format YYYYMMDD

    $stmt = $db->prepare("SELECT COUNT(*) as total FROM event WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $count = $result['total'] + 1;

    return $prefix . $tanggal . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
}

if (isset($_POST['simpan'])) {
    $judul_event     = $_POST['judul_event'];
    $deskripsi_event = $_POST['deskripsi_event'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $lokasi_event    = $_POST['lokasi_event'];
    $harga_event     = $_POST['harga_event'];

    // Data bank dari form
    $bank_tujuan     = $_POST['bank_tujuan'];
    $no_rekening     = $_POST['no_rekening'];
    $nama_rekening   = $_POST['nama_pemilik'];

    $kode_full = generateKodeFull($db);

    // Upload poster event
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
    $stmt = $db->prepare("INSERT INTO event (judul_event, deskripsi_event, tanggal_mulai, tanggal_selesai, lokasi_event, poster_path, harga_event, kode_full) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $judul_event, $deskripsi_event, $tanggal_mulai, $tanggal_selesai, $lokasi_event, $nama_file, $harga_event, $kode_full);

    if ($stmt->execute()) {
        $id_event = $stmt->insert_id;

        // Simpan paddock slots
        if (!empty($_POST['nama_slot']) && is_array($_POST['nama_slot'])) {
            $slots = $_POST['nama_slot'];
            $stmt_slot = $db->prepare("INSERT INTO paddock_slot (id_event, nomor_slot) VALUES (?, ?)");
            foreach ($slots as $slot) {
                $stmt_slot->bind_param("is", $id_event, $slot);
                $stmtgit_slot->execute();
            }
        }

        // Buat Invoice
        $no_invoice = "SSM/INV-PADDOCK/" . date("m/Y") . "/" . str_pad($id_event, 6, "0", STR_PAD_LEFT);
        $kode_unik = rand(100, 999);
        $total_transfer = $harga_event + $kode_unik;

        // Upload logo bank
        $gambar_bank = $_FILES['gambar_bank']['name'];
        $tmp_gambar_bank = $_FILES['gambar_bank']['tmp_name'];
        if ($gambar_bank) {
            $folder_bank = "../uploads/bank/";
            if (!file_exists($folder_bank)) mkdir($folder_bank, 0777, true);
            $nama_file_bank = time() . "_" . basename($gambar_bank);
            move_uploaded_file($tmp_gambar_bank, $folder_bank . $nama_file_bank);
        } else {
            $nama_file_bank = null;
        }

        // Simpan invoice
        $stmt_invoice = $db->prepare("INSERT INTO invoice 
            (nomor_invoice, id_event, total_harga, kode_unik, total_transfer, bank_tujuan, no_rekening, nama_pemilik_rekening, gambar_bank, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt_invoice->bind_param("siiisssss", $no_invoice, $id_event, $harga_event, $kode_unik, $total_transfer, $bank_tujuan, $no_rekening, $nama_rekening, $nama_file_bank);
        $stmt_invoice->execute();

        // SweetAlert sukses
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Event & Invoice Dibuat',
                html: `
                    <p>Event berhasil dibuat dengan kode <b>{$kode_full}</b></p>
                    <hr>
                    <p><b>Nomor Invoice:</b> {$no_invoice}</p>
                    <p><b>Total yang harus dibayar:</b> Rp {$total_transfer}</p>
                    <p>Mohon transfer ke:</p>
                    <p><b>{$bank_tujuan} - {$no_rekening}</b></p>
                    <p>a/n {$nama_rekening}</p>
                    " . (!empty($nama_file_bank) ? "<img src='../uploads/bank/{$nama_file_bank}' alt='Logo Bank' style='max-width:200px; margin-top:10px;'>" : "") . "
                    <hr>
                    <small>Pastikan nominal transfer sesuai termasuk 3 digit terakhir.</small>
                `,
                confirmButtonText: 'OK'
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
    <div class="px-6 py-6 bg-blue-500 rounded-t-2xl mb-6">
        <h1 class="text-3xl font-bold text-white">Tambah Event & Paddock</h1>
        <p class="text-blue-200 mt-1">Isi form berikut untuk menambahkan event dan paddock slot.</p>
    </div>

    <div class="bg-gray-50 border p-6 rounded-2xl shadow-sm">
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label class="block mb-2 font-semibold">Judul Event</label>
            <input type="text" name="judul_event" placeholder="Masukkan judul event" required class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block mb-2 font-semibold">Deskripsi Event</label>
            <textarea name="deskripsi_event" rows="5" placeholder="Tuliskan deskripsi event secara lengkap" required class="w-full border p-2 rounded"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <input type="date" name="tanggal_mulai" required class="border p-2 rounded" title="Tanggal mulai event">
            <input type="date" name="tanggal_selesai" required class="border p-2 rounded" title="Tanggal selesai event">
        </div>

        <div>
            <input type="text" name="lokasi_event" placeholder="Masukkan lokasi event" required class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block mb-2 font-semibold">Upload Poster Event</label>
            <input type="file" name="poster" accept="image/*" class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block mb-2 font-semibold">Harga Event</label>
            <input type="text" id="harga_event" name="harga_event" placeholder="Rp 0" required class="w-full border p-2 rounded">
        </div>

        <hr class="my-4">

        <div>
            <label class="block mb-2 font-semibold">Pilih Bank Tujuan</label>
            <select name="bank_tujuan" required class="w-full border p-2 rounded">
                <option value="">-- Pilih Bank --</option>
                <option value="BCA">BCA</option>
                <option value="Mandiri">Mandiri</option>
                <option value="BRI">BRI</option>
                <option value="BNI">BNI</option>
            </select>
        </div>

        <div>
            <label class="block mb-2 font-semibold">Nomor Rekening</label>
            <input type="text" name="no_rekening" required placeholder="Contoh: 1234567890" class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block mb-2 font-semibold">Nama Pemilik Rekening</label>
            <input type="text" name="nama_pemilik" required placeholder="Nama sesuai buku tabungan" class="w-full border p-2 rounded">
        </div>

        <div>
            <label class="block mb-2 font-semibold">Upload Logo Bank</label>
            <input type="file" name="gambar_bank" accept="image/*" class="w-full border p-2 rounded">
        </div>

        <div id="paddock-container">
            <label class="block mb-2 font-semibold">Paddock Slots</label>
            <div class="flex gap-2 mb-2 paddock-row">
                <input type="text" name="nama_slot[]" placeholder="Nomor Slot (misal A1)" required class="border p-2 rounded w-full">
                <button type="button" onclick="removeRow(this)" class="bg-red-500 text-white px-2 rounded">-</button>
            </div>
        </div>
        <button type="button" onclick="addRow()" class="bg-green-500 text-white px-4 py-1 rounded">Tambah Slot +</button>

        <div class="flex justify-end gap-4 mt-4">
            <button type="submit" name="simpan" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Event</button>
            <a href="data_event.php" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
        </div>
    </form>
</div>

<script>
// Tambah/Hapus row paddock slot
function addRow() {
    const container = document.getElementById('paddock-container');
    const div = document.createElement('div');
    div.classList.add('flex', 'gap-2', 'mb-2', 'paddock-row');
    div.innerHTML = `
        <input type="text" name="nama_slot[]" placeholder="Nomor Slot (misal A1)" required class="border p-2 rounded w-full">
        <button type="button" onclick="removeRow(this)" class="bg-red-500 text-white px-2 rounded">-</button>
    `;
    container.appendChild(div);
}
function removeRow(button) {
    button.parentElement.remove();
}

// Format input harga ke Rupiah
const hargaInput = document.getElementById('harga_event');
hargaInput.addEventListener('keyup', function(e) {
    this.value = formatRupiah(this.value, 'Rp ');
});

function formatRupiah(angka, prefix){
    let number_string = angka.replace(/[^,\d]/g, '').toString(),
        split   	 = number_string.split(','),
        sisa     	 = split[0].length % 3,
        rupiah     	 = split[0].substr(0, sisa),
        ribuan    	 = split[0].substr(sisa).match(/\d{3}/gi);
        
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix + rupiah;
}
</script>

</main>

<script>
function addRow() {
    const container = document.getElementById('paddock-container');
    const div = document.createElement('div');
    div.classList.add('flex', 'gap-2', 'mb-2', 'paddock-row');
    div.innerHTML = `
        <input type="text" name="nama_slot[]" placeholder="Nomor Slot (misal A1)" required class="border p-2 rounded">
        <button type="button" onclick="removeRow(this)" class="bg-red-500 text-white px-2 rounded">-</button>
    `;
    container.appendChild(div);
}
function removeRow(button) {
    button.parentElement.remove();
}
</script>

<?php require 'templates/footer.php'; ?>
