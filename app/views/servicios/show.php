<section class="pt-24 pb-0 bg-white">
  <div>
    
    <?php if ($service): ?>
      
      <?php if ($service['content']): ?>
        <div class="prose prose-lg max-w-none text-ca-dark-gray leading-relaxed">
          <?= $service['content'] ?>
        </div>
      <?php endif; ?>
      
    <?php else: ?>
      <div class="text-center py-20">
        <i class="fas fa-exclamation-circle text-6xl text-ca-light-gray mb-6"></i>
        <h1 class="text-3xl font-bold text-ca-navy mb-4">Servicio no encontrado</h1>
        <p class="text-gray-500 mb-8">El servicio que buscas no existe o ha sido eliminado.</p>
        <a href="<?= BASE_URL ?>/servicios" class="inline-flex items-center bg-ca-green hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full transition-colors">
          <i class="fas fa-arrow-left mr-2"></i> Ver todos los servicios
        </a>
      </div>
    <?php endif; ?>
    
  </div>
</section>
