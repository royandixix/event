
<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../function/config.php';

?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Event.com</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class=" ">

<!-- Navbar Sticky Top -->
<nav class="sticky top-0 left-0 w-full bg-white/80 dark:bg-gray-900/80 
           backdrop-blur-md border-b border-gray-200 dark:border-gray-700 
           shadow-lg z-50 transition-colors duration-300 ease-in-out">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">

      <!-- Logo -->
      <div class="flex items-center">
        <a href="./img/logo.png" class="flex items-center gap-2">
          <img src="./img/logo.png" 
               alt="Logo" class="h-8 w-auto">
          <span class="text-gray-800 dark:text-white font-semibold text-lg tracking-wide transition-colors duration-300">
          ta bangka gasss#2
          </span>
        </a>
      </div>

      <!-- Desktop Menu -->
      <div class="hidden sm:flex items-center space-x-6">
        <div class="relative group">
          <a href="#"
             class="text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white 
                    transition-colors duration-300 px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 
                    flex items-center gap-1 cursor-pointer">
            Menu
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M19 9l-7 7-7-7" />
            </svg>
          </a>

          <!-- Submenu -->
          <div class="absolute top-12 left-0 w-48 overflow-hidden rounded-lg shadow-lg 
                      bg-white dark:bg-gray-800 opacity-0 invisible group-hover:opacity-100 group-hover:visible 
                      transform translate-y-2 group-hover:translate-y-0 
                      transition-all duration-300 ease-in-out">
            <a href="index.php" 
               class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
              Home
            </a>
            <a href="peserta.php" 
               class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
              Daftar Peserta
            </a>
            <a href="manejer.php" 
               class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
              Daftar Manejer
            </a>
          </div>
        </div>
      </div>

      <!-- Mobile Menu Button -->
      <button id="navMenuToggle" class="sm:hidden text-gray-600 dark:text-gray-300 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="navMobileMenu"
       class="sm:hidden max-h-0 overflow-hidden 
              transition-all duration-300 ease-in-out 
              bg-white/95 dark:bg-gray-900/95 backdrop-blur-md">
    <div class="px-4 py-3 space-y-2">
      <a href="index.php" 
         class="block px-3 py-2 text-gray-600 dark:text-gray-300 
                hover:text-black dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 
                rounded-md transition-colors">
        Home
      </a>
      <a href="peserta.php" 
         class="block px-3 py-2 text-gray-600 dark:text-gray-300 
                hover:text-black dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 
                rounded-md transition-colors">
        Daftar Peserta
      </a>
      <a href="manejer.php" 
         class="block px-3 py-2 text-gray-600 dark:text-gray-300 
                hover:text-black dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 
                rounded-md transition-colors">
        Daftar Manejer
      </a>
    </div>
  </div>
</nav>

<!-- Floating WhatsApp Button -->
<button id="floatingMenuToggle"
        class="fixed bottom-6 right-6 z-50 
               w-14 h-14 rounded-full 
               bg-green-500 
               text-white shadow-lg shadow-green-300/50
               flex items-center justify-center
               hover:scale-110 hover:shadow-xl 
               transition-all duration-300 ease-in-out">
  <!-- WhatsApp Icon -->
  <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="h-7 w-7" viewBox="0 0 24 24">
    <path d="M20.52 3.48A11.92 11.92 0 0012 0C5.37 0 0 5.37 0 12c0 2.11.55 4.18 1.6 6.02L0 24l6.21-1.63A12 12 0 1012 0c-3.2 0-6.21 1.25-8.48 3.52zM12 22c-1.99 0-3.93-.52-5.63-1.5l-.4-.23-3.69.97.99-3.6-.26-.38A9.94 9.94 0 012 12C2 6.48 6.48 2 12 2c2.65 0 5.15 1.03 7.07 2.93A9.94 9.94 0 0122 12c0 5.52-4.48 10-10 10zm5.21-6.79c-.28-.14-1.65-.81-1.91-.9-.26-.1-.45-.14-.64.14-.19.28-.73.9-.9 1.09-.17.19-.35.21-.63.07-.28-.14-1.18-.43-2.25-1.37-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.12-.12.28-.31.42-.47.14-.16.19-.28.28-.47.09-.19.05-.35-.02-.49-.07-.14-.64-1.54-.88-2.11-.23-.56-.47-.48-.64-.49-.16-.01-.35-.01-.54-.01s-.49.07-.75.35c-.26.28-1 1-1 2.43s1.02 2.82 1.16 3.02c.14.19 2 3.05 4.85 4.28.68.29 1.21.46 1.63.59.68.22 1.3.19 1.79.12.55-.08 1.65-.67 1.88-1.31.23-.65.23-1.2.16-1.31-.07-.11-.25-.18-.53-.32z"/>
  </svg>
</button>

<script>
  // Navbar mobile toggle
  document.getElementById('navMenuToggle').addEventListener('click', () => {
    const menu = document.getElementById('navMobileMenu');
    if (menu.style.maxHeight) {
      menu.style.maxHeight = '';
    } else {
      menu.style.maxHeight = menu.scrollHeight + 'px';
    }
  });

  // Floating WhatsApp Button
  document.getElementById('floatingMenuToggle').addEventListener('click', () => {
    window.open("https://wa.me/6281234567890?text=Halo%20Admin", "_blank");
  });
</script>

</body>
</html>
