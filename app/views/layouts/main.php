<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <script>
    (function() {
      try {
        var storedTheme = window.localStorage.getItem('ca-theme');
        var theme = storedTheme || ((window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.dataset.theme = theme;
      } catch (error) {
        document.documentElement.dataset.theme = 'light';
      }
    })();
  </script>
  <title><?= !empty($seoTitle) ? htmlspecialchars($seoTitle) : (!empty($title) ? htmlspecialchars($title) . ' | ' . APP_NAME : 'Consultoría Ambiental para Empresas e Industrias en México | ' . APP_NAME) ?></title>
  
  <!-- Canonical URL -->
  <link rel="canonical" href="<?= BASE_URL ?><?= $_SERVER['REQUEST_URI'] ?? '/' ?>"/>

  <!-- Meta Description - Default SEO -->
  <meta name="description" content="<?= !empty($metaDesc) ? htmlspecialchars($metaDesc) : 'Consultoría ambiental para empresas e industrias en México. Gestionamos MIA, residuos, emisiones, COA, LAU, inspecciones PROEPA/PROFEPA y cumplimiento normativo con más de 10 años de experiencia.' ?>"/>
  
  <!-- Open Graph -->
  <meta property="og:title" content="<?= !empty($seoTitle) ? htmlspecialchars($seoTitle) : (!empty($title) ? htmlspecialchars($title) . ' | ' . APP_NAME : 'Consultoría Ambiental para Empresas e Industrias en México | ' . APP_NAME) ?>"/>
  <meta property="og:description" content="<?= !empty($metaDesc) ? htmlspecialchars($metaDesc) : 'Consultoría ambiental para empresas e industrias en México. Gestión de permisos, residuos, emisiones, MIA, COA, LAU e inspecciones PROEPA/PROFEPA.' ?>"/>
  <meta property="og:type" content="website"/>
  <meta property="og:url" content="<?= BASE_URL ?><?= $_SERVER['REQUEST_URI'] ?? '/' ?>"/>
  <meta property="og:image" content="<?= asset_prefer_webp('images/consultoria-ambiental-logo.png') ?>"/>
  <meta property="og:locale" content="es_MX"/>

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="<?= !empty($seoTitle) ? htmlspecialchars($seoTitle) : (!empty($title) ? htmlspecialchars($title) . ' | ' . APP_NAME : 'Consultoría Ambiental para Empresas e Industrias en México | ' . APP_NAME) ?>"/>
  <meta name="twitter:description" content="<?= !empty($metaDesc) ? htmlspecialchars($metaDesc) : 'Consultoría ambiental para empresas e industrias en México. Cumplimiento ambiental, permisos, residuos, emisiones e inspecciones para empresas.' ?>"/>

  <!-- Schema.org - ProfessionalService -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ProfessionalService",
    "name": "<?= htmlspecialchars($settings['brand_company_name'] ?? APP_NAME) ?>",
    "description": "Consultoría ambiental para empresas e industrias. Gestión de permisos, residuos, emisiones, MIA, COA, LAU e inspecciones PROEPA/PROFEPA.",
    "url": "<?= BASE_URL ?>",
    "telephone": "<?= htmlspecialchars($settings['footer_whatsapp_value'] ?? '+523387654321') ?>",
    "email": "<?= htmlspecialchars($settings['footer_email_value'] ?? 'contacto@consultoria-ca.com') ?>",
    "areaServed": "México",
    "serviceType": [
      "Consultoría ambiental",
      "Gestión de residuos",
      "Manifestación de Impacto Ambiental",
      "Licencia Ambiental Única",
      "Cédula de Operación Anual",
      "Atención a inspecciones PROEPA y PROFEPA"
    ]
  }
  </script>
  
  <?php if (!empty($headExtra)): ?>
  <?= $headExtra ?>
  <?php endif; ?>
  
  <?php
    $faviconRelative = !empty($settings['brand_logo'])
      ? ltrim((string) $settings['brand_logo'], '/')
      : 'images/consultoria-ambiental-logo.webp';
    $faviconPath = PUBLIC_DIR . '/' . $faviconRelative;
    if (!file_exists($faviconPath)) {
      $faviconRelative = 'images/consultoria-ambiental-logo.webp';
      $faviconPath = PUBLIC_DIR . '/' . $faviconRelative;
    }
    $faviconVersion = file_exists($faviconPath) ? (string) filemtime($faviconPath) : '1';
    $faviconHref = BASE_URL . '/' . $faviconRelative . '?v=' . $faviconVersion;
    $faviconExt = strtolower(pathinfo($faviconRelative, PATHINFO_EXTENSION));
    $faviconType = match ($faviconExt) {
      'webp' => 'image/webp',
      'jpg', 'jpeg' => 'image/jpeg',
      'svg' => 'image/svg+xml',
      'ico' => 'image/x-icon',
      default => 'image/png',
    };

    $tailwindPath = PUBLIC_DIR . '/css/tailwind.css';
    $tailwindVersion = file_exists($tailwindPath) ? (string) filemtime($tailwindPath) : '1';
    $tailwindHref = BASE_URL . '/css/tailwind.css?v=' . $tailwindVersion;
    $hasAos = strpos((string) ($content ?? ''), 'data-aos') !== false;
  ?>

  <!-- Favicon -->
  <link rel="icon" type="<?= $faviconType ?>" href="<?= htmlspecialchars($faviconHref) ?>">
  <link rel="apple-touch-icon" href="<?= htmlspecialchars($faviconHref) ?>">
  <link rel="shortcut icon" type="<?= $faviconType ?>" href="<?= htmlspecialchars($faviconHref) ?>">
  
  <!-- Preconnect to CDN resources for faster loading -->
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <!-- Tailwind CSS (local build): preload + blocking stylesheet to prevent FOUC -->
  <link rel="preload" href="<?= $tailwindHref ?>" as="style">
  <link rel="stylesheet" href="<?= $tailwindHref ?>">

  <?php if ($hasAos): ?>
  <!-- AOS CSS (non-blocking, loaded only on pages with reveal elements) -->
  <link rel="preload" href="https://unpkg.com/aos@2.3.1/dist/aos.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css"></noscript>
  <?php endif; ?>
  
  <!-- FontAwesome (non-blocking) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all'"/>
  <noscript><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/></noscript>
  
  <!-- Google Fonts with font-display: swap -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'"/>
  <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/></noscript>

  <!-- Critical Inline Styles (renders immediately, no blocking) -->
  <style>
    /* === CRITICAL: Above-the-fold styles === */
    html {
      overflow-x: hidden;
      color-scheme: light;
    }
    html.dark {
      color-scheme: dark;
    }
    body {
      scroll-behavior: smooth;
      margin: 0;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    a, a:link, a:visited, a:hover, a:active {
      text-decoration: none !important;
    }
    .hero-bg {
      position: relative;
      overflow: hidden;
    }
    .pattern-bg {
      background-color: #f8f9fa;
      background-image: radial-gradient(#90A4AE 1px, transparent 1px);
      background-size: 20px 20px;
    }
    /* Font Awesome fallback: ensure icons render with swap behavior */
    .fa, .fas, .far, .fab, .fal {
      font-display: swap;
    }
    /* Blog Hero */
    .hero-blog-bg {
      position: relative;
      background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }
    .hero-blog-bg::before {
      content: '';
      position: absolute;
      inset: 0;
      background-color: #00000080;
      z-index: 1;
    }
    .hero-blog-bg > * {
      position: relative;
      z-index: 2;
    }
    /* Line clamp utility */
    .line-clamp-3 {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .glass-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
    }
    /* Client Logo Carousel */
    .carousel-track {
      animation: scrollLogos 40s linear infinite;
    }
    .carousel-track:hover {
      animation-play-state: paused;
    }
    @keyframes scrollLogos {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }

    [data-aos] {
      will-change: transform, opacity;
    }
    @media (prefers-reduced-motion: reduce) {
      [data-aos] {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
    }
  </style>

  <!-- Custom Header Code (from admin settings) -->
  <?= $settings['custom_head_code'] ?? '' ?>

</head>
<body class="font-sans text-ca-dark-gray antialiased bg-ca-bg flex flex-col min-h-screen">

  <?php require VIEWS_DIR . '/partials/navbar.php'; ?>

  <main class="flex-1">
    <?= $content ?>
  </main>

  <?php require VIEWS_DIR . '/partials/footer.php'; ?>

  <!-- Floating WhatsApp Button -->
  <?php
  $waNumber  = htmlspecialchars($settings['whatsapp_floating_number'] ?? '523387654321');
  $waMessage = htmlspecialchars($settings['whatsapp_floating_message'] ?? 'Hola, me gustaría recibir información sobre sus servicios de consultoría ambiental.');
  $waUrl     = 'https://wa.me/' . $waNumber . '?text=' . urlencode($waMessage);
  ?>
  <a href="<?= $waUrl ?>" target="_blank" aria-label="Contactar por WhatsApp" class="fixed bottom-6 right-6 z-50 bg-[#25D366] text-white w-14 h-14 rounded-full flex items-center justify-center text-3xl shadow-[0_4px_14px_0_rgba(37,211,102,0.5)] hover:scale-110 hover:shadow-[0_6px_20px_rgba(37,211,102,0.6)] transition-all duration-300 animate-bounce">
    <i class="fab fa-whatsapp"></i>
  </a>

  <!-- Carousel Script (optimized: no forced reflow per frame) -->
  <script>
    (function() {
      var track = document.getElementById('newsCarousel');
      if (!track) return;
      
      // Clone cards once
      var cards = Array.from(track.children);
      cards.forEach(function(card) {
        track.appendChild(card.cloneNode(true));
      });
      
      var scrollPosition = 0;
      var speed = 1.0;
      var animationId = null;
      var totalWidth = 0;
      var isInitialized = false;
      
      // Measure layout ONCE (not per frame)
      function measureLayout() {
        if (track.children.length === 0) return;
        var first = track.children[0];
        var cardWidth = first.offsetWidth;
        var originalCount = track.children.length / 2;
        totalWidth = cardWidth * originalCount;
        isInitialized = true;
      }
      
      function animate() {
        if (!isInitialized) {
          animationId = requestAnimationFrame(animate);
          return;
        }
        scrollPosition -= speed;
        if (totalWidth <= 0) {
          animationId = requestAnimationFrame(animate);
          return;
        }
        if (Math.abs(scrollPosition) >= totalWidth) {
          scrollPosition = 0;
        }
        track.style.transform = 'translateX(' + scrollPosition + 'px)';
        animationId = requestAnimationFrame(animate);
      }
      
      // Measure after a small delay to ensure layout is complete
      requestAnimationFrame(function() {
        setTimeout(measureLayout, 100);
      });
      animationId = requestAnimationFrame(animate);
      
      document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
          if (animationId) cancelAnimationFrame(animationId);
        } else {
          animationId = requestAnimationFrame(animate);
        }
      });
      
      var resizeTimeout;
      window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
          scrollPosition = 0;
          track.style.transform = 'translateX(0px)';
          measureLayout(); // Re-measure on resize
        }, 200);
      });
    })();
  </script>

  <!-- About Slideshow Script -->
  <script>
    (function() {
      const container = document.getElementById('aboutSlideshow');
      if (!container) return;
      const images = container.querySelectorAll('img');
      if (images.length === 0) return;
      
      let current = 0;
      let interval;
      const delay = 4000;
      
      images[0].classList.remove('opacity-0');
      images[0].classList.add('opacity-100');
      
      function nextSlide() {
        images[current].classList.remove('opacity-100');
        images[current].classList.add('opacity-0');
        current = (current + 1) % images.length;
        images[current].classList.remove('opacity-0');
        images[current].classList.add('opacity-100');
      }
      
      function startSlideshow() {
        stopSlideshow();
        interval = setInterval(nextSlide, delay);
      }
      
      function stopSlideshow() {
        if (interval) {
          clearInterval(interval);
          interval = null;
        }
      }
      
      container.addEventListener('mouseenter', stopSlideshow);
      container.addEventListener('mouseleave', startSlideshow);
      
      startSlideshow();
    })();
  </script>

  <!-- Animated Counter Script -->
  <script>
    (function() {
      const counters = document.querySelectorAll('.counter');
      if (counters.length === 0) return;

      // Store original target values
      counters.forEach(function(counter) {
        counter.dataset.target = parseInt(counter.dataset.target) || 0;
      });

      // Intersection Observer to trigger animation when visible
      const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            const counter = entry.target;
            animateCounter(counter);
            observer.unobserve(counter);
          }
        });
      }, { threshold: 0.3 });

      counters.forEach(function(counter) {
        observer.observe(counter);
      });

      function animateCounter(element) {
        const target = parseInt(element.dataset.target);
        const suffix = element.dataset.suffix || '';
        const duration = 2000; // ms
        const startTime = performance.now();
        const startValue = 0;

        function update(currentTime) {
          const elapsed = currentTime - startTime;
          const progress = Math.min(elapsed / duration, 1);
          // Ease out cubic
          const eased = 1 - Math.pow(1 - progress, 3);
          const currentValue = Math.floor(eased * target);
          element.textContent = currentValue + suffix;

          if (progress < 1) {
            requestAnimationFrame(update);
          } else {
            element.textContent = target + suffix;
          }
        }

        requestAnimationFrame(update);
      }
    })();
  </script>

  <?php if ($hasAos): ?>
  <!-- AOS reveal animations (loaded only on pages with data-aos elements) -->
  <script>
    (function() {
      var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      function initAosWhenReady() {
        var revealElements = document.querySelectorAll('[data-aos]');
        if (!revealElements.length) return;

        document.querySelectorAll('[data-aos-group]').forEach(function(group) {
          group.querySelectorAll('[data-aos]').forEach(function(item, index) {
            if (!item.hasAttribute('data-aos-delay')) {
              item.setAttribute('data-aos-delay', String(Math.min(index * 70, 280)));
            }
          });
        });

        if (reduceMotion) {
          revealElements.forEach(function(el) {
            el.removeAttribute('data-aos');
            el.removeAttribute('data-aos-delay');
          });
          return;
        }

        function startAos() {
          if (typeof AOS === 'undefined') return;

          AOS.init({
            once: true,
            mirror: false,
            offset: 80,
            duration: 550,
            easing: 'ease-out-cubic',
            debounceDelay: 80,
            throttleDelay: 120,
            disableMutationObserver: true
          });

          window.addEventListener('load', function() {
            window.setTimeout(function() {
              AOS.refresh();
            }, 250);
          }, { once: true });
        }

        function loadAosScript() {
          var script = document.createElement('script');
          script.src = 'https://unpkg.com/aos@2.3.1/dist/aos.js';
          script.async = true;
          script.onload = startAos;
          document.body.appendChild(script);
        }

        if ('requestIdleCallback' in window) {
          window.requestIdleCallback(loadAosScript, { timeout: 900 });
        } else {
          window.setTimeout(loadAosScript, 250);
        }
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAosWhenReady, { once: true });
      } else {
        initAosWhenReady();
      }
    })();
  </script>
  <?php endif; ?>

  <!-- Custom Body Code (from admin settings) -->
  <?= $settings['custom_body_code'] ?? '' ?>
  <script>
    (function() {
      var STORAGE_KEY = 'ca-theme';
      var toggles = document.querySelectorAll('[data-theme-toggle]');
      if (!toggles.length) return;

      function getTheme() {
        return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
      }

      function setTheme(theme) {
        var isDark = theme === 'dark';
        document.documentElement.classList.toggle('dark', isDark);
        document.documentElement.dataset.theme = theme;

        try {
          window.localStorage.setItem(STORAGE_KEY, theme);
        } catch (error) {}

        toggles.forEach(function(toggle) {
          toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
          toggle.setAttribute('aria-label', isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
          var sun = toggle.querySelector('[data-theme-icon="sun"]');
          var moon = toggle.querySelector('[data-theme-icon="moon"]');
          if (sun) sun.style.display = isDark ? 'block' : 'none';
          if (moon) moon.style.display = isDark ? 'none' : 'block';
        });
      }

      toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
          setTheme(getTheme() === 'dark' ? 'light' : 'dark');
        });
      });

      setTheme(getTheme());
    })();
  </script>

</body>
</html>
