<?php
$flashMessage = $_SESSION['flash_message'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_message'], $_SESSION['flash_error']);
?>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h3 class="text-lg font-bold text-ca-navy">Usuarios del Panel</h3>
      <p class="text-sm text-gray-500"><?= count($users) ?> usuario(s) registrados</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/usuarios/crear" class="bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
      <i class="fas fa-user-plus mr-1"></i> Nuevo Usuario
    </a>
  </div>

  <?php if ($flashMessage): ?>
    <div class="mx-6 mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
      <i class="fas fa-check-circle mr-1"></i> <?= $flashMessage ?>
    </div>
  <?php endif; ?>

  <?php if ($flashError): ?>
    <div class="mx-6 mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      <i class="fas fa-exclamation-circle mr-1"></i> <?= $flashError ?>
    </div>
  <?php endif; ?>

  <?php if (empty($users)): ?>
    <div class="p-12 text-center">
      <i class="fas fa-users-cog text-5xl text-ca-light-gray mb-4"></i>
      <p class="text-gray-500 text-lg">No hay usuarios registrados.</p>
    </div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-ca-dark-gray text-xs uppercase tracking-wider">
          <tr>
            <th class="text-left px-6 py-4 font-semibold">Usuario</th>
            <th class="text-left px-6 py-4 font-semibold">Correo</th>
            <th class="text-left px-6 py-4 font-semibold hidden md:table-cell">Alta</th>
            <th class="text-center px-6 py-4 font-semibold">Sesi&oacute;n</th>
            <th class="text-right px-6 py-4 font-semibold">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($users as $user): ?>
            <?php $isCurrent = (int) ($_SESSION['admin_id'] ?? 0) === (int) $user['id']; ?>
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-ca-navy text-white flex items-center justify-center font-bold">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                  </div>
                  <span class="font-semibold text-ca-navy"><?= htmlspecialchars($user['username']) ?></span>
                </div>
              </td>
              <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($user['email']) ?></td>
              <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                <?= !empty($user['created_at']) ? format_cdmx_datetime($user['created_at'], 'd/m/Y') : '-' ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?php if ($isCurrent): ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Actual
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                    Admin
                  </span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <a href="<?= BASE_URL ?>/admin/usuarios/editar/<?= (int) $user['id'] ?>" class="text-ca-green hover:text-ca-navy transition-colors p-1" title="Editar">
                    <i class="fas fa-edit"></i>
                  </a>
                  <?php if (!$isCurrent && count($users) > 1): ?>
                    <form method="POST" action="<?= BASE_URL ?>/admin/usuarios/eliminar/<?= (int) $user['id'] ?>" onsubmit="return confirm('&iquest;Eliminar este usuario?')" class="inline">
                      <button type="submit" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
