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
  <?php
    // Keep page titles unchanged while centralizing all share metadata so each
    // property is emitted exactly once.
    $pageTitleText = !empty($seoTitle)
      ? (string) $seoTitle
      : (!empty($title)
        ? (string) $title . ' | ' . APP_NAME
        : 'Consultoría Ambiental para Empresas e Industrias en México');
    $canonicalHref = $canonicalUrl ?? canonical_url();
    $robotsMeta = $robotsContent ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    $metaDescriptionText = !empty($metaDesc)
      ? (string) $metaDesc
      : 'Consultoría ambiental para empresas en México: permisos, residuos, emisiones, MIA, COA, LAU y atención de inspecciones PROFEPA.';
    $ogTitleText = !empty($ogTitle) ? (string) $ogTitle : $pageTitleText;
    $ogDescriptionText = !empty($ogDescription)
      ? (string) $ogDescription
      : (!empty($metaDesc)
        ? (string) $metaDesc
        : 'Permisos, residuos, emisiones, MIA, COA, LAU e inspecciones ambientales para empresas en México.');
    $twitterTitleText = !empty($twitterTitle) ? (string) $twitterTitle : $ogTitleText;
    $twitterDescriptionText = !empty($twitterDescription)
      ? (string) $twitterDescription
      : (!empty($metaDesc)
        ? (string) $metaDesc
        : 'Permisos, residuos, emisiones, MIA, COA, LAU e inspecciones ambientales para empresas en México.');
    $ogTypeValue = !empty($ogType) ? (string) $ogType : 'website';
    $siteName = (string) ($settings['brand_company_name'] ?? APP_NAME);

    $defaultOgImage = !empty($settings['brand_og_image'])
      ? (string) $settings['brand_og_image']
      : 'images/consultoria-ambiental-logo.webp';
    $ogImageSource = !empty($ogImage) ? (string) $ogImage : $defaultOgImage;
    $ogImageHref = $ogImageSource;
    $ogImagePath = null;
    $ogImageParts = parse_url($ogImageSource);
    $baseParts = parse_url(BASE_URL);

    if (empty($ogImageParts['host']) || ($ogImageParts['host'] ?? '') === ($baseParts['host'] ?? '')) {
      $ogImageRelative = ltrim(rawurldecode((string) ($ogImageParts['path'] ?? $ogImageSource)), '/');
      if (str_starts_with($ogImageRelative, 'public/')) {
        $ogImageRelative = substr($ogImageRelative, 7);
      }
      $ogImagePath = PUBLIC_DIR . '/' . $ogImageRelative;
      $ogImageHref = asset_url($ogImageRelative);
      if (is_file($ogImagePath)) {
        $ogImageHref .= '?v=' . filemtime($ogImagePath);
      }
    }

    $ogImageWidth = null;
    $ogImageHeight = null;
    $ogImageMime = null;
    if ($ogImagePath && is_file($ogImagePath)) {
      $ogImageInfo = @getimagesize($ogImagePath);
      if ($ogImageInfo) {
        $ogImageWidth = (int) $ogImageInfo[0];
        $ogImageHeight = (int) $ogImageInfo[1];
        $ogImageMime = (string) ($ogImageInfo['mime'] ?? '');
      }
    }
    $ogImageAltText = !empty($ogImageAlt)
      ? (string) $ogImageAlt
      : $siteName . ' — imagen para compartir';

    // Keep this URL stable so Google can retain and refresh the favicon.
    $faviconHref = rtrim(public_base_url(), '/') . '/favicon.png';
  ?>
  <title><?= htmlspecialchars($pageTitleText, ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Indexing directives and canonical URL -->
  <meta name="robots" content="<?= htmlspecialchars($robotsMeta, ENT_QUOTES, 'UTF-8') ?>"/>
  <link rel="canonical" href="<?= htmlspecialchars($canonicalHref, ENT_QUOTES, 'UTF-8') ?>"/>

  <!-- Meta Description - Default SEO -->
  <meta name="description" content="<?= htmlspecialchars($metaDescriptionText, ENT_QUOTES, 'UTF-8') ?>"/>
  
  <!-- Open Graph -->
  <meta property="og:title" content="<?= htmlspecialchars($ogTitleText, ENT_QUOTES, 'UTF-8') ?>"/>
  <meta property="og:description" content="<?= htmlspecialchars($ogDescriptionText, ENT_QUOTES, 'UTF-8') ?>"/>
  <meta property="og:type" content="<?= htmlspecialchars($ogTypeValue, ENT_QUOTES, 'UTF-8') ?>"/>
  <meta property="og:url" content="<?= htmlspecialchars($canonicalHref, ENT_QUOTES, 'UTF-8') ?>"/>
  <meta property="og:image" content="<?= htmlspecialchars($ogImageHref, ENT_QUOTES, 'UTF-8') ?>"/>
  <?php if ($ogImageMime): ?><meta property="og:image:type" content="<?= htmlspecialchars($ogImageMime, ENT_QUOTES, 'UTF-8') ?>"/><?php endif; ?>
  <?php if ($ogImageWidth && $ogImageHeight): ?>
  <meta property="og:image:width" content="<?= $ogImageWidth ?>"/>
  <meta property="og:image:height" content="<?= $ogImageHeight ?>"/>
  <?php endif; ?>
  <meta property="og:image:alt" content="<?= htmlspecialchars($ogImageAltText, ENT_QUOTES, 'UTF-8') ?>"/>
  <meta property="og:site_name" content="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>"/>
  <meta property="og:locale" content="es_MX"/>

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image"/>
  <meta name="twitter:title" content="<?= htmlspecialchars($twitterTitleText, ENT_QUOTES, 'UTF-8') ?>"/>
  <meta name="twitter:description" content="<?= htmlspecialchars($twitterDescriptionText, ENT_QUOTES, 'UTF-8') ?>"/>
  <meta name="twitter:image" content="<?= htmlspecialchars($ogImageHref, ENT_QUOTES, 'UTF-8') ?>"/>
  <meta name="twitter:image:alt" content="<?= htmlspecialchars($ogImageAltText, ENT_QUOTES, 'UTF-8') ?>"/>

  <!-- Schema.org - ProfessionalService -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ProfessionalService",
    "name": "<?= htmlspecialchars($settings['brand_company_name'] ?? APP_NAME) ?>",
    "description": "Consultoría ambiental para empresas e industrias. Gestión de permisos, residuos, emisiones, MIA, COA, LAU e inspecciones PROEPA/PROFEPA.",
    "url": "<?= htmlspecialchars(public_base_url(), ENT_QUOTES, 'UTF-8') ?>",
    "logo": "<?= htmlspecialchars(!empty($settings['brand_logo']) ? asset_url($settings['brand_logo']) : asset_url('images/consultoria-ambiental-logo.webp'), ENT_QUOTES, 'UTF-8') ?>",
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
    $tailwindPath = PUBLIC_DIR . '/css/tailwind.css';
    $tailwindVersion = file_exists($tailwindPath) ? (string) filemtime($tailwindPath) : '1';
    $tailwindHref = BASE_URL . '/css/tailwind.css?v=' . $tailwindVersion;
    $hasAos = true;
    $turnstileEnabled = \App\Helpers\Turnstile::canRender($settings);

    // Keep custom integrations configurable while loading Tawk after the
    // critical rendering window. Other custom snippets remain untouched.
    $deferredTawkUrl = null;
    $extractTawkSnippet = static function (string $code) use (&$deferredTawkUrl): string {
      return (string) preg_replace_callback(
        '~<script\b[^>]*>.*?</script>~is',
        static function (array $match) use (&$deferredTawkUrl): string {
          $script = $match[0];
          if (stripos($script, 'embed.tawk.to') === false && stripos($script, 'Tawk_API') === false) {
            return $script;
          }

          if ($deferredTawkUrl === null
              && preg_match('~https://embed\.tawk\.to/[A-Za-z0-9/_-]+~i', $script, $urlMatch)) {
            $deferredTawkUrl = $urlMatch[0];
          }

          return '';
        },
        $code
      );
    };
    $customHeadCode = $extractTawkSnippet((string) ($settings['custom_head_code'] ?? ''));
    $customBodyCode = $extractTawkSnippet((string) ($settings['custom_body_code'] ?? ''));
  ?>

  <!-- Favicon -->
  <link rel="icon" sizes="512x512" href="<?= htmlspecialchars($faviconHref) ?>">
  <link rel="apple-touch-icon" href="<?= htmlspecialchars($faviconHref) ?>">
  <link rel="shortcut icon" href="<?= htmlspecialchars($faviconHref) ?>">
  
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
    html,
    body {
      overflow-x: hidden;
      max-width: 100%;
    }
    html {
      color-scheme: light;
    }
    html.dark {
      color-scheme: dark;
    }
    body {
      scroll-behavior: smooth;
      margin: 0;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      overflow-x: hidden;
    }
    main {
      overflow-x: clip;
    }
    body:has(.service-detail) > main {
      overflow-x: visible;
    }
    @supports not (overflow: clip) {
      main {
        overflow-x: hidden;
      }
      body:has(.service-detail) > main {
        overflow-x: visible;
      }
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
      background-image: radial-gradient(#90a4ae59 1px, transparent 1px);
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

  <?php if ($turnstileEnabled): ?>
  <!-- Cloudflare Turnstile: load only when a visible form or modal needs it. -->
  <script>
    (function() {
      var pendingRoot = null;

      window.loadTurnstile = function(root) {
        pendingRoot = root || pendingRoot || document;
        if (window.turnstile) {
          window.renderTurnstileWidgets(pendingRoot);
          return;
        }
        if (document.getElementById('turnstile-api')) return;

        var script = document.createElement('script');
        script.id = 'turnstile-api';
        script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad&render=explicit';
        script.async = true;
        script.defer = true;
        script.onerror = function() {
          script.remove();
        };
        document.head.appendChild(script);
      };

    window.renderTurnstileWidgets = function(root) {
      if (!window.turnstile) {
        window.loadTurnstile(root);
        return;
      }

      (root || document).querySelectorAll('.js-turnstile').forEach(function(container) {
        if (container.dataset.widgetId || container.getClientRects().length === 0) return;

        var widgetId = window.turnstile.render(container, {
          sitekey: container.dataset.sitekey,
          action: container.dataset.action,
          theme: 'auto',
          language: 'es',
          size: 'flexible'
        });
        container.dataset.widgetId = widgetId;
      });
    };

    window.resetTurnstileWidgets = function(root) {
      if (!window.turnstile) return;

      (root || document).querySelectorAll('.js-turnstile[data-widget-id]').forEach(function(container) {
        window.turnstile.reset(container.dataset.widgetId);
      });
    };

    window.onTurnstileLoad = function() {
      window.renderTurnstileWidgets(pendingRoot || document);
    };

      function loadForVisibleWidget() {
        var visibleWidget = Array.prototype.find.call(
          document.querySelectorAll('.js-turnstile'),
          function(widget) { return widget.getClientRects().length > 0; }
        );
        if (visibleWidget) window.loadTurnstile(visibleWidget.closest('form') || document);
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadForVisibleWidget, { once: true });
      } else {
        loadForVisibleWidget();
      }
    })();
  </script>
  <?php endif; ?>

  <!-- Custom Header Code (from admin settings) -->
  <?= $customHeadCode ?>

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

  <!-- Blog Carousel Script -->
  <script>
    (function() {
      var track = document.getElementById('newsCarousel');
      var viewport = document.getElementById('newsCarouselViewport');
      if (!track || !viewport) return;

      var originalSlides = Array.prototype.slice.call(track.children);
      if (!originalSlides.length) return;

      originalSlides.forEach(function(card) {
        track.appendChild(card.cloneNode(true));
      });

      var originalCount = originalSlides.length;
      var offset = 0;
      var slideWidth = 0;
      var totalWidth = 0;
      var animationId = 0;
      var lastTime = 0;
      var isInViewport = false;
      var isPointerDown = false;
      var isDragging = false;
      var isHovering = false;
      var startX = 0;
      var startY = 0;
      var startOffset = 0;
      var didDrag = false;
      var prefersReducedMotion = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      function measure() {
        var first = track.children[0];
        if (!first) return;

        slideWidth = first.getBoundingClientRect().width || first.offsetWidth || 0;
        totalWidth = slideWidth * originalCount;
        normalizeOffset();
        applyTransform(false);
      }

      function normalizeOffset() {
        if (!totalWidth) return;

        while (offset <= -totalWidth) {
          offset += totalWidth;
        }

        while (offset > 0) {
          offset -= totalWidth;
        }
      }

      function applyTransform(animated) {
        track.style.transition = animated ? 'transform 280ms ease' : 'none';
        track.style.transform = 'translate3d(' + offset + 'px, 0, 0)';
      }

      function snapToSlide() {
        if (!slideWidth) return;

        offset = Math.round(offset / slideWidth) * slideWidth;
        normalizeOffset();
        applyTransform(true);
      }

      function moveBySlide(direction) {
        if (!slideWidth) return;

        offset += direction * slideWidth;
        normalizeOffset();
        applyTransform(true);
      }

      function tick(now) {
        if (!lastTime) lastTime = now;
        var delta = Math.min(now - lastTime, 64);
        lastTime = now;

        if (!prefersReducedMotion && !isDragging && !isHovering && document.visibilityState === 'visible' && totalWidth > viewport.clientWidth) {
          offset -= delta * 0.045;
          normalizeOffset();
          applyTransform(false);
        }

        animationId = isInViewport ? requestAnimationFrame(tick) : 0;
      }

      function startAnimation() {
        if (animationId || !isInViewport || prefersReducedMotion || document.visibilityState !== 'visible') return;
        lastTime = 0;
        animationId = requestAnimationFrame(tick);
      }

      function stopAnimation() {
        if (animationId) cancelAnimationFrame(animationId);
        animationId = 0;
        lastTime = 0;
      }

      function endDrag(event) {
        if (!isPointerDown && !isDragging) return;

        isPointerDown = false;
        isDragging = false;
        viewport.classList.remove('is-dragging');

        if (event && event.pointerId !== undefined && viewport.releasePointerCapture) {
          try {
            viewport.releasePointerCapture(event.pointerId);
          } catch (error) {}
        }

        if (didDrag) {
          snapToSlide();
        }
      }

      viewport.addEventListener('mouseenter', function() {
        isHovering = true;
      });

      viewport.addEventListener('mouseleave', function() {
        isHovering = false;
        endDrag();
      });

      viewport.addEventListener('pointerdown', function(event) {
        if (event.button !== undefined && event.button !== 0) return;

        measure();
        isPointerDown = true;
        isDragging = false;
        didDrag = false;
        startX = event.clientX;
        startY = event.clientY;
        startOffset = offset;
      });

      viewport.addEventListener('pointermove', function(event) {
        if (!isPointerDown) return;

        var deltaX = event.clientX - startX;
        var deltaY = event.clientY - startY;

        if (!isDragging) {
          if (Math.abs(deltaX) < 12 || Math.abs(deltaX) < Math.abs(deltaY) * 1.2) return;

          isDragging = true;
          didDrag = true;
          viewport.classList.add('is-dragging');

          if (viewport.setPointerCapture && event.pointerId !== undefined) {
            viewport.setPointerCapture(event.pointerId);
          }
        }

        event.preventDefault();
        offset = startOffset + deltaX;
        normalizeOffset();
        applyTransform(false);
      });

      viewport.addEventListener('pointerup', endDrag);
      viewport.addEventListener('pointercancel', endDrag);

      viewport.addEventListener('click', function(event) {
        if (!didDrag) return;

        event.preventDefault();
        event.stopPropagation();
        didDrag = false;
      }, true);

      track.addEventListener('dragstart', function(event) {
        event.preventDefault();
      });

      viewport.addEventListener('keydown', function(event) {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;

        event.preventDefault();
        moveBySlide(event.key === 'ArrowRight' ? -1 : 1);
      });

      var resizeTimeout;
      window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
          offset = 0;
          measure();
        }, 160);
      });

      window.addEventListener('load', measure, { once: true });
      requestAnimationFrame(measure);
      if ('IntersectionObserver' in window) {
        var viewportObserver = new IntersectionObserver(function(entries) {
          isInViewport = entries[0].isIntersecting;
          if (isInViewport) startAnimation();
          else stopAnimation();
        }, { rootMargin: '150px 0px' });
        viewportObserver.observe(viewport);
      } else {
        isInViewport = true;
        startAnimation();
      }
      document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') startAnimation();
        else stopAnimation();
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
  <!-- AOS reveal animations -->
  <script>
    (function() {
      var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      function initAosWhenReady() {
        document.querySelectorAll('main > section, main > article > section').forEach(function(section, index) {
          if (!section.hasAttribute('data-aos')) {
            section.setAttribute('data-aos', 'fade-up');
          }
          if (!section.hasAttribute('data-aos-offset')) {
            section.setAttribute('data-aos-offset', index === 0 ? '40' : '80');
          }
        });

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

  <script>
    (function() {
      var jumpbar = document.querySelector('.service-jumpbar');
      if (!jumpbar) return;

      var header = document.getElementById('mainHeader');
      var placeholder = document.createElement('div');
      var ticking = false;
      placeholder.className = 'service-jumpbar-placeholder';
      placeholder.setAttribute('aria-hidden', 'true');
      jumpbar.parentNode.insertBefore(placeholder, jumpbar);

      function measureHeader() {
        var height = header ? Math.ceil(header.getBoundingClientRect().height) : 89;
        document.documentElement.style.setProperty('--main-header-height', height + 'px');
        return height;
      }

      function updateJumpbar() {
        ticking = false;

        var headerHeight = measureHeader();
        var barHeight = Math.ceil(jumpbar.getBoundingClientRect().height) || 60;
        var triggerTop = placeholder.getBoundingClientRect().top + window.pageYOffset;
        var shouldFix = window.pageYOffset + headerHeight >= triggerTop;

        document.documentElement.style.setProperty('--service-jumpbar-height', barHeight + 'px');

        if (shouldFix) {
          placeholder.style.height = barHeight + 'px';
          jumpbar.classList.add('is-fixed');
        } else {
          jumpbar.classList.remove('is-fixed');
          placeholder.style.height = '0px';
        }
      }

      function requestUpdate() {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(updateJumpbar);
      }

      requestUpdate();
      window.addEventListener('scroll', requestUpdate, { passive: true });
      window.addEventListener('resize', requestUpdate);
      window.addEventListener('load', requestUpdate);
    })();
  </script>

  <script>
    (function() {
      function ready(fn) {
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', fn, { once: true });
        } else {
          fn();
        }
      }

      ready(function() {
        var carousels = document.querySelectorAll('.client-logo-carousel');
        if (!carousels.length) return;

        var prefersReducedMotion = window.matchMedia &&
          window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        carousels.forEach(function(carousel) {
          var track = carousel.querySelector('.carousel-track');
          if (!track) return;

          var halfWidth = 0;
          var rafId = 0;
          var lastTime = 0;
          var isInViewport = false;
          var isDragging = false;
          var isHovering = false;
          var startX = 0;
          var startScroll = 0;
          var didDrag = false;

          function measure() {
            halfWidth = Math.floor(track.scrollWidth / 2);
            normalizeScroll();
          }

          function normalizeScroll() {
            if (!halfWidth || halfWidth <= carousel.clientWidth) return 0;

            var before = carousel.scrollLeft;
            if (carousel.scrollLeft >= halfWidth) {
              carousel.scrollLeft -= halfWidth;
            } else if (carousel.scrollLeft <= 0) {
              carousel.scrollLeft += halfWidth;
            }

            return carousel.scrollLeft - before;
          }

          function tick(now) {
            if (!lastTime) lastTime = now;
            var delta = Math.min(now - lastTime, 64);
            lastTime = now;

            if (!prefersReducedMotion && !isDragging && !isHovering && document.visibilityState === 'visible' && halfWidth > carousel.clientWidth) {
              carousel.scrollLeft += delta * 0.04;
              if (carousel.scrollLeft >= halfWidth) {
                carousel.scrollLeft -= halfWidth;
              }
            }

            rafId = isInViewport ? window.requestAnimationFrame(tick) : 0;
          }

          function startAnimation() {
            if (rafId || !isInViewport || prefersReducedMotion || document.visibilityState !== 'visible') return;
            lastTime = 0;
            rafId = window.requestAnimationFrame(tick);
          }

          function stopAnimation() {
            if (rafId) window.cancelAnimationFrame(rafId);
            rafId = 0;
            lastTime = 0;
          }

          function endDrag(event) {
            if (!isDragging) return;

            isDragging = false;
            carousel.classList.remove('is-dragging');

            if (event && event.pointerId !== undefined && carousel.releasePointerCapture) {
              try {
                carousel.releasePointerCapture(event.pointerId);
              } catch (error) {}
            }
          }

          measure();
          window.addEventListener('resize', measure);
          window.addEventListener('load', measure, { once: true });
          track.querySelectorAll('img').forEach(function(image) {
            if (!image.complete) {
              image.addEventListener('load', measure, { once: true });
            }
          });

          carousel.addEventListener('mouseenter', function() {
            isHovering = true;
          });

          carousel.addEventListener('mouseleave', function() {
            isHovering = false;
            endDrag();
          });

          carousel.addEventListener('pointerdown', function(event) {
            if (event.button !== undefined && event.button !== 0) return;

            measure();
            isDragging = true;
            didDrag = false;
            carousel.classList.add('is-dragging');
            startX = event.clientX;
            startScroll = carousel.scrollLeft;

            if (carousel.setPointerCapture && event.pointerId !== undefined) {
              carousel.setPointerCapture(event.pointerId);
            }
          });

          carousel.addEventListener('pointermove', function(event) {
            if (!isDragging) return;

            var deltaX = event.clientX - startX;
            if (Math.abs(deltaX) > 3) didDrag = true;

            carousel.scrollLeft = startScroll - deltaX;
            startScroll += normalizeScroll();
          });

          carousel.addEventListener('pointerup', endDrag);
          carousel.addEventListener('pointercancel', endDrag);

          carousel.addEventListener('click', function(event) {
            if (!didDrag) return;

            event.preventDefault();
            event.stopPropagation();
            didDrag = false;
          }, true);

          carousel.addEventListener('keydown', function(event) {
            if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;

            event.preventDefault();
            carousel.scrollLeft += event.key === 'ArrowRight' ? 160 : -160;
            normalizeScroll();
          });

          if ('IntersectionObserver' in window) {
            var carouselObserver = new IntersectionObserver(function(entries) {
              isInViewport = entries[0].isIntersecting;
              if (isInViewport) startAnimation();
              else stopAnimation();
            }, { rootMargin: '150px 0px' });
            carouselObserver.observe(carousel);
          } else {
            isInViewport = true;
            startAnimation();
          }
          document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') startAnimation();
            else stopAnimation();
          });
        });
      });
    })();
  </script>

  <!-- Custom Body Code (from admin settings) -->
  <?= $customBodyCode ?>
  <?php if ($deferredTawkUrl): ?>
  <script>
    (function() {
      var originalTitle = document.title;
      var titleElement = document.querySelector('title');
      var tawkLoaded = false;
      var fallbackTimer = 0;

      function preserveSiteTitle() {
        if (document.title !== originalTitle) document.title = originalTitle;
      }

      if (titleElement && 'MutationObserver' in window) {
        new MutationObserver(preserveSiteTitle).observe(titleElement, {
          childList: true,
          characterData: true,
          subtree: true
        });
      }

      window.Tawk_API = window.Tawk_API || {};
      var previousOnLoad = window.Tawk_API.onLoad;
      window.Tawk_API.onLoad = function() {
        preserveSiteTitle();
        if (typeof previousOnLoad === 'function') previousOnLoad();
      };

      function removeInteractionListeners() {
        window.removeEventListener('pointerdown', loadTawk);
        window.removeEventListener('touchstart', loadTawk);
        window.removeEventListener('keydown', loadTawk);
        window.removeEventListener('wheel', loadTawk);
      }

      function loadTawk() {
        if (tawkLoaded) return;
        tawkLoaded = true;
        removeInteractionListeners();
        if (fallbackTimer) window.clearTimeout(fallbackTimer);

        window.Tawk_LoadStart = new Date();
        var script = document.createElement('script');
        script.async = true;
        script.src = <?= json_encode($deferredTawkUrl, JSON_UNESCAPED_SLASHES) ?>;
        script.charset = 'UTF-8';
        script.setAttribute('crossorigin', '*');
        script.addEventListener('load', preserveSiteTitle, { once: true });
        document.body.appendChild(script);
      }

      window.addEventListener('pointerdown', loadTawk, { once: true, passive: true });
      window.addEventListener('touchstart', loadTawk, { once: true, passive: true });
      window.addEventListener('keydown', loadTawk, { once: true });
      window.addEventListener('wheel', loadTawk, { once: true, passive: true });
      fallbackTimer = window.setTimeout(loadTawk, 12000);
    })();
  </script>
  <?php endif; ?>
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
