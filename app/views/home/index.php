<?php require VIEWS_DIR . '/partials/hero.php'; ?>

<!-- Inspection Alert Banner -->
<div class="bg-gradient-to-r from-ca-light-green to-ca-green py-4 shadow-inner relative z-20">
  <div class="container mx-auto px-4 flex flex-col md:flex-row justify-center items-center text-ca-navy font-bold text-center gap-4">
    <div class="flex items-center text-white text-sm md:text-base">
      <div class="bg-white/20 p-2 rounded-full mr-3 animate-bounce">
        <i class="fas fa-exclamation-triangle text-yellow-300"></i>
      </div>
      ¿Recibiste una visita de inspección de PROEPA o PROFEPA? Te ayudamos a revisar observaciones, integrar documentación y regularizar tu empresa.
    </div>
    <a href="<?= BASE_URL ?>/contacto" class="text-sm bg-ca-navy text-white px-5 py-2 rounded-full hover:bg-ca-dark-gray transition-colors shadow-md whitespace-nowrap">
      Evita multas y clausuras <i class="fas fa-shield-alt ml-1"></i>
    </a>
  </div>
</div>

<!-- Stats Grid -->
<section class="py-12 bg-white border-b border-gray-100 relative -mt-4 rounded-t-3xl z-20 shadow-sm mx-4 md:mx-8" data-aos="fade-up">
  <div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 divide-y md:divide-y-0 md:divide-x divide-gray-200">
      <div class="text-center p-4">
        <div class="flex justify-center items-center mb-2">
          <span class="text-5xl font-extrabold text-ca-navy"><span class="counter" data-target="10" data-suffix="+">0</span></span>
        </div>
        <h2 class="text-ca-dark-gray font-bold text-lg uppercase tracking-wide">Años de experiencia</h2>
        <p class="text-sm text-gray-500 mt-1">Especializada en el medio</p>
      </div>
      <div class="text-center p-4">
        <div class="flex justify-center items-center mb-2">
          <span class="text-5xl font-extrabold text-ca-navy"><span class="counter" data-target="1000" data-suffix="+">0</span></span>
        </div>
        <h2 class="text-ca-dark-gray font-bold text-lg uppercase tracking-wide">Proyectos de gestión</h2>
        <p class="text-sm text-gray-500 mt-1">Y regularización ambiental</p>
      </div>
      <div class="text-center p-4">
        <div class="flex justify-center items-center mb-2">
          <i class="fas fa-map-marked-alt text-4xl text-ca-green mb-1"></i>
        </div>
        <h2 class="text-ca-dark-gray font-bold text-lg uppercase tracking-wide">Cobertura Nacional</h2>
        <p class="text-sm text-gray-500 mt-1">Atención en distintos sectores</p>
      </div>
    </div>
  </div>
</section>

<!-- About / Value Proposition Section -->
<section class="py-24 pattern-bg relative">
  <div class="container mx-auto px-4 md:px-8">
    <div class="flex flex-col lg:flex-row items-center gap-16">
      
      <!-- Text Content -->
      <div class="w-full lg:w-1/2" data-aos="fade-right">
        <div class="flex items-center gap-3 mb-4">
          <span class="h-1 w-12 bg-ca-green rounded"></span>
          <p class="text-ca-green font-bold tracking-widest uppercase text-sm">Por qué elegirnos</p>
        </div>
        <h2 class="text-3xl md:text-5xl font-bold text-ca-navy mb-6 leading-tight">
          Acompañamiento ambiental con <span class="text-ca-green relative inline-block">experiencia técnica y normativa
            <svg class="absolute w-full h-3 -bottom-1 left-0 text-ca-light-green/30" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="8" fill="transparent"/></svg>
          </span>
        </h2>
        <p class="text-ca-dark-gray text-lg mb-6 leading-relaxed">
          Somos un equipo multidisciplinario de ingenieros y biólogos especializados en consultoría ambiental, auditoría, regulación y cumplimiento normativo. Acompañamos a empresas, industrias y desarrollos inmobiliarios en procesos de regularización, permisos, auditorías ambientales e inspecciones.
        </p>
        <p class="text-gray-600 text-base mb-8 leading-relaxed">
          Regularizamos empresas ante autoridades ambientales municipales, estatales y federales para evitar multas, clausuras y riesgos operativos. Nuestro enfoque combina diagnóstico técnico, conocimiento regulatorio y seguimiento personalizado para que cada empresa pueda operar con mayor seguridad, legalidad y continuidad.
        </p>
        
        <div class="grid grid-cols-2 gap-4 mb-8">
          <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-ca-green text-xl"></i>
            <span class="font-semibold text-ca-navy">+10 años de experiencia</span>
          </div>
          <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-ca-green text-xl"></i>
            <span class="font-semibold text-ca-navy">+60 MIA aprobadas</span>
          </div>
          <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-ca-green text-xl"></i>
            <span class="font-semibold text-ca-navy">+140 inspecciones atendidas</span>
          </div>
          <div class="flex items-center gap-3">
            <i class="fas fa-check-circle text-ca-green text-xl"></i>
            <span class="font-semibold text-ca-navy">Cobertura en Jalisco y Guadalajara</span>
          </div>
        </div>
        
        <a href="<?= BASE_URL ?>/nosotros" class="inline-flex font-bold text-ca-green hover:text-ca-navy transition-colors items-center group">
          Conoce más sobre nosotros
          <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
        </a>
      </div>

      <!-- Image Layout -->
      <div class="w-full lg:w-1/2 relative" data-aos="fade-left">
        <div class="rounded-2xl overflow-hidden shadow-2xl relative z-10 border-4 border-white">
          <div id="aboutSlideshow" class="relative w-full aspect-[4/3]">
            <img alt="Impacto ambiental imagen 1" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-700" src="<?= BASE_URL ?>/images/impacto%20ambiental%20imagen1.webp" data-index="0"/>
            <img alt="Impacto ambiental imagen 2" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-700" src="<?= BASE_URL ?>/images/impacto%20ambiental%20imagen2.webp" data-index="1"/>
            <img alt="Impacto ambiental imagen 3" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-700" src="<?= BASE_URL ?>/images/impacto%20ambiental%20imagen3.webp" data-index="2"/>
          </div>
        </div>
        <div class="absolute -bottom-8 -left-8 bg-ca-navy p-6 rounded-xl shadow-xl z-20 hidden md:block">
          <div class="flex items-center gap-4 text-white">
            <div class="bg-ca-light-green p-3 rounded-full">
              <i class="fas fa-award text-2xl"></i>
            </div>
            <div>
              <p class="font-bold text-xl"><span class="counter" data-target="60" data-suffix="+">0</span></p>
              <p class="text-xs uppercase tracking-wider text-gray-300">MIA's Aprobadas</p>
            </div>
          </div>
        </div>
        <div class="absolute top-8 -right-8 w-full h-full border-2 border-ca-green rounded-2xl z-0 hidden lg:block"></div>
      </div>
    </div>
  </div>
</section>

<!-- Services Section -->
<section class="py-24 bg-white relative">
  <div class="container mx-auto px-4 text-center mb-16" data-aos="fade-up">
    <p class="text-ca-green font-bold tracking-widest uppercase text-sm mb-2">Servicios Ambientales</p>
    <h2 class="text-3xl md:text-5xl font-bold text-ca-navy mb-4">
      Servicios de Consultoría Ambiental
    </h2>
    <p class="text-gray-500 max-w-2xl mx-auto text-lg">
      Gestión de residuos, emisiones, descargas de aguas residuales, manifestaciones de impacto ambiental, certificación empresarial y atención a inspecciones PROEPA/PROFEPA para empresas e industrias.
    </p>
  </div>

  <div class="container mx-auto px-4 md:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto" data-aos-group="true">
      
      <?php if (!empty($services)): ?>
        <?php foreach ($services as $service): ?>
          <div class="bg-ca-bg rounded-2xl p-8 border border-gray-100 hover:shadow-xl hover:border-ca-green transition-all duration-300 group flex flex-col h-full" data-aos="fade-up">
            <div class="w-16 h-16 bg-white shadow-sm border border-gray-100 text-ca-green rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:bg-ca-green group-hover:text-white transition-colors">
              <i class="<?= htmlspecialchars($service['icon'] ?? 'fas fa-leaf') ?>"></i>
            </div>
            <h3 class="font-bold text-ca-navy text-xl mb-3 group-hover:text-ca-green transition-colors"><?= htmlspecialchars($service['title']) ?></h3>
            <p class="text-gray-600 text-sm mb-6 flex-grow"><?= htmlspecialchars($service['description']) ?></p>
            <a href="<?= BASE_URL ?>/servicios/<?= htmlspecialchars($service['slug']) ?>" class="text-ca-navy font-semibold text-sm inline-flex items-center group-hover:text-ca-green">
              Ver detalles <i class="fas fa-chevron-right ml-1 text-xs"></i>
            </a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Static fallback services -->
        <div class="bg-ca-bg rounded-2xl p-8 border border-gray-100 hover:shadow-xl hover:border-ca-green transition-all duration-300 group flex flex-col h-full" data-aos="fade-up">
          <div class="w-16 h-16 bg-white shadow-sm border border-gray-100 text-ca-green rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:bg-ca-green group-hover:text-white transition-colors">
            <i class="fas fa-dumpster"></i>
          </div>
          <h3 class="font-bold text-ca-navy text-xl mb-3 group-hover:text-ca-green transition-colors">Gestión de Residuos</h3>
          <p class="text-gray-600 text-sm mb-6 flex-grow">Registro como generador, planes de manejo, COA, señalética y recolección de residuos de manejo especial para empresas e industrias.</p>
          <a href="<?= BASE_URL ?>/servicios" class="text-ca-navy font-semibold text-sm inline-flex items-center group-hover:text-ca-green">
            Ver detalles <i class="fas fa-chevron-right ml-1 text-xs"></i>
          </a>
        </div>

        <div class="bg-ca-bg rounded-2xl p-8 border border-gray-100 hover:shadow-xl hover:border-ca-green transition-all duration-300 group flex flex-col h-full" data-aos="fade-up">
          <div class="w-16 h-16 bg-white shadow-sm border border-gray-100 text-ca-green rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:bg-ca-green group-hover:text-white transition-colors">
            <i class="fas fa-industry"></i>
          </div>
          <h3 class="font-bold text-ca-navy text-xl mb-3 group-hover:text-ca-green transition-colors">Emisiones a la Atmósfera</h3>
          <p class="text-gray-600 text-sm mb-6 flex-grow">Licencia Ambiental Única, planes de contingencia, análisis de emisiones y reportes ambientales para cumplimiento normativo.</p>
          <a href="<?= BASE_URL ?>/servicios" class="text-ca-navy font-semibold text-sm inline-flex items-center group-hover:text-ca-green">
            Ver detalles <i class="fas fa-chevron-right ml-1 text-xs"></i>
          </a>
        </div>

        <div class="bg-ca-bg rounded-2xl p-8 border border-gray-100 hover:shadow-xl hover:border-ca-green transition-all duration-300 group flex flex-col h-full" data-aos="fade-up">
          <div class="w-16 h-16 bg-white shadow-sm border border-gray-100 text-ca-green rounded-xl flex items-center justify-center text-3xl mb-6 group-hover:bg-ca-green group-hover:text-white transition-colors">
            <i class="fas fa-water"></i>
          </div>
          <h3 class="font-bold text-ca-navy text-xl mb-3 group-hover:text-ca-green transition-colors">Descargas de Aguas Residuales</h3>
          <p class="text-gray-600 text-sm mb-6 flex-grow">Registro, regularización y cumplimiento para descargas de aguas residuales ante la autoridad ambiental correspondiente.</p>
          <a href="<?= BASE_URL ?>/servicios" class="text-ca-navy font-semibold text-sm inline-flex items-center group-hover:text-ca-green">
            Ver detalles <i class="fas fa-chevron-right ml-1 text-xs"></i>
          </a>
        </div>
      <?php endif; ?>

    </div>
    
    <div class="text-center mt-12">
      <a href="<?= BASE_URL ?>/servicios" class="inline-flex justify-center items-center bg-ca-navy hover:bg-gray-800 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-colors">
        Ver Catálogo Completo de Servicios
      </a>
    </div>
  </div>
</section>

<!-- Sectors Section -->
<section class="py-20 bg-ca-navy text-white relative border-t border-ca-green" data-aos="fade-up">
  <div class="absolute inset-0 opacity-5 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
  
  <div class="container mx-auto px-4 relative z-10 text-center">
    <h2 class="text-3xl md:text-4xl font-bold mb-10">
      Sectores de Atención Especializada
    </h2>
    <div class="flex flex-wrap justify-center gap-3 md:gap-4 max-w-5xl mx-auto">
      <span class="px-6 py-3 bg-white/5 hover:bg-ca-green border border-ca-light-gray/30 hover:border-ca-green rounded-full text-sm font-medium transition-colors cursor-default">Inmobiliario</span>
      <span class="px-6 py-3 bg-white/5 hover:bg-ca-green border border-ca-light-gray/30 hover:border-ca-green rounded-full text-sm font-medium transition-colors cursor-default">Tequilero</span>
      <span class="px-6 py-3 bg-white/5 hover:bg-ca-green border border-ca-light-gray/30 hover:border-ca-green rounded-full text-sm font-medium transition-colors cursor-default">Alimenticio</span>
      <span class="px-6 py-3 bg-white/5 hover:bg-ca-green border border-ca-light-gray/30 hover:border-ca-green rounded-full text-sm font-medium transition-colors cursor-default">Agropecuario</span>
      <span class="px-6 py-3 bg-white/5 hover:bg-ca-green border border-ca-light-gray/30 hover:border-ca-green rounded-full text-sm font-medium transition-colors cursor-default">Tratamiento de Residuos</span>
      <span class="px-6 py-3 bg-white/5 hover:bg-ca-green border border-ca-light-gray/30 hover:border-ca-green rounded-full text-sm font-medium transition-colors cursor-default">Industrial (Ligero, Mediano, Pesado)</span>
      <span class="px-6 py-3 bg-white/5 hover:bg-ca-green border border-ca-light-gray/30 hover:border-ca-green rounded-full text-sm font-medium transition-colors cursor-default">Químico</span>
      <span class="px-6 py-3 bg-white/5 hover:bg-ca-green border border-ca-light-gray/30 hover:border-ca-green rounded-full text-sm font-medium transition-colors cursor-default">Metalurgia</span>
      <span class="px-6 py-3 bg-white/5 hover:bg-ca-green border border-ca-light-gray/30 hover:border-ca-green rounded-full text-sm font-medium transition-colors cursor-default">Farmacéutica</span>
    </div>
  </div>
</section>

<!-- Clients / Success Cases Section -->
<section class="py-24 bg-white overflow-hidden" data-aos="fade-up">
  <div class="container mx-auto px-4 text-center max-w-7xl">
    <p class="text-ca-green font-bold tracking-widest uppercase text-sm mb-2">Confianza que respalda</p>
    <h2 class="text-3xl md:text-4xl font-bold text-ca-navy mb-16">
      Nuestros Clientes y Proyectos
    </h2>
    
    <div class="relative">
      <!-- Logo Carousel Track -->
      <div id="clientCarouselHome" class="flex overflow-hidden">
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
<section class="bg-ca-green py-12 relative overflow-hidden" data-aos="fade-up">
  <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/3 -translate-y-1/4">
      <i class="fas fa-leaf text-[200px] text-white"></i>
  </div>
  <div class="container mx-auto px-4 relative z-10 flex flex-col md:flex-row items-center justify-between">
    <div class="text-white mb-6 md:mb-0">
      <h3 class="text-2xl md:text-3xl font-bold mb-2">¿Necesitas regularizar tu empresa ante autoridades ambientales?</h3>
      <p class="text-green-100">Solicita un diagnóstico ambiental confidencial y recibe una propuesta de regularización en 48 horas.</p>
    </div>
    <div class="flex gap-4">
      <a href="<?= BASE_URL ?>/contacto" class="bg-white text-ca-green font-bold py-3 px-8 rounded-full shadow-lg hover:bg-gray-100 transition-colors">
        Solicitar Diagnóstico Ambiental
      </a>
    </div>
  </div>
</section>

<!-- Latest News Section -->
<section class="py-24 bg-white relative" data-aos="fade-up">
  <div class="container mx-auto px-4 md:px-8">
    <div class="text-center mb-12">
      <p class="text-ca-green font-bold tracking-widest uppercase text-sm mb-2">Mantente Informado</p>
      <h2 class="text-3xl md:text-5xl font-bold text-ca-navy">
        Últimas Noticias
      </h2>
    </div>

    <div class="relative max-w-5xl mx-auto overflow-hidden">
      <div id="newsCarousel" class="flex" style="will-change: transform;">
        
        <?php if (!empty($blogPosts)): ?>
          <?php foreach ($blogPosts as $post): ?>
            <div class="min-w-[50%] lg:min-w-[33.333%] px-2 flex-shrink-0">
              <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="flex items-center bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:border-ca-light-green transition-all duration-300 overflow-hidden group h-24">
                <div class="w-24 h-24 flex-shrink-0 overflow-hidden bg-ca-bg">
                  <?php if (!empty($post['featured_image'])): ?>
                    <img alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="<?= htmlspecialchars(asset_prefer_webp($post['featured_image'])) ?>"/>
                  <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-ca-light-gray text-2xl"><i class="fas fa-newspaper"></i></div>
                  <?php endif; ?>
                </div>
                <div class="w-0 flex-1 p-3 overflow-hidden">
                  <?php if (!empty($post['category_name'])): ?>
                    <span class="text-[10px] font-semibold text-ca-green uppercase tracking-wider"><?= htmlspecialchars($post['category_name']) ?></span>
                  <?php endif; ?>
                  <h3 class="text-xs font-bold text-ca-navy leading-tight group-hover:text-ca-green transition-colors break-words mt-0.5">
                    <?= htmlspecialchars($post['title']) ?>
                  </h3>
                  <?php if (!empty($post['created_at'])): ?>
                    <span class="text-[10px] text-gray-400 mt-1 block"><?= date('d M Y', strtotime($post['created_at'])) ?></span>
                  <?php endif; ?>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Static fallback news cards -->
          <div class="min-w-[50%] lg:min-w-[33.333%] px-2 flex-shrink-0">
            <div class="flex items-center bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:border-ca-light-green transition-all duration-300 overflow-hidden group h-24">
              <div class="w-24 h-24 flex-shrink-0 overflow-hidden bg-ca-bg">
                <div class="w-full h-full flex items-center justify-center text-ca-light-gray text-2xl"><i class="fas fa-newspaper"></i></div>
              </div>
              <div class="w-0 flex-1 p-3 overflow-hidden">
                <h3 class="text-xs font-bold text-ca-navy leading-tight break-words">
                  Próximamente artículos y noticias del sector ambiental
                </h3>
              </div>
            </div>
          </div>

          <div class="min-w-[50%] lg:min-w-[33.333%] px-2 flex-shrink-0">
            <div class="flex items-center bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:border-ca-light-green transition-all duration-300 overflow-hidden group h-24">
              <div class="w-24 h-24 flex-shrink-0 overflow-hidden bg-ca-bg">
                <div class="w-full h-full flex items-center justify-center text-ca-light-gray text-2xl"><i class="fas fa-newspaper"></i></div>
              </div>
              <div class="w-0 flex-1 p-3 overflow-hidden">
                <h3 class="text-xs font-bold text-ca-navy leading-tight break-words">
                  Mantente al día con nuestras publicaciones
                </h3>
              </div>
            </div>
          </div>

          <div class="min-w-[50%] lg:min-w-[33.333%] px-2 flex-shrink-0">
            <div class="flex items-center bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:border-ca-light-green transition-all duration-300 overflow-hidden group h-24">
              <div class="w-24 h-24 flex-shrink-0 overflow-hidden bg-ca-bg">
                <div class="w-full h-full flex items-center justify-center text-ca-light-gray text-2xl"><i class="fas fa-newspaper"></i></div>
              </div>
              <div class="w-0 flex-1 p-3 overflow-hidden">
                <h3 class="text-xs font-bold text-ca-navy leading-tight break-words">
                  Suscríbete para recibir contenido exclusivo
                </h3>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>

    <div class="text-center mt-10">
      <a href="<?= BASE_URL ?>/blog" class="inline-flex items-center text-ca-green font-semibold hover:text-ca-navy transition-colors group">
        Ver todas las noticias
        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
      </a>
    </div>
  </div>
</section>
