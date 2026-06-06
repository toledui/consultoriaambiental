<!-- Dashboard Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
  <!-- Blog Posts Count -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Artículos</p>
        <p class="text-4xl font-bold text-ca-navy mt-2"><?= $blogCount ?></p>
      </div>
      <div class="w-14 h-14 bg-ca-green/10 rounded-full flex items-center justify-center text-ca-green text-2xl">
        <i class="fas fa-newspaper"></i>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/admin/blog" class="inline-flex items-center text-sm text-ca-green hover:text-ca-navy mt-4 transition-colors">
      Administrar blog <i class="fas fa-arrow-right ml-1 text-xs"></i>
    </a>
  </div>

  <!-- Contacts Count -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Contactos</p>
        <p class="text-4xl font-bold text-ca-navy mt-2"><?= $contactCount ?></p>
        <?php if ($unreadContacts > 0): ?>
          <p class="text-xs text-red-600 mt-1">
            <i class="fas fa-circle text-[6px] mr-1 text-red-500"></i>
            <?= $unreadContacts ?> no leído<?= $unreadContacts !== 1 ? 's' : '' ?>
          </p>
        <?php endif; ?>
      </div>
      <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 text-2xl">
        <i class="fas fa-inbox"></i>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/admin/contactos" class="inline-flex items-center text-sm text-ca-green hover:text-ca-navy mt-4 transition-colors">
      Administrar contactos <i class="fas fa-arrow-right ml-1 text-xs"></i>
    </a>
  </div>

  <!-- Quick Actions -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Acción rápida</p>
        <p class="text-lg font-bold text-ca-navy mt-2">Nuevo artículo</p>
      </div>
      <div class="w-14 h-14 bg-ca-navy/10 rounded-full flex items-center justify-center text-ca-navy text-2xl">
        <i class="fas fa-plus-circle"></i>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/admin/blog/crear" class="inline-flex items-center text-xs bg-ca-green hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition-colors mt-4">
      + Artículo
    </a>
  </div>
</div>

<!-- Welcome -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
  <h3 class="text-xl font-bold text-ca-navy mb-2">Bienvenido al Panel de Administración</h3>
  <p class="text-gray-500">Desde aquí puedes gestionar el contenido administrable del sitio web, revisar contactos y actualizar configuraciones generales.</p>
</div>
