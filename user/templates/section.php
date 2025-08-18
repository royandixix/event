<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../function/config.php';
?>

<section class="bg-gradient-to-r from-blue-200 via-white to-white pt-24 pb-16 px-4 lg:px-16">
  <div class="max-w-7xl mx-auto flex flex-col-reverse lg:flex-row gap-10 items-start">

    <!-- Grid Card -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full lg:max-w-2xl">

      <!-- Card 1 -->
      <div class="card group bg-white p-6 rounded-2xl shadow-lg cursor-pointer relative overflow-hidden">
        <div class="mb-4">
          <svg class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M18.364 5.636a9 9 0 11-12.728 0M15 11a3 3 0 11-6 0" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2">Sentul Drag Mobil</h3>
        <p class="text-sm text-gray-500">
          Kejuaraan Drag Race Nasional Seri 2 — Sentul International Circuit. Aksi kecepatan tinggi mobil-mobil balap terbaik!
        </p>
        <div class="absolute top-3 right-3 bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded shadow-lg">
          Siap Gaspol!
        </div>
      </div>

      <!-- Card 2 -->
      <div class="card group bg-white p-6 rounded-2xl shadow-lg cursor-pointer relative overflow-hidden">
        <div class="mb-4">
          <svg class="h-12 w-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2">Takalar Car Drag Cup</h3>
        <p class="text-sm text-gray-500">
          Ajang Drag Mobil Kabupaten Takalar — pertarungan ketat kelas 2000cc hingga unlimited.
        </p>
        <div class="absolute top-3 right-3 bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded shadow-lg">
          Gaskeun!
        </div>
      </div>

      <!-- Card 3 -->
      <div class="card group bg-white p-6 rounded-2xl shadow-lg cursor-pointer relative overflow-hidden">
        <div class="mb-4">
          <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12h6m2 0a8 8 0 11-16 0 8 8 0 0116 0z" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2">Bolsel Drag Car</h3>
        <p class="text-sm text-gray-500">
          Kejuaraan Balap Drag Mobil — trek lurus penuh adrenalin, 17 April – 8 Mei 2025.
        </p>
        <div class="absolute top-3 right-3 bg-green-100 text-green-800 text-sm px-3 py-1 rounded shadow-lg">
          ⚡ Ayo Daftar
        </div>
      </div>

      <!-- Card 4 -->
      <div class="card group bg-white p-6 rounded-2xl shadow-lg cursor-pointer relative overflow-hidden">
        <div class="mb-4">
          <svg class="h-12 w-12 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2">Bracket Mobil 8–10 Detik</h3>
        <p class="text-sm text-gray-500">
          Kategori mobil sedan, hatchback, dan sport — Kecepatan optimal di 8–10 detik.
        </p>
        <div class="absolute top-3 right-3 bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded shadow-lg">
          Coba Tantangan
        </div>
      </div>

    </div>

    <!-- Horizontal Card Section -->
    <div class="w-full px-4 sm:px-6 md:px-8 lg:px-16 mt-16 flex justify-center">
      <div class="horizontal-card group grid grid-cols-1 lg:grid-cols-2 bg-white rounded-3xl overflow-hidden max-w-5xl w-full shadow-xl ring-1 ring-gray-200">

        <!-- Image Section -->
        <div class="relative">
          <img src="./img/gambar.jpeg" alt="BRIDE Drag Race Mobil"
            class="w-full h-full object-cover aspect-video lg:aspect-auto" />
          <!-- Overlay Badge -->
          <span class="absolute top-4 left-4 bg-blue-600 text-white text-xs font-semibold uppercase px-3 py-1 rounded-full shadow-lg tracking-wider">
            Drag Race Mobil
          </span>
        </div>

        <!-- Content Section -->
        <div class="p-6 sm:p-8 space-y-4 flex flex-col justify-center">
          <!-- Tag -->
          <div class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold uppercase px-3 py-1 rounded-full shadow-sm tracking-wide">
            Event Nasional
          </div>

          <!-- Title -->
          <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">
            BRIDE SENTUL NATIONAL DRAG RACE MOBIL 2025 – Round 2
          </h2>

          <!-- Info List -->
          <ul class="text-sm text-gray-600 space-y-1">
            <li>
              <i data-lucide="calendar" class="inline w-4 h-4 text-blue-500 mr-2"></i>
              5–6 Juli 2025
            </li>
            <li>
              <i data-lucide="map-pin" class="inline w-4 h-4 text-blue-500 mr-2"></i>
              Sentul International Circuit
            </li>
          </ul>

          <!-- Description -->
          <p class="text-gray-500 text-sm leading-relaxed">
            Saksikan putaran kedua kejuaraan drag race mobil nasional, dengan lintasan lurus penuh tantangan. Hadirkan pembalap-pembalap mobil tercepat dari seluruh Indonesia!
          </p>

          <!-- CTA Button -->
          <a href="event.php"
            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow-md w-max hover:bg-blue-700 transition">
            Lihat Detail Event
            <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
