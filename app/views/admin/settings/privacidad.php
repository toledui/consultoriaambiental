<?php
// Default content shown when no custom content has been saved yet
$privacidadDefault = <<<'HTML'
<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">1. Identidad y domicilio del responsable</h2>
  <p>
    <strong>{{brand_company_name}}</strong> (en adelante, "la Empresa"), con domicilio en {{footer_address}}, es el responsable del tratamiento de sus datos personales.
  </p>
  <p class="mt-3">
    Para cualquier comunicación relacionada con el presente aviso de privacidad, puede contactarnos a través de:
  </p>
  <ul class="list-disc pl-6 mt-2 space-y-1">
    <li><strong>Correo electrónico:</strong> {{footer_email_value}}</li>
    <li><strong>Teléfono:</strong> {{footer_phone_value}}</li>
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
    {{footer_email_value}}
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
HTML;
?>
<form method="POST" action="<?= BASE_URL ?>/admin/settings/privacidad/guardar">
  <div class="space-y-6">
    <div>
      <h2 class="text-lg font-semibold text-ca-navy">Aviso de Privacidad</h2>
      <p class="text-sm text-gray-500 mt-1">Edita el contenido del Aviso de Privacidad que se muestra en <code>/aviso-de-privacidad</code>. Puedes usar HTML básico para dar formato.</p>
    </div>

    <div>
      <label for="privacidad_content" class="block text-sm font-medium text-gray-700 mb-2">Contenido del Aviso de Privacidad</label>
      <p class="text-xs text-gray-400 mb-2">Escribe el contenido completo. Se mostrará dentro de un contenedor con estilos tipográficos. Puedes usar etiquetas HTML como <code>&lt;h2&gt;</code>, <code>&lt;h3&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;a&gt;</code>.</p>
      <textarea
        id="privacidad_content"
        name="privacidad_content"
        rows="30"
        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition-colors"
        placeholder="Escribe aquí el contenido del aviso de privacidad..."
      ><?= htmlspecialchars($settings['privacidad_content'] ?? $privacidadDefault) ?></textarea>
    </div>

    <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
      <button type="submit" class="bg-ca-green text-white px-6 py-2.5 rounded-lg hover:bg-ca-navy transition-colors font-medium">
        <i class="fas fa-save mr-2"></i>Guardar cambios
      </button>
      <a href="<?= BASE_URL ?>/aviso-de-privacidad" target="_blank" class="text-ca-green hover:underline text-sm">
        <i class="fas fa-external-link-alt mr-1"></i>Ver página pública
      </a>
    </div>
  </div>
</form>
