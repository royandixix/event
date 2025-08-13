<?php
require '../function/config.php';
require 'templates/navbar.php';
require 'templates/header.php';
require 'templates/sub.php';

// Ambil semua event dari database
$sql = "SELECT * FROM event ORDER BY created_at DESC";
$result = mysqli_query($db, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Event</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
  </style>
</head>
<body class="fade-in-up">
<div class="bg-gradient-to-r from-blue-200 via-white to-white py-6">
  <div class="max-w-6xl mx-auto grid sm:grid-cols-1 md:grid-cols-2 gap-6" id="event-container">

    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="event-card opacity-0 translate-y-8 transition-all duration-700 ease-out">
              <a href="detail_event.php?id=<?= $row['id_event'] ?>" class="flex items-start gap-4 bg-white p-4 rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-transform duration-300 group no-underline">
                
                <!-- Poster -->
                <img src="../uploads/poster/<?= htmlspecialchars($row['poster_path']) ?>" 
                     alt="Poster Event" 
                     class="w-24 h-28 rounded-lg object-cover border">

                <!-- Konten Event -->
                <div class="flex-1">
                  <div class="flex items-center text-sm text-gray-600 gap-2 mb-1">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                    <span><?= $row['tanggal_mulai'] ?> s.d <?= $row['tanggal_selesai'] ?></span>
                  </div>
                  <h2 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition duration-200">
                    <?= htmlspecialchars($row['judul_event']) ?>
                  </h2>
                  <div class="flex items-center text-sm text-gray-600 mt-1 gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-blue-500"></i>
                    <span><?= htmlspecialchars($row['lokasi_event']) ?></span>
                  </div>
                  <p class="text-gray-600 text-sm mt-2">
                    <?= htmlspecialchars(substr($row['deskripsi_event'], 0, 100)) ?>...
                  </p>
                </div>
              </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="col-span-2 text-center text-gray-500">Belum ada event.</p>
    <?php endif; ?>

  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.remove('opacity-0', 'translate-y-8');
          entry.target.classList.add('opacity-100', 'translate-y-0');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.event-card').forEach(card => {
      observer.observe(card);
    });
  });
</script>
</body>
</html>
<?php 
require 'templates/footer.php';
?>
