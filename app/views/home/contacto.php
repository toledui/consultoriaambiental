<!-- Inner Hero Section -->
<section class="hero-contact-bg relative pt-32 pb-20 md:pt-40 md:pb-28 border-b-8 border-ca-green">
  <!-- Overlay -->
  <div class="absolute inset-0" style="background-color: #00000080;"></div>
  
  <div class="container mx-auto px-4 md:px-8 relative z-10 text-center">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-ca-light-green/20 border border-ca-light-green/30 text-ca-light-green font-semibold text-sm mb-4 backdrop-blur-sm uppercase tracking-widest">
      Atención Especializada
    </div>
    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight">
      Hablemos de tu proyecto <br class="hidden md:block"/>
      <span class="text-ca-light-green">y su cumplimiento</span>
    </h1>
    <p class="text-lg md:text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
      Nuestro equipo de ingenieros está listo para brindarte un diagnóstico confidencial y soluciones a la medida de tu industria.
    </p>
  </div>
</section>

<!-- Contact Main Section -->
<section class="py-16 md:py-24 pattern-bg relative z-20">
  <div class="container mx-auto px-4 md:px-8 max-w-7xl">
    
    <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">
      
      <!-- Left Column: Contact Info -->
      <div class="w-full lg:w-5/12" data-aos="fade-right">
        <h2 class="text-3xl font-bold text-ca-navy mb-6">Información de Contacto</h2>
        <p class="text-gray-600 mb-10 leading-relaxed">
          Estamos a tu disposición para resolver cualquier duda sobre trámites, inspecciones, gestión de residuos o regularización ambiental. <strong>Contáctanos de forma directa.</strong>
        </p>

        <?php
        $waContactNumber  = preg_replace('/[^0-9]/', '', $settings['footer_whatsapp_value'] ?? '+52 (33) 8765-4321');
        $waContactDisplay = htmlspecialchars($settings['footer_whatsapp_value'] ?? '+52 (33) 8765-4321');
        $waContactMessage = htmlspecialchars($settings['whatsapp_floating_message'] ?? 'Hola, me gustaría recibir información sobre sus servicios de consultoría ambiental.');
        ?>
        <div class="space-y-8">
          <!-- Info Item -->
          <div class="flex items-start gap-4">
            <div class="w-14 h-14 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center text-ca-green text-2xl flex-shrink-0">
              <i class="fab fa-whatsapp"></i>
            </div>
            <div>
              <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Chat Directo</h4>
              <p class="text-lg font-bold text-ca-navy mb-1"><?= $waContactDisplay ?></p>
              <a href="https://wa.me/<?= $waContactNumber ?>?text=<?= urlencode($waContactMessage) ?>" target="_blank" class="text-sm text-ca-green hover:text-ca-navy font-semibold transition-colors">Enviar mensaje por WhatsApp &rarr;</a>
            </div>
          </div>

          <!-- Info Item -->
          <div class="flex items-start gap-4">
            <div class="w-14 h-14 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center text-ca-green text-xl flex-shrink-0">
              <i class="fas fa-phone-alt"></i>
            </div>
            <div>
              <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Teléfono Oficina</h4>
              <p class="text-lg font-bold text-ca-navy mb-1">+52 (33) 1234-5678</p>
              <p class="text-sm text-gray-500">Lunes a Viernes, 9:00 AM - 6:00 PM</p>
            </div>
          </div>

          <!-- Info Item -->
          <div class="flex items-start gap-4">
            <div class="w-14 h-14 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center text-ca-green text-xl flex-shrink-0">
              <i class="fas fa-envelope"></i>
            </div>
            <div>
              <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Correo Electrónico</h4>
              <p class="text-lg font-bold text-ca-navy mb-1">contacto@consultoria-ca.com</p>
              <p class="text-sm text-gray-500">Respuesta en menos de 24 horas hábiles</p>
            </div>
          </div>
        </div>

        <!-- National Coverage Card -->
        <div class="mt-12 bg-ca-navy p-8 rounded-2xl text-white shadow-xl relative overflow-hidden">
          <div class="absolute right-0 bottom-0 opacity-10 transform translate-x-1/4 translate-y-1/4">
            <i class="fas fa-globe-americas text-9xl"></i>
          </div>
          <div class="relative z-10">
            <div class="w-12 h-12 bg-ca-light-green rounded-full flex items-center justify-center mb-4">
              <i class="fas fa-map-marked-alt text-ca-navy text-xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Cobertura Nacional</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
              Brindamos atención, gestión y acompañamiento en proyectos ubicados en distintas regiones del país, coordinados con autoridades de los 3 niveles de gobierno.
            </p>
          </div>
        </div>
      </div>

      <!-- Right Column: Contact Form -->
      <div class="w-full lg:w-7/12" data-aos="fade-left">
        <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl border border-gray-100 relative">
          <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-ca-navy to-ca-green rounded-t-2xl"></div>
          
          <h3 class="text-2xl font-bold text-ca-navy mb-2 mt-2">Solicita un diagnóstico</h3>
          <p class="text-gray-500 text-sm mb-8">Completa el formulario y un especialista en tu sector se pondrá en contacto contigo a la brevedad.</p>
          
          <form action="<?= BASE_URL ?>/contacto" method="POST" class="space-y-6">
            <?php if (isset($_SESSION['contact_error'])): ?>
              <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= $_SESSION['contact_error'] ?></span>
              </div>
              <?php unset($_SESSION['contact_error']); ?>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name -->
              <div>
                <label for="nombre" class="block text-sm font-semibold text-ca-dark-gray mb-2">Nombre completo <span class="text-red-500">*</span></label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                  </div>
                  <input type="text" id="nombre" name="nombre" required class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-gray-50 focus:ring-2 focus:ring-ca-green/50 focus:border-ca-green transition-shadow" placeholder="Ej. Ing. Roberto Sánchez">
                </div>
              </div>
              
              <!-- Email -->
              <div>
                <label for="correo" class="block text-sm font-semibold text-ca-dark-gray mb-2">Correo electrónico <span class="text-red-500">*</span></label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                  </div>
                  <input type="email" id="correo" name="correo" required class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-gray-50 focus:ring-2 focus:ring-ca-green/50 focus:border-ca-green transition-shadow" placeholder="tu@empresa.com">
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Phone -->
              <div>
                <label for="telefono" class="block text-sm font-semibold text-ca-dark-gray mb-2">Teléfono / Celular <span class="text-red-500">*</span></label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-phone text-gray-400"></i>
                  </div>
                  <input type="tel" id="telefono" name="telefono" required class="form-input block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-gray-50 focus:ring-2 focus:ring-ca-green/50 focus:border-ca-green transition-shadow" placeholder="(33) 0000 0000">
                </div>
              </div>

              <!-- Sector (Dropdown for B2B) -->
              <div>
                <label for="sector" class="block text-sm font-semibold text-ca-dark-gray mb-2">Sector de tu empresa <span class="text-red-500">*</span></label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-industry text-gray-400"></i>
                  </div>
                  <select id="sector" name="sector" required class="form-select block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-gray-50 focus:ring-2 focus:ring-ca-green/50 focus:border-ca-green transition-shadow appearance-none">
                    <option value="" disabled selected>Selecciona una industria...</option>
                    <option value="inmobiliario">Inmobiliario / Construcción</option>
                    <option value="tequilero">Industria Tequilera</option>
                    <option value="alimenticio">Alimenticio / Bebidas</option>
                    <option value="agropecuario">Agropecuario</option>
                    <option value="residuos">Tratamiento de Residuos</option>
                    <option value="quimico_farmaceutico">Químico / Farmacéutica</option>
                    <option value="metalurgia_automotriz">Metalurgia / Automotriz</option>
                    <option value="manufactura_general">Manufactura General</option>
                    <option value="comercio_servicios">Plazas Comerciales / Servicios</option>
                    <option value="otro">Otro sector</option>
                  </select>
                  <!-- Custom Arrow for Select -->
                  <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                    <i class="fas fa-chevron-down text-xs"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Message -->
            <div>
              <label for="mensaje" class="block text-sm font-semibold text-ca-dark-gray mb-2">Mensaje o necesidad específica <span class="text-red-500">*</span></label>
              <div class="relative">
                <div class="absolute top-3 left-3 pointer-events-none">
                  <i class="fas fa-comment-alt text-gray-400"></i>
                </div>
                <textarea id="mensaje" name="mensaje" rows="4" required class="form-textarea block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg text-sm text-gray-900 bg-gray-50 focus:ring-2 focus:ring-ca-green/50 focus:border-ca-green transition-shadow resize-none" placeholder="Describe brevemente en qué podemos ayudarte (Ej. Requiero regularizar mis emisiones, tuve una visita de inspección, necesito una MIA...)"><?php
                  $mensajePrefill = '';
                  if (!empty($paquete)) {
                    switch ($paquete) {
                      case 'gestion-evento':
                        $mensajePrefill = 'Hola, me interesa el paquete de Gestión por Evento. Quisiera cotizar un trámite específico. Quedo atento a su respuesta.';
                        break;
                      case 'acompanamiento-regularizacion':
                        $mensajePrefill = 'Hola, me interesa el paquete de Acompañamiento y Regularización. Solicito una evaluación para mi empresa. Quedo atento a su respuesta.';
                        break;
                      case 'acompanamiento-certificacion':
                        $mensajePrefill = 'Hola, me interesa el paquete de Acompañamiento para Certificación Ambiental. Quisiera agendar una cita para conocer más detalles. Quedo atento a su respuesta.';
                        break;
                    }
                  }
                  echo htmlspecialchars($mensajePrefill);
                ?></textarea>
              </div>
            </div>

            <!-- Newsletter Checkbox -->
            <div class="flex items-start gap-3">
              <input type="checkbox" id="newsletter" name="newsletter" value="1" checked
                     class="mt-1 w-4 h-4 text-ca-green border-gray-300 rounded focus:ring-ca-green">
              <label for="newsletter" class="text-sm text-gray-600 leading-relaxed">
                <span class="font-medium text-ca-navy">Suscríbete a nuestro boletín</span>
                <br/>Recibe información relevante sobre regulación ambiental, novedades normativas y consejos para tu empresa. Puedes darte de baja en cualquier momento.
              </label>
            </div>

            <!-- Submit & Privacy -->
            <div class="pt-4 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-gray-100">
              <div class="flex items-start text-xs text-gray-500 max-w-xs">
                <i class="fas fa-shield-alt text-ca-light-green mt-0.5 mr-2 text-sm"></i>
                <span>Tus datos están protegidos. Al enviar, aceptas nuestro <a href="#" class="text-ca-navy underline hover:text-ca-green">Aviso de Privacidad</a>.</span>
              </div>
              
              <button type="submit" class="w-full md:w-auto bg-ca-green hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                Enviar solicitud <i class="fas fa-paper-plane text-sm"></i>
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Custom Styles for this page -->
<style>
  .hero-contact-bg {
    background-image: url('<?= BASE_URL ?>/images/bannercontacto.webp');
    background-size: cover;
    background-position: center 20%;
    background-attachment: fixed;
  }
  .form-input:focus, .form-select:focus, .form-textarea:focus {
    --tw-ring-color: #2E7D32;
    border-color: #2E7D32;
  }
</style>
