<form method="POST" action="<?= BASE_URL ?>/admin/settings/smtp/guardar" class="space-y-6">
  <div>
    <h3 class="text-lg font-semibold text-ca-navy mb-4">Servidor SMTP</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-1">Host del servidor</label>
        <input type="text" id="smtp_host" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
      <div>
        <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-1">Puerto</label>
        <input type="text" id="smtp_port" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>" placeholder="587" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
      <div>
        <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 mb-1">Encriptación</label>
        <select id="smtp_encryption" name="smtp_encryption" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
          <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
          <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
          <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>Sin encriptación</option>
        </select>
      </div>
      <div>
        <label for="smtp_username" class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
        <input type="text" id="smtp_username" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" placeholder="correo@ejemplo.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
      <div>
        <label for="smtp_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
        <input type="password" id="smtp_password" name="smtp_password" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>" placeholder="••••••••" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
    </div>
  </div>

  <hr class="border-gray-200">

  <div>
    <h3 class="text-lg font-semibold text-ca-navy mb-4">Correo por defecto</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="smtp_from_email" class="block text-sm font-medium text-gray-700 mb-1">Correo remitente</label>
        <input type="email" id="smtp_from_email" name="smtp_from_email" value="<?= htmlspecialchars($settings['smtp_from_email'] ?? '') ?>" placeholder="no-reply@gestoriaambiental.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
      <div>
        <label for="smtp_from_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre remitente</label>
        <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? 'Gestoría Ambiental') ?>" placeholder="Gestoría Ambiental" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
    </div>
  </div>

  <div class="flex justify-end pt-4 border-t border-gray-200">
    <button type="submit" class="px-6 py-2.5 bg-ca-green text-white font-medium rounded-lg hover:bg-ca-navy transition-colors text-sm">
      <i class="fas fa-save mr-2"></i>
      Guardar configuración SMTP
    </button>
  </div>
</form>

<!-- ─── Test Email Section ──────────────────────────────────────── -->
<hr class="border-gray-300 my-8">

<div class="bg-gradient-to-r from-ca-navy/5 to-transparent rounded-xl p-6 border border-gray-200">
  <div class="flex items-center gap-3 mb-4">
    <div class="bg-ca-green/10 p-2.5 rounded-full">
      <i class="fas fa-paper-plane text-ca-green text-lg"></i>
    </div>
    <div>
      <h3 class="text-lg font-semibold text-ca-navy">Probar configuración SMTP</h3>
      <p class="text-sm text-gray-500">Envía un correo de prueba para verificar que la configuración funciona correctamente.</p>
    </div>
  </div>

  <form method="POST" action="<?= BASE_URL ?>/admin/settings/smtp/probar" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
    <div class="flex-1">
      <label for="test_email" class="block text-sm font-medium text-gray-700 mb-1">Correo destino</label>
      <input type="email" id="test_email" name="test_email" required placeholder="tucorreo@ejemplo.com" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
    </div>
    <button type="submit" class="px-6 py-2.5 bg-ca-navy text-white font-medium rounded-lg hover:bg-ca-green transition-colors text-sm whitespace-nowrap">
      <i class="fas fa-paper-plane mr-2"></i>
      Enviar correo de prueba
    </button>
  </form>

  <div class="mt-4 flex items-start gap-2 text-xs text-gray-400">
    <i class="fas fa-info-circle mt-0.5"></i>
    <p>Se enviará un correo con diseño HTML completo al destino que indiques. Asegúrate de haber guardado la configuración SMTP antes de probar.</p>
  </div>
</div>
