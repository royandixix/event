{/* <script defer> */}
document.addEventListener("DOMContentLoaded", function() {
    const tambahKelasBtn = document.getElementById('tambahKelas');
    const kelasWrapper = document.getElementById('kelas-wrapper');

    function generateKelasHTML() {
        return `
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
                        <p class="text-xs text-gray-500 mt-1">Contoh: Merah, Hitam, Biru</p>
                    </div>
                </div>
                <button type="button" onclick="hapusKelas(this)"
                    class="group absolute top-3 right-3 bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold 
                           rounded-full shadow-sm hover:shadow-md hover:bg-red-500 hover:text-white 
                           transition-all duration-300 flex items-center gap-1">
                    <span class="transition-transform group-hover:rotate-90 duration-300">✕</span>
                    <span class="hidden sm:inline">Hapus</span>
                </button>
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
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Minimal harus ada satu kelas.',
                confirmButtonColor: '#3085d6'
            });
        }
    };
});
// </script>