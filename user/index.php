<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header.php';
require 'templates/section.php';
require 'templates/sub.php';

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Form Pendaftaran</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up {
      animation: fadeInUp 0.6s ease-out forwards;
    }
    .kelas-enter {
      animation: fadeInUp 0.4s ease-out forwards;
    }
    .kelas-exit {
      animation: fadeInUp 0.3s ease-in reverse forwards;
    }
  </style>

  <script defer>
    document.addEventListener("DOMContentLoaded", function () {
      const tambahKelasBtn = document.getElementById('tambahKelas');
      const kelasWrapper = document.getElementById('kelas-wrapper');

      function generateKelasHTML() {
        return `
          <div class="kelas-item kelas-enter bg-blue-50 p-4 rounded-lg space-y-4 relative mt-4 shadow-sm transition hover:shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <input type="text" name="kelas[]" placeholder="Kelas" class="w-full p-2 border rounded-md" required />
              <input type="text" name="warna_kendaraan[]" placeholder="Warna Kendaraan" class="w-full p-2 border rounded-md" required />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <input type="text" name="tipe_kendaraan[]" placeholder="Tipe Kendaraan" class="w-full p-2 border rounded-md" required />
                <p class="text-xs text-gray-500 mt-1">Contoh: Fortuner, Pajero, Mio, Jupiter</p>
              </div>
              <div>
                <p class="text-xs text-gray-500 mt-1">Contoh: Merah, Hitam, Biru</p>
              </div>
            </div>
            <button type="button" onclick="hapusKelas(this)" class="absolute top-2 right-2 bg-red-100 hover:bg-red-200 text-red-700 px-2 py-1 text-xs rounded transition">
              Hapus Kelas
            </button>
          </div>
        `;
      }

      tambahKelasBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.innerHTML = generateKelasHTML();
        kelasWrapper.appendChild(div.firstElementChild);
      });

      window.hapusKelas = function (btn) {
        const kelasItems = document.querySelectorAll('.kelas-item');
        if (kelasItems.length > 1) {
          const kelas = btn.closest('.kelas-item');
          if (kelas) {
            kelas.classList.add('kelas-exit');
            setTimeout(() => kelas.remove(), 300);
          }
        } else {
          alert("Minimal harus ada satu kelas.");
        }
      };
    });
  </script>
</head>
<body class="fade-in-up">
  
  
</body>
</html>
<?php 
require 'templates/footer.php';
?>
