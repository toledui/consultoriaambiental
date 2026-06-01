<div class="max-w-4xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-ca-navy">Configuración</h1>
      <p class="text-gray-500 text-sm mt-1">Administra la configuración del sistema</p>
    </div>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="mb-6 p-4 rounded-lg <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' ?>">
      <div class="flex items-center gap-2">
        <i class="fas <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <span><?= $_SESSION['flash_message'] ?></span>
      </div>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <!-- Tabs -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="border-b border-gray-200">
      <nav class="flex overflow-x-auto">
        <a href="<?= BASE_URL ?>/admin/settings?tab=smtp" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap <?= ($tab ?? 'smtp') === 'smtp' ? 'border-b-2 border-ca-green text-ca-green' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
          <i class="fas fa-envelope mr-2"></i>
          SMTP
        </a>
        <a href="<?= BASE_URL ?>/admin/settings?tab=brand" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap <?= ($tab ?? '') === 'brand' ? 'border-b-2 border-ca-green text-ca-green' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
          <i class="fas fa-palette mr-2"></i>
          Marca / Logo
        </a>
        <a href="<?= BASE_URL ?>/admin/settings?tab=footer" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap <?= ($tab ?? '') === 'footer' ? 'border-b-2 border-ca-green text-ca-green' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
          <i class="fas fa-shoe-prints mr-2"></i>
          Footer
        </a>
        <a href="<?= BASE_URL ?>/admin/settings?tab=social" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap <?= ($tab ?? '') === 'social' ? 'border-b-2 border-ca-green text-ca-green' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
          <i class="fas fa-share-alt mr-2"></i>
          Redes Sociales
        </a>
        <a href="<?= BASE_URL ?>/admin/settings?tab=contacto" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap <?= ($tab ?? '') === 'contacto' ? 'border-b-2 border-ca-green text-ca-green' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
          <i class="fas fa-envelope-open-text mr-2"></i>
          Contacto
        </a>
        <a href="<?= BASE_URL ?>/admin/settings?tab=privacidad" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap <?= ($tab ?? '') === 'privacidad' ? 'border-b-2 border-ca-green text-ca-green' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
          <i class="fas fa-shield-alt mr-2"></i>
          Privacidad
        </a>
        <a href="<?= BASE_URL ?>/admin/settings?tab=codigo" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap <?= ($tab ?? '') === 'codigo' ? 'border-b-2 border-ca-green text-ca-green' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
          <i class="fas fa-code mr-2"></i>
          Código
        </a>
      </nav>
    </div>

    <div class="p-6">
      <?php if (($tab ?? 'smtp') === 'smtp'): ?>
        <?php include __DIR__ . '/smtp.php'; ?>
      <?php elseif ($tab === 'brand'): ?>
        <?php include __DIR__ . '/brand.php'; ?>
      <?php elseif ($tab === 'footer'): ?>
        <?php include __DIR__ . '/footer.php'; ?>
      <?php elseif ($tab === 'social'): ?>
        <?php include __DIR__ . '/social.php'; ?>
      <?php elseif ($tab === 'contacto'): ?>
        <?php include __DIR__ . '/contacto.php'; ?>
      <?php elseif ($tab === 'privacidad'): ?>
        <?php include __DIR__ . '/privacidad.php'; ?>
      <?php elseif ($tab === 'codigo'): ?>
        <?php include __DIR__ . '/codigo.php'; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
