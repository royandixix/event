document.addEventListener("DOMContentLoaded", function() {
    const tambahKelasBtn = document.getElementById('tambahKelas');
    const kelasWrapper = document.getElementById('kelas-wrapper');

    function generateKelasHTML() {
        return `
            <div class="kelas-item bg-blue-50 p-6 rounded-xl space-y-4 relative border border-blue-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="kelas[]" placeholder="Kelas" required class="w-full p-3 border rounded-lg" />
                    <input type="text" name="warna_kendaraan[]" placeholder="Warna Kendaraan" required class="w-full p-3 border rounded-lg" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="tipe_kendaraan[]" placeholder="Tipe Kendaraan" required class="w-full p-3 border rounded-lg" />
                </div>
                <button type="button" onclick="hapusKelas(this)" class="absolute top-3 right-3 bg-red-100 px-3 py-1 text-xs rounded-full">Hapus</button>
            </div>
        `;
    }

    tambahKelasBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.innerHTML = generateKelasHTML();
        kelasWrapper.appendChild(div.firstElementChild);
    });

    window.hapusKelas = function(btn) {
        const kelasItems = document.querySelectorAll('.kelas-item');
        if (kelasItems.length > 1) {
            btn.closest('.kelas-item').remove();
        } else {
            Swal.fire({ icon: 'warning', title: 'Minimal 1 kelas diperlukan' });
        }
    };
});
