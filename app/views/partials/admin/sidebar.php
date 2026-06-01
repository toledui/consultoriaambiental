<!-- Sidebar -->
<aside class="w-64 bg-ca-navy text-white flex flex-col shadow-xl z-20 hidden md:flex">
  <div class="p-6 border-b border-gray-700">
    <?php if (!empty($settings['brand_logo'])): ?>
      <img alt="<?= htmlspecialchars($settings['brand_company_name'] ?? 'Logo') ?>" class="h-12 w-auto object-contain mb-2" src="<?= BASE_URL ?>/<?= htmlspecialchars($settings['brand_logo']) ?>"/>
    <?php else: ?>
      <h1 class="text-xl font-bold tracking-wider">THagencia</h1>
    <?php endif; ?>
    <p class="text-xs text-ca-light-green mt-1 tracking-widest uppercase">Admin Panel</p>
    <div class="mt-4 pt-4 border-t border-gray-700">
      <span class="text-sm font-semibold block">Usuario:</span>
      <span class="text-lg font-bold text-white"><?= htmlspecialchars($_SESSION['admin_user'] ?? '') ?></span>
    </div>
  </div>
  
  <nav class="flex-1 overflow-y-auto py-4">
    <ul class="space-y-1">
      <li>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false || $_SERVER['REQUEST_URI'] === '/admin' ? 'bg-ca-green text-white border-r-4 border-ca-light-green' : 'text-gray-300 hover:bg-ca-green/20 hover:text-white' ?>">
          <i class="fas fa-tachometer-alt w-5 text-center"></i>
          Dashboard
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/blog" class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/blog') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/blog/categorias') === false ? 'bg-ca-green text-white border-r-4 border-ca-light-green' : 'text-gray-300 hover:bg-ca-green/20 hover:text-white' ?>">
          <i class="fas fa-newspaper w-5 text-center"></i>
          Blog
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/blog/categorias" class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/blog/categorias') !== false ? 'bg-ca-green text-white border-r-4 border-ca-light-green' : 'text-gray-300 hover:bg-ca-green/20 hover:text-white' ?>">
          <i class="fas fa-folder-open w-5 text-center"></i>
          Categorías
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/servicios" class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/servicios') !== false ? 'bg-ca-green text-white border-r-4 border-ca-light-green' : 'text-gray-300 hover:bg-ca-green/20 hover:text-white' ?>">
          <i class="fas fa-concierge-bell w-5 text-center"></i>
          Servicios
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/contactos" class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/contactos') !== false ? 'bg-ca-green text-white border-r-4 border-ca-light-green' : 'text-gray-300 hover:bg-ca-green/20 hover:text-white' ?>">
          <i class="fas fa-inbox w-5 text-center"></i>
          <span class="flex-1">Contactos</span>
          <?php
          $unreadCount = \App\Models\Contact::getUnreadCount();
          if ($unreadCount > 0):
          ?>
            <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold bg-red-500 text-white"><?= $unreadCount ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/media" class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/media') !== false ? 'bg-ca-green text-white border-r-4 border-ca-light-green' : 'text-gray-300 hover:bg-ca-green/20 hover:text-white' ?>">
          <i class="fas fa-photo-video w-5 text-center"></i>
          Mediateca
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/usuarios" class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/usuarios') !== false ? 'bg-ca-green text-white border-r-4 border-ca-light-green' : 'text-gray-300 hover:bg-ca-green/20 hover:text-white' ?>">
          <i class="fas fa-users-cog w-5 text-center"></i>
          Usuarios
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/settings" class="flex items-center gap-3 px-6 py-3 text-sm font-medium transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'bg-ca-green text-white border-r-4 border-ca-light-green' : 'text-gray-300 hover:bg-ca-green/20 hover:text-white' ?>">
          <i class="fas fa-cog w-5 text-center"></i>
          Configuración
        </a>
      </li>
      <li class="pt-4 mt-4 border-t border-gray-700">
        <a href="<?= BASE_URL ?>" target="_blank" class="flex items-center gap-3 px-6 py-3 text-sm font-medium text-gray-300 hover:bg-ca-green/20 hover:text-white transition-colors">
          <i class="fas fa-external-link-alt w-5 text-center"></i>
          Ver Sitio Web
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/logout" class="flex items-center gap-3 px-6 py-3 text-sm font-medium text-gray-300 hover:bg-red-600/20 hover:text-red-400 transition-colors">
          <i class="fas fa-sign-out-alt w-5 text-center"></i>
          Cerrar Sesión
        </a>
      </li>
    </ul>
  </nav>
</aside>
