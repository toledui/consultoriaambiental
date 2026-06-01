<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
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
  <meta property="og:image" content="<?= BASE_URL ?>/images/consultoria-ambiental-logo.png"/>
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
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/favicon.svg">
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/favicon.ico">
  <link rel="shortcut icon" type="image/x-icon" href="<?= BASE_URL ?>/favicon.ico">
  
  <!-- Preconnect to CDN resources for faster loading -->
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <!-- Tailwind CSS (local build) - blocking stylesheet to prevent FOUC -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/tailwind.css">
  
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

    /* GSAP reveal elements start invisible */
    .gsap-reveal {
      opacity: 0;
      visibility: hidden;
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

  <!-- GSAP + ScrollTrigger (local files, loaded only if reveal elements exist) -->
  <script>
    (function() {
      // Check if any GSAP-reveal elements exist on the page
      var hasReveal = document.querySelector('.gsap-reveal, .gsap-stagger');
      if (!hasReveal) return;

      // Dynamically load GSAP and ScrollTrigger
      var gsapScript = document.createElement('script');
      gsapScript.src = '<?= BASE_URL ?>/js/gsap.min.js';
      gsapScript.onload = function() {
        var stScript = document.createElement('script');
        stScript.src = '<?= BASE_URL ?>/js/ScrollTrigger.min.js';
        stScript.onload = function() {
          initGSAP();
        };
        document.body.appendChild(stScript);
      };
      document.body.appendChild(gsapScript);

      function initGSAP() {
        gsap.registerPlugin(ScrollTrigger);

        // ==========================================
        // 1. REVEAL ANIMATIONS (fade in + translate)
        // ==========================================
        gsap.utils.toArray('.gsap-reveal').forEach(function(el) {
          var animType = el.getAttribute('data-gsap') || 'fade-up';
          var vars = { opacity: 1, visibility: 'visible', duration: 0.7, ease: 'power2.out' };

          if (animType === 'fade-up') {
            vars.y = 0;
            gsap.set(el, { y: 40 });
          } else if (animType === 'fade-left') {
            vars.x = 0;
            gsap.set(el, { x: -40 });
          } else if (animType === 'fade-right') {
            vars.x = 0;
            gsap.set(el, { x: 40 });
          } else if (animType === 'scale') {
            vars.scale = 1;
            gsap.set(el, { scale: 0.85 });
          }

          ScrollTrigger.create({
            trigger: el,
            start: 'top 85%',
            toggleActions: 'play none none none',
            once: true,
            onEnter: function() {
              gsap.to(el, vars);
            }
          });
        });

        // ==========================================
        // 2. STAGGER ANIMATIONS (for grids/lists)
        // ==========================================
        gsap.utils.toArray('.gsap-stagger').forEach(function(parent) {
          var children = parent.querySelectorAll('.gsap-stagger-item');
          if (children.length === 0) return;

          var staggerDelay = parseFloat(parent.getAttribute('data-stagger-delay')) || 0.1;

          gsap.set(children, { opacity: 0, y: 30 });

          ScrollTrigger.create({
            trigger: parent,
            start: 'top 85%',
            once: true,
            onEnter: function() {
              gsap.to(children, {
                opacity: 1,
                y: 0,
                duration: 0.6,
                stagger: staggerDelay,
                ease: 'power2.out',
                onComplete: function() {
                  children.forEach(function(child) {
                    child.style.visibility = 'visible';
                  });
                }
              });
            }
          });
        });

        // Refresh ScrollTrigger after everything is rendered
        setTimeout(function() {
          ScrollTrigger.refresh();
        }, 500);
      }
    })();
  </script>

  <!-- Custom Body Code (from admin settings) -->
  <?= $settings['custom_body_code'] ?? '' ?>

</body>
</html>
