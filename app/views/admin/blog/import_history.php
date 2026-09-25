<?php
$escapeHistory = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$templateLabels = [
  'Slug-Seo' => 'URL del post (slug)',
  'Meta Título' => 'Título SEO',
  'Meta Descripción' => 'Descripción SEO',
  'Principal H2' => 'Contenido principal',
];
?>
<div class="max-w-6xl mx-auto space-y-6">
  <div class="flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-ca-navy">Importaciones guardadas</h1>
      <p class="text-sm text-gray-500 mt-1">Consulta o edita el mapeo y repite el archivo guardado, o usa uno nuevo. Solo se actualizan los posts que cambien.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/blog/importar" class="inline-flex items-center bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg"><i class="fas fa-plus mr-2"></i>Nueva importación</a>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="rounded-xl p-4 <?= ($_SESSION['flash_type'] ?? '') === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>">
      <?= $escapeHistory($_SESSION['flash_message']) ?>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <?php if ($profiles === []): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">Todavía no hay importaciones guardadas.</div>
  <?php endif; ?>

  <?php foreach ($profiles as $profile): ?>
    <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="p-5 flex flex-wrap items-center justify-between gap-4 border-b border-gray-200">
        <div>
          <h2 class="text-lg font-bold text-ca-navy"><?= $escapeHistory($profile['name']) ?></h2>
          <p class="text-xs text-gray-500 mt-1">Plantilla #<?= (int)$profile['id'] ?> · <?= (int)$profile['run_count'] ?> ejecuciones</p>
          <p class="text-xs text-gray-500 mt-1"><?= !empty($profile['has_source']) ? 'Archivo guardado: ' . $escapeHistory($profile['source_filename']) : 'Sube un archivo una vez para poder repetirlo sin volver a cargarlo.' ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
          <a href="<?= BASE_URL ?>/admin/blog/importaciones/plantilla/<?= (int)$profile['id'] ?>/editar" class="inline-flex items-center border border-gray-300 text-ca-navy hover:bg-gray-50 text-sm font-bold py-2.5 px-4 rounded-lg"><i class="fas fa-edit mr-2"></i>Editar plantilla</a>
          <?php if (!empty($profile['has_source'])): ?>
            <a href="<?= BASE_URL ?>/admin/blog/importar?profile=<?= (int)$profile['id'] ?>&amp;reuse=1" class="inline-flex items-center bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-4 rounded-lg"><i class="fas fa-redo mr-2"></i>Repetir archivo guardado</a>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>/admin/blog/importar?profile=<?= (int)$profile['id'] ?>" class="inline-flex items-center border border-ca-green text-ca-green hover:bg-green-50 text-sm font-bold py-2.5 px-4 rounded-lg"><i class="fas fa-file-upload mr-2"></i>Usar otro CSV / Excel</a>
        </div>
      </div>
      <details class="border-b border-gray-200">
        <summary class="cursor-pointer px-5 py-3 text-sm font-semibold text-ca-green hover:bg-gray-50">Ver plantilla guardada</summary>
        <div class="px-5 pb-5 space-y-4">
          <div>
            <h3 class="text-sm font-bold text-ca-navy mb-2">Columnas esperadas del archivo</h3>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($profile['headers'] as $header): ?>
                <span class="rounded-full bg-gray-100 text-gray-700 px-2.5 py-1 text-xs font-semibold"><?= $escapeHistory($header) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <div>
            <h3 class="text-sm font-bold text-ca-navy mb-2">Asignación de campos del post</h3>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
              <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="text-left px-4 py-2">Campo del post</th><th class="text-left px-4 py-2">Valor de la plantilla</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                  <?php foreach ($profile['mapping'] as $field => $template): ?>
                    <?php if (!is_string($template) || trim($template) === '') continue; ?>
                    <tr>
                      <td class="px-4 py-3 font-semibold text-ca-navy whitespace-nowrap"><?= $escapeHistory($templateLabels[$field] ?? $field) ?></td>
                      <td class="px-4 py-3 font-mono text-xs text-gray-700 whitespace-pre-wrap break-words"><?= $escapeHistory($template) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php if (trim((string)($profile['mapping']['Identificador'] ?? '')) === '' && trim((string)($profile['mapping']['Slug-Seo'] ?? '')) === ''): ?>
              <p class="text-xs text-amber-700 mt-3">Esta plantilla reconoce los posts por el título. Si el título cambia en el siguiente archivo, puede crearse un post nuevo.</p>
            <?php endif; ?>
          </div>
        </div>
      </details>
      <?php if ($profile['runs'] === []): ?>
        <p class="p-5 text-sm text-gray-500">La plantilla está guardada; todavía no se han importado posts con ella.</p>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
              <tr>
                <th class="text-left px-5 py-3">Ejecución</th>
                <th class="text-left px-5 py-3">Archivo</th>
                <th class="text-left px-5 py-3">Resultado</th>
                <th class="text-right px-5 py-3">Acción</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <?php foreach ($profile['runs'] as $run): ?>
                <tr>
                  <td class="px-5 py-4 whitespace-nowrap">
                    <strong class="text-ca-navy">#<?= (int)$run['id'] ?></strong>
                    <span class="block text-xs text-gray-500"><?= $escapeHistory(format_cdmx_datetime($run['created_at'])) ?></span>
                  </td>
                  <td class="px-5 py-4 text-gray-700"><?= $escapeHistory($run['source_filename']) ?></td>
                  <td class="px-5 py-4">
                    <span class="font-semibold <?= $run['status'] === 'undone' ? 'text-gray-500' : 'text-green-700' ?>"><?= $run['status'] === 'undone' ? 'Deshecha' : 'Completada' ?></span>
                    <span class="block text-xs text-gray-500"><?php if ((int)$run['created_count'] === 0 && (int)$run['updated_count'] === 0): ?>Sin cambios<?php else: ?><?= (int)$run['created_count'] ?> creados · <?= (int)$run['updated_count'] ?> actualizados<?php endif; ?></span>
                  </td>
                  <td class="px-5 py-4 text-right">
                    <?php if ((int)$profile['latest_completed_id'] === (int)$run['id'] && $run['status'] === 'completed'): ?>
                      <form method="POST" action="<?= BASE_URL ?>/admin/blog/importaciones/deshacer/<?= (int)$run['id'] ?>" onsubmit="return confirm('¿Deshacer esta ejecución? Se eliminarán sus posts nuevos y se restaurarán los posts que actualizó.');">
                        <input type="hidden" name="csrf_token" value="<?= $escapeHistory($csrfToken) ?>">
                        <button type="submit" class="text-red-700 hover:text-red-900 font-semibold">Deshacer ejecución</button>
                      </form>
                    <?php elseif ($run['status'] === 'completed'): ?>
                      <span class="text-xs text-gray-400">Deshaz primero la ejecución más reciente</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  <?php endforeach; ?>

  <p class="text-xs text-gray-500">Al deshacer se eliminan los posts creados en esa ejecución y se restauran los campos importados de los posts actualizados. Las categorías creadas permanecen disponibles.</p>
</div>
