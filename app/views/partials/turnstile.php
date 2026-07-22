<?php
$turnstileAction = $turnstileAction ?? 'public_form';
$turnstileClass = $turnstileClass ?? 'flex justify-center';
?>
<?php if (\App\Helpers\Turnstile::canRender($settings ?? [])): ?>
  <div
    class="js-turnstile <?= htmlspecialchars($turnstileClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
    data-sitekey="<?= htmlspecialchars($settings['turnstile_site_key'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
    data-action="<?= htmlspecialchars($turnstileAction, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
    aria-label="Verificaci&oacute;n de seguridad"
  ></div>
<?php endif; ?>
