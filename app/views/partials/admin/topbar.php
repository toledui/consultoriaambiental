<!-- Topbar -->
<header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center justify-between">
  <div>
    <h2 class="text-xl font-bold text-ca-navy"><?= $title ?? 'Dashboard' ?></h2>
  </div>
  <div class="flex items-center gap-4">
    <a href="<?= BASE_URL ?>" target="_blank" class="text-sm text-ca-green hover:text-ca-navy transition-colors flex items-center gap-1">
      <i class="fas fa-external-link-alt"></i> Ver sitio
    </a>
    <a href="<?= BASE_URL ?>/admin/logout" class="text-sm text-red-500 hover:text-red-700 transition-colors flex items-center gap-1">
      <i class="fas fa-sign-out-alt"></i> Cerrar sesión
    </a>
  </div>
</header>
