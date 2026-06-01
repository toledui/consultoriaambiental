<div class="max-w-3xl mx-auto">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h2 class="text-2xl font-bold text-ca-navy">Nuevo Usuario</h2>
      <p class="text-sm text-gray-500 mt-1">Crea accesos adicionales para el panel de administraci&oacute;n.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/usuarios" class="text-sm text-ca-green hover:text-ca-navy font-medium transition-colors">
      <i class="fas fa-arrow-left mr-1"></i> Volver
    </a>
  </div>

  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <?php if (!empty($errors)): ?>
      <div class="m-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc pl-5 space-y-1">
          <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/admin/usuarios/crear" class="p-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="username">Usuario *</label>
          <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="username" name="username" type="text" required value="<?= htmlspecialchars($old['username'] ?? '') ?>" placeholder="admin2">
          <p class="text-xs text-gray-400 mt-1">Letras, n&uacute;meros, punto, guion y guion bajo.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="email">Correo *</label>
          <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="email" name="email" type="email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="admin@empresa.com">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="password">Contrase&ntilde;a *</label>
          <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
          <p class="text-xs text-gray-400 mt-1">M&iacute;nimo 8 caracteres.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="password_confirmation">Confirmar contrase&ntilde;a *</label>
          <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
        </div>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
        <a href="<?= BASE_URL ?>/admin/usuarios" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-ca-navy transition-colors">Cancelar</a>
        <button type="submit" class="bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
          <i class="fas fa-save mr-1"></i> Crear Usuario
        </button>
      </div>
    </form>
  </div>
</div>
