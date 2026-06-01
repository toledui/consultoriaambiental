<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h3 class="text-lg font-bold text-ca-navy">Todos los Artículos</h3>
      <p class="text-sm text-gray-500"><?= count($posts) ?> artículo(s) registrados</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/blog/crear" class="bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
      <i class="fas fa-plus mr-1"></i> Nuevo Artículo
    </a>
  </div>

  <?php if (empty($posts)): ?>
    <div class="p-12 text-center">
      <i class="fas fa-newspaper text-5xl text-ca-light-gray mb-4"></i>
      <p class="text-gray-500 text-lg">No hay artículos aún.</p>
      <a href="<?= BASE_URL ?>/admin/blog/crear" class="inline-block mt-4 text-ca-green hover:text-ca-navy font-medium transition-colors">
        Crear el primer artículo
      </a>
    </div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-ca-dark-gray text-xs uppercase tracking-wider">
          <tr>
            <th class="text-left px-6 py-4 font-semibold">Título</th>
            <th class="text-left px-6 py-4 font-semibold hidden md:table-cell">Slug</th>
            <th class="text-center px-6 py-4 font-semibold hidden lg:table-cell">Categoría</th>
            <th class="text-center px-6 py-4 font-semibold">Estado</th>
            <th class="text-center px-6 py-4 font-semibold hidden lg:table-cell">Fecha</th>
            <th class="text-right px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($posts as $post): ?>
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <span class="font-semibold text-ca-navy"><?= htmlspecialchars($post['title']) ?></span>
              </td>
              <td class="px-6 py-4 text-gray-500 hidden md:table-cell"><?= htmlspecialchars($post['slug']) ?></td>
              <td class="px-6 py-4 text-center hidden lg:table-cell">
                <?php if (!empty($post['category_name'])): ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-ca-navy/10 text-ca-navy">
                    <?= htmlspecialchars($post['category_name']) ?>
                  </span>
                <?php else: ?>
                  <span class="text-gray-400 text-xs">—</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?php if ($post['published']): ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Publicado
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Borrador
                  </span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center text-gray-500 hidden lg:table-cell"><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <a href="<?= BASE_URL ?>/admin/blog/editar/<?= $post['id'] ?>" class="text-ca-green hover:text-ca-navy transition-colors p-1" title="Editar">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form method="POST" action="<?= BASE_URL ?>/admin/blog/eliminar/<?= $post['id'] ?>" onsubmit="return confirm('¿Eliminar este artículo?')" class="inline">
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
