<section class="pt-32 pb-24 bg-white">
  <div class="container mx-auto px-4 md:px-8 max-w-4xl">
    
    <?php if ($post): ?>
      <a href="<?= BASE_URL ?>/blog" class="inline-flex items-center text-ca-green hover:text-ca-navy transition-colors mb-8">
        <i class="fas fa-arrow-left mr-2"></i> Volver al blog
      </a>
      
      <article>
        <?php if ($post['featured_image']): ?>
          <div class="rounded-2xl overflow-hidden mb-10 shadow-lg">
            <img alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-80 object-cover" src="<?= htmlspecialchars($post['featured_image']) ?>"/>
          </div>
        <?php endif; ?>
        
        <div class="flex items-center gap-4 text-sm text-ca-light-gray mb-6 flex-wrap">
          <?php if (!empty($post['category_name'])): ?>
            <a href="<?= BASE_URL ?>/blog?categoria=<?= htmlspecialchars($post['category_slug']) ?>" class="inline-flex items-center gap-1.5 bg-ca-navy/10 text-ca-navy font-semibold px-3 py-1 rounded-full hover:bg-ca-navy hover:text-white transition-colors">
              <i class="fas fa-folder text-xs"></i>
              <?= htmlspecialchars($post['category_name']) ?>
            </a>
          <?php endif; ?>
          <span><i class="far fa-calendar-alt mr-1"></i> <?= date('d M Y', strtotime($post['created_at'])) ?></span>
        </div>
        
        <h1 class="text-4xl md:text-5xl font-extrabold text-ca-navy mb-6 leading-tight">
          <?= htmlspecialchars($post['title']) ?>
        </h1>
        
        <?php if ($post['excerpt']): ?>
          <p class="text-xl text-gray-500 mb-8 leading-relaxed"><?= htmlspecialchars($post['excerpt']) ?></p>
        <?php endif; ?>
        
        <div class="prose prose-lg max-w-none text-ca-dark-gray leading-relaxed">
          <?= $post['content'] ?>
        </div>
      </article>
      
    <?php else: ?>
      <div class="text-center py-20">
        <i class="fas fa-exclamation-circle text-6xl text-ca-light-gray mb-6"></i>
        <h1 class="text-3xl font-bold text-ca-navy mb-4">Artículo no encontrado</h1>
        <p class="text-gray-500 mb-8">El artículo que buscas no existe o ha sido eliminado.</p>
        <a href="<?= BASE_URL ?>/blog" class="inline-flex items-center bg-ca-green hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full transition-colors">
          <i class="fas fa-arrow-left mr-2"></i> Volver al blog
        </a>
      </div>
    <?php endif; ?>
    
  </div>
</section>
