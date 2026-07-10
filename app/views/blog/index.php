<!-- Hero Section -->
<section class="hero-blog-bg pt-40 pb-28">
  <div class="container mx-auto px-4 md:px-8 text-center">
    <span class="inline-block bg-white/20 backdrop-blur-sm text-white text-sm font-semibold px-5 py-2 rounded-full mb-6">
      <i class="fas fa-newspaper mr-2"></i>Actualidad y Normatividad
    </span>
    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-5 leading-tight">
      Blog de Cumplimiento Ambiental
    </h1>
    <p class="text-white/80 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
      Noticias, artículos y análisis sobre normativa ambiental, sostenibilidad y gestión de residuos en México.
    </p>
  </div>
</section>

<!-- Blog Content + Sidebar -->
<section class="py-16 bg-white">
  <div class="container mx-auto px-4 md:px-8">
    <div class="flex flex-col lg:flex-row gap-12">

      <!-- Main Content: Blog Posts Grid -->
      <div class="w-full lg:w-2/3">
        <?php if (!empty($posts)): ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($posts as $post): ?>
              <?php $displayDate = $post['published_at'] ?? $post['created_at']; ?>
              <article class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                <!-- Featured Image -->
                <div class="relative overflow-hidden h-52">
                  <?php if ($post['featured_image']): ?>
                    <img alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="<?= htmlspecialchars(asset_prefer_webp($post['featured_image'])) ?>"/>
                  <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-ca-navy to-ca-green flex items-center justify-center">
                      <i class="fas fa-leaf text-5xl text-white/30"></i>
                    </div>
                  <?php endif; ?>
                  <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                    <?php if (!empty($post['category_name'])): ?>
                      <span class="bg-ca-navy text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                        <i class="fas fa-folder mr-1"></i><?= htmlspecialchars($post['category_name']) ?>
                      </span>
                    <?php endif; ?>
                    <span class="bg-ca-green text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                      <i class="far fa-calendar-alt mr-1"></i><?= date('d M Y', strtotime($displayDate)) ?>
                    </span>
                  </div>
                </div>
                <!-- Content -->
                <div class="p-6 flex flex-col flex-1">
                  <h3 class="text-xl font-bold text-ca-navy mb-3 group-hover:text-ca-green transition-colors leading-snug">
                    <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>"><?= htmlspecialchars($post['title']) ?></a>
                  </h3>
                  <?php if ($post['excerpt']): ?>
                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-4"><?= htmlspecialchars($post['excerpt']) ?></p>
                  <?php endif; ?>
                  <div class="mt-auto">
                    <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="inline-flex items-center text-ca-green font-semibold text-sm hover:text-ca-navy transition-colors group/link">
                      Leer artículo completo
                      <i class="fas fa-arrow-right ml-2 text-xs group-hover/link:translate-x-1 transition-transform"></i>
                    </a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <!-- Pagination -->
          <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
            <nav class="mt-12 flex justify-center items-center gap-2" aria-label="Paginación del blog">
              <!-- Previous -->
              <?php if ($pagination['currentPage'] > 1): ?>
                <a href="?page=<?= $pagination['currentPage'] - 1 ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-300 text-gray-500 hover:bg-ca-green hover:text-white hover:border-ca-green transition-all duration-200">
                  <i class="fas fa-chevron-left text-sm"></i>
                </a>
              <?php else: ?>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 text-gray-300 cursor-not-allowed">
                  <i class="fas fa-chevron-left text-sm"></i>
                </span>
              <?php endif; ?>

              <!-- Page Numbers -->
              <?php
                $total = $pagination['totalPages'];
                $current = $pagination['currentPage'];
                $start = max(1, $current - 2);
                $end = min($total, $current + 2);
                if ($start > 1): ?>
                  <a href="?page=1" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-300 text-gray-600 hover:bg-ca-green hover:text-white hover:border-ca-green transition-all duration-200 text-sm font-medium">1</a>
                  <?php if ($start > 2): ?>
                    <span class="text-gray-400 px-1">...</span>
                  <?php endif;
                endif;
                for ($i = $start; $i <= $end; $i++): ?>
                  <?php if ($i === $current): ?>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-ca-green text-white text-sm font-bold shadow-md"><?= $i ?></span>
                  <?php else: ?>
                    <a href="?page=<?= $i ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-300 text-gray-600 hover:bg-ca-green hover:text-white hover:border-ca-green transition-all duration-200 text-sm font-medium"><?= $i ?></a>
                  <?php endif; ?>
                <?php endfor;
                if ($end < $total):
                  if ($end < $total - 1): ?>
                    <span class="text-gray-400 px-1">...</span>
                  <?php endif; ?>
                  <a href="?page=<?= $total ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-300 text-gray-600 hover:bg-ca-green hover:text-white hover:border-ca-green transition-all duration-200 text-sm font-medium"><?= $total ?></a>
                <?php endif; ?>

              <!-- Next -->
              <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                <a href="?page=<?= $pagination['currentPage'] + 1 ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-300 text-gray-500 hover:bg-ca-green hover:text-white hover:border-ca-green transition-all duration-200">
                  <i class="fas fa-chevron-right text-sm"></i>
                </a>
              <?php else: ?>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 text-gray-300 cursor-not-allowed">
                  <i class="fas fa-chevron-right text-sm"></i>
                </span>
              <?php endif; ?>
            </nav>
          <?php endif; ?>

        <?php else: ?>
          <!-- Empty State -->
          <div class="text-center py-20">
            <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
              <i class="fas fa-newspaper text-4xl text-ca-light-gray"></i>
            </div>
            <h3 class="text-2xl font-bold text-ca-navy mb-3">No hay artículos publicados aún</h3>
            <p class="text-gray-500 max-w-md mx-auto">Vuelve pronto para conocer nuestras últimas noticias y actualizaciones sobre normativa ambiental.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar -->
      <aside class="w-full lg:w-1/3 space-y-8">

        <!-- Search Widget -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
          <h3 class="text-lg font-bold text-ca-navy mb-4 flex items-center">
            <i class="fas fa-search text-ca-green mr-3 text-sm"></i>
            Buscar en el Blog
          </h3>
          <form action="<?= BASE_URL ?>/blog" method="GET" class="relative">
            <input type="text" name="s" placeholder="Buscar artículos..." value="<?= htmlspecialchars($_GET['s'] ?? '') ?>" class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:border-ca-green focus:ring-2 focus:ring-ca-green/20 outline-none transition-all text-sm"/>
            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 bg-ca-green hover:bg-green-700 text-white rounded-lg flex items-center justify-center transition-colors">
              <i class="fas fa-arrow-right text-xs"></i>
            </button>
          </form>
        </div>

        <!-- Categories Widget -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
          <h3 class="text-lg font-bold text-ca-navy mb-4 flex items-center">
            <i class="fas fa-folder-open text-ca-green mr-3 text-sm"></i>
            Categorías
          </h3>
          <ul class="space-y-3">
            <li>
              <a href="<?= BASE_URL ?>/blog" class="flex items-center justify-between group">
                <span class="text-gray-600 group-hover:text-ca-green transition-colors text-sm <?= empty($activeCategory) ? 'font-bold text-ca-green' : '' ?>">
                  <i class="fas fa-chevron-right text-xs mr-2 <?= empty($activeCategory) ? 'text-ca-green' : 'text-ca-light-gray group-hover:text-ca-green' ?> transition-colors"></i>
                  Todos los artículos
                </span>
                <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-0.5 rounded-full"><?= $pagination['total'] ?? 0 ?></span>
              </a>
            </li>
            <?php if (!empty($categories)): ?>
              <?php foreach ($categories as $cat): ?>
                <li>
                  <a href="<?= BASE_URL ?>/blog?categoria=<?= htmlspecialchars($cat['slug']) ?>" class="flex items-center justify-between group">
                    <span class="text-gray-600 group-hover:text-ca-green transition-colors text-sm <?= ($activeCategory ?? '') === $cat['slug'] ? 'font-bold text-ca-green' : '' ?>">
                      <i class="fas fa-chevron-right text-xs mr-2 <?= ($activeCategory ?? '') === $cat['slug'] ? 'text-ca-green' : 'text-ca-light-gray group-hover:text-ca-green' ?> transition-colors"></i>
                      <?= htmlspecialchars($cat['name']) ?>
                    </span>
                    <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-0.5 rounded-full"><?= (int)$cat['post_count'] ?></span>
                  </a>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Locations / SEO Widget -->
        <div class="bg-ca-navy rounded-2xl shadow-lg p-6 text-white">
          <h3 class="text-lg font-bold mb-4 flex items-center">
            <i class="fas fa-map-marker-alt text-ca-light-green mr-3 text-sm"></i>
            Presencia en México
          </h3>
          <p class="text-white/70 text-sm mb-4 leading-relaxed">
            Brindamos servicios de consultoría ambiental en toda la República Mexicana.
          </p>
          <ul class="space-y-2">
            <li class="flex items-center text-sm text-white/80">
              <i class="fas fa-check-circle text-ca-light-green mr-3 text-xs"></i>
              Ciudad de México
            </li>
            <li class="flex items-center text-sm text-white/80">
              <i class="fas fa-check-circle text-ca-light-green mr-3 text-xs"></i>
              Estado de México
            </li>
            <li class="flex items-center text-sm text-white/80">
              <i class="fas fa-check-circle text-ca-light-green mr-3 text-xs"></i>
              Nuevo León
            </li>
            <li class="flex items-center text-sm text-white/80">
              <i class="fas fa-check-circle text-ca-light-green mr-3 text-xs"></i>
              Jalisco
            </li>
            <li class="flex items-center text-sm text-white/80">
              <i class="fas fa-check-circle text-ca-light-green mr-3 text-xs"></i>
              Querétaro
            </li>
          </ul>
        </div>

        <!-- CTA Banner -->
        <div class="bg-gradient-to-br from-ca-green to-green-700 rounded-2xl shadow-lg p-6 text-center text-white">
          <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-white/20 flex items-center justify-center">
            <i class="fas fa-headset text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold mb-2">¿Necesitas Asesoría?</h3>
          <p class="text-white/80 text-sm mb-6 leading-relaxed">
            Contáctanos para recibir orientación especializada en cumplimiento ambiental.
          </p>
          <a href="<?= BASE_URL ?>/contacto" class="inline-block bg-white text-ca-green font-bold px-6 py-3 rounded-full hover:bg-gray-100 transition-colors shadow-md">
            <i class="fas fa-phone-alt mr-2"></i>Contactar ahora
          </a>
        </div>

      </aside>
    </div>
  </div>
</section>

<!-- Newsletter Section -->
<section class="py-20 bg-ca-bg pattern-bg">
  <div class="container mx-auto px-4 md:px-8 max-w-3xl text-center">
    <span class="inline-block bg-ca-green/10 text-ca-green text-sm font-semibold px-4 py-1.5 rounded-full mb-4">
      <i class="fas fa-envelope-open-text mr-2"></i>Mantente Actualizado
    </span>
    <h2 class="text-3xl md:text-4xl font-extrabold text-ca-navy mb-4">
      Suscríbete a nuestro Newsletter
    </h2>
    <p class="text-gray-500 mb-8 max-w-xl mx-auto">
      Recibe las últimas noticias sobre normativa ambiental, sostenibilidad y consejos de cumplimiento directamente en tu correo.
    </p>
    <?php if (isset($_SESSION['newsletter_success'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl max-w-lg mx-auto mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <span class="text-sm font-medium"><?= htmlspecialchars($_SESSION['newsletter_success']) ?></span>
      </div>
      <?php unset($_SESSION['newsletter_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['newsletter_error'])): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl max-w-lg mx-auto mb-6 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span class="text-sm font-medium"><?= htmlspecialchars($_SESSION['newsletter_error']) ?></span>
      </div>
      <?php unset($_SESSION['newsletter_error']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/blog/newsletter" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
      <input type="email" name="correo" placeholder="Tu correo electrónico" required class="flex-1 px-5 py-3.5 rounded-xl border border-gray-300 focus:border-ca-green focus:ring-2 focus:ring-ca-green/20 outline-none transition-all text-sm"/>
      <button type="submit" class="bg-ca-green hover:bg-green-700 text-white font-bold px-8 py-3.5 rounded-xl transition-colors shadow-md whitespace-nowrap">
        Suscribirse <i class="fas fa-paper-plane ml-2 text-sm"></i>
      </button>
    </form>
    <p class="text-xs text-gray-400 mt-4">
      <i class="fas fa-shield-alt mr-1"></i>Sin spam. Puedes darte de baja en cualquier momento.
    </p>
  </div>
</section>
