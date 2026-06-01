<div class="max-w-4xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <a href="<?= BASE_URL ?>/admin/contactos" class="text-sm text-ca-green hover:text-ca-navy transition-colors mb-2 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Volver a contactos
      </a>
      <h1 class="text-2xl font-bold text-ca-navy">Detalle del Contacto</h1>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/admin/contactos/eliminar/<?= $contact['id'] ?>" onsubmit="return confirm('¿Eliminar este contacto?')" class="inline">
      <button type="submit" class="px-4 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
        <i class="fas fa-trash mr-1"></i> Eliminar
      </button>
    </form>
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

  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header Status -->
    <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 bg-ca-navy/10 rounded-full flex items-center justify-center text-ca-navy text-xl">
          <i class="fas fa-user"></i>
        </div>
        <div>
          <h2 class="text-xl font-bold text-ca-navy"><?= htmlspecialchars($contact['nombre']) ?></h2>
          <p class="text-sm text-gray-500">
            Recibido: <?= date('d/m/Y \a \l\a\s H:i', strtotime($contact['created_at'])) ?>
          </p>
        </div>
      </div>
      <div>
        <?php if ($contact['read_at'] !== null): ?>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
            <i class="fas fa-check-circle mr-1"></i> Leído <?= date('d/m/Y H:i', strtotime($contact['read_at'])) ?>
          </span>
        <?php else: ?>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <i class="fas fa-circle text-[6px] mr-1 text-blue-500"></i> No leído
          </span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Contact Details -->
    <div class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
          <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Correo electrónico</label>
          <a href="mailto:<?= htmlspecialchars($contact['correo']) ?>" class="text-ca-green hover:underline font-medium">
            <?= htmlspecialchars($contact['correo']) ?>
          </a>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
          <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Teléfono</label>
          <a href="tel:<?= htmlspecialchars($contact['telefono']) ?>" class="text-ca-navy hover:underline font-medium">
            <?= htmlspecialchars($contact['telefono']) ?>
          </a>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
          <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Sector</label>
          <span class="text-ca-navy font-medium"><?= htmlspecialchars($contact['sector'] ?? 'No especificado') ?></span>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
          <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Newsletter</label>
          <?php if ($contact['newsletter']): ?>
            <span class="inline-flex items-center gap-1 text-ca-green font-medium">
              <i class="fas fa-check-circle"></i> Suscrito al boletín
            </span>
          <?php else: ?>
            <span class="inline-flex items-center gap-1 text-gray-400 font-medium">
              <i class="fas fa-times-circle"></i> No suscrito
            </span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Message -->
      <div>
        <h3 class="text-lg font-semibold text-ca-navy mb-3 flex items-center gap-2">
          <i class="fas fa-comment-alt text-ca-green"></i>
          Mensaje
        </h3>
        <div class="p-5 bg-gray-50 rounded-lg border border-gray-100">
          <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($contact['mensaje']) ?></p>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap gap-3">
        <a href="mailto:<?= htmlspecialchars($contact['correo']) ?>?subject=Respuesta%20a%20tu%20solicitud%20-%20<?= rawurlencode(APP_NAME) ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-ca-green text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
          <i class="fas fa-reply"></i>
          Responder por correo
        </a>
        <a href="tel:<?= htmlspecialchars($contact['telefono']) ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-ca-navy text-white rounded-lg hover:bg-gray-800 transition-colors text-sm font-medium">
          <i class="fas fa-phone"></i>
          Llamar
        </a>
      </div>
    </div>
  </div>
</div>
