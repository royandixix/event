<!-- templates/layout.php -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? "My Dashboard"; ?></title>
  <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    /* ===== SIDEBAR ===== */
    .sidebar {
      height: 100vh;
      font-size: 0.95rem;
      background: #fff;
      box-shadow: 2px 0 12px rgba(0,0,0,0.06);
      padding-top: 1rem;
      transition: transform 0.3s ease;
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      z-index: 1050;
      overflow-y: auto;
    }
    .sidebar.show { transform: translateX(0); }

    @media (max-width: 991px) {
      .sidebar { transform: translateX(-100%); }
    }

    .sidebar-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 1040;
      display: none;
    }
    .sidebar-overlay.show { display: block; }

    .sidebar .brand {
      font-weight: 700;
      font-size: 1.3rem;
      color: #343a40;
      display: flex;
      align-items: center;
      margin-bottom: 2rem;
      padding: 0.5rem 0.75rem;
    }
    .sidebar .brand i {
      background: linear-gradient(135deg, #6c63ff, #4dabf7);
      color: #fff;
      border-radius: 0.5rem;
      padding: 6px;
      margin-right: 0.6rem;
      box-shadow: 0 3px 6px rgba(108,99,255,0.3);
    }

    .sidebar .menu-title {
      font-size: 0.75rem;
      font-weight: 600;
      color: #868e96;
      text-transform: uppercase;
      margin: 1rem 0 0.5rem;
      padding-left: 0.75rem;
      letter-spacing: 0.5px;
    }

    .sidebar .nav-link {
      color: #495057;
      border-radius: 0.7rem;
      padding: 0.65rem 1rem;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      font-weight: 500;
      position: relative;
    }
    .sidebar .nav-link i {
      transition: transform 0.2s, color 0.2s;
    }
    .sidebar .nav-link:hover {
      background: #f8f9fa;
      color: #6c63ff;
      box-shadow: inset 0 0 6px rgba(108,99,255,0.15);
    }
    .sidebar .nav-link:hover i {
      transform: scale(1.1);
      color: #6c63ff;
    }

    .sidebar .nav-link.active {
      background: linear-gradient(90deg, #6c63ff, #4dabf7);
      color: #fff !important;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(108,99,255,0.3);
    }
    .sidebar .nav-link.active::before {
      content: "";
      position: absolute;
      left: 0;
      top: 20%;
      bottom: 20%;
      width: 4px;
      border-radius: 2px;
      background: #ffc107;
    }

    .sidebar .nav-link.text-danger {
      font-weight: 600;
    }
    .sidebar .nav-link.text-danger:hover {
      background: #fff5f5;
      color: #e03131 !important;
      box-shadow: inset 0 0 6px rgba(224,49,49,0.15);
    }

    /* ===== HEADER ===== */
    header {
      background: linear-gradient(90deg, #0d6efd, #0a58ca);
    }
    .nav-icon {
      transition: 0.2s;
      cursor: pointer;
    }
    .nav-icon:hover {
      transform: scale(1.15);
      color: #ffc107 !important;
    }

    .sidebar-toggle {
      display: none;
      background: none;
      border: none;
      color: #fff;
      font-size: 1.5rem;
    }
    @media (max-width: 991px) {
      .sidebar-toggle { display: inline-block; }
    }

    .main-content {
      margin-left: 250px;
      padding: 1.5rem;
      transition: margin-left 0.3s;
    }
    @media (max-width: 991px) {
      .main-content { margin-left: 0; }
    }
  </style>
</head>
<body>

  <!-- ===== SIDEBAR ===== -->
  <aside id="sidebar" class="sidebar border-end p-3">
    <div class="brand"><i data-lucide="layout-dashboard"></i> Admin Panel</div>

    <a href="index.php" class="nav-link"><i data-lucide="home" class="me-2"></i> Dashboard</a>
    <a href="data_event.php" class="nav-link"><i data-lucide="calendar" class="me-2"></i> Data Event</a>
    <a href="data_peserta.php" class="nav-link"><i data-lucide="users" class="me-2"></i> Data Peserta</a>
    <a href="data_kelas.php" class="nav-link"><i data-lucide="layers" class="me-2"></i> Data Kelas</a>
    <a href="data_manajer.php" class="nav-link"><i data-lucide="user-check" class="me-2"></i> Data Manajer</a>
  </aside>

  <!-- Overlay -->
  <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="main-content">
    <h2>Welcome 🎉</h2>
    <p>Ini adalah konten utama dashboard kamu.</p>
  </main>

  <!-- ===== SCRIPTS ===== -->
  <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    lucide.createIcons();

    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("show");
      document.getElementById("sidebar-overlay").classList.toggle("show");
    }
  </script>
</body>
</html>
