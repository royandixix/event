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

<body class="bg-gradient-to-r from-pink-200 via-white to-white min-h-screen">

  <div class="bg-gradient-to-r from-pink-200 via-white to-white pt-24 pb-16 px-4 lg:px-16">
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
      class="block rounded-2xl overflow-hidden shadow-md border border-gray-100 
             hover:shadow-2xl hover:-translate-y-2 transform transition duration-300 group
             bg-white/90 backdrop-blur-sm relative">

      <!-- Poster -->
      <div class="relative overflow-hidden h-64">
        <img src="<?= $poster ?>" alt="Poster Event"
          class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 ease-out">

        <!-- Overlay gradient -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>

        <!-- Badge Harga -->
        <span class="absolute top-3 right-3 bg-gradient-to-r from-pink-500 to-pink-700 text-white text-xs  px-3 py-1 rounded-full shadow-md">
          <?= $harga ?>
        </span>
      </div>

      <!-- Konten Event -->
      <div class="p-6 space-y-3">
        <!-- Tanggal -->
        <div class="flex items-center text-xs text-gray-500 gap-2">
          <i data-lucide="calendar" class="w-4 h-4 text-pink-500"></i>
          <span><?= htmlspecialchars($row['tanggal_mulai']) ?> - <?= htmlspecialchars($row['tanggal_selesai']) ?></span>
        </div>

        <!-- Judul -->
        <h2 class="text-lg  text-gray-800 group-hover:text-pink-600 transition-colors duration-200 line-clamp-2">
          <?= htmlspecialchars($row['judul_event']) ?>
        </h2>

        <!-- Lokasi -->
        <div class="flex items-center text-sm text-gray-500 gap-2">
          <i data-lucide="map-pin" class="w-4 h-4 text-pink-500"></i>
          <span><?= htmlspecialchars($row['lokasi_event']) ?></span>
        </div>

        <!-- Deskripsi -->
        <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">
          <?= htmlspecialchars(mb_substr($row['deskripsi_event'], 0, 100)) ?>...
        </p>

        <!-- Tombol -->
        <div class="pt-2">
          <span class="inline-flex items-center px-4 py-2 text-sm  rounded-lg 
                        bg-pink-500 text-white shadow-md hover:bg-pink-600 transition">
            Lihat Detail
            <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
          </span>
        </div>
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
