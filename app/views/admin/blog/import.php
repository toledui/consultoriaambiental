<?php
$previewRows = is_array($preview['rows'] ?? null) ? $preview['rows'] : [];
$hasPreview = is_array($preview);
?>

<div class="max-w-6xl mx-auto space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-ca-navy">Importar artículos desde CSV</h1>
      <p class="text-sm text-gray-500 mt-1">Valida y revisa todo el lote antes de crear borradores.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/blog" class="inline-flex items-center text-sm font-semibold text-ca-green hover:text-ca-navy">
      <i class="fas fa-arrow-left mr-2"></i>Volver al blog
    </a>
  </div>

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
      <h2 class="text-lg font-bold text-ca-navy">1. Selecciona el CSV</h2>
      <p class="text-sm text-gray-500 mt-1">UTF-8, máximo 10 MB y 500 filas de datos.</p>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/admin/blog/importar/previsualizar" enctype="multipart/form-data" class="p-6 space-y-5">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
      <div>
        <label for="csv_file" class="block text-sm font-semibold text-ca-dark-gray mb-2">Archivo CSV *</label>
        <input
          id="csv_file"
          name="csv_file"
          type="file"
          accept=".csv,text/csv,text/plain,application/vnd.ms-excel"
          required
          class="block w-full text-sm text-gray-600 border border-gray-300 rounded-lg file:mr-4 file:py-3 file:px-4 file:border-0 file:bg-ca-navy file:text-white file:font-semibold hover:file:bg-ca-green transition-colors"
        />
      </div>
      <div class="flex flex-wrap gap-3">
        <button type="submit" class="inline-flex items-center bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
          <i class="fas fa-search mr-2"></i>Previsualizar y validar
        </button>
        <?php if ($hasPreview): ?>
          <span class="inline-flex items-center text-xs text-gray-500">
            Selecciona nuevamente el archivo para reemplazar esta previsualización.
          </span>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <details class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <summary class="cursor-pointer p-5 font-semibold text-ca-navy hover:bg-gray-50">
      <i class="fas fa-table-columns text-ca-green mr-2"></i>Estructura exacta requerida
    </summary>
    <div class="px-5 pb-5">
      <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="min-w-full text-xs">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left px-3 py-2 text-gray-500">#</th>
              <th class="text-left px-3 py-2 text-gray-500">Encabezado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($expectedHeaders as $index => $header): ?>
              <tr>
                <td class="px-3 py-2 text-gray-400"><?= $index + 1 ?></td>
                <td class="px-3 py-2 font-mono text-ca-dark-gray"><?= htmlspecialchars($header, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="text-xs text-gray-500 mt-3">
        <strong>Shortcode</strong> y <strong>Directorio</strong> deben estar vacíos. Slug-Seo, Youtube y Categoría pueden quedar vacíos.
      </p>
    </div>
  </details>

  <?php if ($hasPreview): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div>
            <h2 class="text-lg font-bold text-ca-navy">2. Resultado de la validación</h2>
            <p class="text-sm text-gray-500 mt-1">Los posts se crearán como borradores y se asignarán a tu usuario.</p>
          </div>
          <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <span class="px-3 py-1.5 rounded-full bg-blue-100 text-blue-800"><?= (int)$preview['total_rows'] ?> filas</span>
            <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-800"><?= (int)$preview['valid_rows'] ?> válidas</span>
            <span class="px-3 py-1.5 rounded-full <?= (int)$preview['error_count'] > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600' ?>">
              <?= (int)$preview['error_count'] ?> errores
            </span>
            <span class="px-3 py-1.5 rounded-full bg-yellow-100 text-yellow-800"><?= (int)$preview['warning_count'] ?> avisos</span>
          </div>
        </div>
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
                  <?php if (!empty($row['slug'])): ?>
                    <p class="text-xs text-gray-500 font-mono mt-1"><?= htmlspecialchars($row['slug'], ENT_QUOTES, 'UTF-8') ?></p>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-4 min-w-44">
                  <?php if (!empty($row['category_name'])): ?>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold <?= $row['category_is_new'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' ?>">
                      <?= htmlspecialchars($row['category_name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                      <?= $row['category_is_new'] ? ' · nueva' : '' ?>
                    </span>
                  <?php else: ?>
                    <span class="text-xs text-gray-400">Sin categoría</span>
                  <?php endif; ?>
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

      <div class="p-6 border-t border-gray-200 bg-gray-50">
        <?php if ($preview['can_import'] && !empty($batchToken)): ?>
          <form method="POST" action="<?= BASE_URL ?>/admin/blog/importar/confirmar" onsubmit="return confirm('¿Crear <?= (int)$preview['total_rows'] ?> posts como borradores?');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"/>
            <input type="hidden" name="batch_token" value="<?= htmlspecialchars($batchToken, ENT_QUOTES, 'UTF-8') ?>"/>
            <button type="submit" class="inline-flex items-center bg-ca-green hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors shadow-sm">
              <i class="fas fa-file-import mr-2"></i>Confirmar importación de <?= (int)$preview['total_rows'] ?> posts
            </button>
            <p class="text-xs text-gray-500 mt-2">La previsualización expira en 30 minutos y solo puede confirmarse una vez.</p>
          </form>
        <?php else: ?>
          <div class="flex items-start gap-3 text-red-700">
            <i class="fas fa-ban mt-0.5"></i>
            <div>
              <p class="font-semibold">La importación está bloqueada.</p>
              <p class="text-sm">Corrige todas las filas con errores y vuelve a subir el archivo.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
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
