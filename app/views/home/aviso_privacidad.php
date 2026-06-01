<!-- Hero Banner -->
<section class="relative bg-ca-navy overflow-hidden">
  <div class="absolute inset-0 opacity-10">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-ca-light-green via-transparent to-transparent"></div>
  </div>
  <div class="container mx-auto px-4 py-20 md:py-28 relative z-10">
    <div class="max-w-3xl mx-auto text-center gsap-reveal" data-gsap="fade-up">
      <div class="flex items-center justify-center gap-3 mb-4">
        <span class="h-1 w-12 bg-ca-light-green rounded"></span>
        <p class="text-ca-light-green font-bold tracking-widest uppercase text-sm">Legal</p>
        <span class="h-1 w-12 bg-ca-light-green rounded"></span>
      </div>
      <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
        Aviso de Privacidad
      </h1>
      <p class="text-gray-300 text-lg max-w-2xl mx-auto">
        En cumplimiento con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares.
      </p>
    </div>
  </div>
  <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
</section>

<!-- Content Section -->
<section class="py-16 md:py-20 bg-white">
  <div class="container mx-auto px-4 md:px-8 max-w-4xl">
    <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-8 gsap-reveal" data-gsap="fade-up">

      <div class="bg-ca-bg p-6 rounded-xl border-l-4 border-ca-green mb-8">
        <p class="text-sm text-gray-600">
          <strong>Última actualización:</strong> Mayo 2026
        </p>
      </div>

<?php
// Render privacy policy content from settings, with fallback to hardcoded default
$privacidadContent = $settings['privacidad_content'] ?? '';

if (!empty(trim($privacidadContent))):
    // Replace dynamic placeholders with actual values from settings
    $replacements = [
        '{{brand_company_name}}' => htmlspecialchars($settings['brand_company_name'] ?? 'Consultoría Ambiental CA.'),
        '{{footer_address}}'     => htmlspecialchars($settings['footer_address'] ?? 'Zona Metropolitana de Guadalajara, Jalisco, México'),
        '{{footer_email_value}}' => htmlspecialchars($settings['footer_email_value'] ?? 'contacto@consultoria-ca.com'),
        '{{footer_phone_value}}' => htmlspecialchars($settings['footer_phone_value'] ?? '+52 (33) 1234-5678'),
    ];
    $rendered = str_replace(array_keys($replacements), array_values($replacements), $privacidadContent);
    echo $rendered;
else:
    // ─── Fallback hardcoded content ──────────────────────────────────
?>
      <section>
        <h2 class="text-2xl font-bold text-ca-navy mb-4">1. Identidad y domicilio del responsable</h2>
        <p>
          <strong><?= htmlspecialchars($settings['brand_company_name'] ?? 'Consultoría Ambiental CA.') ?></strong> (en adelante, "la Empresa"), con domicilio en <?= htmlspecialchars($settings['footer_address'] ?? 'Zona Metropolitana de Guadalajara, Jalisco, México') ?>, es el responsable del tratamiento de sus datos personales.
        </p>
        <p class="mt-3">
          Para cualquier comunicación relacionada con el presente aviso de privacidad, puede contactarnos a través de:
        </p>
        <ul class="list-disc pl-6 mt-2 space-y-1">
          <li><strong>Correo electrónico:</strong> <a href="mailto:<?= htmlspecialchars($settings['footer_email_value'] ?? 'contacto@consultoria-ca.com') ?>" class="text-ca-green hover:underline"><?= htmlspecialchars($settings['footer_email_value'] ?? 'contacto@consultoria-ca.com') ?></a></li>
          <li><strong>Teléfono:</strong> <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $settings['footer_phone_value'] ?? '')) ?>" class="text-ca-green hover:underline"><?= htmlspecialchars($settings['footer_phone_value'] ?? '+52 (33) 1234-5678') ?></a></li>
        </ul>
      </section>

      <section>
        <h2 class="text-2xl font-bold text-ca-navy mb-4">2. Datos personales que recabamos</h2>
        <p>Para las finalidades descritas en el presente aviso de privacidad, podemos recabar los siguientes datos personales:</p>
        <ul class="list-disc pl-6 mt-2 space-y-1">
          <li>Nombre completo</li>
          <li>Correo electrónico</li>
          <li>Teléfono (fijo y/o móvil)</li>
          <li>Empresa o institución para la que labora</li>
          <li>Cargo o puesto</li>
          <li>Información de contacto proporcionada a través de formularios en nuestro sitio web</li>
        </ul>
        <p class="mt-3">
          No recabamos datos personales sensibles conforme a la Ley Federal de Protección de Datos Personales en Posesión de los Particulares.
        </p>
      </section>

      <section>
        <h2 class="text-2xl font-bold text-ca-navy mb-4">3. Finalidades del tratamiento de datos</h2>
        <p class="font-semibold text-ca-navy">Finalidades primarias (necesarias):</p>
        <ul class="list-disc pl-6 mt-2 space-y-1">
          <li>Atender solicitudes de información, cotizaciones y servicios de consultoría ambiental</li>
          <li>Dar seguimiento a comunicaciones iniciadas a través de nuestros formularios de contacto</li>
          <li>Prestar los servicios de consultoría ambiental contratados</li>
          <li>Dar cumplimiento a obligaciones legales y regulatorias aplicables</li>
        </ul>
        <p class="font-semibold text-ca-navy mt-4">Finalidades secundarias (no necesarias):</p>
        <ul class="list-disc pl-6 mt-2 space-y-1">
          <li>Enviar boletines informativos, noticias y actualizaciones sobre normativa ambiental</li>
          <li>Realizar encuestas de satisfacción sobre nuestros servicios</li>
          <li>Invitar a eventos, webinars y capacitaciones relacionadas con materia ambiental</li>
        </ul>
      </section>

      <section>
        <h2 class="text-2xl font-bold text-ca-navy mb-4">4. Transferencia de datos personales</h2>
        <p>
          No transferimos sus datos personales a terceros sin su consentimiento, salvo las excepciones previstas en el artículo 37 de la Ley Federal de Protección de Datos Personales en Posesión de los Particulares, como autoridades competentes en ejercicio de sus funciones.
        </p>
      </section>

      <section>
        <h2 class="text-2xl font-bold text-ca-navy mb-4">5. Derechos ARCO (Acceso, Rectificación, Cancelación y Oposición)</h2>
        <p>
          Usted o su representante legal podrán ejercer los derechos de acceso, rectificación, cancelación u oposición al tratamiento de sus datos personales (derechos ARCO) enviando una solicitud a nuestro correo electrónico:
        </p>
        <p class="mt-2">
          <a href="mailto:<?= htmlspecialchars($settings['footer_email_value'] ?? 'contacto@consultoria-ca.com') ?>" class="text-ca-green font-semibold hover:underline"><?= htmlspecialchars($settings['footer_email_value'] ?? 'contacto@consultoria-ca.com') ?></a>
        </p>
        <p class="mt-3">
          La solicitud deberá contener: nombre completo del titular, correo electrónico para recibir notificaciones, documentos que acrediten la identidad, descripción clara y precisa de los datos personales respecto de los cuales se busca ejercer alguno de los derechos ARCO, y cualquier otro elemento que facilite la localización de los datos.
        </p>
        <p class="mt-3">
          Le responderemos en un plazo máximo de 20 días hábiles contados desde la fecha de recepción de su solicitud.
        </p>
      </section>

      <section>
        <h2 class="text-2xl font-bold text-ca-navy mb-4">6. Limitación y divulgación de datos</h2>
        <p>
          Usted puede limitar el uso o divulgación de sus datos personales enviando una solicitud a nuestro correo electrónico. Asimismo, podrá solicitar dejar de recibir comunicaciones promocionales o de marketing en cualquier momento, mediante el enlace de baja que incluimos en cada comunicación o contactándonos directamente.
        </p>
      </section>

      <section>
        <h2 class="text-2xl font-bold text-ca-navy mb-4">7. Uso de cookies y tecnologías de rastreo</h2>
        <p>
          Nuestro sitio web puede utilizar cookies y otras tecnologías de rastreo para mejorar la experiencia del usuario, analizar el tráfico del sitio y personalizar el contenido. Puede configurar su navegador para rechazar todas las cookies o para indicar cuándo se envía una cookie.
        </p>
      </section>

      <section>
        <h2 class="text-2xl font-bold text-ca-navy mb-4">8. Cambios al aviso de privacidad</h2>
        <p>
          Nos reservamos el derecho de modificar o actualizar este aviso de privacidad en cualquier momento. Las modificaciones entrarán en vigor inmediatamente después de su publicación en nuestro sitio web. Le recomendamos revisar periódicamente esta página para mantenerse informado sobre cualquier cambio.
        </p>
      </section>

      <section>
        <h2 class="text-2xl font-bold text-ca-navy mb-4">9. Consentimiento</h2>
        <p>
          Al proporcionar sus datos personales a través de nuestros formularios, usted manifiesta su consentimiento para el tratamiento de los mismos conforme a los términos y condiciones del presente aviso de privacidad.
        </p>
      </section>
<?php endif; ?>

    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="bg-ca-navy py-16 relative overflow-hidden gsap-reveal" data-gsap="fade-up">
  <div class="absolute right-0 top-0 opacity-5 transform translate-x-1/3 -translate-y-1/4">
    <i class="fas fa-shield-alt text-[200px] text-white"></i>
  </div>
  <div class="container mx-auto px-4 relative z-10 text-center">
    <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">¿Tienes dudas sobre el tratamiento de tus datos?</h2>
    <p class="text-gray-300 mb-8 max-w-2xl mx-auto">Estamos a tu disposición para resolver cualquier inquietud sobre tu privacidad y la protección de tus datos personales.</p>
    <a href="<?= BASE_URL ?>/contacto" class="inline-flex items-center bg-ca-light-green text-ca-navy font-bold py-3 px-8 rounded-full shadow-lg hover:bg-white transition-colors">
      <i class="fas fa-envelope mr-2"></i> Contáctanos
    </a>
  </div>
</section>
