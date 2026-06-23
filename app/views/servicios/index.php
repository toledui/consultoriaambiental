<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
  .hero-video-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    transform: translate(-50%, -50%);
    object-fit: cover;
  }

  /* Swiper Custom Styles */
  .swiper-pagination-bullet {
    background-color: #90A4AE;
    opacity: 0.5;
  }
  .swiper-pagination-bullet-active {
    background-color: #2E7D32;
    opacity: 1;
  }

  /* Swiper cursor - horizontal resize arrows */
  .servicesSwiper {
    cursor: ew-resize;
  }
  .servicesSwiper:active {
    cursor: ew-resize;
  }

  /* Center slide highlight effect */
  .servicesSwiper .swiper-slide {
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    transform: scale(0.92);
  }
  .servicesSwiper .swiper-slide.swiper-slide-active {
    transform: scale(1);
    z-index: 2;
  }
  .servicesSwiper .swiper-slide.swiper-slide-active .service-card {
    box-shadow: 0 20px 40px -10px rgba(46,125,50,0.2);
    border-color: rgba(102, 187, 106, 0.4);
  }

  .service-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px -10px rgba(46,125,50,0.15);
    border-color: rgba(102, 187, 106, 0.3);
  }
  .icon-wrapper {
    transition: all 0.4s ease;
  }
  .service-card:hover .icon-wrapper {
    background-color: #2E7D32;
    color: white;
    transform: scale(1.1) rotate(5deg);
  }
</style>

<!-- Inner Hero Section -->
<section class="relative pt-32 pb-20 md:pt-40 md:pb-28 border-b-8 border-ca-green overflow-hidden">
  <!-- Video Background -->
  <video class="hero-video-bg" autoplay muted loop playsinline poster="<?= BASE_URL ?>/images/consultoria-ambiental-logo.webp">
    <source src="<?= BASE_URL ?>/images/videopaginaservicios.mp4" type="video/mp4">
  </video>
  <!-- Overlay -->
  <div class="absolute inset-0" style="background-color: #00000080;"></div>
  
  <div class="container mx-auto px-4 md:px-8 relative z-10 text-center">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-ca-light-green/20 border border-ca-light-green/30 text-ca-light-green font-semibold text-sm mb-4 backdrop-blur-sm uppercase tracking-widest">
      Cat&aacute;logo de Soluciones
    </div>
    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight">
      Soluciones Ambientales <br class="hidden md:block"/>
      <span class="text-ca-light-green">Integrales</span>
    </h1>
    <p class="text-lg md:text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed font-light">
      Aseguramos la operatividad de su empresa mediante estrategias t&eacute;cnicas y regulatorias. Conozca nuestro portafolio especializado.
    </p>
  </div>
</section>

<!-- General Services Carousel Section -->
<section class="py-24 pattern-bg relative z-20 overflow-hidden">
  <div class="container mx-auto px-4 md:px-8 max-w-7xl">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
      <div>
        <h4 class="text-ca-green font-bold tracking-widest uppercase text-sm mb-2">&Aacute;reas de Especialidad</h4>
        <h2 class="text-3xl md:text-4xl font-bold text-ca-navy">Cat&aacute;logo de Servicios</h2>
        <p class="text-gray-500 mt-3 max-w-xl">Deslice para explorar nuestras soluciones ambientales. Haga clic en cada una para conocer detalles, normativas y beneficios espec&iacute;ficos.</p>
      </div>
      
      <!-- Custom Navigation Buttons (Desktop) -->
      <div class="hidden md:flex gap-3">
        <button class="swiper-prev w-12 h-12 rounded-full border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-ca-green hover:text-white hover:border-ca-green transition-colors focus:outline-none">
          <i class="fas fa-arrow-left"></i>
        </button>
        <button class="swiper-next w-12 h-12 rounded-full border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-ca-green hover:text-white hover:border-ca-green transition-colors focus:outline-none">
          <i class="fas fa-arrow-right"></i>
        </button>
      </div>
    </div>

    <?php if (!empty($services)): ?>
    <!-- Swiper Container -->
    <div class="swiper servicesSwiper !pb-16">
      <div class="swiper-wrapper">
        <?php foreach ($services as $service): ?>
        <div class="swiper-slide h-auto">
          <div class="service-card bg-white rounded-2xl p-8 border border-gray-100 h-full flex flex-col group">
            <div class="icon-wrapper w-16 h-16 bg-ca-bg rounded-xl flex items-center justify-center text-ca-green text-3xl mb-8 shadow-sm">
              <i class="<?= htmlspecialchars($service['icon'] ?? 'fas fa-leaf') ?>"></i>
            </div>
            <h3 class="text-xl font-bold text-ca-navy mb-3"><?= htmlspecialchars($service['title']) ?></h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-8 flex-grow">
              <?= htmlspecialchars($service['description']) ?>
            </p>
            <a href="<?= BASE_URL ?>/servicios/<?= htmlspecialchars($service['slug']) ?>" class="mt-auto inline-flex items-center text-ca-dark-gray font-bold text-sm group-hover:text-ca-green transition-colors">
              Ver detalles del servicio <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <!-- Pagination (Dots) -->
      <div class="swiper-pagination"></div>
    </div>
    <?php else: ?>
    <div class="text-center py-20">
      <i class="fas fa-concierge-bell text-6xl text-ca-light-gray mb-6"></i>
      <p class="text-xl text-gray-500">No hay servicios disponibles en este momento.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Packages / Modalities Section -->
<section id="esquemas-contratacion" class="py-24 bg-white border-t border-gray-200 scroll-mt-32 md:scroll-mt-40">
  <div class="container mx-auto px-4 md:px-8 max-w-7xl">
    <div class="text-center mb-16">
      <h4 class="text-ca-green font-bold tracking-widest uppercase text-sm mb-2">Esquemas de Contrataci&oacute;n</h4>
      <h2 class="text-3xl md:text-4xl font-bold text-ca-navy">Modalidades de Trabajo</h2>
      <p class="text-gray-500 max-w-2xl mx-auto mt-4">Planes estructurados para cubrir desde necesidades puntuales hasta el acompa&ntilde;amiento integral de un departamento ambiental externo.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      
      <!-- Tier 1: Gestión por Evento -->
      <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-xl transition-shadow flex flex-col relative">
        <div class="mb-6 border-b border-gray-100 pb-6">
          <h3 class="text-xl font-bold text-ca-navy mb-2">Gesti&oacute;n por Evento</h3>
          <p class="text-sm text-gray-500 font-medium">Ideal para micro y peque&ntilde;as empresas.</p>
        </div>
        <div class="flex-grow">
          <ul class="space-y-4 text-sm text-gray-600">
            <li class="flex items-start gap-3"><i class="fas fa-check text-ca-light-green mt-1"></i> <span>Gesti&oacute;n y resoluci&oacute;n de un <strong>tr&aacute;mite espec&iacute;fico</strong> requerido por la autoridad.</span></li>
            <li class="flex items-start gap-3"><i class="fas fa-check text-ca-light-green mt-1"></i> <span>Asesor&iacute;a puntual.</span></li>
          </ul>
        </div>
        <div class="mt-8 pt-6">
          <a href="<?= BASE_URL ?>/contacto?paquete=gestion-evento" class="block text-center border-2 border-ca-navy text-ca-navy hover:bg-ca-navy hover:text-white font-bold py-3 px-4 rounded-xl transition-colors">Cotizar Tr&aacute;mite</a>
        </div>
      </div>

      <!-- Tier 2: Acompañamiento y Regularización (Highlighted) -->
      <div class="bg-ca-navy rounded-2xl border border-ca-navy p-8 shadow-2xl transform md:-translate-y-4 flex flex-col relative overflow-hidden">
        <div class="absolute top-0 right-0 bg-ca-light-green text-ca-navy text-xs font-bold px-3 py-1 rounded-bl-lg uppercase tracking-wider">M&aacute;s Popular</div>
        <div class="mb-6 border-b border-gray-700 pb-6">
          <h3 class="text-xl font-bold text-white mb-2">Acompa&ntilde;amiento <br/>y Regularizaci&oacute;n</h3>
          <p class="text-sm text-ca-light-green font-medium">Para peque&ntilde;as y medianas empresas.</p>
        </div>
        <div class="flex-grow">
          <p class="text-sm text-gray-300 mb-4 font-medium">Sustituye o apoya al departamento ambiental interno:</p>
          <ul class="space-y-3 text-sm text-gray-300">
            <li class="flex items-start gap-3"><i class="fas fa-check text-ca-light-green mt-1"></i> <span>Diagn&oacute;stico inicial completo.</span></li>
            <li class="flex items-start gap-3"><i class="fas fa-check text-ca-light-green mt-1"></i> <span>Registro/Plan residuos (ME/Peligrosos).</span></li>
            <li class="flex items-start gap-3"><i class="fas fa-check text-ca-light-green mt-1"></i> <span>Registro descarga agua / LAU / COA.</span></li>
            <li class="flex items-start gap-3"><i class="fas fa-check text-ca-light-green mt-1"></i> <span>Se&ntilde;al&eacute;tica b&aacute;sica de residuos.</span></li>
            <li class="flex items-start gap-3"><i class="fas fa-check text-ca-light-green mt-1"></i> <span>Atenci&oacute;n t&eacute;cnica ante inspecciones.</span></li>
            <li class="flex items-start gap-3"><i class="fas fa-check text-ca-light-green mt-1"></i> <span><strong>Visitas de auditor&iacute;a cuatrimestrales.</strong></span></li>
          </ul>
        </div>
        <div class="mt-8 pt-6">
          <a href="<?= BASE_URL ?>/contacto?paquete=acompanamiento-regularizacion" class="block text-center bg-ca-green hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-lg">Solicitar Evaluaci&oacute;n</a>
        </div>
      </div>

      <!-- Tier 3: Acompañamiento para Certificación -->
      <div class="bg-white rounded-2xl border border-ca-light-green p-8 shadow-md hover:shadow-xl transition-shadow flex flex-col relative bg-gradient-to-b from-white to-green-50/50">
        <div class="mb-6 border-b border-gray-200 pb-6">
          <h3 class="text-xl font-bold text-ca-navy mb-2">Acompa&ntilde;amiento <br/>para Certificaci&oacute;n</h3>
          <p class="text-sm text-ca-green font-medium">Compromiso Total y Prestigio.</p>
        </div>
        <div class="flex-grow">
          <p class="text-sm text-gray-600 mb-4 font-bold border-b border-gray-100 pb-2">Todo lo del paquete Regularizaci&oacute;n +</p>
          <ul class="space-y-4 text-sm text-gray-600">
            <li class="flex items-start gap-3"><i class="fas fa-star text-yellow-500 mt-1"></i> <span>Proceso integral de certificaci&oacute;n ambiental en el Programa de Cumplimiento Voluntario (SEMADET).</span></li>
            <li class="flex items-start gap-3"><i class="fas fa-star text-yellow-500 mt-1"></i> <span>Auditor&iacute;a de validaci&oacute;n y gesti&oacute;n de distintivos.</span></li>
          </ul>
        </div>
        <div class="mt-8 pt-6">
          <a href="<?= BASE_URL ?>/contacto?paquete=acompanamiento-certificacion" class="block text-center border-2 border-ca-green text-ca-green hover:bg-ca-green hover:text-white font-bold py-3 px-4 rounded-xl transition-colors">Agendar Cita</a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CTA Banner -->
<section class="bg-ca-green py-12 relative overflow-hidden">
  <div class="container mx-auto px-4 relative z-10 text-center">
    <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">&iquest;Tiene dudas sobre las obligaciones de su sector?</h3>
    <a href="<?= BASE_URL ?>/contacto" class="inline-block bg-white text-ca-green font-bold py-3 px-8 rounded-full shadow-lg hover:bg-gray-100 transition-colors">
      Hablemos de su proyecto
    </a>
  </div>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Carousel Logic Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  var swiperEl = document.querySelector('.servicesSwiper');
  if (!swiperEl) return;

  var swiper = new Swiper('.servicesSwiper', {
    slidesPerView: 1,
    spaceBetween: 24,
    centeredSlides: true,
    initialSlide: 1,
    loop: false,
    
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
      dynamicBullets: true,
    },
    
    navigation: {
      nextEl: '.swiper-next',
      prevEl: '.swiper-prev',
    },

    breakpoints: {
      768: {
        slidesPerView: 2,
        spaceBetween: 32
      },
      1024: {
        slidesPerView: 3,
        spaceBetween: 40
      }
    },
  });
});
</script>
