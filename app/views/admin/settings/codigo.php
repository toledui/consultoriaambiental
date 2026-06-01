<form method="POST" action="<?= BASE_URL ?>/admin/settings/codigo/guardar" class="space-y-8">

  <!-- ─── Header Code ──────────────────────────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-code text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Código Personalizado — &lt;head></h3>
        <p class="text-sm text-gray-500">Este código se insertará antes del cierre de la etiqueta <code>&lt;/head></code> en todas las páginas.</p>
      </div>
    </div>
    <div>
      <textarea
        name="custom_head_code"
        rows="8"
        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition-colors"
        placeholder="&lt;!-- Píxel de Facebook -->
&lt;script>
  // tu código aquí
&lt;/script>

&lt;link rel=&quot;stylesheet&quot; href=&quot;...&quot;>"
      ><?= htmlspecialchars($settings['custom_head_code'] ?? '') ?></textarea>
      <p class="text-xs text-gray-400 mt-1">Útil para píxeles de redes sociales, Google Analytics, Google Tag Manager, CSS personalizado, etc.</p>
    </div>
  </div>

  <hr class="border-gray-200">

  <!-- ─── Body Code ────────────────────────────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-code text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Código Personalizado — antes de &lt;/body></h3>
        <p class="text-sm text-gray-500">Este código se insertará antes del cierre de la etiqueta <code>&lt;/body></code> en todas las páginas.</p>
      </div>
    </div>
    <div>
      <textarea
        name="custom_body_code"
        rows="8"
        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition-colors"
        placeholder="&lt;!-- Google Tag Manager (noscript) -->
&lt;noscript>&lt;iframe src=&quot;...&quot; height=&quot;0&quot; width=&quot;0&quot;>&lt;/iframe>&lt;/noscript>

&lt;script>
  // tu código aquí
&lt;/script>"
      ><?= htmlspecialchars($settings['custom_body_code'] ?? '') ?></textarea>
      <p class="text-xs text-gray-400 mt-1">Útil para scripts de terceros, Google Tag Manager (noscript), widgets de chat, etc.</p>
    </div>
  </div>

  <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
    <button type="submit" class="bg-ca-green text-white px-6 py-2.5 rounded-lg hover:bg-ca-navy transition-colors font-medium">
      <i class="fas fa-save mr-2"></i>Guardar cambios
    </button>
  </div>
</form>
