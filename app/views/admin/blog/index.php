<?php if (isset($_SESSION['flash_message'])): ?>
  <div class="mb-6 p-4 rounded-lg border <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
    <div class="flex items-center gap-2">
      <i class="fas <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
      <span><?= htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
    </div>
  </div>
  <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h3 class="text-lg font-bold text-ca-navy">Todos los Artículos</h3>
      <p class="text-sm text-gray-500"><?= count($posts) ?> artículo(s) registrados</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="<?= BASE_URL ?>/admin/blog/importar" class="border border-ca-green text-ca-green hover:bg-green-50 text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
        <i class="fas fa-file-csv mr-1"></i> Importar CSV
      </a>
      <a href="<?= BASE_URL ?>/admin/blog/crear" class="bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
        <i class="fas fa-plus mr-1"></i> Nuevo Artículo
      </a>
    </div>
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
            <th class="text-left px-6 py-4 font-semibold">Autor</th>
            <th class="text-center px-6 py-4 font-semibold">Estado</th>
            <th class="text-center px-6 py-4 font-semibold hidden lg:table-cell">Publicaci&oacute;n</th>
            <th class="text-right px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php $nowTs = (new DateTimeImmutable('now', app_timezone()))->getTimestamp(); ?>
          <?php foreach ($posts as $post): ?>
            <?php
              $publishedAt = $post['published_at'] ?? null;
              $isScheduled = !empty($publishedAt) && strtotime($publishedAt) > $nowTs;
              $displayDate = $publishedAt ?: ($post['created_at'] ?? null);
              $statusValue = !$post['published']
                ? 'draft'
                : ($isScheduled ? 'scheduled' : 'published');
              $publishedAtValue = !empty($publishedAt)
                ? format_cdmx_datetime($publishedAt, 'Y-m-d\TH:i')
                : '';
            ?>
            <tr class="hover:bg-gray-50 transition-colors" id="post-row-<?= (int)$post['id'] ?>">
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
              <td class="px-6 py-4 text-gray-600">
                <?php if (!empty($post['author_name'])): ?>
                  <span class="inline-flex items-center gap-2">
                    <i class="fas fa-user-circle text-ca-green" aria-hidden="true"></i>
                    <?= htmlspecialchars($post['author_name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                  </span>
                <?php else: ?>
                  <span class="text-gray-400 text-xs">Sin registro</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?php if ($post['published'] && $isScheduled): ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Programado
                  </span>
                <?php elseif ($post['published']): ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Publicado
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Borrador
                  </span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-center text-gray-500 hidden lg:table-cell">
                <?= format_cdmx_datetime($displayDate) ?>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <button
                    type="button"
                    class="inline-flex items-center gap-1 text-blue-600 hover:text-ca-navy transition-colors p-1"
                    title="Edición rápida"
                    aria-expanded="false"
                    aria-controls="quick-edit-<?= (int)$post['id'] ?>"
                    onclick="toggleBlogQuickEdit(<?= (int)$post['id'] ?>, this)"
                  >
                    <i class="fas fa-sliders-h"></i>
                    <span class="hidden xl:inline text-xs font-semibold">Rápido</span>
                  </button>
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
            <tr id="quick-edit-<?= (int)$post['id'] ?>" class="hidden bg-blue-50/60">
              <td colspan="7" class="px-6 py-5">
                <form method="POST" action="<?= BASE_URL ?>/admin/blog/actualizar-rapido/<?= (int)$post['id'] ?>" class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto] gap-4 items-end">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($quickEditCsrfToken, ENT_QUOTES, 'UTF-8') ?>"/>

                  <div>
                    <label for="quick-status-<?= (int)$post['id'] ?>" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1.5">Estado</label>
                    <select
                      id="quick-status-<?= (int)$post['id'] ?>"
                      name="status"
                      data-quick-status
                      onchange="syncQuickEditStatus(this)"
                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none"
                    >
                      <option value="draft" <?= $statusValue === 'draft' ? 'selected' : '' ?>>Borrador</option>
                      <option value="published" <?= $statusValue === 'published' ? 'selected' : '' ?>>Publicado</option>
                      <option value="scheduled" <?= $statusValue === 'scheduled' ? 'selected' : '' ?>>Programado</option>
                    </select>
                  </div>

                  <div>
                    <label for="quick-date-<?= (int)$post['id'] ?>" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1.5">Fecha de publicación (CDMX)</label>
                    <input
                      id="quick-date-<?= (int)$post['id'] ?>"
                      name="published_at"
                      type="datetime-local"
                      data-quick-date
                      value="<?= htmlspecialchars($publishedAtValue, ENT_QUOTES, 'UTF-8') ?>"
                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none"
                    />
                    <p class="text-[11px] text-gray-500 mt-1" data-quick-date-help>Vacía significa publicación inmediata.</p>
                  </div>

                  <div>
                    <label for="quick-author-<?= (int)$post['id'] ?>" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1.5">Autor</label>
                    <select
                      id="quick-author-<?= (int)$post['id'] ?>"
                      name="author_id"
                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none"
                    >
                      <option value="">— Sin autor —</option>
                      <?php foreach ($users as $user): ?>
                        <option value="<?= (int)$user['id'] ?>" <?= (int)($post['author_id'] ?? 0) === (int)$user['id'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($user['username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="flex flex-wrap xl:flex-nowrap gap-2">
                    <button type="submit" class="inline-flex items-center justify-center bg-ca-green hover:bg-green-700 text-white font-bold px-4 py-2.5 rounded-lg transition-colors whitespace-nowrap">
                      <i class="fas fa-save mr-2"></i>Guardar
                    </button>
                    <button type="button" onclick="toggleBlogQuickEdit(<?= (int)$post['id'] ?>)" class="inline-flex items-center justify-center border border-gray-300 hover:bg-white text-gray-600 font-semibold px-4 py-2.5 rounded-lg transition-colors">
                      Cancelar
                    </button>
                  </div>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script>
function toggleBlogQuickEdit(postId, trigger) {
  var row = document.getElementById('quick-edit-' + postId);
  if (!row) return;

  var willOpen = row.classList.contains('hidden');
  row.classList.toggle('hidden', !willOpen);

  var control = trigger || document.querySelector('[aria-controls="quick-edit-' + postId + '"]');
  if (control) {
    control.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
  }

  if (willOpen) {
    var status = row.querySelector('[data-quick-status]');
    syncQuickEditStatus(status);
    status.focus();
  }
}

function syncQuickEditStatus(select) {
  if (!select) return;
  var form = select.closest('form');
  var dateInput = form.querySelector('[data-quick-date]');
  var help = form.querySelector('[data-quick-date-help]');
  var isScheduled = select.value === 'scheduled';

  dateInput.required = isScheduled;
  dateInput.classList.toggle('border-blue-400', isScheduled);
  if (isScheduled) {
    help.textContent = 'Programado requiere una fecha futura.';
    help.classList.add('text-blue-700', 'font-semibold');
  } else if (select.value === 'published') {
    help.textContent = 'Vacía publica de inmediato; una fecha futura requiere Programado.';
    help.classList.remove('text-blue-700', 'font-semibold');
  } else {
    help.textContent = 'En borrador la fecha se conserva, pero el post no será público.';
    help.classList.remove('text-blue-700', 'font-semibold');
  }
}
</script>
