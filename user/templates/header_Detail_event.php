<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../function/config.php';
?>


<!-- Hero Section - Pink Blur Background -->
<div class="relative min-h-screen w-full font-sans overflow-hidden">
  <!-- Animated Pink Background -->
  <div class="absolute inset-0 overflow-hidden">
    <div class="absolute inset-0 w-full h-full animate-bg-slide">
      <img src="./img/gambar.jpeg" alt="Background" 
           class="w-full h-full object-cover filter blur-sm scale-110" />
    </div>
    <!-- Pink Overlay -->
    <div class="absolute inset-0 bg-pink-500/60 mix-blend-multiply"></div>
  </div>

  <!-- Content -->
  <section class="relative z-10 min-h-screen flex flex-col lg:flex-row items-center justify-between 
    px-4 sm:px-6 md:px-10 lg:px-20 
    pt-32 pb-16 sm:pt-40 sm:pb-24 text-white animate-fade-in">

    <!-- Text -->
    <div class="max-w-2xl space-y-6">
      <h1 class="text-[clamp(1.6rem,4vw,3rem)] leading-tight drop-shadow-xl">
        Detail Acara<br />
        <span class="text-pink-100">
          Event Drag Bike <?= htmlspecialchars($nama_event ?? '') ?>
        </span>
      </h1>

      <p class="text-[clamp(1rem,2.2vw,1.2rem)] text-pink-100 leading-relaxed drop-shadow">
        Berikut adalah informasi lengkap untuk event ini — mulai dari jadwal, lokasi, kategori kelas, 
        hingga biaya pendaftaran. Pastikan Anda membaca seluruh detail sebelum mendaftar.
      </p>

      <a href="#daftar" 
         class="inline-block mt-4 px-6 py-3 rounded-lg bg-pink-600/80 hover:bg-pink-700/90 
         border border-pink-200/40 backdrop-blur-md text-white text-sm transition duration-300">
        Lanjut ke Form Pendaftaran
      </a>
    </div>

    <!-- Image Card -->
    <div class="mt-12 lg:mt-0 lg:ml-10 w-full max-w-sm rounded-2xl overflow-hidden shadow-lg 
      bg-pink-600/20 border border-pink-200/40 backdrop-blur-md 
      hover:shadow-lg hover:-translate-y-1 transition duration-300">
      <img src="./img/gambar.jpeg" alt="Event Drag Bike" class="w-full h-auto object-cover rounded-t-2xl" />
      <div class="p-6 text-white">
        <h3 class="text-lg mb-2">Info Event Lengkap</h3>
        <p class="text-sm text-pink-100">
          Mulai dari kelas yang dilombakan, biaya pendaftaran, hingga link resmi pendaftaran online.
        </p>
      </div>
    </div>
  </section>
</div>

<!-- Animations -->
<style>
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
  }

  @keyframes bg-slide {
    0% { transform: translateX(0); }
    100% { transform: translateX(-15%); }
  }
  .animate-bg-slide {
    animation: bg-slide 20s linear infinite;
  }

  @media (max-width: 768px) {
    .animate-bg-slide img {
      scale: 1.1;
      object-position: center;
    }
  }
</style>
