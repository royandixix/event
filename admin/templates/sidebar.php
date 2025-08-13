<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Panel</title>
  
  <!-- Google Icons & Tailwind -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Animate.css & SweetAlert2 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  
  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50">

  <!-- Tombol Toggle Sidebar (Mobile) -->
  <button id="sidebarToggle"
    class="lg:hidden fixed bottom-6 right-6 z-50 w-14 h-14 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-blue-500 hover:scale-110 transition duration-300">
    <span class="material-icons">menu</span>
  </button>

  <!-- Tombol Logout (Kiri Bawah) -->
  <button id="logoutBtn" title="Logout"
    class="fixed bottom-6 left-6 z-50 w-14 h-14 bg-red-500 text-white rounded-full flex items-center justify-center shadow-xl hover:bg-red-600 hover:rotate-12 hover:scale-110 transition-all duration-300">
    <span class="material-icons">logout</span>
  </button>

  <!-- Overlay (Mobile) -->
  <div id="overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 hidden z-30"></div>

  <!-- Sidebar -->
  <aside id="sidebar"
    class="bg-white text-gray-800 w-64 fixed top-16 left-0 bottom-0 flex flex-col shadow-2xl transform -translate-x-full transition-transform duration-300 z-40 lg:translate-x-0">

    <!-- Menu -->
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
      <a href="index.php" class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition transform hover:translate-x-1 hover:shadow">
        <span class="material-icons text-blue-500 mr-3">dashboard</span> Dashboard
      </a>
      <a href="data_event.php" class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition transform hover:translate-x-1 hover:shadow">
        <span class="material-icons text-green-500 mr-3">event</span> Data Event
      </a>
      <a href="data_peserta.php" class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition transform hover:translate-x-1 hover:shadow">
        <span class="material-icons text-green-500 mr-3">people</span> Data Peserta
      </a>
      <a href="data_kelas.php" class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition transform hover:translate-x-1 hover:shadow">
        <span class="material-icons text-yellow-500 mr-3">category</span> Data Kelas
      </a>
      <a href="data_manajer.php" class="flex items-center p-3 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition transform hover:translate-x-1 hover:shadow">
        <span class="material-icons text-purple-500 mr-3">supervisor_account</span> Data Manajer
      </a>
    </nav>
  </aside>

  <!-- Header -->
  <header class="bg-white shadow-md fixed top-0 left-0 right-0 z-30 border-b border-gray-100">
    <div class="flex items-center justify-between px-4 lg:px-6 h-16">
      
      <!-- Judul -->
      <a href="index.php" class="flex items-center space-x-2">
        <span class="material-icons text-blue-500">admin_panel_settings</span>
        <span class="text-lg font-semibold text-gray-700">Admin Panel</span>
      </a>

      <!-- Profil -->
      <div class="relative">
        <button id="profileBtn" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-50 transition">
          <img src="https://ui-avatars.com/api/?name=Administrator&background=random&color=fff"
               alt="Profile" class="w-8 h-8 rounded-full shadow-md">
          <span class="hidden sm:block text-gray-700 font-medium">Administrator</span>
          <span class="material-icons text-gray-500">expand_more</span>
        </button>
      </div>
    </div>
  </header>

  <!-- Konten Utama -->
  <main class="ml-0 lg:ml-64 mt-16 p-4">
    <!-- Isi konten admin di sini -->
  </main>



</body>
</html>

  <!-- Script -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const sidebarToggle = document.getElementById("sidebarToggle");
      const sidebar = document.getElementById("sidebar");
      const overlay = document.getElementById("overlay");
      const logoutBtn = document.getElementById("logoutBtn");

      function toggleSidebar() {
        sidebar.classList.toggle("-translate-x-full");
        overlay.classList.toggle("hidden");
      }
      
      sidebarToggle.addEventListener("click", toggleSidebar);
      overlay.addEventListener("click", toggleSidebar);

      logoutBtn.addEventListener("click", function () {
        Swal.fire({
          title: 'Yakin mau keluar?',
          text: "Sesi Anda akan diakhiri.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#3b82f6',
          confirmButtonText: 'Ya, Logout',
          cancelButtonText: 'Batal',
          reverseButtons: true,
          background: '#fff',
          showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
          hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' },
          customClass: {
            popup: 'rounded-2xl shadow-2xl',
            title: 'text-lg font-semibold',
            confirmButton: 'px-5 py-2 rounded-full',
            cancelButton: 'px-5 py-2 rounded-full'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: 'Memproses...',
              html: '<div class="flex flex-col items-center"><div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mb-3"></div><span class="text-gray-600">Sedang logout</span></div>',
              showConfirmButton: false,
              allowOutsideClick: false,
              timer: 2000,
              background: '#fff',
              didOpen: () => {
                setTimeout(() => {
                  Swal.fire({
                    title: 'Logout Berhasil',
                    text: 'Sampai jumpa lagi 👋',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#fff',
                    customClass: { popup: 'rounded-2xl shadow-lg' }
                  }).then(() => {
                    window.location.href = "logout.html";
                  });
                }, 2000);
              }
            });
          }
        });
      });
    });
  </script>
</body>
</html>
