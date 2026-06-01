<style>
  /* Overlay starts hidden */
  #mobileMenuOverlay {
    opacity: 0 !important;
    visibility: hidden !important;
  }
  #mobileMenuOverlay.visible {
    opacity: 1 !important;
    visibility: visible !important;
  }
</style>

<!-- Navigation -->
<?php
  $currentUri = $_SERVER['REQUEST_URI'] ?? '';
  $isDetailPage = preg_match('#^/(servicios|blog)/[^/]+$#', $currentUri);
  $headerBg = $isDetailPage ? '#1B3A4B' : '#00000033';
?>
<header id="mainHeader" class="fixed w-full top-0 z-50"
        style="background-color: <?= $headerBg ?>; backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1); border-bottom: 1px solid rgba(255,255,255,0.1);">
  <div class="container mx-auto px-4 md:px-8 py-3 flex justify-between items-center">
    
    <!-- Logo -->
    <a class="flex items-center group bg-white rounded-md p-1.5 shadow-sm" href="<?= BASE_URL ?>" aria-label="Ir al inicio">
      <?php if (!empty($settings['brand_logo'])): ?>
        <img alt="<?= htmlspecialchars($settings['brand_company_name'] ?? 'Consultoría Ambiental') ?>" width="64" height="64" class="h-16 w-auto object-contain" src="<?= BASE_URL ?>/<?= htmlspecialchars($settings['brand_logo']) ?>"/>
      <?php else: ?>
        <img alt="Consultoría Ambiental" width="64" height="64" class="h-16 w-auto object-contain" src="<?= BASE_URL ?>/images/consultoria-ambiental-logo.webp"/>
      <?php endif; ?>
    </a>
    
    <!-- Desktop Menu -->
    <nav class="hidden lg:flex items-center space-x-8 text-sm font-medium">
      <a class="<?= $currentPage === 'home' ? 'text-ca-light-green border-b-2 border-ca-light-green' : 'text-white/90 hover:text-ca-light-green hover:border-b-2 hover:border-ca-light-green' ?> pb-1 transition-all" href="<?= BASE_URL ?>">Inicio</a>
      <a class="<?= $currentPage === 'nosotros' ? 'text-ca-light-green border-b-2 border-ca-light-green' : 'text-white/90 hover:text-ca-light-green hover:border-b-2 hover:border-ca-light-green' ?> pb-1 transition-all" href="<?= BASE_URL ?>/nosotros">Nosotros</a>
      
      <!-- Servicios Dropdown -->
      <div class="relative group">
        <a class="<?= $currentPage === 'servicios' ? 'text-ca-light-green border-b-2 border-ca-light-green' : 'text-white/90 hover:text-ca-light-green hover:border-b-2 hover:border-ca-light-green' ?> pb-1 transition-all inline-flex items-center gap-1" href="<?= BASE_URL ?>/servicios">
          Servicios
          <i class="fas fa-chevron-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
        </a>
        <!-- Dropdown Menu -->
        <div class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform -translate-y-1 group-hover:translate-y-0 pointer-events-none group-hover:pointer-events-auto">
          <div class="bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden min-w-[260px] py-2">
            <?php if (!empty($navbarServices)): ?>
              <?php foreach ($navbarServices as $svc): ?>
                <a href="<?= BASE_URL ?>/servicios/<?= htmlspecialchars($svc['slug']) ?>" class="flex items-center gap-3 px-5 py-3 text-sm text-gray-700 hover:bg-ca-green/5 hover:text-ca-green transition-colors border-b border-gray-50 last:border-b-0">
                  <span class="w-8 h-8 rounded-lg bg-ca-bg flex items-center justify-center text-ca-green text-xs flex-shrink-0">
                    <i class="<?= htmlspecialchars($svc['icon'] ?? 'fas fa-leaf') ?>"></i>
                  </span>
                  <span class="font-medium"><?= htmlspecialchars($svc['title']) ?></span>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <a href="<?= BASE_URL ?>/servicios" class="block px-5 py-3 text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                Ver todos los servicios
              </a>
            <?php endif; ?>
            <!-- View all link -->
            <div class="border-t border-gray-100 mt-1 pt-1">
              <a href="<?= BASE_URL ?>/servicios" class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-ca-green hover:bg-ca-green/5 transition-colors">
                <i class="fas fa-th-list text-xs"></i>
                Ver catálogo completo
                <i class="fas fa-arrow-right ml-auto text-xs"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <a class="<?= $currentPage === 'blog' ? 'text-ca-light-green border-b-2 border-ca-light-green' : 'text-white/90 hover:text-ca-light-green hover:border-b-2 hover:border-ca-light-green' ?> pb-1 transition-all" href="<?= BASE_URL ?>/blog">Blog</a>
    </nav>

    <!-- Desktop Actions -->
    <div class="hidden lg:flex items-center space-x-5">
      <div class="flex space-x-3 text-lg">
        <?php
        $headerSocial = [];
        if (!empty($settings['social_header'])) {
            $decoded = json_decode($settings['social_header'], true);
            $headerSocial = is_array($decoded) ? $decoded : [];
        }
        foreach ($headerSocial as $item):
            $icon = $item['icon'] ?? '';
            $url  = $item['url'] ?? '';
            if (empty($icon) || empty($url)) continue;
            $iconClass = (in_array($icon, ['envelope', 'globe', 'link'])) ? 'fas fa-' . $icon : 'fab fa-' . $icon;
            $socialName = $item['name'] ?? $icon;
        ?>
          <a class="text-white/80 hover:text-ca-light-green transition-colors" href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($socialName) ?>"><i class="<?= $iconClass ?>"></i></a>
        <?php endforeach; ?>
      </div>
      <a class="bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-6 rounded-full shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all" href="<?= BASE_URL ?>/contacto">
        Contacto
      </a>
    </div>

  </div>
</header>

<!-- Mobile Menu Button - FUERA del header, elemento independiente -->
<button id="mobileMenuBtn" type="button" aria-label="Abrir menú"
  style="position:fixed;top:18px;right:16px;z-index:99999;width:44px;height:44px;background-color:#1B3A4B;border:1px solid rgba(255,255,255,0.3);border-radius:12px;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.3);color:white;cursor:pointer;outline:none;">
  <svg id="menuIcon" xmlns="http://www.w3.org/2000/svg" style="display:block;width:20px;height:20px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
  </svg>
  <svg id="closeIcon" xmlns="http://www.w3.org/2000/svg" style="display:none;width:20px;height:20px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
  </svg>
</button>

<script>
// INMEDIATELY show button on mobile, hide on desktop
// This runs BEFORE any other scripts
(function(){
  var btn = document.getElementById('mobileMenuBtn');
  if (!btn) return;
  if (window.innerWidth >= 1024) {
    btn.style.display = 'none';
  } else {
    btn.style.display = 'flex';
  }
})();
</script>

<!-- Mobile Menu Overlay -->
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black/60 z-40 transition-opacity duration-300 lg:hidden"></div>

<!-- Mobile Menu Panel -->
<div id="mobileMenuPanel" class="fixed top-0 right-0 w-full max-w-sm bg-ca-navy shadow-2xl z-50 overflow-y-auto lg:hidden" style="min-height: 100vh; max-height: 100vh; transform: translateX(100%); transition: transform 300ms ease-in-out;">
    <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
      <span class="text-white font-bold text-lg">Menú</span>
      <button id="mobileMenuClose" class="text-white/80 hover:text-white text-2xl focus:outline-none" aria-label="Cerrar menú">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <nav class="px-4 py-6 space-y-1">
      <!-- Inicio -->
      <a class="flex items-center gap-4 px-4 py-3.5 rounded-xl text-base font-medium transition-all <?= $currentPage === 'home' ? 'text-ca-light-green bg-white/10' : 'text-white hover:text-ca-light-green hover:bg-white/5' ?>" href="<?= BASE_URL ?>">
        <span class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-sm"><i class="fas fa-home"></i></span>
        Inicio
      </a>

      <!-- Nosotros -->
      <a class="flex items-center gap-4 px-4 py-3.5 rounded-xl text-base font-medium transition-all <?= $currentPage === 'nosotros' ? 'text-ca-light-green bg-white/10' : 'text-white hover:text-ca-light-green hover:bg-white/5' ?>" href="<?= BASE_URL ?>/nosotros">
        <span class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-sm"><i class="fas fa-building"></i></span>
        Nosotros
      </a>

      <!-- Servicios (with expandable submenu) -->
      <div id="mobileServicesGroup">
        <button id="mobileServicesToggle" class="w-full flex items-center justify-between gap-4 px-4 py-3.5 rounded-xl text-base font-medium transition-all <?= $currentPage === 'servicios' ? 'text-ca-light-green bg-white/10' : 'text-white hover:text-ca-light-green hover:bg-white/5' ?>">
          <div class="flex items-center gap-4">
            <span class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-sm"><i class="fas fa-leaf"></i></span>
            Servicios
          </div>
          <i id="mobileServicesChevron" class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
        </button>
        <div id="mobileServicesSubmenu" class="overflow-hidden transition-all duration-300 max-h-0">
          <div class="pl-4 pr-2 pb-2 pt-1 space-y-0.5">
            <?php if (!empty($navbarServices)): ?>
              <?php foreach ($navbarServices as $svc): ?>
                <a href="<?= BASE_URL ?>/servicios/<?= htmlspecialchars($svc['slug']) ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all <?= (($currentPage === 'servicios' && !empty($currentSlug) && $currentSlug === $svc['slug'])) ? 'text-ca-light-green bg-white/10' : 'text-white/90 hover:text-white hover:bg-white/5' ?>">
                  <span class="w-6 h-6 rounded-md bg-white/10 flex items-center justify-center text-ca-light-green text-[10px] flex-shrink-0">
                    <i class="<?= htmlspecialchars($svc['icon'] ?? 'fas fa-leaf') ?>"></i>
                  </span>
                  <?= htmlspecialchars($svc['title']) ?>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <a href="<?= BASE_URL ?>/servicios" class="block px-4 py-2.5 rounded-lg text-sm text-white/90 hover:text-white hover:bg-white/5 transition-all">
                Ver todos los servicios
              </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/servicios" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold text-ca-light-green hover:bg-white/5 transition-all mt-1">
              <i class="fas fa-th-list text-xs"></i>
              Catálogo completo
            </a>
          </div>
        </div>
      </div>

      <!-- Blog -->
      <a class="flex items-center gap-4 px-4 py-3.5 rounded-xl text-base font-medium transition-all <?= $currentPage === 'blog' ? 'text-ca-light-green bg-white/10' : 'text-white hover:text-ca-light-green hover:bg-white/5' ?>" href="<?= BASE_URL ?>/blog">
        <span class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-sm"><i class="fas fa-newspaper"></i></span>
        Blog
      </a>

      <hr class="border-white/10 my-4">

      <!-- Contacto CTA -->
      <a class="flex items-center gap-4 px-4 py-3.5 rounded-xl text-base font-bold text-white bg-ca-green hover:bg-green-700 transition-all shadow-lg" href="<?= BASE_URL ?>/contacto">
        <span class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center text-sm"><i class="fas fa-headset"></i></span>
        Contacto
      </a>

      <!-- Social Icons -->
      <?php if (!empty($headerSocial)): ?>
        <div class="px-4 pt-6">
          <p class="text-xs text-white/40 uppercase tracking-widest mb-3 font-semibold">Síguenos</p>
          <div class="flex gap-3 text-xl">
            <?php foreach ($headerSocial as $item):
                $icon = $item['icon'] ?? '';
                $url  = $item['url'] ?? '';
                if (empty($icon) || empty($url)) continue;
                $iconClass = (in_array($icon, ['envelope', 'globe', 'link'])) ? 'fas fa-' . $icon : 'fab fa-' . $icon;
                $socialName = $item['name'] ?? $icon;
            ?>
              <a class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center text-white/90 hover:bg-ca-light-green hover:text-white transition-all" href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($socialName) ?>"><i class="<?= $iconClass ?> text-sm"></i></a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </nav>
  </div>

<!-- Mobile Menu JavaScript -->
<script>
(function() {
  'use strict';

  var header = document.getElementById('mainHeader');
  var menuBtn = document.getElementById('mobileMenuBtn');
  var menuClose = document.getElementById('mobileMenuClose');
  var overlay = document.getElementById('mobileMenuOverlay');
  var panel = document.getElementById('mobileMenuPanel');
  var servicesToggle = document.getElementById('mobileServicesToggle');
  var servicesSubmenu = document.getElementById('mobileServicesSubmenu');
  var servicesChevron = document.getElementById('mobileServicesChevron');
  var menuIcon = document.getElementById('menuIcon');
  var closeIcon = document.getElementById('closeIcon');

  if (!menuBtn || !menuClose || !overlay || !panel) return;

  // Show/hide on resize
  window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
      menuBtn.style.display = 'none';
      if (overlay) overlay.style.display = 'none';
      if (panel) panel.style.display = 'none';
    } else {
      menuBtn.style.display = 'flex';
      if (overlay) overlay.style.display = '';
      if (panel) panel.style.display = '';
    }
  });

  function openMenu() {
    overlay.classList.add('visible');
    panel.style.transform = 'translateX(0)';
    document.body.style.overflow = 'hidden';
    if (menuIcon) menuIcon.style.display = 'none';
    if (closeIcon) closeIcon.style.display = 'block';
  }

  function closeMenu() {
    overlay.classList.remove('visible');
    panel.style.transform = 'translateX(100%)';
    document.body.style.overflow = '';
    if (closeIcon) closeIcon.style.display = 'none';
    if (menuIcon) menuIcon.style.display = 'block';
    closeServicesSubmenu();
  }

  menuBtn.addEventListener('click', function() {
    if (panel.style.transform === 'translateX(0px)' || panel.style.transform === 'translateX(0)') {
      closeMenu();
    } else {
      openMenu();
    }
  });

  menuClose.addEventListener('click', closeMenu);
  overlay.addEventListener('click', closeMenu);

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && panel.style.transform === 'translateX(0px)') {
      closeMenu();
    }
  });

  function toggleServicesSubmenu() {
    if (servicesSubmenu.classList.contains('max-h-0')) {
      servicesSubmenu.classList.remove('max-h-0');
      servicesSubmenu.classList.add('max-h-[800px]');
      servicesChevron.classList.add('rotate-180');
    } else {
      closeServicesSubmenu();
    }
  }

  function closeServicesSubmenu() {
    servicesSubmenu.classList.remove('max-h-[800px]');
    servicesSubmenu.classList.add('max-h-0');
    servicesChevron.classList.remove('rotate-180');
  }

  if (servicesToggle && servicesSubmenu) {
    servicesToggle.addEventListener('click', toggleServicesSubmenu);
  }

  // Navbar scroll effect
  // Keep solid dark background on individual service/blog detail pages
  var isDetailPage = /^\/(servicios|blog)\/[^\/]+$/.test(window.location.pathname);
  window.addEventListener('scroll', function() {
    var scrollY = window.pageYOffset;
    if (isDetailPage) {
      header.style.backgroundColor = '#1B3A4B';
      header.style.backdropFilter = 'blur(16px)';
      header.style.borderBottomColor = 'rgba(102, 187, 106, 0.2)';
    } else if (scrollY > 80) {
      header.style.backgroundColor = 'rgba(27, 58, 75, 0.95)';
      header.style.backdropFilter = 'blur(16px)';
      header.style.borderBottomColor = 'rgba(102, 187, 106, 0.2)';
    } else {
      header.style.backgroundColor = '#00000033';
      header.style.backdropFilter = 'blur(16px)';
      header.style.borderBottomColor = 'rgba(255,255,255,0.1)';
    }
  });
})();
</script>
