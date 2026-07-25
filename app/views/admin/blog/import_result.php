<div class="max-w-6xl mx-auto space-y-6">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-8 text-center border-b border-gray-200 bg-green-50">
      <div class="w-16 h-16 mx-auto rounded-full bg-green-100 text-green-700 flex items-center justify-center text-3xl">
        <i class="fas fa-check"></i>
      </div>
      <h1 class="text-2xl font-bold text-ca-navy mt-4">Importación completada</h1>
      <p class="text-gray-600 mt-2">
        Se crearon <strong><?= (int)$result['post_count'] ?> posts</strong> como borradores
        y <strong><?= (int)$result['category_count'] ?> categorías</strong> nuevas.
      </p>
    </div>

    <?php if (!empty($result['categories'])): ?>
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-bold text-ca-navy mb-3">Categorías creadas</h2>
        <div class="flex flex-wrap gap-2">
          <?php foreach ($result['categories'] as $category): ?>
            <span class="inline-flex px-3 py-1.5 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">
              <?= htmlspecialchars($category['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($result['slug_adjustments'])): ?>
      <details class="border-b border-gray-200">
        <summary class="p-6 cursor-pointer font-bold text-ca-navy hover:bg-gray-50">
          <i class="fas fa-link text-ca-green mr-2"></i>
          <?= count($result['slug_adjustments']) ?> ajustes de slug
        </summary>
        <div class="px-6 pb-6 overflow-x-auto">
          <table class="w-full text-sm border border-gray-200 rounded-lg">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
              <tr>
                <th class="text-left px-4 py-3">Post</th>
                <th class="text-left px-4 py-3">Original</th>
                <th class="text-left px-4 py-3">Guardado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <?php foreach ($result['slug_adjustments'] as $adjustment): ?>
                <tr>
                  <td class="px-4 py-3"><?= htmlspecialchars($adjustment['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                  <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= htmlspecialchars($adjustment['from'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="px-4 py-3 font-mono text-xs text-ca-green"><?= htmlspecialchars($adjustment['to'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </details>
    <?php endif; ?>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
          <tr>
            <th class="text-left px-6 py-4">Post</th>
            <th class="text-left px-6 py-4">Slug final</th>
            <th class="text-left px-6 py-4">Categoría</th>
            <th class="text-right px-6 py-4">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($result['posts'] as $post): ?>
            <tr>
              <td class="px-6 py-4 font-semibold text-ca-navy"><?= htmlspecialchars($post['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
              <td class="px-6 py-4 font-mono text-xs text-gray-500"><?= htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8') ?></td>
              <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($post['category_name'] ?: 'Sin categoría', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
              <td class="px-6 py-4 text-right">
                <a href="<?= BASE_URL ?>/admin/blog/editar/<?= (int)$post['id'] ?>" class="inline-flex items-center text-ca-green hover:text-ca-navy font-semibold">
                  <i class="fas fa-edit mr-1"></i>Editar borrador
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="p-6 border-t border-gray-200 bg-gray-50 flex flex-wrap gap-3">
      <a href="<?= BASE_URL ?>/admin/blog" class="bg-ca-navy hover:bg-gray-800 text-white font-bold py-2.5 px-5 rounded-lg transition-colors">
        Ver todos los posts
      </a>
      <a href="<?= BASE_URL ?>/admin/blog/importar" class="border border-ca-green text-ca-green hover:bg-green-50 font-bold py-2.5 px-5 rounded-lg transition-colors">
        Importar otro CSV
      </a>
    </div>
  </div>
</div>
