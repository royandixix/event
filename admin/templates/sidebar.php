<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? "My Dashboard"; ?></title>
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/fonts/icomoon/style.css">

    <link rel="stylesheet" href="css/css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="css/css/style.css">
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

        /* ===== NAV LINK ===== */
        .sidebar .nav-link {
            color: #0d6efd;
            border-radius: 0.7rem;
            padding: 0.65rem 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-weight: 500;
            position: relative;
        }
        .sidebar .nav-link i {
            transition: transform 0.2s, color 0.2s;
            color: #0d6efd;
        }
        .sidebar .nav-link:hover {
            background: linear-gradient(90deg, #4dabf7, #6c63ff);
            color: #fff;
            box-shadow: inset 0 0 6px rgba(13, 110, 253, 0.2);
        }
        .sidebar .nav-link:hover i {
            transform: scale(1.2);
            color: #fff;
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

        /* ===== SIDEBAR TOGGLE ===== */
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

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 250px;
            padding: 1.5rem;
            transition: margin-left 0.3s;
        }
        @media (max-width: 991px) {
            .main-content { margin-left: 0; }
        }

        /* ===== BORDER SIDEBAR KEREN ===== */
        .sidebar.border-end {
            border-right: 6px solid;
            border-image: linear-gradient(to bottom, #0d6efd, #4dabf7) 1;
            box-shadow: 2px 0 12px rgba(13, 110, 253, 0.3);
            transition: border 0.3s, box-shadow 0.3s;
        }
        .sidebar.border-end:hover {
            border-image: linear-gradient(to bottom, #4dabf7, #6c63ff) 1;
            box-shadow: 4px 0 16px rgba(108, 99, 255, 0.4);
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
        
        <a href="data_manajer.php" class="nav-link"><i data-lucide="user-check" class="me-2"></i> Data Manajer</a>
    </aside>

    <!-- ===== SIDEBAR OVERLAY ===== -->
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

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
