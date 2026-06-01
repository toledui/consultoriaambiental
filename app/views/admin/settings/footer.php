<form method="POST" action="<?= BASE_URL ?>/admin/settings/footer/guardar" class="space-y-8" enctype="multipart/form-data">

  <?php
  // Decode footer links JSON
  $footerLinks = [];
  if (!empty($settings['footer_links'])) {
      $decoded = json_decode($settings['footer_links'], true);
      $footerLinks = is_array($decoded) ? $decoded : [];
  }
  ?>

  <!-- ─── Contact Info ─────────────────────────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-address-card text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Información de Contacto — Footer</h3>
        <p class="text-sm text-gray-500">Estos datos aparecen en la sección de contacto del pie de página.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Phone -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-phone-alt mr-1 text-ca-navy"></i> Teléfono — Etiqueta
        </label>
        <input type="text" name="footer_phone_label" value="<?= htmlspecialchars($settings['footer_phone_label'] ?? 'Teléfono') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-phone-alt mr-1 text-ca-navy"></i> Teléfono — Valor
        </label>
        <input type="text" name="footer_phone_value" value="<?= htmlspecialchars($settings['footer_phone_value'] ?? '+52 (33) 1234-5678') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>

      <!-- WhatsApp -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fab fa-whatsapp mr-1 text-[#25D366]"></i> WhatsApp — Etiqueta
        </label>
        <input type="text" name="footer_whatsapp_label" value="<?= htmlspecialchars($settings['footer_whatsapp_label'] ?? 'WhatsApp') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fab fa-whatsapp mr-1 text-[#25D366]"></i> WhatsApp — Valor
        </label>
        <input type="text" name="footer_whatsapp_value" value="<?= htmlspecialchars($settings['footer_whatsapp_value'] ?? '+52 (33) 8765-4321') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>

      <!-- WhatsApp Floating Button -->
      <div class="md:col-span-2">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="bg-[#25D366] p-2 rounded-lg">
              <i class="fab fa-whatsapp text-white"></i>
            </div>
            <div>
              <h4 class="text-sm font-semibold text-ca-navy">Botón Flotante de WhatsApp</h4>
              <p class="text-xs text-gray-500">Configura el número y mensaje del botón flotante que aparece en toda la web.</p>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fab fa-whatsapp mr-1 text-[#25D366]"></i> Número (solo dígitos)
              </label>
              <input type="text" name="whatsapp_floating_number" value="<?= htmlspecialchars($settings['whatsapp_floating_number'] ?? '523387654321') ?>" placeholder="523387654321" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
              <p class="text-xs text-gray-400 mt-1">Solo dígitos, sin + ni espacios. Ej: 523387654321</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-comment-dots mr-1 text-[#25D366]"></i> Mensaje predeterminado
              </label>
              <input type="text" name="whatsapp_floating_message" value="<?= htmlspecialchars($settings['whatsapp_floating_message'] ?? 'Hola, me gustaría recibir información sobre sus servicios de consultoría ambiental.') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
              <p class="text-xs text-gray-400 mt-1">Texto que se enviará automáticamente al abrir el chat.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-envelope mr-1 text-ca-navy"></i> Correo — Etiqueta
        </label>
        <input type="text" name="footer_email_label" value="<?= htmlspecialchars($settings['footer_email_label'] ?? 'Correo') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-envelope mr-1 text-ca-navy"></i> Correo — Valor
        </label>
        <input type="email" name="footer_email_value" value="<?= htmlspecialchars($settings['footer_email_value'] ?? 'contacto@consultoria-ca.com') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
    </div>
  </div>

  <hr class="border-gray-200">

  <!-- ─── Tagline ───────────────────────────────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-quote-left text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Descripción / Tagline</h3>
        <p class="text-sm text-gray-500">Texto descriptivo que aparece debajo del logo en el footer.</p>
      </div>
    </div>
    <textarea name="footer_tagline" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm"><?= htmlspecialchars($settings['footer_tagline'] ?? '') ?></textarea>
  </div>

  <hr class="border-gray-200">

  <!-- ─── Footer Links ──────────────────────────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-link text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Enlaces del Footer</h3>
        <p class="text-sm text-gray-500">Agrega, edita o elimina los enlaces que aparecen en la sección "Enlaces" del pie de página.</p>
      </div>
    </div>
    <div id="footer-links-container" class="space-y-3">
      <?php foreach ($footerLinks as $link): ?>
      <div class="footer-link-row flex items-center gap-3">
        <div class="flex-1">
          <input type="text" name="footer_links[text][]" value="<?= htmlspecialchars($link['text'] ?? '') ?>" placeholder="Texto del enlace" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <div class="flex-[2]">
          <input type="text" name="footer_links[url][]" value="<?= htmlspecialchars($link['url'] ?? '') ?>" placeholder="/ruta-o-https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <button type="button" class="remove-link-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <?php endforeach; ?>
      <?php if (empty($footerLinks)): ?>
      <div class="footer-link-row flex items-center gap-3">
        <div class="flex-1">
          <input type="text" name="footer_links[text][]" value="" placeholder="Texto del enlace" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <div class="flex-[2]">
          <input type="text" name="footer_links[url][]" value="" placeholder="/ruta-o-https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <button type="button" class="remove-link-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <?php endif; ?>
    </div>
    <button type="button" class="add-link-btn mt-3 px-4 py-2 bg-ca-navy/10 text-ca-navy rounded-lg hover:bg-ca-navy/20 transition-colors text-sm font-medium">
      <i class="fas fa-plus mr-1"></i> Agregar enlace
    </button>
  </div>

  <hr class="border-gray-200">

  <!-- ─── Checklist Ambiental ────────────────────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-file-download text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Checklist Ambiental</h3>
        <p class="text-sm text-gray-500">Documento descargable que aparece en el footer. Sube un archivo PDF.</p>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-tag mr-1 text-ca-navy"></i> Texto del botón
        </label>
        <input type="text" name="footer_checklist_label" value="<?= htmlspecialchars($settings['footer_checklist_label'] ?? 'Checklist Ambiental') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-upload mr-1 text-ca-navy"></i> Archivo PDF
        </label>
        <input type="file" name="footer_checklist_file" accept=".pdf,.doc,.docx" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm file:mr-3 file:py-1.5 file:px-3 file:border-0 file:bg-ca-navy/10 file:text-ca-navy file:rounded file:text-xs hover:file:bg-ca-navy/20">
        <?php if (!empty($settings['footer_checklist_url'])): ?>
          <p class="text-xs text-gray-500 mt-2">
            <i class="fas fa-check-circle text-ca-green mr-1"></i> Archivo actual:
            <a href="<?= BASE_URL ?>/<?= htmlspecialchars($settings['footer_checklist_url']) ?>" target="_blank" class="text-ca-green hover:underline">Ver archivo</a>
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <hr class="border-gray-200">

  <!-- ─── Copyright ─────────────────────────────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-copyright text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Copyright</h3>
        <p class="text-sm text-gray-500">Texto de derechos reservados. Se mostrará como: "Derechos reservados © 2026 [tu texto]".</p>
      </div>
    </div>
    <input type="text" name="footer_copyright" value="<?= htmlspecialchars($settings['footer_copyright'] ?? 'Consultoría Ambiental CA.') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
  </div>

  <div class="flex justify-end pt-4 border-t border-gray-200">
    <button type="submit" class="px-6 py-2.5 bg-ca-green text-white font-medium rounded-lg hover:bg-ca-navy transition-colors text-sm">
      <i class="fas fa-save mr-2"></i>
      Guardar configuración del footer
    </button>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add link row
    document.querySelector('.add-link-btn')?.addEventListener('click', function() {
        const container = document.getElementById('footer-links-container');
        const row = document.createElement('div');
        row.className = 'footer-link-row flex items-center gap-3';
        row.innerHTML = `
            <div class="flex-1">
                <input type="text" name="footer_links[text][]" value="" placeholder="Texto del enlace" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
            </div>
            <div class="flex-[2]">
                <input type="text" name="footer_links[url][]" value="" placeholder="/ruta-o-https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
            </div>
            <button type="button" class="remove-link-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(row);
        attachRemoveHandler(row.querySelector('.remove-link-btn'));
    });

    // Remove link row
    document.querySelectorAll('.remove-link-btn').forEach(function(btn) {
        attachRemoveHandler(btn);
    });

    function attachRemoveHandler(btn) {
        btn.addEventListener('click', function() {
            const row = this.closest('.footer-link-row');
            const container = row.parentElement;
            if (container.querySelectorAll('.footer-link-row').length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input').forEach(function(input) { input.value = ''; });
            }
        });
    }
});
</script>
