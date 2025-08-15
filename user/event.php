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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    @keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body class="bg-gradient-to-r from-blue-200 via-white to-white min-h-screen">

  <div class="py-12 bg-gradient-to-r from-blue-200 via-white to-white">
    <div class="max-w-7xl mx-auto px-4 grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="event-container">

      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <?php
          $poster = !empty($row['poster_path']) && file_exists("../uploads/poster/" . $row['poster_path'])
            ? "../uploads/poster/" . htmlspecialchars(basename($row['poster_path']))
            : "https://via.placeholder.com/400x500?text=No+Image";

          $harga = isset($row['harga_event']) && $row['harga_event'] > 0
            ? "Rp " . number_format($row['harga_event'], 0, ',', '.')
            : "Gratis";
          ?>
          <div class="event-card opacity-0 translate-y-8 transition-all duration-700 ease-out">
            <a href="detail_event.php?id=<?= $row['id_event'] ?>"
              class="block rounded-2xl overflow-hidden shadow-lg border border-gray-200 
         hover:shadow-xl hover:-translate-y-2 transform transition duration-300 group
         bg-white/80 backdrop-blur-sm border border-white/20">

              <!-- Poster -->
              <div class="relative overflow-hidden h-64">
                <img src="<?= $poster ?>" alt="Poster Event"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                <!-- Badge Harga -->
                <span class="absolute top-3 right-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg">
                  <?= $harga ?>
                </span>
              </div>

              <!-- Konten Event -->
              <div class="p-5 space-y-3">
                <!-- Tanggal -->
                <div class="flex items-center text-xs text-gray-500 gap-2">
                  <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                  <span><?= htmlspecialchars($row['tanggal_mulai']) ?> - <?= htmlspecialchars($row['tanggal_selesai']) ?></span>
                </div>

                <!-- Judul -->
                <h2 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors duration-200">
                  <?= htmlspecialchars($row['judul_event']) ?>
                </h2>

                <!-- Lokasi -->
                <div class="flex items-center text-sm text-gray-500 gap-2">
                  <i data-lucide="map-pin" class="w-4 h-4 text-blue-500"></i>
                  <span><?= htmlspecialchars($row['lokasi_event']) ?></span>
                </div>

                <!-- Deskripsi -->
                <p class="text-gray-600 text-sm leading-relaxed">
                  <?= htmlspecialchars(mb_substr($row['deskripsi_event'], 0, 100)) ?>...
                </p>
              </div>
            </a>

          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="col-span-3 text-center text-gray-500">Belum ada event.</p>
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
      }, {
        threshold: 0.1
      });

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