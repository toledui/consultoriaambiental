<!-- Inner Hero Section -->
<section class="hero-inner-bg relative pt-32 pb-20 md:pt-40 md:pb-28 border-b-8 border-ca-green">
  <!-- Overlay -->
  <div class="absolute inset-0" style="background-color: #00000080;"></div>
  
  <div class="container mx-auto px-4 md:px-8 relative z-10 text-center">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-ca-light-green/20 border border-ca-light-green/30 text-ca-light-green font-semibold text-sm mb-4 backdrop-blur-sm uppercase tracking-widest">
      Nuestra Esencia
    </div>
    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight">
      Especialistas en Gestión y <br/>
      <span class="text-ca-light-green">Cumplimiento Ambiental</span>
    </h1>
    <p class="text-lg md:text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed font-light">
      Acompañamos a nuestros clientes con soluciones técnicas, legales y operativas orientadas a reducir riesgos y garantizar una operación sostenible.
    </p>
  </div>
</section>

<!-- Who We Are & What We Do -->
<section class="py-20 md:py-28 bg-white relative">
  <div class="container mx-auto px-4 md:px-8">
    <div class="flex flex-col lg:flex-row gap-16 items-center">
      
      <!-- Text Content -->
      <div class="w-full lg:w-1/2 gsap-reveal" data-gsap="fade-left">
        <div class="flex items-center gap-3 mb-4">
          <span class="h-1 w-12 bg-ca-green rounded"></span>
          <p class="text-ca-navy font-bold tracking-widest uppercase text-sm">¿Quiénes Somos?</p>
        </div>
        
        <h2 class="text-3xl md:text-4xl font-bold text-ca-dark-gray mb-6 leading-tight">
          Un equipo multidisciplinario con <span class="text-ca-green">más de 10 años de experiencia</span>
        </h2>
        
        <p class="text-gray-600 text-lg mb-6 leading-relaxed">
          Somos ingenieros y biólogos especializados en consultoría ambiental, auditoría, regulación y cumplimiento normativo. Contamos con experiencia en la atención de asuntos para empresas, industrias y desarrollos inmobiliarios, incluyendo <strong>experiencia previa dentro de autoridades estatales ambientales</strong>.
        </p>
        
        <p class="text-gray-600 text-lg mb-8 leading-relaxed">
          Ayudamos a nuestros clientes a identificar, diagnosticar y resolver sus necesidades mediante estrategias adaptadas a cada proyecto. <strong>Nuestro objetivo:</strong> Que las empresas operen de manera segura, sostenible y conforme a la legislación.
        </p>

        <div class="bg-ca-bg p-6 rounded-xl border border-gray-100 shadow-inner mb-8">
          <h3 class="font-bold text-ca-navy mb-4 border-b border-gray-200 pb-2">Brindamos acompañamiento especializado en:</h3>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-y-3 gap-x-4 text-sm text-gray-700 font-medium">
            <div class="flex items-center gap-2"><i class="fas fa-check text-ca-light-green"></i> Impacto ambiental</div>
            <div class="flex items-center gap-2"><i class="fas fa-check text-ca-light-green"></i> Gestión de residuos</div>
            <div class="flex items-center gap-2"><i class="fas fa-check text-ca-light-green"></i> Regularización</div>
            <div class="flex items-center gap-2"><i class="fas fa-check text-ca-light-green"></i> Agua y energía</div>
            <div class="flex items-center gap-2"><i class="fas fa-check text-ca-light-green"></i> Cumplimiento</div>
            <div class="flex items-center gap-2"><i class="fas fa-check text-ca-light-green"></i> Licencias y permisos</div>
            <div class="flex items-center gap-2"><i class="fas fa-check text-ca-light-green"></i> Auditorías</div>
            <div class="flex items-center gap-2"><i class="fas fa-check text-ca-light-green"></i> Inspecciones</div>
            <div class="flex items-center gap-2"><i class="fas fa-check text-ca-light-green"></i> Seguridad ambiental</div>
          </div>
        </div>
      </div>

      <!-- Image Layout -->
      <div class="w-full lg:w-1/2 relative gsap-reveal" data-gsap="fade-right">
        <div class="rounded-2xl overflow-hidden shadow-2xl relative z-10 border-8 border-white bg-ca-bg">
          <div id="nosotrosSlideshow" class="relative w-full" style="padding-bottom: 100%;">
            <?php
            $nosotrosImages = [
              'agua y energía.webp',
              'gestion de residuos.webp',
              'inspecciones.webp',
              'licenciasypermisos.webp',
            ];
            foreach ($nosotrosImages as $index => $img):
            ?>
              <img alt="Nosotros - <?= htmlspecialchars(pathinfo($img, PATHINFO_FILENAME)) ?>" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700 <?= $index === 0 ? 'opacity-100' : 'opacity-0' ?>" src="<?= BASE_URL ?>/images/nosotros/<?= rawurlencode($img) ?>" data-index="<?= $index ?>"/>
            <?php endforeach; ?>
          </div>
        </div>
        <!-- Badge -->
        <div class="absolute top-1/2 -left-8 md:-left-12 transform -translate-y-1/2 bg-ca-navy p-6 rounded-2xl shadow-xl z-20 border border-gray-700">
          <div class="text-center text-white">
            <span class="block text-4xl font-extrabold text-ca-light-green mb-1"><span class="counter" data-target="10" data-suffix="+">0</span></span>
            <span class="block text-xs uppercase tracking-widest font-semibold">Años de<br/>Trayectoria</span>
          </div>
        </div>
        <!-- Decor -->
        <div class="absolute -bottom-6 -right-6 w-full h-full border-4 border-ca-green rounded-2xl z-0 opacity-20"></div>
      </div>

    </div>
  </div>
</section>

<!-- Stats / Compromiso que respalda -->
<section class="py-20 bg-ca-navy text-white relative gsap-reveal" data-gsap="fade-up">
  <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
  <div class="container mx-auto px-4 md:px-8 relative z-10">
    
    <div class="text-center mb-16">
      <h2 class="text-3xl md:text-4xl font-bold mb-4">Compromiso que respalda nuestra experiencia</h2>
      <p class="text-ca-light-gray max-w-2xl mx-auto text-lg">Cifras que demuestran nuestra capacidad técnica y efectividad en la gestión ambiental a nivel nacional.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 gsap-stagger" data-stagger-delay="0.1">
      
      <div class="bg-gray-800/50 backdrop-blur rounded-xl p-6 border border-gray-700 text-center hover:bg-gray-800 transition-colors gsap-stagger-item">
        <div class="text-4xl font-extrabold text-ca-light-green mb-3"><span class="counter" data-target="60" data-suffix="+">0</span></div>
        <h3 class="font-bold text-sm uppercase tracking-wider mb-2">MIA's Aprobadas</h3>
        <p class="text-xs text-gray-400">Para proyectos inmobiliarios, industriales, tequileros y de residuos.</p>
      </div>

      <div class="bg-gray-800/50 backdrop-blur rounded-xl p-6 border border-gray-700 text-center hover:bg-gray-800 transition-colors gsap-stagger-item">
        <div class="text-4xl font-extrabold text-ca-light-green mb-3"><span class="counter" data-target="140" data-suffix="+">0</span></div>
        <h3 class="font-bold text-sm uppercase tracking-wider mb-2">Inspecciones Atendidas</h3>
        <p class="text-xs text-gray-400">Ante PROEPA con éxito, logrando reducciones de multa de hasta el 100%.</p>
      </div>

      <div class="bg-gray-800/50 backdrop-blur rounded-xl p-6 border border-gray-700 text-center hover:bg-gray-800 transition-colors gsap-stagger-item">
        <div class="text-4xl font-extrabold text-ca-light-green mb-3"><span class="counter" data-target="20" data-suffix="+">0</span></div>
        <h3 class="font-bold text-sm uppercase tracking-wider mb-2">Empresas Certificadas</h3>
        <p class="text-xs text-gray-400">Dentro del Programa de Cumplimiento Ambiental Voluntario de SEMADET.</p>
      </div>

      <div class="bg-gray-800/50 backdrop-blur rounded-xl p-6 border border-gray-700 text-center hover:bg-gray-800 transition-colors gsap-stagger-item">
        <div class="text-4xl font-extrabold text-ca-light-green mb-3"><span class="counter" data-target="300" data-suffix="+">0</span></div>
        <h3 class="font-bold text-sm uppercase tracking-wider mb-2">Trámites Gestionados</h3>
        <p class="text-xs text-gray-400">COA, LAU, registros como generador y planes de manejo de residuos.</p>
      </div>

      <div class="bg-gray-800/50 backdrop-blur rounded-xl p-6 border border-gray-700 text-center hover:bg-gray-800 transition-colors lg:col-start-auto md:col-span-2 lg:col-span-1 gsap-stagger-item">
        <div class="text-4xl font-extrabold text-ca-light-green mb-3"><span class="counter" data-target="230" data-suffix="+">0</span></div>
        <h3 class="font-bold text-sm uppercase tracking-wider mb-2">Clientes en Recolección</h3>
        <p class="text-xs text-gray-400">Clientes satisfechos en servicios de recolección de manejo especial.</p>
      </div>

    </div>
  </div>
</section>

<!-- ¿Por qué elegirnos? & Valores -->
<section class="py-20 pattern-bg border-b border-gray-200">
  <div class="container mx-auto px-4 md:px-8 max-w-7xl">
    <div class="flex flex-col md:flex-row gap-12 items-center">
      
      <div class="w-full md:w-1/2 gsap-reveal" data-gsap="fade-left">
        <h2 class="text-3xl md:text-4xl font-bold text-ca-navy mb-6">¿Por qué elegirnos?</h2>
        <p class="text-gray-600 text-lg mb-4">
          Conocemos profundamente las obligaciones de distintos sectores económicos. Nos mantenemos actualizados en la legislación local y requerimientos de la autoridad.
        </p>
        <div class="bg-white p-5 rounded-lg border-l-4 border-ca-green shadow-sm mb-6">
          <p class="font-semibold text-ca-dark-gray">
            Brindamos propuestas para cada cliente buscando una solución efectiva y positiva, operando bajo un <strong>estricto apego a la legalidad y transparencia técnica</strong>, sin recurrir a prácticas irregulares con la autoridad.
          </p>
        </div>
      </div>

      <div class="w-full md:w-1/2 gsap-reveal" data-gsap="fade-right">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-ca-light-green/20 rounded-full flex items-center justify-center text-ca-green text-xl"><i class="fas fa-handshake"></i></div>
            <span class="font-bold text-ca-navy">Honestidad</span>
          </div>
          <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-ca-light-green/20 rounded-full flex items-center justify-center text-ca-green text-xl"><i class="fas fa-shield-alt"></i></div>
            <span class="font-bold text-ca-navy">Responsabilidad</span>
          </div>
          <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-ca-light-green/20 rounded-full flex items-center justify-center text-ca-green text-xl"><i class="fas fa-check-double"></i></div>
            <span class="font-bold text-ca-navy">Compromiso</span>
          </div>
          <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-ca-light-green/20 rounded-full flex items-center justify-center text-ca-green text-xl"><i class="fas fa-balance-scale"></i></div>
            <span class="font-bold text-ca-navy text-sm">Transparencia / Legalidad</span>
          </div>
          <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow sm:col-span-2">
            <div class="w-12 h-12 bg-ca-light-green/20 rounded-full flex items-center justify-center text-ca-green text-xl"><i class="fas fa-bullseye"></i></div>
            <span class="font-bold text-ca-navy">Asertividad y Efectividad</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Proceso de Trabajo -->
<section class="py-24 bg-white gsap-reveal" data-gsap="fade-up">
  <div class="container mx-auto px-4 md:px-8 max-w-6xl">
    <div class="text-center mb-16">
      <p class="text-ca-green font-bold tracking-widest uppercase text-sm mb-2">Nuestra Metodología</p>
      <h2 class="text-3xl md:text-4xl font-bold text-ca-navy mb-4">Más que consultoría, soluciones estratégicas</h2>
      <p class="text-gray-500 max-w-2xl mx-auto text-lg">Nos enfocamos en resolver problemas ambientales de manera práctica, técnica y eficiente. A diferencia de una consultoría tradicional, te acompañamos durante todo el proceso:</p>
    </div>

    <!-- Desktop connecting line (behind circles) -->
    <div class="hidden md:block absolute top-8 left-[10%] right-[10%] h-0.5 bg-gray-200 z-0"></div>

    <div class="relative">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-8 gsap-stagger" data-stagger-delay="0.1">
        
        <!-- Step 1 -->
        <div class="timeline-item relative text-center group gsap-stagger-item">
          <div class="relative w-16 h-16 mx-auto bg-ca-navy rounded-full flex items-center justify-center text-white text-xl font-bold mb-4 shadow-lg group-hover:bg-ca-green group-hover:scale-110 transition-all z-10 border-4 border-white">
            1
          </div>
          <h3 class="font-bold text-ca-navy mb-2">Diagnóstico y evaluación</h3>
          <p class="text-sm text-gray-500">Identificamos riesgos, incumplimientos y necesidades regulatorias.</p>
        </div>

        <!-- Step 2 -->
        <div class="timeline-item relative text-center group gsap-stagger-item">
          <div class="relative w-16 h-16 mx-auto bg-ca-navy rounded-full flex items-center justify-center text-white text-xl font-bold mb-4 shadow-lg group-hover:bg-ca-green group-hover:scale-110 transition-all z-10 border-4 border-white">
            2
          </div>
          <h3 class="font-bold text-ca-navy mb-2">Evaluación de cumplimiento</h3>
          <p class="text-sm text-gray-500">Analizamos la situación ambiental y normativa de la operación o proyecto.</p>
        </div>

        <!-- Step 3 -->
        <div class="timeline-item relative text-center group gsap-stagger-item">
          <div class="relative w-16 h-16 mx-auto bg-ca-navy rounded-full flex items-center justify-center text-white text-xl font-bold mb-4 shadow-lg group-hover:bg-ca-green group-hover:scale-110 transition-all z-10 border-4 border-white">
            3
          </div>
          <h3 class="font-bold text-ca-navy mb-2">Gestión de permisos</h3>
          <p class="text-sm text-gray-500">Desarrollamos y gestionamos la documentación necesaria ante autoridades.</p>
        </div>

        <!-- Step 4 -->
        <div class="timeline-item relative text-center group gsap-stagger-item">
          <div class="relative w-16 h-16 mx-auto bg-ca-navy rounded-full flex items-center justify-center text-white text-xl font-bold mb-4 shadow-lg group-hover:bg-ca-green group-hover:scale-110 transition-all z-10 border-4 border-white">
            4
          </div>
          <h3 class="font-bold text-ca-navy mb-2">Seguimiento y resolución</h3>
          <p class="text-sm text-gray-500">Acompañamiento técnico durante inspecciones, procesos y requerimientos.</p>
        </div>

        <!-- Step 5 -->
        <div class="timeline-item relative text-center group gsap-stagger-item">
          <div class="relative w-16 h-16 mx-auto bg-ca-navy rounded-full flex items-center justify-center text-white text-xl font-bold mb-4 shadow-lg group-hover:bg-ca-green group-hover:scale-110 transition-all z-10 border-4 border-white">
            5
          </div>
          <h3 class="font-bold text-ca-navy mb-2">Monitoreo y mejora</h3>
          <p class="text-sm text-gray-500">Ayudamos a mantener el cumplimiento ambiental a largo plazo.</p>
        </div>

      </div>
    </div>
    
    <div class="mt-16 text-center">
      <p class="text-ca-navy font-medium italic border-t border-b border-gray-100 py-4 max-w-3xl mx-auto">
        "Nuestro enfoque combina experiencia técnica, conocimiento regulatorio y acompañamiento personalizado para reducir riesgos y facilitar la operación de cada empresa."
      </p>
    </div>
  </div>
</section>

<!-- Sectors & Coverage -->
<section class="py-20 bg-ca-bg border-t border-gray-200 relative overflow-hidden gsap-reveal" data-gsap="fade-up">
  <div class="container mx-auto px-4 md:px-8 relative z-10">
    
    <div class="flex flex-col lg:flex-row gap-16">
      
      <!-- Sectors -->
      <div class="w-full lg:w-2/3">
        <h3 class="text-2xl font-bold text-ca-navy mb-8 border-l-4 border-ca-green pl-4">Experiencia en Múltiples Sectores Productivos</h3>
        
        <div class="space-y-6">
          <!-- Sector Inmobiliario -->
          <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
              <i class="fas fa-city text-2xl text-ca-green"></i>
              <h3 class="text-xl font-bold text-ca-dark-gray">Sector Inmobiliario</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Desarrollamos manifestaciones de impacto ambiental, estrategias de cumplimiento, gestión y recolección de residuos (construcción y operación) para:</p>
            <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Torres departamentales</span>
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Fraccionamientos</span>
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Parques industriales</span>
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Lotificaciones</span>
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Plazas comerciales</span>
            </div>
          </div>

          <!-- Sector Tequilero -->
          <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
              <i class="fas fa-glass-whiskey text-2xl text-ca-green"></i>
              <h3 class="text-xl font-bold text-ca-dark-gray">Industria Tequilera</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Atendemos proyectos de nuevas plantas tequileras y procesos de regularización para operaciones existentes. Servicios relacionados con impacto ambiental, gestión de residuos y cumplimiento normativo.</p>
          </div>

          <!-- Industria General -->
          <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-3">
              <i class="fas fa-industry text-2xl text-ca-green"></i>
              <h3 class="text-xl font-bold text-ca-dark-gray">Industria en General</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">Ayudamos a regularizar operaciones, obtener permisos y mantener el cumplimiento continuo en sectores como:</p>
            <div class="flex flex-wrap gap-2">
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Alimenticio</span>
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Agropecuario</span>
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Automotriz</span>
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Farmacéutico</span>
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Metal mecánico</span>
              <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Industrial pesado</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Coverage Map Area -->
      <div class="w-full lg:w-1/3 flex flex-col justify-center">
        <div class="bg-ca-navy p-8 rounded-2xl text-white text-center shadow-xl relative overflow-hidden">
          <div class="absolute inset-0 opacity-10 flex items-center justify-center">
            <i class="fas fa-map-marker-alt text-[200px]"></i>
          </div>
          <div class="relative z-10">
            <div class="w-16 h-16 bg-ca-green rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
              <i class="fas fa-globe-americas text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-4">Cobertura Nacional</h3>
            <p class="text-gray-300 text-sm leading-relaxed mb-6">
              Brindamos atención y acompañamiento ambiental en proyectos ubicados en distintas regiones del país, trabajando de manera coordinada con autoridades municipales, estatales y federales.
            </p>
            <a href="<?= BASE_URL ?>/contacto" class="inline-block border border-ca-light-green text-ca-light-green hover:bg-ca-light-green hover:text-ca-navy font-bold py-2 px-6 rounded-full transition-colors text-sm">
              Consultar Cobertura
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Clients Section -->
<section class="py-16 bg-white overflow-hidden gsap-reveal" data-gsap="fade-up">
  <div class="container mx-auto px-4 text-center max-w-7xl">
    <p class="text-ca-green font-bold tracking-widest uppercase text-sm mb-2">Empresas que confían</p>
    <h2 class="text-2xl md:text-3xl font-bold text-ca-navy mb-10">
      Hemos colaborado en procesos de regularización, auditoría y cumplimiento
    </h2>
    
    <div class="relative">
      <!-- Logo Carousel Track -->
      <div id="clientCarouselNosotros" class="flex overflow-hidden">
        <div class="flex gap-12 md:gap-16 items-center carousel-track">
          <?php
          $logosPath = PUBLIC_DIR . '/images/logos clientes';
          $logoFiles = glob($logosPath . '/*.{webp,svg,gif}', GLOB_BRACE);
          if (!empty($logoFiles)):
            // Shuffle for variety
            shuffle($logoFiles);
            // Render each logo twice for seamless loop
            for ($round = 0; $round < 2; $round++):
              foreach ($logoFiles as $logo):
                $relativePath = 'images/logos clientes/' . rawurlencode(basename($logo));
          ?>
            <div class="flex-shrink-0 w-28 md:w-36 h-20 md:h-24 flex items-center justify-center grayscale hover:grayscale-0 transition-all duration-300 hover:scale-110">
              <img alt="Logo cliente" class="max-w-full max-h-full object-contain" src="<?= BASE_URL ?>/<?= $relativePath ?>" loading="lazy"/>
            </div>
          <?php
              endforeach;
            endfor;
          endif;
          ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Pre-Footer -->
<section class="bg-ca-green py-12 relative overflow-hidden gsap-reveal" data-gsap="fade-up">
  <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/3 -translate-y-1/4">
      <i class="fas fa-handshake text-[200px] text-white"></i>
  </div>
  <div class="container mx-auto px-4 relative z-10 flex flex-col md:flex-row items-center justify-between">
    <div class="text-white mb-6 md:mb-0">
      <h3 class="text-2xl md:text-3xl font-bold mb-2">¿Buscas un aliado estratégico en gestión ambiental?</h3>
      <p class="text-green-100">Garantizamos una gestión transparente y eficiente para tu empresa.</p>
    </div>
    <div class="flex gap-4">
      <a href="<?= BASE_URL ?>/contacto" class="bg-white text-ca-green font-bold py-3 px-8 rounded-full shadow-lg hover:bg-gray-100 transition-colors">
        Hablar con un experto
      </a>
    </div>
  </div>
</section>

<!-- Custom Styles for this page -->
<style>
  .hero-inner-bg {
    background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
    background-size: cover;
    background-position: center 30%;
    background-attachment: fixed;
  }
</style>

<!-- Slideshow Script -->
<script>
(function() {
  const container = document.getElementById('nosotrosSlideshow');
  if (!container) return;
  const images = container.querySelectorAll('img');
  if (images.length < 2) return;

  let current = 0;
  let interval;
  const delay = 4000;

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
