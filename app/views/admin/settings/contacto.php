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

  <div class="border-t border-gray-200 pt-6">
    <div class="flex items-start gap-3 mb-5">
      <div class="bg-orange-50 p-2 rounded-lg">
        <i class="fas fa-shield-halved text-orange-600"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Cloudflare Turnstile</h3>
        <p class="text-sm text-gray-500">Protege los formularios p&uacute;blicos de contacto, descarga del checklist y newsletter contra env&iacute;os automatizados.</p>
      </div>
    </div>

    <label class="flex items-start gap-3 p-4 mb-5 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer">
      <input type="checkbox" name="turnstile_enabled" value="1" class="mt-1 w-4 h-4 rounded border-gray-300 text-ca-green focus:ring-ca-green" <?= ($settings['turnstile_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
      <span>
        <span class="block text-sm font-semibold text-gray-700">Activar protecci&oacute;n Turnstile</span>
        <span class="block text-xs text-gray-500 mt-1">Al activarlo, los formularios no se procesar&aacute;n hasta que Cloudflare valide el token.</span>
      </span>
    </label>

    <div class="grid grid-cols-1 gap-5">
      <div>
        <label for="turnstile_site_key" class="block text-sm font-medium text-gray-700 mb-2">Clave del sitio (Site Key)</label>
        <input type="text" id="turnstile_site_key" name="turnstile_site_key" autocomplete="off" value="<?= htmlspecialchars($settings['turnstile_site_key'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm font-mono" placeholder="0x4AAAAAAA...">
        <p class="text-xs text-gray-500 mt-1">Esta clave es p&uacute;blica y se usa para mostrar el widget.</p>
      </div>

      <div>
        <label for="turnstile_secret_key" class="block text-sm font-medium text-gray-700 mb-2">Clave secreta</label>
        <input type="password" id="turnstile_secret_key" name="turnstile_secret_key" autocomplete="new-password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm font-mono" placeholder="<?= !empty($settings['turnstile_secret_key']) ? 'Configurada; deja vac&iacute;o para conservarla' : '0x4AAAAAAA...' ?>">
        <p class="text-xs text-gray-500 mt-1">La clave secreta nunca se muestra en el frontend. Si ya est&aacute; configurada, deja este campo vac&iacute;o para conservarla.</p>
      </div>
    </div>

    <div class="mt-5 p-4 rounded-lg bg-blue-50 border border-blue-200 text-sm text-blue-700">
      <i class="fas fa-circle-info mr-1"></i>
      Crea el widget en el panel de Cloudflare Turnstile y agrega los dominios donde funciona este sitio.
      <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank" rel="noopener noreferrer" class="font-semibold underline">Abrir Cloudflare</a>
      <span class="block mt-1">Usa las claves de prueba oficiales solamente en desarrollo; en producci&oacute;n debes usar las claves de tu widget.</span>
    </div>
  </div>

  <div class="flex justify-end pt-4 border-t border-gray-200">
    <button type="submit" class="px-6 py-2.5 bg-ca-green text-white font-medium rounded-lg hover:bg-ca-navy transition-colors text-sm">
      <i class="fas fa-save mr-2"></i>
      Guardar configuración de contacto
    </button>
  </div>
</form>
