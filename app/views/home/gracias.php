<!-- Thank You / Gracias Page -->
<section class="hero-contact-bg relative pt-32 pb-20 md:pt-40 md:pb-28 border-b-8 border-ca-green">
  <div class="absolute inset-0" style="background-color: #00000033;"></div>
  
  <div class="container mx-auto px-4 md:px-8 relative z-10 text-center">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-ca-light-green/20 border border-ca-light-green/30 text-ca-light-green font-semibold text-sm mb-4 backdrop-blur-sm uppercase tracking-widest">
      Mensaje Enviado
    </div>
    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight">
      ¡Gracias por <br class="hidden md:block"/>
      <span class="text-ca-light-green">contactarnos!</span>
    </h1>
    <p class="text-lg md:text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
      Hemos recibido tu solicitud correctamente. Nuestro equipo de ingenieros revisará tu mensaje y te contactaremos a la brevedad posible.
    </p>
  </div>
</section>

<!-- Success Message -->
<section class="py-20 md:py-28 pattern-bg relative gsap-reveal" data-gsap="fade-up">
  <div class="container mx-auto px-4 md:px-8 max-w-3xl text-center">
    
    <!-- Success Icon -->
    <div class="w-24 h-24 bg-ca-green/10 rounded-full flex items-center justify-center mx-auto mb-8 gsap-reveal" data-gsap="scale">
      <i class="fas fa-check-circle text-6xl text-ca-green"></i>
    </div>

    <h2 class="text-3xl md:text-4xl font-bold text-ca-navy mb-6">Solicitud recibida con éxito</h2>
    
    <p class="text-gray-600 text-lg mb-4 leading-relaxed">
      En un plazo máximo de <strong>24 a 48 horas hábiles</strong> uno de nuestros especialistas se comunicará contigo para brindarte atención personalizada.
    </p>

    <p class="text-gray-500 mb-10 leading-relaxed">
      Mientras tanto, te invitamos a conocer más sobre nuestros servicios y mantenerte informado con las últimas noticias del sector ambiental.
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <a href="<?= BASE_URL ?>" class="bg-ca-navy hover:bg-gray-800 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-colors">
        <i class="fas fa-home mr-2"></i> Volver al Inicio
      </a>
      <a href="<?= BASE_URL ?>/servicios" class="border-2 border-ca-navy text-ca-navy hover:bg-ca-navy hover:text-white font-bold py-3 px-8 rounded-full transition-colors">
        <i class="fas fa-concierge-bell mr-2"></i> Ver Servicios
      </a>
    </div>

    <!-- Google Ads Conversion Tracking -->
    <div id="google-ads-conversion" class="mt-12 p-6 bg-white rounded-xl border border-gray-200 shadow-sm">
      <p class="text-xs text-gray-400 mb-2">Conversión registrada para Google Ads</p>
      <div class="flex items-center justify-center gap-2 text-ca-green text-sm">
        <i class="fas fa-chart-line"></i>
        <span class="font-medium">Evento de conversión: formulario_contacto</span>
      </div>
      <!--
        Google Ads conversion tracking code can be placed here.
        Replace the comment below with your actual Google Ads snippet.
        Event: form_submission
      -->
    </div>

  </div>
</section>

<style>
  .hero-contact-bg {
    background-image: url('<?= BASE_URL ?>/images/bannercontacto.webp');
    background-size: cover;
    background-position: center 20%;
    background-attachment: fixed;
  }
</style>
