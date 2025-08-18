<!-- ===== HEADER ===== -->
<style>
  header.navbar {
    background-color: #343a40; /* solid gelap, profesional */
    padding: 1rem 1.5rem; /* header lebih tinggi */
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  header .navbar-brand {
    font-size: 1.35rem;
    font-weight: 600;
    letter-spacing: 0.5px;
  }
  header .nav-icon {
    font-size: 1.2rem;
    cursor: pointer;
    transition: transform 0.2s;
  }
  header .nav-icon:hover {
    transform: scale(1.15);
  }
  header .dropdown-toggle {
    font-size: 1rem;
    font-weight: 500;
    color: #f8f9fa;
  }
  header .dropdown-menu {
    min-width: 10rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
</style>

<header class="navbar navbar-expand-lg navbar-dark px-3">
  <!-- Tombol toggle sidebar (muncul hanya di mobile) -->
  <button class="sidebar-toggle me-3 btn btn-link text-white d-lg-none" onclick="toggleSidebar()">
    <i data-lucide="menu"></i>
  </button>

  <!-- Judul halaman -->
  <span class="navbar-brand mb-0 h1 text-white">
    <?= $title ?? "My Dashboard"; ?>
  </span>

  <div class="ms-auto d-flex align-items-center">
    <!-- Notifikasi -->
    <i data-lucide="bell" class="nav-icon text-white me-3"></i>

    <!-- User -->
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i data-lucide="user" class="me-2"></i> Admin
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="#"><i data-lucide="settings" class="me-2"></i>Pengaturan</a></li>
        <li><a class="dropdown-item text-danger" href="#"><i data-lucide="log-out" class="me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</header>

<!-- Lucide Icon Loader -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
</script>
