<?php require 'templates/header.php'; ?>
<?php require 'templates/sidebar.php'; ?>

<!-- Konten Utama -->
<main class="p-6 transition-all duration-300 lg:ml-64 mt-16 max-w-7xl mx-auto">

  <!-- Judul Halaman -->
  <h1 class="text-3xl font-bold mb-8 text-gray-800">
    Dashboard Admin
  </h1>

  <!-- Statistik Card -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    <!-- Card Event -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition p-5 border-l-4 border-blue-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500">Total Event</p>
          <h2 class="text-2xl font-bold text-gray-800">12</h2>
        </div>
        <span class="material-icons text-blue-500 text-4xl">event</span>
      </div>
    </div>

    <!-- Card Peserta -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition p-5 border-l-4 border-green-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500">Total Peserta</p>
          <h2 class="text-2xl font-bold text-gray-800">245</h2>
        </div>
        <span class="material-icons text-green-500 text-4xl">people</span>
      </div>
    </div>

    <!-- Card Kelas -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition p-5 border-l-4 border-yellow-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500">Total Kelas</p>
          <h2 class="text-2xl font-bold text-gray-800">8</h2>
        </div>
        <span class="material-icons text-yellow-500 text-4xl">category</span>
      </div>
    </div>

    <!-- Card Manajer -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition p-5 border-l-4 border-purple-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500">Total Manajer</p>
          <h2 class="text-2xl font-bold text-gray-800">15</h2>
        </div>
        <span class="material-icons text-purple-500 text-4xl">supervisor_account</span>
      </div>
    </div>

  </div>

  <!-- Tabel Statistik -->
  <div class="mt-12 bg-white rounded-xl shadow-md hover:shadow-xl transition p-6">
    <h2 class="text-xl font-semibold text-gray-700 mb-6">
      Statistik Peserta per Bulan
    </h2>
    
    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-blue-600 text-white">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold">No</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Nama Depan</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Nama Belakang</th>
            <th class="px-6 py-3 text-left text-sm font-semibold">Media Sosial</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr class="bg-gray-50 hover:bg-gray-100 transition">
            <td class="px-6 py-3">1</td>
            <td class="px-6 py-3">John</td>
            <td class="px-6 py-3">Doe</td>
            <td class="px-6 py-3">@twitter</td>
          </tr>
          <tr class="hover:bg-gray-100 transition">
            <td class="px-6 py-3">2</td>
            <td class="px-6 py-3">Jane</td>
            <td class="px-6 py-3">Smith</td>
            <td class="px-6 py-3">@facebook</td>
          </tr>
          <tr class="bg-gray-50 hover:bg-gray-100 transition">
            <td class="px-6 py-3">3</td>
            <td class="px-6 py-3">Alice</td>
            <td class="px-6 py-3">Williams</td>
            <td class="px-6 py-3">@instagram</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</main>
