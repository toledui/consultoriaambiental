<?php
$previewRows = is_array($preview['rows'] ?? null) ? $preview['rows'] : [];
$hasPreview = is_array($preview);
$hasSource = is_array($source ?? null);
$escapeImport = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$mainPostFields = [
  'Título' => ['Título', 'Obligatorio. Nombre del post.'],
  'Contenido' => ['Contenido', 'Obligatorio. Texto o HTML del post. Puedes combinar varias columnas aquí.'],
  'Imagen destacada' => ['Imagen destacada', 'Opcional. URL de la imagen o ruta de la mediateca.'],
  'Categoría' => ['Categoría', 'Opcional. Se usará una categoría existente o se creará una nueva.'],
  'Extracto' => ['Extracto', 'Opcional. Resumen breve del post.'],
];
$optionalPostFields = [
  'Slug-Seo' => ['URL del post (slug)', 'Opcional. Si se deja vacío, se genera desde el título. Mantén el mismo valor para actualizar el post al reemplazar el archivo.'],
  'Meta Título' => ['Título SEO', 'Opcional. Título para buscadores.'],
  'Meta Descripción' => ['Descripción SEO', 'Opcional. Descripción para buscadores.'],
];
$legacyMappingFields = array_values(array_diff(\App\Services\BlogCsvImporter::mappingFields(), array_keys($mainPostFields + $optionalPostFields)));
$hasOptionalMapping = false;
foreach (array_keys($optionalPostFields) as $optionalField) {
  $hasOptionalMapping = $hasOptionalMapping || trim((string)($mapping[$optionalField] ?? '')) !== '';
}
?>

<div class="max-w-6xl mx-auto space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-ca-navy">Importar artículos</h1>
      <p class="text-sm text-gray-500 mt-1">Sube el archivo, asigna las columnas a los datos del post y revisa el resultado antes de importar.</p>
    </div>
    <div class="flex flex-wrap gap-4">
      <a href="<?= BASE_URL ?>/admin/blog/importaciones" class="inline-flex items-center text-sm font-semibold text-ca-green hover:text-ca-navy"><i class="fas fa-history mr-2"></i>Importaciones guardadas</a>
      <a href="<?= BASE_URL ?>/admin/blog" class="inline-flex items-center text-sm font-semibold text-ca-green hover:text-ca-navy"><i class="fas fa-arrow-left mr-2"></i>Volver al blog</a>
    </div>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="rounded-xl p-4 <?= ($_SESSION['flash_type'] ?? '') === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>">
      <?= $escapeImport($_SESSION['flash_message']) ?>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <?php if (!empty($globalError)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 flex items-start gap-3">
      <i class="fas fa-exclamation-circle mt-0.5"></i>
      <div>
        <p class="font-semibold">No se pudo procesar el archivo</p>
        <p class="text-sm mt-1"><?= htmlspecialchars($globalError, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
      </div>
    </div>
  <?php endif; ?>

  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-200">
      <h2 class="text-lg font-bold text-ca-navy">1. Selecciona el archivo</h2>
      <p class="text-sm text-gray-500 mt-1">CSV UTF-8 o Excel .xlsx; máximo 10 MB y 500 filas de datos. Se usa la primera hoja.</p>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/admin/blog/importar/previsualizar" enctype="multipart/form-data" class="p-6 space-y-5">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
      <?php if (!empty($profile['id'])): ?>
        <input type="hidden" name="profile_id" value="<?= (int)$profile['id'] ?>">
        <div class="rounded-lg bg-blue-50 border border-blue-200 p-3 text-sm text-blue-800">
          Usando la plantilla <strong><?= $escapeImport($profile['name']) ?></strong>. Sube otro CSV o Excel y se reutilizará el mapeo guardado.
          <a href="<?= BASE_URL ?>/admin/blog/importar" class="underline font-semibold ml-2">Nueva importación</a>
        </div>
      <?php endif; ?>
      <div>
        <label for="source_file" class="block text-sm font-semibold text-ca-dark-gray mb-2">Archivo CSV o Excel *</label>
        <input
          id="source_file"
          name="source_file"
          type="file"
          accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
          required
          class="block w-full text-sm text-gray-600 border border-gray-300 rounded-lg file:mr-4 file:py-3 file:px-4 file:border-0 file:bg-ca-navy file:text-white file:font-semibold hover:file:bg-ca-green transition-colors"
        />
      </div>
      <div class="flex flex-wrap gap-3">
        <button type="submit" class="inline-flex items-center bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
          <i class="fas fa-upload mr-2"></i>Cargar columnas
        </button>
        <?php if ($hasSource): ?>
          <span class="inline-flex items-center text-xs text-gray-500">
            Selecciona otro archivo para reemplazar <?= $escapeImport($source['name']) ?>.
          </span>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <?php if ($hasSource): ?>
    <?php if (!empty($source['reused'])): ?>
      <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">Usando el archivo guardado <strong><?= $escapeImport($source['name']) ?></strong>. Revisa el mapeo y previsualiza los cambios antes de confirmar la nueva corrida.</div>
    <?php endif; ?>
    <form id="blog-mapping-form" method="POST" action="<?= BASE_URL ?>/admin/blog/importar/previsualizar" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <input type="hidden" name="csrf_token" value="<?= $escapeImport($csrfToken) ?>">
      <input type="hidden" name="source_token" value="<?= $escapeImport($source['token']) ?>">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-bold text-ca-navy">2. Arrastra las columnas a los campos</h2>
        <p class="text-sm text-gray-500 mt-1"><?= $escapeImport($source['name']) ?> · <?= (int)$source['total_rows'] ?> filas. Arrastra una columna al dato correspondiente del post, o haz clic en la columna y luego en el campo. Puedes combinar columnas y texto fijo.</p>
        <label for="profile-name" class="block text-sm font-semibold text-ca-navy mt-4 mb-1">Nombre de la importación y plantilla</label>
        <input id="profile-name" name="profile_name" type="text" maxlength="150" required value="<?= $escapeImport($profileName !== '' ? $profileName : (pathinfo($source['name'], PATHINFO_FILENAME) ?: 'Importación del blog')) ?>" class="w-full max-w-lg px-3 py-2 border border-gray-300 rounded-lg text-sm">
        <p class="text-xs text-gray-500 mt-1">El mapeo se guardará con este nombre para reutilizarlo con otro archivo.</p>
      </div>
      <?php foreach ($legacyMappingFields as $field): ?>
        <?php if (is_string($mapping[$field] ?? null) && $mapping[$field] !== ''): ?>
          <input type="hidden" name="mapping[<?= $escapeImport($field) ?>]" value="<?= $escapeImport($mapping[$field]) ?>"<?= in_array($field, ['Introducción', 'Youtube', 'Principal H2', 'PAA H2', 'Respaldo H2', 'FAQ', 'Respuestas', 'Amazon-Resenas'], true) ? ' class="blog-legacy-content-map"' : '' ?>>
        <?php endif; ?>
      <?php endforeach; ?>
      <div class="blog-map-layout">
        <div class="blog-map-fields">
          <?php foreach ($mainPostFields as $field => [$label, $hint]): ?>
            <?php $value = is_string($mapping[$field] ?? null) ? $mapping[$field] : ''; ?>
            <div class="blog-map-field">
              <label for="map-<?= md5($field) ?>"><?= $escapeImport($label) ?></label>
              <p><?= $escapeImport($hint) ?></p>
              <textarea id="map-<?= md5($field) ?>" name="mapping[<?= $escapeImport($field) ?>]" class="blog-map-target" rows="<?= $field === 'Contenido' ? 4 : 1 ?>" placeholder="Suelta aquí una columna o escribe texto fijo"><?= $escapeImport($value) ?></textarea>
              <div class="blog-map-sample" data-sample-for="<?= md5($field) ?>"></div>
            </div>
          <?php endforeach; ?>
          <details class="border-t border-gray-200 pt-4 mt-4"<?= $hasOptionalMapping ? ' open' : '' ?>>
            <summary class="cursor-pointer text-sm font-bold text-ca-navy">URL y SEO (opcionales)</summary>
            <div class="pt-4">
              <?php foreach ($optionalPostFields as $field => [$label, $hint]): ?>
                <?php $value = is_string($mapping[$field] ?? null) ? $mapping[$field] : ''; ?>
                <div class="blog-map-field">
                  <label for="map-<?= md5($field) ?>"><?= $escapeImport($label) ?></label>
                  <p><?= $escapeImport($hint) ?></p>
                  <textarea id="map-<?= md5($field) ?>" name="mapping[<?= $escapeImport($field) ?>]" class="blog-map-target" rows="1" placeholder="Suelta aquí una columna o escribe texto fijo"><?= $escapeImport($value) ?></textarea>
                  <div class="blog-map-sample" data-sample-for="<?= md5($field) ?>"></div>
                </div>
              <?php endforeach; ?>
            </div>
          </details>
        </div>
        <aside class="blog-map-source" aria-label="Columnas del archivo">
          <div class="blog-map-source-inner">
            <h3>Columnas del archivo</h3>
            <p>Arrastra cada columna tantas veces como necesites.</p>
            <div id="blog-map-columns" class="blog-map-columns">
              <?php foreach ($source['headers'] as $index => $header): ?>
                <button type="button" draggable="true" class="blog-map-column" data-column="<?= (int)$index ?>" aria-label="Seleccionar columna <?= $escapeImport($header) ?>">
                  <span class="blog-map-grip" aria-hidden="true">⠿</span>
                  <span><strong><?= $escapeImport($header) ?></strong><small><?= $escapeImport(mb_strimwidth((string)($source['sample'][$index] ?? ''), 0, 100, '…', 'UTF-8')) ?></small></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        </aside>
      </div>
      <div class="p-6 border-t border-gray-200 bg-gray-50 flex flex-wrap items-center gap-4">
        <button type="submit" class="inline-flex items-center bg-ca-green hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-sm"><i class="fas fa-search mr-2"></i>Previsualizar y validar</button>
        <button type="submit" formaction="<?= BASE_URL ?>/admin/blog/importar/guardar-plantilla" class="inline-flex items-center border border-ca-green text-ca-green hover:bg-green-50 font-bold py-3 px-6 rounded-lg"><i class="fas fa-save mr-2"></i>Guardar plantilla</button>
        <span class="text-xs text-gray-500">El archivo temporal expira en 30 minutos.</span>
      </div>
    </form>
  <?php endif; ?>

  <?php if ($hasPreview): ?>
    <div id="blog-import-preview" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div>
            <h2 class="text-lg font-bold text-ca-navy">3. Resultado de la validación</h2>
            <p class="text-sm text-gray-500 mt-1">El estado elegido se aplicará a los posts de esta corrida, incluidos los ya importados con esta plantilla.</p>
          </div>
          <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <span class="px-3 py-1.5 rounded-full bg-blue-100 text-blue-800"><?= (int)$preview['total_rows'] ?> filas</span>
            <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-800"><?= (int)$preview['valid_rows'] ?> válidas</span>
            <span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800"><span id="import-create-count"><?= (int)($preview['create_count'] ?? 0) ?></span> nuevos</span>
            <span class="px-3 py-1.5 rounded-full bg-indigo-100 text-indigo-800"><span id="import-update-count"><?= (int)($preview['update_count'] ?? 0) ?></span> por actualizar</span>
            <span class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-700"><span id="import-unchanged-count"><?= (int)($preview['unchanged_count'] ?? 0) ?></span> sin cambios</span>
            <span class="px-3 py-1.5 rounded-full <?= (int)$preview['error_count'] > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600' ?>">
              <?= (int)$preview['error_count'] ?> errores
            </span>
            <span class="px-3 py-1.5 rounded-full bg-yellow-100 text-yellow-800"><?= (int)$preview['warning_count'] ?> avisos</span>
          </div>
        </div>
      </div>

      <div class="p-5 border-b border-gray-200 bg-gray-50">
        <div class="blog-preview-actions">
          <button type="submit" form="blog-mapping-form" class="inline-flex items-center justify-center border border-ca-green text-ca-green hover:bg-green-50 font-bold px-5 rounded-lg text-sm">
            <i class="fas fa-rotate mr-2"></i>Actualizar categorías y validar
          </button>
          <?php if ($preview['can_import'] && !empty($batchToken)): ?>
            <form method="POST" action="<?= BASE_URL ?>/admin/blog/importar/confirmar" class="blog-preview-confirm" onsubmit="return confirm('¿Crear ' + document.getElementById('import-create-count').textContent + ' posts y actualizar ' + document.getElementById('import-update-count').textContent + ' con esta importación? El estado final será ' + (this.elements['publication_status'].value === 'published' ? 'Publicado' : 'Borrador') + '. ' + document.getElementById('import-unchanged-count').textContent + ' quedarán sin cambios.');">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
              <input type="hidden" name="batch_token" value="<?= htmlspecialchars($batchToken, ENT_QUOTES, 'UTF-8') ?>"/>
              <div class="blog-preview-status">
                <label for="publication-status" class="block text-sm font-semibold text-ca-navy mb-1">Estado de los posts</label>
                <select id="publication-status" name="publication_status" class="w-full border border-gray-300 rounded-lg bg-white px-3 text-sm text-ca-navy">
                  <option value="published" selected>Publicado</option>
                  <option value="draft">Borrador</option>
                </select>
              </div>
              <button id="confirm-import-button" type="submit" class="inline-flex items-center justify-center bg-ca-green hover:bg-green-700 text-white font-bold px-5 rounded-lg text-sm transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-file-import mr-2"></i>Ejecutar importación de <?= (int)$preview['total_rows'] ?> posts
              </button>
            </form>
          <?php else: ?>
            <div class="flex items-start gap-3 text-red-700">
              <i class="fas fa-ban mt-0.5"></i>
              <div>
                <p class="font-semibold">La importación está bloqueada.</p>
                <p class="text-sm">Corrige las categorías o el mapeo y actualiza la previsualización.</p>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <?php if ($preview['can_import'] && !empty($batchToken)): ?>
          <p id="preview-needs-update" class="hidden text-sm text-amber-700 font-semibold mt-3">Actualiza la previsualización para confirmar los cambios de categoría o mapeo.</p>
          <p class="text-xs text-gray-500 mt-3">La previsualización expira en 30 minutos y solo puede confirmarse una vez.</p>
        <?php endif; ?>
      </div>

      <div class="p-5 border-b border-gray-200 bg-green-50/50">
        <label for="bulk-category" class="block text-sm font-bold text-ca-navy">Categoría para todos los posts</label>
        <p class="text-xs text-gray-600 mt-1 mb-3">Elige una categoría existente de la lista o escribe el nombre de una nueva. Déjalo vacío para importar todos sin categoría.</p>
        <div class="flex flex-col sm:flex-row gap-2">
          <input id="bulk-category" name="bulk_category" form="blog-mapping-form" type="text" list="blog-category-options" maxlength="100" placeholder="Selecciona o crea una categoría" class="flex-1 min-w-0 px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white">
          <button type="submit" name="apply_bulk_category" value="1" form="blog-mapping-form" class="bg-ca-navy hover:bg-ca-green text-white text-sm font-bold px-4 py-2.5 rounded-lg">Aplicar a todos y validar</button>
          <button type="submit" name="use_source_categories" value="1" form="blog-mapping-form" class="border border-gray-300 hover:bg-white text-ca-navy text-sm font-bold px-4 py-2.5 rounded-lg">Usar categorías del archivo</button>
        </div>
        <datalist id="blog-category-options">
          <?php foreach ($categoryChoices ?? [] as $category): ?>
            <option value="<?= $escapeImport($category['name'] ?? '') ?>"></option>
          <?php endforeach; ?>
        </datalist>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
            <tr>
              <th class="text-left px-4 py-3">Fila</th>
              <th class="text-left px-4 py-3">Post</th>
              <th class="text-left px-4 py-3">Categoría</th>
              <th class="text-left px-4 py-3">Validación</th>
              <th class="text-right px-4 py-3">Contenido</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($previewRows as $index => $row): ?>
              <?php $previewId = 'content-preview-' . $index; ?>
              <tr class="align-top <?= empty($row['errors']) ? '' : 'bg-red-50/40' ?>">
                <td class="px-4 py-4 font-semibold text-gray-500"><?= (int)$row['row_number'] ?></td>
                <td class="px-4 py-4 min-w-64">
                  <p class="font-semibold text-ca-navy"><?= htmlspecialchars($row['title'] ?: 'Sin título', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                  <?php if (empty($row['errors'])): ?>
                    <span class="blog-import-action inline-flex mt-1 px-2 py-0.5 rounded-full text-xs font-semibold <?= match ($row['import_action'] ?? 'created') { 'updated' => 'bg-indigo-100 text-indigo-800', 'unchanged' => 'bg-gray-100 text-gray-700', default => 'bg-emerald-100 text-emerald-800' } ?>" data-action-published="<?= $escapeImport($row['import_actions']['published'] ?? $row['import_action'] ?? 'created') ?>" data-action-draft="<?= $escapeImport($row['import_actions']['draft'] ?? $row['import_action'] ?? 'created') ?>">
                      <?= match ($row['import_action'] ?? 'created') { 'updated' => 'Se actualizará', 'unchanged' => 'Sin cambios', default => 'Nuevo post' } ?>
                    </span>
                  <?php endif; ?>
                  <?php if (!empty($row['slug'])): ?>
                    <p class="text-xs text-gray-500 font-mono mt-1"><?= htmlspecialchars($row['slug'], ENT_QUOTES, 'UTF-8') ?></p>
                  <?php endif; ?>
                  <?php if (!empty($row['data']['featured_image'])): ?>
                    <img src="<?= $escapeImport($row['data']['featured_image']) ?>" alt="Imagen destacada de <?= $escapeImport($row['title']) ?>" class="mt-2 h-16 w-24 object-cover rounded border border-gray-200" loading="lazy">
                  <?php endif; ?>
                </td>
                <td class="px-4 py-4 min-w-72">
                  <?php if (!empty($row['category_name'])): ?>
                    <span class="blog-category-badge inline-flex px-2.5 py-1 rounded-full text-xs font-semibold <?= $row['category_is_new'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' ?>">
                      <?= htmlspecialchars($row['category_name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                      <?= $row['category_is_new'] ? ' · nueva' : '' ?>
                    </span>
                  <?php else: ?>
                    <span class="text-xs text-gray-400">Sin categoría</span>
                  <?php endif; ?>
                  <label for="category-row-<?= (int)$row['row_number'] ?>" class="block text-xs font-semibold text-ca-navy mt-3 mb-1">Elegir o crear categoría</label>
                  <input id="category-row-<?= (int)$row['row_number'] ?>" name="category_overrides[<?= (int)$row['row_number'] ?>]" form="blog-mapping-form" type="text" list="blog-category-options" maxlength="100" value="<?= $escapeImport($row['category_name']) ?>" placeholder="Sin categoría" class="blog-category-override w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                  <p class="text-xs text-gray-500 mt-1">Elige una opción, escribe una nueva o borra el texto.</p>
                </td>
                <td class="px-4 py-4 min-w-72">
                  <?php if (empty($row['errors'])): ?>
                    <p class="text-green-700 font-semibold text-xs"><i class="fas fa-check-circle mr-1"></i>Fila válida</p>
                  <?php else: ?>
                    <ul class="space-y-1 text-xs text-red-700">
                      <?php foreach ($row['errors'] as $error): ?>
                        <li><i class="fas fa-times-circle mr-1"></i><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                  <?php if (!empty($row['warnings'])): ?>
                    <ul class="space-y-1 text-xs text-yellow-700 mt-2">
                      <?php foreach ($row['warnings'] as $warning): ?>
                        <li><i class="fas fa-exclamation-triangle mr-1"></i><?= htmlspecialchars($warning, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-4 text-right">
                  <?php if (!empty($row['content'])): ?>
                    <button type="button" onclick="document.getElementById('<?= $previewId ?>').showModal()" class="text-ca-green hover:text-ca-navy font-semibold text-xs">
                      <i class="fas fa-eye mr-1"></i>Ver HTML
                    </button>
                    <dialog id="<?= $previewId ?>" class="import-content-dialog rounded-xl shadow-2xl w-full max-w-4xl p-0">
                      <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between z-10">
                        <div class="text-left">
                          <p class="font-bold text-ca-navy"><?= htmlspecialchars($row['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                          <p class="text-xs text-gray-500">Vista del contenido final almacenado</p>
                        </div>
                        <button type="button" onclick="document.getElementById('<?= $previewId ?>').close()" class="text-gray-500 hover:text-red-600 p-2">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                      <div class="import-content-preview text-left p-6 overflow-y-auto">
                        <?= $row['content'] ?>
                      </div>
                    </dialog>
                  <?php else: ?>
                    <span class="text-xs text-gray-400">No disponible</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  <?php endif; ?>
</div>

<style>
  #blog-import-preview { scroll-margin-top: 16px; }
  .blog-preview-actions, .blog-preview-confirm { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px; }
  .blog-preview-confirm { min-width: 0; }
  .blog-preview-actions button, .blog-preview-status select { min-height: 46px; }
  .blog-preview-status { min-width: 190px; }
  @media (max-width: 640px) {
    .blog-preview-actions, .blog-preview-confirm { flex-direction: column; align-items: stretch; width: 100%; }
    .blog-preview-actions button, .blog-preview-status { width: 100%; }
  }
  .blog-category-badge { max-width: 20rem; overflow-wrap: anywhere; white-space: normal; }
  .blog-map-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; }
  .blog-map-fields { padding: 24px; min-width: 0; }
  .blog-map-field { margin-bottom: 18px; }
  .blog-map-field label { display: block; color: #1b3a4b; font-size: 14px; font-weight: 700; }
  .blog-map-field p { color: #6b7280; font-size: 12px; margin: 2px 0 7px; }
  .blog-map-target { display: block; width: 100%; min-height: 46px; padding: 10px 12px; border: 1px dashed #9ca3af; border-radius: 8px; font-size: 13px; line-height: 1.5; background: #fff; color: #263238; resize: vertical; }
  .blog-map-target:focus, .blog-map-target.is-over { outline: 2px solid #2e7d32; border-color: #2e7d32; background: #f2faf2; }
  .blog-map-sample { color: #64748b; font-size: 12px; margin-top: 5px; white-space: pre-wrap; overflow-wrap: anywhere; max-height: 72px; overflow: hidden; }
  .blog-map-sample:not(:empty)::before { content: 'Ejemplo: '; color: #2e7d32; font-weight: 700; }
  .blog-map-source { border-left: 1px solid #e5e7eb; background: #f8faf9; }
  .blog-map-source-inner { position: sticky; top: 18px; padding: 24px 18px; max-height: 85vh; overflow-y: auto; }
  .blog-map-source h3 { color: #1b3a4b; font-weight: 800; font-size: 16px; }
  .blog-map-source p { color: #6b7280; font-size: 12px; margin: 4px 0 14px; }
  .blog-map-columns { display: grid; gap: 8px; }
  .blog-map-column { display: flex; width: 100%; align-items: flex-start; gap: 8px; text-align: left; padding: 10px; border: 1px solid #dbe5df; border-radius: 8px; background: #fff; cursor: grab; }
  .blog-map-column:hover, .blog-map-column.is-selected { border-color: #2e7d32; background: #eff8ef; }
  .blog-map-column:active { cursor: grabbing; }
  .blog-map-column strong { display: block; color: #1b3a4b; font-size: 13px; overflow-wrap: anywhere; }
  .blog-map-column small { display: block; color: #64748b; font-size: 11px; line-height: 1.4; max-height: 45px; overflow: hidden; overflow-wrap: anywhere; }
  .blog-map-grip { color: #2e7d32; font-size: 20px; line-height: 1; }
  @media (max-width: 850px) { .blog-map-layout { grid-template-columns: 1fr; } .blog-map-source { grid-row: 1; border-left: 0; border-bottom: 1px solid #e5e7eb; } .blog-map-source-inner { position: static; max-height: 240px; } .blog-map-fields { padding: 18px; } }
  .import-content-dialog {
    width: min(900px, 94vw);
    max-height: 90vh;
  }
  .import-content-dialog::backdrop { background: rgba(0, 0, 0, .5); }
  .import-content-preview {
    color: #263238;
    font-size: 16px;
    line-height: 1.75;
  }
  .import-content-preview p { margin: 0 0 1rem; }
  .import-content-preview h2 { color: #1B3A4B; font-size: 1.65rem; font-weight: 800; margin: 1.6rem 0 .7rem; }
  .import-content-preview h3 { color: #1B3A4B; font-size: 1.25rem; font-weight: 700; margin: 1.25rem 0 .55rem; }
  .import-content-preview strong { color: #1B3A4B; font-weight: 800; }
  .import-content-preview table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
  .import-content-preview th,
  .import-content-preview td { border: 1px solid #dbe5df; padding: .75rem; text-align: left; }
  .import-content-preview th { background: #f1f8f2; color: #1B3A4B; }
  .import-content-preview .video-embed {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    margin: 1.5rem 0;
    overflow: hidden;
    border-radius: .75rem;
    background: #0f172a;
  }
  .import-content-preview .video-embed iframe { width: 100%; height: 100%; border: 0; }
</style>
<?php if ($hasPreview): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  requestAnimationFrame(() => document.getElementById('blog-import-preview')?.scrollIntoView({ behavior: 'smooth', block: 'start' }));
  const counts = <?= json_encode($preview['status_counts'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  document.getElementById('publication-status')?.addEventListener('change', event => {
    const status = event.target.value;
    const selected = counts[status];
    if (!selected) return;
    for (const [key, id] of [['create_count', 'import-create-count'], ['update_count', 'import-update-count'], ['unchanged_count', 'import-unchanged-count']]) {
      document.getElementById(id).textContent = selected[key];
    }
    document.querySelectorAll('.blog-import-action').forEach(badge => {
      const action = status === 'draft' ? badge.dataset.actionDraft : badge.dataset.actionPublished;
      badge.classList.remove('bg-indigo-100', 'text-indigo-800', 'bg-gray-100', 'text-gray-700', 'bg-emerald-100', 'text-emerald-800');
      const style = action === 'updated' ? ['bg-indigo-100', 'text-indigo-800'] : action === 'unchanged' ? ['bg-gray-100', 'text-gray-700'] : ['bg-emerald-100', 'text-emerald-800'];
      badge.classList.add(...style);
      badge.textContent = action === 'updated' ? 'Se actualizará' : action === 'unchanged' ? 'Sin cambios' : 'Nuevo post';
    });
  });
});
</script>
<?php endif; ?>
<?php if ($hasSource): ?>
<script>
(() => {
  const columns = <?= json_encode(array_values($source['sample']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const columnTokens = <?= json_encode(array_values($source['column_tokens']), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const sampleByToken = Object.fromEntries(columnTokens.map((token, index) => [token, columns[index] ?? '']));
  const chips = [...document.querySelectorAll('#blog-map-columns .blog-map-column')];
  const targets = [...document.querySelectorAll('#blog-mapping-form .blog-map-target')];
  const confirmButton = document.getElementById('confirm-import-button');
  const updateNotice = document.getElementById('preview-needs-update');
  let selected = null;

  function markPreviewDirty() {
    if (confirmButton) confirmButton.disabled = true;
    if (updateNotice) updateNotice.classList.remove('hidden');
  }

  function insert(target, index) {
    const token = columnTokens[Number(index)];
    const start = target.selectionStart ?? target.value.length;
    const end = target.selectionEnd ?? start;
    target.value = target.value.slice(0, start) + token + target.value.slice(end);
    target.focus();
    target.setSelectionRange(start + token.length, start + token.length);
    target.dispatchEvent(new Event('input', { bubbles: true }));
  }
  function showSample(target) {
    const destination = document.querySelector(`[data-sample-for="${target.id.slice(4)}"]`);
    if (!destination) return;
    const sample = target.value.replace(/\{[^{}]+\}/g, token => sampleByToken[token] ?? token);
    destination.textContent = sample.length > 300 ? sample.slice(0, 300) + '…' : sample;
  }
  chips.forEach(chip => {
    chip.addEventListener('dragstart', event => {
      event.dataTransfer.setData('text/plain', chip.dataset.column);
      event.dataTransfer.effectAllowed = 'copy';
    });
    chip.addEventListener('click', () => {
      selected = chip.dataset.column;
      chips.forEach(item => item.classList.toggle('is-selected', item === chip));
    });
  });
  targets.forEach(target => {
    target.addEventListener('dragover', event => { event.preventDefault(); target.classList.add('is-over'); });
    target.addEventListener('dragleave', () => target.classList.remove('is-over'));
    target.addEventListener('drop', event => {
      event.preventDefault();
      target.classList.remove('is-over');
      const index = event.dataTransfer.getData('text/plain');
      if (/^\d+$/.test(index) && Number(index) < columns.length) insert(target, index);
    });
    target.addEventListener('click', () => {
      if (selected !== null) {
        insert(target, selected);
        selected = null;
        chips.forEach(item => item.classList.remove('is-selected'));
      }
    });
    target.addEventListener('input', () => {
      if (target.name === 'mapping[Contenido]') {
        document.querySelectorAll('.blog-legacy-content-map').forEach(input => { input.value = ''; });
      }
      showSample(target);
      markPreviewDirty();
    });
    showSample(target);
  });
  document.querySelectorAll('.blog-category-override').forEach(input => {
    input.addEventListener('input', markPreviewDirty);
  });
  document.getElementById('profile-name')?.addEventListener('input', markPreviewDirty);
})();
</script>
<?php endif; ?>
