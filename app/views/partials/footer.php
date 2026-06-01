<!-- Footer -->
<?php
  // Use dark footer theme on individual service and blog detail pages
  $currentUri = $_SERVER['REQUEST_URI'] ?? '';
  $isDetailPage = preg_match('#^/(servicios|blog)/[^/]+$#', $currentUri);
?>
<footer class="<?= $isDetailPage ? 'bg-ca-navy' : 'bg-ca-dark-gray' ?> text-ca-light-gray pt-16 pb-8 border-t-4 border-ca-navy mt-auto" role="contentinfo">
  <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
    
    <!-- Brand & About -->
    <div>
      <a class="flex items-center mb-6" href="<?= BASE_URL ?>" aria-label="Ir al inicio">
        <?php if (!empty($settings['brand_logo'])): ?>
          <img alt="<?= htmlspecialchars($settings['brand_company_name'] ?? 'Consultoría Ambiental') ?>" width="80" height="80" class="h-20 w-auto object-contain" src="<?= BASE_URL ?>/<?= htmlspecialchars($settings['brand_logo']) ?>"/>
        <?php else: ?>
          <img alt="Consultoría Ambiental" width="80" height="80" class="h-20 w-auto object-contain" src="<?= BASE_URL ?>/images/consultoria-ambiental-logo.png"/>
        <?php endif; ?>
      </a>
      <p class="text-sm mb-6 leading-relaxed">
        <?= htmlspecialchars($settings['footer_tagline'] ?? 'Especialistas en soluciones integrales para el cumplimiento ambiental. Protegiendo tu inversión y cuidando el entorno con responsabilidad técnica, compromiso y cumplimiento normativo.') ?>
      </p>
      <?php
        $checklistUrl = !empty($settings['footer_checklist_url']) ? BASE_URL . '/' . htmlspecialchars($settings['footer_checklist_url']) : '#';
        $checklistLabel = htmlspecialchars($settings['footer_checklist_label'] ?? 'Checklist Ambiental');
      ?>
      <button type="button" id="checklistModalBtn" class="inline-flex items-center bg-ca-navy border border-gray-600 hover:border-ca-light-green text-white font-semibold text-xs px-5 py-2.5 rounded shadow transition-colors group cursor-pointer" aria-label="Descargar <?= $checklistLabel ?>">
        <i class="fas fa-file-download mr-2 text-ca-light-green group-hover:animate-bounce"></i> <?= $checklistLabel ?>
      </button>
    </div>

    <!-- Links -->
    <div>
      <h3 class="text-white font-bold mb-6 uppercase text-sm tracking-widest border-l-2 border-ca-green pl-3">Enlaces</h3>
      <ul class="space-y-3 text-sm">
        <?php
        $footerLinks = [];
        if (!empty($settings['footer_links'])) {
            $decoded = json_decode($settings['footer_links'], true);
            $footerLinks = is_array($decoded) ? $decoded : [];
        }
        foreach ($footerLinks as $link):
            $text = $link['text'] ?? '';
            $url  = $link['url'] ?? '';
            if (empty($text)) continue;
            // Determine if it's a full URL or relative path
            $href = (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, '/')) ? $url : BASE_URL . '/' . ltrim($url, '/');
        ?>
          <li><a href="<?= htmlspecialchars($href) ?>" class="hover:text-ca-light-green transition flex items-center"><i class="fas fa-angle-right mr-2 text-xs"></i> <?= htmlspecialchars($text) ?></a></li>
        <?php endforeach; ?>
        <?php if (empty($footerLinks)): ?>
          <li><a href="<?= BASE_URL ?>" class="hover:text-ca-light-green transition flex items-center"><i class="fas fa-angle-right mr-2 text-xs"></i> Inicio</a></li>
          <li><a href="<?= BASE_URL ?>/nosotros" class="hover:text-ca-light-green transition flex items-center"><i class="fas fa-angle-right mr-2 text-xs"></i> Nosotros</a></li>
          <li><a href="<?= BASE_URL ?>/servicios" class="hover:text-ca-light-green transition flex items-center"><i class="fas fa-angle-right mr-2 text-xs"></i> Servicios</a></li>
          <li><a href="<?= BASE_URL ?>/contacto" class="hover:text-ca-light-green transition flex items-center"><i class="fas fa-angle-right mr-2 text-xs"></i> Contacto</a></li>
          <li><a href="<?= BASE_URL ?>/contacto" class="hover:text-ca-light-green transition flex items-center"><i class="fas fa-angle-right mr-2 text-xs"></i> Cobertura</a></li>
          <li><a href="<?= BASE_URL ?>/aviso-de-privacidad" class="hover:text-ca-light-green transition flex items-center"><i class="fas fa-angle-right mr-2 text-xs"></i> Aviso de Privacidad</a></li>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Contact -->
    <div>
      <h3 class="text-white font-bold mb-6 uppercase text-sm tracking-widest border-l-2 border-ca-green pl-3">Contacto</h3>
      <ul class="space-y-4 text-sm">
        <li class="flex items-start gap-3">
          <div class="bg-ca-navy p-2 rounded text-ca-light-green"><i class="fas fa-phone-alt"></i></div>
          <div class="pt-1">
            <span class="block text-xs text-gray-400 uppercase"><?= htmlspecialchars($settings['footer_phone_label'] ?? 'Teléfono') ?></span>
            <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $settings['footer_phone_value'] ?? '')) ?>" class="hover:text-white transition"><?= htmlspecialchars($settings['footer_phone_value'] ?? '+52 (33) 1234-5678') ?></a>
          </div>
        </li>
        <li class="flex items-start gap-3">
          <div class="bg-ca-navy p-2 rounded text-ca-light-green"><i class="fab fa-whatsapp"></i></div>
          <div class="pt-1">
            <span class="block text-xs text-gray-400 uppercase"><?= htmlspecialchars($settings['footer_whatsapp_label'] ?? 'WhatsApp') ?></span>
            <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9]/', '', $settings['footer_whatsapp_value'] ?? '')) ?>" class="hover:text-white transition"><?= htmlspecialchars($settings['footer_whatsapp_value'] ?? '+52 (33) 8765-4321') ?></a>
          </div>
        </li>
        <li class="flex items-start gap-3">
          <div class="bg-ca-navy p-2 rounded text-ca-light-green"><i class="fas fa-envelope"></i></div>
          <div class="pt-1">
            <span class="block text-xs text-gray-400 uppercase"><?= htmlspecialchars($settings['footer_email_label'] ?? 'Correo') ?></span>
            <a href="mailto:<?= htmlspecialchars($settings['footer_email_value'] ?? 'contacto@consultoria-ca.com') ?>" class="hover:text-white transition"><?= htmlspecialchars($settings['footer_email_value'] ?? 'contacto@consultoria-ca.com') ?></a>
          </div>
        </li>
      </ul>
    </div>

    <!-- Social / News -->
    <div>
      <h3 class="text-white font-bold mb-6 uppercase text-sm tracking-widest border-l-2 border-ca-green pl-3">Síguenos</h3>
      <div class="flex items-center space-x-3 mb-8 flex-wrap gap-y-3">
        <?php
        $footerSocial = [];
        if (!empty($settings['social_footer'])) {
            $decoded = json_decode($settings['social_footer'], true);
            $footerSocial = is_array($decoded) ? $decoded : [];
        }
        foreach ($footerSocial as $item):
            $icon = $item['icon'] ?? '';
            $url  = $item['url'] ?? '';
            if (empty($icon) || empty($url)) continue;
            // Determine icon class
            $iconClass = (in_array($icon, ['envelope', 'globe', 'link'])) ? 'fas fa-' . $icon : 'fab fa-' . $icon;
            $socialName = $item['name'] ?? $icon;
        ?>
          <a class="w-10 h-10 rounded bg-ca-navy flex items-center justify-center hover:bg-ca-green hover:text-white transition-colors shadow" href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($socialName) ?>"><i class="<?= $iconClass ?>"></i></a>
        <?php endforeach; ?>
      </div>
      
      <!-- Latest News Banner (dynamic) -->
      <?php
        // Fetch the latest published blog post for the footer
        $footerLatestPost = \App\Models\BlogPost::getLatest(1);
        $footerPost = $footerLatestPost[0] ?? null;
      ?>
      <div class="bg-ca-navy/50 p-4 rounded-lg border-l-4 border-ca-light-green text-xs shadow-inner">
        <div class="flex items-center gap-2 mb-2">
          <span class="bg-red-700 text-white px-2 py-0.5 rounded text-[10px] font-bold uppercase animate-pulse">Nuevo</span>
          <?php if ($footerPost): ?>
            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($footerPost['slug']) ?>" class="text-white font-bold hover:text-ca-light-green transition-colors"><?= htmlspecialchars($footerPost['title']) ?></a>
          <?php else: ?>
            <span class="text-white font-bold">Ley de Economía Circular</span>
          <?php endif; ?>
        </div>
        <?php if ($footerPost && !empty($footerPost['excerpt'])): ?>
          <p class="text-gray-300 leading-relaxed"><?= htmlspecialchars($footerPost['excerpt']) ?></p>
        <?php else: ?>
          <p class="text-gray-300 leading-relaxed">Prepárate para las nuevas obligaciones ambientales. Impulsamos un modelo empresarial enfocado en la reducción de residuos.</p>
        <?php endif; ?>
      </div>
    </div>

  </div>
  
  <div class="container mx-auto px-4 pt-6 border-t border-gray-700/50 flex flex-col md:flex-row justify-between items-center text-xs text-gray-400">
    <p>Derechos reservados &copy; <?= date('Y') ?> <?= htmlspecialchars($settings['footer_copyright'] ?? 'Consultoría Ambiental CA.') ?></p>
    <p class="mt-2 md:mt-0 font-medium">Cumplimiento <span class="text-ca-green mx-1">+</span> Sostenibilidad <span class="text-ca-green mx-1">+</span> Compromiso</p>
  </div>
</footer>

<!-- Checklist Download Modal -->
<div id="checklistModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="checklistModalTitle">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="checklistModalContent">
    <!-- Modal Header -->
    <div class="bg-ca-navy px-6 py-4 rounded-t-2xl flex items-center justify-between">
      <div>
        <h3 id="checklistModalTitle" class="text-white text-lg font-bold flex items-center gap-2">
          <i class="fas fa-file-download text-ca-light-green"></i>
          Descargar Checklist Ambiental
        </h3>
        <p class="text-gray-300 text-xs mt-1">Completa tus datos para recibir el documento</p>
      </div>
      <button type="button" id="checklistModalClose" class="text-gray-400 hover:text-white transition-colors text-2xl leading-none cursor-pointer" aria-label="Cerrar">&times;</button>
    </div>

    <!-- Modal Body -->
    <div class="p-6">
      <!-- Success state (hidden by default) -->
      <div id="checklistSuccess" class="hidden text-center py-6">
        <div class="text-ca-green text-5xl mb-4"><i class="fas fa-check-circle"></i></div>
        <h4 class="text-xl font-bold text-ca-navy mb-2">¡Gracias por registrarte!</h4>
        <p class="text-gray-600 mb-6" id="checklistSuccessMessage">Tu descarga comenzará en breve.</p>
        <a id="checklistDownloadLink" href="#" class="inline-flex items-center bg-ca-navy hover:bg-ca-green text-white font-semibold px-6 py-3 rounded-lg shadow transition-colors" download>
          <i class="fas fa-download mr-2"></i> Descargar ahora
        </a>
      </div>

      <!-- Form -->
      <form id="checklistForm" novalidate>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <label for="checklistNombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
            <input type="text" id="checklistNombre" name="nombre" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition text-sm" placeholder="Tu nombre">
          </div>
          <div>
            <label for="checklistApellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellidos <span class="text-red-500">*</span></label>
            <input type="text" id="checklistApellidos" name="apellidos" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition text-sm" placeholder="Tus apellidos">
          </div>
        </div>
        <div class="mb-4">
          <label for="checklistCorreo" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico <span class="text-red-500">*</span></label>
          <input type="email" id="checklistCorreo" name="correo" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition text-sm" placeholder="correo@ejemplo.com">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
          <div>
            <label for="checklistEmpresa" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
            <input type="text" id="checklistEmpresa" name="empresa" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition text-sm" placeholder="Nombre de tu empresa">
          </div>
          <div>
            <label for="checklistGiro" class="block text-sm font-medium text-gray-700 mb-1">Giro / Sector</label>
            <select id="checklistGiro" name="giro" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition text-sm bg-white">
              <option value="">Selecciona un sector</option>
              <option value="Agroindustrial">Agroindustrial</option>
              <option value="Automotriz">Automotriz</option>
              <option value="Comercio">Comercio</option>
              <option value="Construcción">Construcción</option>
              <option value="Energía">Energía</option>
              <option value="Gobierno">Gobierno</option>
              <option value="Hotelero">Hotelero</option>
              <option value="Industrial">Industrial</option>
              <option value="Manufactura">Manufactura</option>
              <option value="Minería">Minería</option>
              <option value="Químico">Químico</option>
              <option value="Servicios">Servicios</option>
              <option value="Tecnología">Tecnología</option>
              <option value="Transporte">Transporte</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
        </div>

        <!-- Newsletter subscription -->
        <div class="mb-6">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="newsletter" value="1" checked class="w-4 h-4 rounded border-gray-300 text-ca-green focus:ring-ca-green cursor-pointer">
            <span class="text-sm text-gray-600">Acepto suscribirme al newsletter para recibir noticias y actualizaciones sobre normativa ambiental</span>
          </label>
        </div>

        <!-- Error messages -->
        <div id="checklistError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4"></div>

        <button type="submit" id="checklistSubmitBtn" class="w-full bg-ca-navy hover:bg-ca-green text-white font-bold py-3 px-6 rounded-lg shadow transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
          <i class="fas fa-download"></i>
          <span id="checklistSubmitText">Descargar Checklist</span>
          <svg id="checklistSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </button>

        <p class="text-xs text-gray-400 text-center mt-4">Tus datos están seguros. No compartiremos tu información con terceros.</p>
      </form>
    </div>
  </div>
</div>

<script>
(function() {
  'use strict';

  const modal       = document.getElementById('checklistModal');
  const modalContent = document.getElementById('checklistModalContent');
  const openBtn     = document.getElementById('checklistModalBtn');
  const closeBtn    = document.getElementById('checklistModalClose');
  const form        = document.getElementById('checklistForm');
  const errorDiv    = document.getElementById('checklistError');
  const successDiv  = document.getElementById('checklistSuccess');
  const submitBtn   = document.getElementById('checklistSubmitBtn');
  const submitText  = document.getElementById('checklistSubmitText');
  const spinner     = document.getElementById('checklistSpinner');
  const downloadLink = document.getElementById('checklistDownloadLink');
  const successMsg  = document.getElementById('checklistSuccessMessage');

  const BASE = '<?= BASE_URL ?>';

  function openModal() {
    modal.classList.remove('hidden');
    // Trigger reflow for transition
    void modal.offsetWidth;
    modalContent.classList.remove('scale-95', 'opacity-0');
    modalContent.classList.add('scale-100', 'opacity-100');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = '';
      // Reset form state after close animation
      setTimeout(() => {
        form.reset();
        form.classList.remove('hidden');
        successDiv.classList.add('hidden');
        errorDiv.classList.add('hidden');
        submitBtn.disabled = false;
        submitText.textContent = 'Descargar Checklist';
        spinner.classList.add('hidden');
      }, 100);
    }, 300);
  }

  // Open button
  if (openBtn) {
    openBtn.addEventListener('click', openModal);
  }

  // Close button
  if (closeBtn) {
    closeBtn.addEventListener('click', closeModal);
  }

  // Click outside modal content
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      closeModal();
    }
  });

  // Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
      closeModal();
    }
  });

  // Form submission
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      // Basic client-side validation
      const nombre    = document.getElementById('checklistNombre').value.trim();
      const apellidos = document.getElementById('checklistApellidos').value.trim();
      const correo    = document.getElementById('checklistCorreo').value.trim();

      if (!nombre || !apellidos || !correo) {
        errorDiv.textContent = 'Por favor completa todos los campos obligatorios.';
        errorDiv.classList.remove('hidden');
        return;
      }

      // Email validation
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        errorDiv.textContent = 'Por favor ingresa un correo electrónico válido.';
        errorDiv.classList.remove('hidden');
        return;
      }

      // Show loading state
      errorDiv.classList.add('hidden');
      submitBtn.disabled = true;
      submitText.textContent = 'Procesando...';
      spinner.classList.remove('hidden');

      const formData = new FormData(form);

      fetch(BASE + '/checklist/descargar', {
        method: 'POST',
        body: formData
      })
      .then(function(response) {
        return response.json().then(function(data) {
          return { status: response.status, data: data };
        });
      })
      .then(function(result) {
        if (result.data.success) {
          // Show success state
          form.classList.add('hidden');
          successDiv.classList.remove('hidden');
          successMsg.textContent = result.data.message || '¡Gracias! Tu descarga comenzará en breve.';
          if (result.data.file_url) {
            downloadLink.href = result.data.file_url;
            // Auto-trigger download after a short delay
            setTimeout(function() {
              window.open(result.data.file_url, '_blank');
            }, 500);
          }
        } else {
          errorDiv.textContent = result.data.message || 'Ocurrió un error. Intenta de nuevo.';
          errorDiv.classList.remove('hidden');
          submitBtn.disabled = false;
          submitText.textContent = 'Descargar Checklist';
          spinner.classList.add('hidden');
        }
      })
      .catch(function() {
        errorDiv.textContent = 'Error de conexión. Intenta de nuevo.';
        errorDiv.classList.remove('hidden');
        submitBtn.disabled = false;
        submitText.textContent = 'Descargar Checklist';
        spinner.classList.add('hidden');
      });
    });
  }
})();
</script>
