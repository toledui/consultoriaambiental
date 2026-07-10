<div class="max-w-6xl mx-auto">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-ca-navy">Contactos</h1>
      <p class="text-gray-500 text-sm mt-1">
        Administra los mensajes recibidos desde el formulario de contacto
        <?php if ($unreadCount > 0): ?>
          <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
            <?= $unreadCount ?> no leído<?= $unreadCount !== 1 ? 's' : '' ?>
          </span>
        <?php endif; ?>
      </p>
    </div>
    <div class="mt-4 sm:mt-0">
      <a href="<?= BASE_URL ?>/admin/contactos/csv" class="inline-flex items-center gap-2 bg-ca-green hover:bg-ca-navy text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors shadow">
        <i class="fas fa-file-csv"></i>
        Exportar CSV
      </a>
    </div>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="mb-6 p-4 rounded-lg <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' ?>">
      <div class="flex items-center gap-2">
        <i class="fas <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <span><?= $_SESSION['flash_message'] ?></span>
      </div>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <!-- Filters / Stats -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-wrap items-center gap-4 text-sm">
      <span class="text-gray-500 font-medium">Total: <strong class="text-ca-navy"><?= count($contacts) ?></strong></span>
      <span class="text-gray-500 font-medium">No leídos: <strong class="text-red-600"><?= $unreadCount ?></strong></span>
      <span class="text-gray-500 font-medium">Leídos: <strong class="text-ca-green"><?= count($contacts) - $unreadCount ?></strong></span>
    </div>

    <?php if (empty($contacts)): ?>
      <div class="p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-inbox text-2xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-500 mb-1">No hay contactos aún</h3>
        <p class="text-sm text-gray-400">Los mensajes del formulario de contacto aparecerán aquí.</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Estado</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Nombre</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Correo</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Teléfono</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Sector</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Newsletter</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Fecha</th>
              <th class="text-right px-4 py-3 font-semibold text-gray-600">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($contacts as $contact): ?>
              <tr class="hover:bg-gray-50 transition-colors <?= $contact['read_at'] === null ? 'bg-blue-50/50 font-medium' : '' ?>">
                <td class="px-4 py-3">
                  <?php if ($contact['read_at'] === null): ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      <i class="fas fa-circle text-[6px] mr-1 text-blue-500"></i> Nuevo
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                      <i class="fas fa-check-circle mr-1 text-xs"></i> Leído
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-ca-navy"><?= htmlspecialchars($contact['nombre']) ?></td>
                <td class="px-4 py-3">
                  <a href="mailto:<?= htmlspecialchars($contact['correo']) ?>" class="text-ca-green hover:underline"><?= htmlspecialchars($contact['correo']) ?></a>
                </td>
                <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($contact['telefono']) ?></td>
                <td class="px-4 py-3">
                  <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded"><?= htmlspecialchars($contact['sector'] ?? '—') ?></span>
                </td>
                <td class="px-4 py-3">
                  <?php if ($contact['newsletter']): ?>
                    <span class="text-ca-green"><i class="fas fa-check-circle"></i> Suscrito</span>
                  <?php else: ?>
                    <span class="text-gray-400"><i class="fas fa-times-circle"></i> No</span>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs"><?= format_cdmx_datetime($contact['created_at']) ?> CDMX</td>
                <td class="px-4 py-3 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <a href="<?= BASE_URL ?>/admin/contactos/<?= $contact['id'] ?>" class="px-3 py-1.5 text-xs bg-ca-navy/10 text-ca-navy rounded-lg hover:bg-ca-navy/20 transition-colors" title="Ver detalle">
                      <i class="fas fa-eye"></i>
                    </a>
                    <form method="POST" action="<?= BASE_URL ?>/admin/contactos/eliminar/<?= $contact['id'] ?>" onsubmit="return confirm('¿Eliminar este contacto?')" class="inline">
                      <button type="submit" class="px-3 py-1.5 text-xs bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Eliminar">
                        <i class="fas fa-trash"></i>
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
</div>
