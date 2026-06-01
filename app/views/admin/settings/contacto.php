<form method="POST" action="<?= BASE_URL ?>/admin/settings/contacto/guardar" class="space-y-6">
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-envelope-open-text text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Correos receptores del formulario de contacto</h3>
        <p class="text-sm text-gray-500">Define a qué direcciones de correo se enviarán las notificaciones cuando un usuario complete el formulario de contacto.</p>
      </div>
    </div>

    <div class="mb-4">
      <label for="contact_emails" class="block text-sm font-medium text-gray-700 mb-2">
        <i class="fas fa-at mr-1 text-ca-navy"></i> Correos electrónicos
      </label>
      <textarea id="contact_emails" name="contact_emails" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm font-mono" placeholder="contacto@consultoria-ca.com&#10;admin@consultoria-ca.com"><?= htmlspecialchars($settings['contact_emails'] ?? '') ?></textarea>
      <p class="text-xs text-gray-500 mt-2">
        <i class="fas fa-info-circle mr-1"></i>
        Ingresa un correo por línea o sepáralos por comas. Se enviará una notificación a cada dirección cuando alguien envíe el formulario de contacto.
      </p>
    </div>
  </div>

  <div class="flex justify-end pt-4 border-t border-gray-200">
    <button type="submit" class="px-6 py-2.5 bg-ca-green text-white font-medium rounded-lg hover:bg-ca-navy transition-colors text-sm">
      <i class="fas fa-save mr-2"></i>
      Guardar configuración de contacto
    </button>
  </div>
</form>
