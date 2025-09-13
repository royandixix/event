<?php 
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  require_once __DIR__ . '/../../function/config.php';
?>
<!-- AOS (Animation on Scroll) -->
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<div class="bg-gradient-to-r from-pink-200 via-white to-white">
  <!-- Search Section -->
  <section class="max-w-5xl px-6 py-24 mx-auto text-left" data-aos="fade-up">
    <h1 class="text-2xl sm:text-3xl md:text-4xl text-gray-900 leading-snug">
      Informasi & <span class="text-pink-700">Pendaftaran Event</span><br />
      <span class="underline decoration-pink-500">Drag Bike</span> Nasional & Regional
    </h1>

    <p class="mt-6 text-gray-600 max-w-2xl text-sm sm:text-base text-justify leading-relaxed">
      Kumpulan event resmi Drag Bike dari seluruh Indonesia, mulai dari seri kejuaraan nasional hingga event lokal. 
      Temukan jadwal lomba, kategori kelas, biaya pendaftaran, hingga link daftar online. 
      Jadilah bagian dari aksi balap lurus yang memacu adrenalin!
    </p>

    <!-- Search Box -->
    <!-- 
    <div class="mt-8 flex items-center gap-3">
      <div class="relative w-full max-w-md">
        <input 
          type="text" 
          id="searchInput"
          placeholder="Cari event, lokasi, atau bulan..."
          class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-pink-500 focus:outline-none"
        />
        <button 
          id="clearBtn"
          class="absolute right-2 top-2 text-gray-400 hover:text-gray-600 hidden"
          aria-label="Clear"
        >
          ✕
        </button>
        <ul 
          id="suggestions" 
          class="absolute z-20 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden"
        ></ul>
      </div>
      <button 
        id="searchButton"
        class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white font-medium rounded-lg"
      >
        Cari
      </button>
    </div> 
    -->
  </section>
</div>

<!-- Script untuk AOS, Lucide, Search -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    AOS.init({ once: true });
    lucide.createIcons();

    const input = document.getElementById("searchInput");
    const button = document.getElementById("searchButton");
    const clearBtn = document.getElementById("clearBtn");
    const suggestions = document.getElementById("suggestions");

    const dummySuggestions = ["Sentul", "DB21", "Agustus", "Jakarta", "DB Drag Series", "Sumatera"];

    input?.addEventListener("input", () => {
      const val = input.value.trim();
      clearBtn.style.display = val.length > 0 ? "block" : "none";

      suggestions.innerHTML = "";
      if (val.length > 1) {
        const filtered = dummySuggestions.filter(item =>
          item.toLowerCase().includes(val.toLowerCase())
        );

        if (filtered.length) {
          filtered.forEach(s => {
            const li = document.createElement("li");
            li.textContent = s;
            li.className = "px-4 py-2 hover:bg-pink-50 cursor-pointer";
            li.addEventListener("click", () => {
              input.value = s;
              suggestions.classList.add("hidden");
            });
            suggestions.appendChild(li);
          });
          suggestions.classList.remove("hidden");
        } else {
          suggestions.classList.add("hidden");
        }
      } else {
        suggestions.classList.add("hidden");
      }
    });

    input?.addEventListener("keydown", e => {
      if (e.key === "Enter") {
        button.click();
      }
    });

    clearBtn?.addEventListener("click", () => {
      input.value = "";
      suggestions.classList.add("hidden");
      clearBtn.style.display = "none";
      input.focus();
    });

    button?.addEventListener("click", () => {
      const keyword = input.value.trim();
      if (keyword) {
        window.location.href = `/event?search=${encodeURIComponent(keyword)}`;
      } else {
        alert("Masukkan kata kunci pencarian.");
      }
    });

    document.addEventListener("click", e => {
      if (!e.target.closest("#searchInput") && !e.target.closest("#suggestions")) {
        suggestions.classList.add("hidden");
      }
    });
  });
</script>
