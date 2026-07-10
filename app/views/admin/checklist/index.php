<div class="p-6">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
      <h1 class="text-2xl font-bold text-ca-navy">Descargas de Checklist</h1>
      <p class="text-gray-500 text-sm mt-1">Leads capturados a través del formulario de descarga del Checklist Ambiental</p>
    </div>
    <div class="flex items-center gap-3 mt-4 sm:mt-0">
      <span class="inline-flex items-center gap-2 bg-ca-navy/10 text-ca-navy px-4 py-2 rounded-lg font-semibold text-sm">
        <i class="fas fa-users"></i>
        Total: <?= (int)($count ?? 0) ?>
      </span>
      <a href="<?= BASE_URL ?>/admin/checklist/csv" class="inline-flex items-center gap-2 bg-ca-green hover:bg-ca-navy text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors shadow">
        <i class="fas fa-file-csv"></i>
        Exportar CSV
      </a>
    </div>
  </div>

  <!-- Flash Messages -->
  <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="mb-6 px-5 py-4 rounded-lg border text-sm font-medium <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
      <?= htmlspecialchars($_SESSION['flash_message']) ?>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <!-- Table -->
  <?php if (empty($downloads)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
      <div class="text-gray-300 text-5xl mb-4"><i class="fas fa-inbox"></i></div>
      <h3 class="text-lg font-semibold text-gray-500 mb-2">No hay descargas registradas</h3>
      <p class="text-gray-400 text-sm">Cuando alguien descargue el Checklist Ambiental desde el sitio web, los registros aparecerán aquí.</p>
    </div>
  <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="text-left px-5 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">ID</th>
              <th class="text-left px-5 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Nombre</th>
              <th class="text-left px-5 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Apellidos</th>
              <th class="text-left px-5 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Correo</th>
              <th class="text-left px-5 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Empresa</th>
              <th class="text-left px-5 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Giro</th>
              <th class="text-left px-5 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">IP</th>
              <th class="text-left px-5 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Fecha</th>
              <th class="text-center px-5 py-3.5 font-semibold text-gray-600 text-xs uppercase tracking-wider">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($downloads as $download): ?>
            <tr class="hover:bg-gray-50/50 transition-colors">
              <td class="px-5 py-3.5 text-gray-400 font-mono text-xs">#<?= (int)$download['id'] ?></td>
              <td class="px-5 py-3.5 font-medium text-gray-800"><?= htmlspecialchars($download['nombre']) ?></td>
              <td class="px-5 py-3.5 text-gray-600"><?= htmlspecialchars($download['apellidos']) ?></td>
              <td class="px-5 py-3.5">
                <a href="mailto:<?= htmlspecialchars($download['correo']) ?>" class="text-ca-green hover:text-ca-navy transition-colors font-medium">
                  <?= htmlspecialchars($download['correo']) ?>
                </a>
              </td>
              <td class="px-5 py-3.5 text-gray-600"><?= htmlspecialchars($download['empresa'] ?? '—') ?></td>
              <td class="px-5 py-3.5 text-gray-600"><?= htmlspecialchars($download['giro'] ?? '—') ?></td>
              <td class="px-5 py-3.5 text-gray-400 font-mono text-xs"><?= htmlspecialchars($download['ip_address'] ?? '—') ?></td>
              <td class="px-5 py-3.5 text-gray-500 text-xs whitespace-nowrap">
                <?= format_cdmx_datetime($download['created_at']) ?> CDMX
              </td>
              <td class="px-5 py-3.5 text-center">
                <form action="<?= BASE_URL ?>/admin/checklist/eliminar/<?= (int)$download['id'] ?>" method="POST" onsubmit="return confirm('¿Eliminar este registro de forma permanente?')">
                  <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-sm font-medium" title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>
