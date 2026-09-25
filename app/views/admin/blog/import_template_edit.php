<?php
$escapeTemplate = static fn($value): string => htmlspecialchars(is_scalar($value) ? (string)$value : '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$tokens = \App\Services\BlogCsvImporter::columnTokens($profile['headers']);
$mainFields = [
  'Título' => 'Título del post · obligatorio',
  'Contenido' => 'Contenido del post en texto o HTML',
  'Imagen destacada' => 'URL de la imagen o ruta de la mediateca',
  'Categoría' => 'Categoría del post',
  'Extracto' => 'Resumen breve',
];
$optionalFields = [
  'Slug-Seo' => 'URL del post (slug)',
  'Meta Título' => 'Título SEO',
  'Meta Descripción' => 'Descripción SEO',
  'Identificador' => 'Identificador para reconocer el mismo post en otra corrida',
];
$legacyFields = array_values(array_diff(\App\Services\BlogCsvImporter::mappingFields(), array_keys($mainFields + $optionalFields)));
$legacyNames = ['Principal H2' => 'Contenido principal'];
$hasLegacyMapping = false;
foreach ($legacyFields as $legacyField) {
  $hasLegacyMapping = $hasLegacyMapping || trim((string)($mapping[$legacyField] ?? '')) !== '';
}
?>
<div class="max-w-6xl mx-auto space-y-6">
  <div class="flex flex-wrap items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-ca-navy">Editar plantilla</h1>
      <p class="text-sm text-gray-500 mt-1">Ajusta el mapeo guardado sin subir un archivo. La siguiente corrida usará esta versión.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/blog/importaciones" class="text-sm font-semibold text-ca-green hover:text-ca-navy"><i class="fas fa-arrow-left mr-2"></i>Volver a importaciones</a>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="rounded-xl p-4 <?= ($_SESSION['flash_type'] ?? '') === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>"><?= $escapeTemplate($_SESSION['flash_message']) ?></div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>
  <?php if (!empty($globalError)): ?>
    <div class="rounded-xl p-4 bg-red-50 text-red-700 border border-red-200"><?= $escapeTemplate($globalError) ?></div>
  <?php endif; ?>

  <form id="template-edit-form" method="POST" action="<?= BASE_URL ?>/admin/blog/importaciones/plantilla/<?= (int)$profile['id'] ?>/editar" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <input type="hidden" name="csrf_token" value="<?= $escapeTemplate($csrfToken) ?>">
    <div class="p-6 border-b border-gray-200">
      <label for="template-name" class="block text-sm font-bold text-ca-navy mb-2">Nombre de la plantilla</label>
      <input id="template-name" name="profile_name" type="text" maxlength="150" required value="<?= $escapeTemplate($profileName) ?>" class="w-full max-w-xl px-3 py-2 border border-gray-300 rounded-lg">
      <p class="text-xs text-gray-500 mt-2">Arrastra una columna a un campo, o haz clic en la columna y luego en el campo. Puedes combinar columnas y texto fijo.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_300px]">
      <div class="p-6 min-w-0">
        <h2 class="text-base font-bold text-ca-navy mb-4">Datos del post</h2>
        <?php foreach ($mainFields as $field => $hint): ?>
          <div class="mb-5">
            <label for="template-map-<?= md5($field) ?>" class="block text-sm font-semibold text-ca-navy"><?= $escapeTemplate($field) ?></label>
            <p class="text-xs text-gray-500 mb-2"><?= $escapeTemplate($hint) ?></p>
            <textarea id="template-map-<?= md5($field) ?>" name="mapping[<?= $escapeTemplate($field) ?>]" rows="<?= $field === 'Contenido' ? 4 : 1 ?>" class="template-map-target w-full rounded-lg border border-dashed border-gray-400 px-3 py-2 text-sm focus:border-ca-green focus:outline-none focus:ring-2 focus:ring-ca-green/30" placeholder="Suelta aquí una columna o escribe texto fijo"><?= $escapeTemplate($mapping[$field] ?? '') ?></textarea>
          </div>
        <?php endforeach; ?>

        <details class="border-t border-gray-200 pt-4 mt-5" open>
          <summary class="cursor-pointer text-sm font-bold text-ca-navy">URL, SEO y actualización de posts</summary>
          <p class="text-xs text-gray-500 mt-3 mb-4">Mantén el mismo identificador o slug entre corridas para actualizar el post existente, incluso si cambia el título.</p>
          <?php foreach ($optionalFields as $field => $hint): ?>
            <div class="mb-5">
              <label for="template-map-<?= md5($field) ?>" class="block text-sm font-semibold text-ca-navy"><?= $escapeTemplate($field === 'Slug-Seo' ? 'URL del post (slug)' : $field) ?></label>
              <p class="text-xs text-gray-500 mb-2"><?= $escapeTemplate($hint) ?></p>
              <textarea id="template-map-<?= md5($field) ?>" name="mapping[<?= $escapeTemplate($field) ?>]" rows="1" class="template-map-target w-full rounded-lg border border-dashed border-gray-400 px-3 py-2 text-sm focus:border-ca-green focus:outline-none focus:ring-2 focus:ring-ca-green/30" placeholder="Suelta aquí una columna o escribe texto fijo"><?= $escapeTemplate($mapping[$field] ?? '') ?></textarea>
            </div>
          <?php endforeach; ?>
        </details>

        <details class="border-t border-gray-200 pt-4 mt-5"<?= $hasLegacyMapping ? ' open' : '' ?>>
          <summary class="cursor-pointer text-sm font-bold text-ca-navy">Campos de plantillas anteriores</summary>
          <p class="text-xs text-gray-500 mt-3 mb-4">Estos campos se conservaron de importaciones previas. Los campos de contenido se agregan al contenido principal.</p>
          <?php foreach ($legacyFields as $field): ?>
            <div class="mb-5">
              <label for="template-map-<?= md5($field) ?>" class="block text-sm font-semibold text-ca-navy"><?= $escapeTemplate($legacyNames[$field] ?? $field) ?></label>
              <textarea id="template-map-<?= md5($field) ?>" name="mapping[<?= $escapeTemplate($field) ?>]" rows="2" class="template-map-target w-full rounded-lg border border-dashed border-gray-400 px-3 py-2 text-sm focus:border-ca-green focus:outline-none focus:ring-2 focus:ring-ca-green/30" placeholder="Suelta aquí una columna o escribe texto fijo"><?= $escapeTemplate($mapping[$field] ?? '') ?></textarea>
            </div>
          <?php endforeach; ?>
        </details>
      </div>

      <aside class="bg-gray-50 border-t lg:border-t-0 lg:border-l border-gray-200 p-5" aria-label="Columnas de la plantilla">
        <div class="lg:sticky lg:top-5">
          <h2 class="font-bold text-ca-navy">Columnas guardadas</h2>
          <p class="text-xs text-gray-500 mt-1 mb-4">Estas son las columnas del archivo con el que se creó la plantilla.</p>
          <div id="template-columns" class="space-y-2">
            <?php foreach ($profile['headers'] as $index => $header): ?>
              <button type="button" draggable="true" data-token="<?= $escapeTemplate($tokens[$index]) ?>" class="template-column w-full text-left rounded-lg border border-gray-200 bg-white hover:border-ca-green hover:bg-green-50 px-3 py-2 text-sm font-semibold text-ca-navy break-words"><i class="fas fa-grip-vertical text-ca-green mr-2" aria-hidden="true"></i><?= $escapeTemplate($header) ?></button>
            <?php endforeach; ?>
          </div>
        </div>
      </aside>
    </div>

    <div class="p-6 border-t border-gray-200 bg-gray-50 flex flex-wrap gap-3">
      <button type="submit" class="bg-ca-green hover:bg-green-700 text-white font-bold px-5 py-2.5 rounded-lg">Guardar plantilla</button>
      <button type="submit" name="save_and_run" value="1" class="border border-ca-green text-ca-green hover:bg-green-50 font-bold px-5 py-2.5 rounded-lg"><?= !empty($profile['source_records_json']) ? 'Guardar y repetir archivo guardado' : 'Guardar y subir archivo para nueva corrida' ?></button>
    </div>
  </form>
</div>

<script>
(() => {
  const columns = [...document.querySelectorAll('#template-columns .template-column')];
  const targets = [...document.querySelectorAll('#template-edit-form .template-map-target')];
  let selectedToken = null;
  function insert(target, token) {
    const start = target.selectionStart ?? target.value.length;
    const end = target.selectionEnd ?? start;
    target.value = target.value.slice(0, start) + token + target.value.slice(end);
    target.focus();
    target.setSelectionRange(start + token.length, start + token.length);
  }
  columns.forEach(column => {
    column.addEventListener('dragstart', event => {
      event.dataTransfer.setData('text/plain', column.dataset.token);
      event.dataTransfer.effectAllowed = 'copy';
    });
    column.addEventListener('click', () => {
      selectedToken = column.dataset.token;
      columns.forEach(item => item.classList.toggle('border-ca-green', item === column));
    });
  });
  targets.forEach(target => {
    target.addEventListener('dragover', event => event.preventDefault());
    target.addEventListener('drop', event => {
      event.preventDefault();
      const token = event.dataTransfer.getData('text/plain');
      if (columns.some(column => column.dataset.token === token)) insert(target, token);
    });
    target.addEventListener('click', () => {
      if (selectedToken !== null) {
        insert(target, selectedToken);
        selectedToken = null;
        columns.forEach(item => item.classList.remove('border-ca-green'));
      }
    });
  });
})();
</script>
