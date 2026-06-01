<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h3 class="text-lg font-bold text-ca-navy">Todos los Servicios</h3>
      <p class="text-sm text-gray-500"><?= count($services) ?> servicio(s) registrados</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/servicios/crear" class="bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
      <i class="fas fa-plus mr-1"></i> Nuevo Servicio
    </a>
  </div>

  <?php if (empty($services)): ?>
    <div class="p-12 text-center">
      <i class="fas fa-concierge-bell text-5xl text-ca-light-gray mb-4"></i>
      <p class="text-gray-500 text-lg">No hay servicios aún.</p>
      <a href="<?= BASE_URL ?>/admin/servicios/crear" class="inline-block mt-4 text-ca-green hover:text-ca-navy font-medium transition-colors">
        Crear el primer servicio
      </a>
    </div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-ca-dark-gray text-xs uppercase tracking-wider">
          <tr>
            <th class="text-left px-6 py-4 font-semibold w-12">Img</th>
            <th class="text-left px-6 py-4 font-semibold">Título</th>
            <th class="text-left px-6 py-4 font-semibold hidden md:table-cell">Slug</th>
            <th class="text-center px-6 py-4 font-semibold">Orden</th>
            <th class="text-center px-6 py-4 font-semibold">SEO</th>
            <th class="text-center px-6 py-4 font-semibold">Estado</th>
            <th class="text-right px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($services as $service): ?>
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <?php if (!empty($service['featured_image'])): ?>
                  <img src="<?= BASE_URL ?>/<?= htmlspecialchars($service['featured_image']) ?>" alt="<?= htmlspecialchars($service['title']) ?>" class="w-10 h-10 rounded-lg object-cover border border-gray-200"/>
                <?php else: ?>
                  <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i class="<?= htmlspecialchars($service['icon'] ?? 'fas fa-leaf') ?> text-ca-green text-sm"></i>
                  </div>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4">
                <span class="font-semibold text-ca-navy"><?= htmlspecialchars($service['title']) ?></span>
              </td>
              <td class="px-6 py-4 text-gray-500 hidden md:table-cell"><?= htmlspecialchars($service['slug']) ?></td>
              <td class="px-6 py-4 text-center text-gray-500"><?= $service['sort_order'] ?></td>
              <td class="px-6 py-4 text-center">
                <?php if (!empty($service['meta_title']) && !empty($service['meta_description'])): ?>
                  <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-100 text-green-600" title="SEO configurado">
                    <i class="fas fa-check text-xs"></i>
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-400" title="SEO no configurado">
                    <i class="fas fa-minus text-xs"></i>
                  </span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?php if ($service['published']): ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Activo
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Inactivo
                  </span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <a href="<?= BASE_URL ?>/admin/servicios/editar/<?= $service['id'] ?>" class="text-ca-green hover:text-ca-navy transition-colors p-1" title="Editar">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form method="POST" action="<?= BASE_URL ?>/admin/servicios/eliminar/<?= $service['id'] ?>" onsubmit="return confirm('¿Eliminar este servicio?')" class="inline">
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
