<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h3 class="text-lg font-bold text-ca-navy">Categorías del Blog</h3>
      <p class="text-sm text-gray-500"><?= count($categories) ?> categoría(s) registradas</p>
    </div>
    <div class="flex gap-3">
      <a href="<?= BASE_URL ?>/admin/blog" class="border border-gray-300 hover:bg-gray-50 text-ca-dark-gray text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Artículos
      </a>
      <a href="<?= BASE_URL ?>/admin/blog/categorias/crear" class="bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
        <i class="fas fa-plus mr-1"></i> Nueva Categoría
      </a>
    </div>
  </div>

  <?php if (empty($categories)): ?>
    <div class="p-12 text-center">
      <i class="fas fa-folder-open text-5xl text-ca-light-gray mb-4"></i>
      <p class="text-gray-500 text-lg">No hay categorías aún.</p>
      <a href="<?= BASE_URL ?>/admin/blog/categorias/crear" class="inline-block mt-4 text-ca-green hover:text-ca-navy font-medium transition-colors">
        Crear la primera categoría
      </a>
    </div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-ca-dark-gray text-xs uppercase tracking-wider">
          <tr>
            <th class="text-left px-6 py-4 font-semibold">Nombre</th>
            <th class="text-left px-6 py-4 font-semibold hidden md:table-cell">Slug</th>
            <th class="text-left px-6 py-4 font-semibold hidden lg:table-cell">Descripción</th>
            <th class="text-center px-6 py-4 font-semibold">Artículos</th>
            <th class="text-right px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($categories as $category): ?>
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <span class="font-semibold text-ca-navy"><?= htmlspecialchars($category['name']) ?></span>
              </td>
              <td class="px-6 py-4 text-gray-500 hidden md:table-cell"><?= htmlspecialchars($category['slug']) ?></td>
              <td class="px-6 py-4 text-gray-500 hidden lg:table-cell max-w-xs truncate"><?= htmlspecialchars($category['description'] ?? '') ?></td>
              <td class="px-6 py-4 text-center">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  <?= (int)$category['post_count'] ?>
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <a href="<?= BASE_URL ?>/admin/blog/categorias/editar/<?= $category['id'] ?>" class="text-ca-green hover:text-ca-navy transition-colors p-1" title="Editar">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form method="POST" action="<?= BASE_URL ?>/admin/blog/categorias/eliminar/<?= $category['id'] ?>" onsubmit="return confirm('¿Eliminar esta categoría? Los artículos asociados quedarán sin categoría.')" class="inline">
                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Eliminar">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
