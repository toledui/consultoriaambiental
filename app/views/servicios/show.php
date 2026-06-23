<?php
$e = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<?php if (!$service): ?>
  <section class="service-not-found">
    <div class="service-not-found__inner">
      <div class="service-not-found__icon"><i class="fas fa-exclamation-circle"></i></div>
      <h1>Servicio no encontrado</h1>
      <p>El servicio que buscas no existe o fue movido.</p>
      <a href="<?= BASE_URL ?>/servicios" class="service-button service-button--dark">
        <i class="fas fa-arrow-left"></i>
        Ver todos los servicios
      </a>
    </div>
  </section>
<?php else: ?>
  <?php
    $heroImage = asset_url($service['hero_image'] ?? 'images/videopaginaservicios.mp4');
    $relatedServices = $relatedServices ?? [];
    $accent = $service['accent'] ?? '#2E7D32';
    $softAccent = $service['soft_accent'] ?? '#EEF7EF';
  ?>

  <article class="service-detail" style="--service-accent: <?= $e($accent) ?>; --service-soft: <?= $e($softAccent) ?>; --service-image: url('<?= $e($heroImage) ?>');">
    <section class="service-hero" aria-labelledby="service-title">
      <div class="service-hero__inner">
        <div class="service-hero__copy" data-aos="fade-right">
          <a href="<?= BASE_URL ?>/servicios" class="service-back-link">
            <i class="fas fa-arrow-left"></i>
            Servicios
          </a>
          <div class="service-kicker">
            <span><i class="<?= $e($service['icon']) ?>"></i></span>
            <?= $e($service['eyebrow']) ?>
          </div>
          <h1 id="service-title"><?= $e($service['title']) ?></h1>
          <p><?= $e($service['description']) ?></p>
          <div class="service-hero__actions">
            <a href="<?= BASE_URL ?>/contacto?servicio=<?= $e($service['slug']) ?>" class="service-button">
              Solicitar diagnóstico
              <i class="fas fa-arrow-right"></i>
            </a>
            <a href="#proceso" class="service-button service-button--ghost">
              Ver proceso
            </a>
          </div>
        </div>

        <aside class="service-hero-panel" aria-label="Resumen del servicio" data-aos="fade-left" data-aos-delay="120">
          <p class="service-hero-panel__label">Enfoque de trabajo</p>
          <ul>
            <?php foreach (array_slice($service['compliance'], 0, 4) as $item): ?>
              <li>
                <i class="fas fa-check"></i>
                <span><?= $e($item) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </aside>
      </div>
    </section>

    <nav class="service-jumpbar" aria-label="Secciones del servicio">
      <a href="#alcance">Alcance</a>
      <a href="#entregables">Entregables</a>
      <a href="#proceso">Proceso</a>
      <?php if (!empty($service['certificate_types'])): ?>
        <a href="#certificados">Certificados</a>
      <?php endif; ?>
      <a href="#cumplimiento">Cumplimiento</a>
    </nav>

    <section id="alcance" class="service-section service-section--intro">
      <div class="service-container service-intro-grid">
        <div data-aos="fade-right">
          <p class="service-section__eyebrow">Alcance técnico</p>
          <h2>Una ruta clara para operar con menos riesgo ambiental</h2>
          <?php foreach ($service['intro'] as $paragraph): ?>
            <p><?= $e($paragraph) ?></p>
          <?php endforeach; ?>
        </div>
        <div class="service-metrics" aria-label="Métricas del servicio" data-aos-group="true">
          <?php foreach ($service['metrics'] as $metric): ?>
            <div class="service-metric" data-aos="fade-up">
              <strong><?= $e($metric['value']) ?></strong>
              <span><?= $e($metric['label']) ?></span>
              <p><?= $e($metric['detail']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="service-section service-section--cards">
      <div class="service-container">
        <div class="service-section-heading" data-aos="fade-up">
          <p class="service-section__eyebrow">Qué resolvemos</p>
          <h2>Del diagnóstico a la evidencia documental</h2>
        </div>
        <div class="service-card-grid" data-aos-group="true">
          <?php foreach ($service['highlights'] as $highlight): ?>
            <article class="service-feature-card" data-aos="fade-up">
              <div class="service-feature-card__icon"><i class="<?= $e($highlight['icon']) ?>"></i></div>
              <h3><?= $e($highlight['title']) ?></h3>
              <p><?= $e($highlight['body']) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section id="entregables" class="service-section service-section--deliverables">
      <div class="service-container service-split">
        <div data-aos="fade-right">
          <p class="service-section__eyebrow">Entregables</p>
          <h2>Documentos, controles y seguimiento listos para usar</h2>
          <p>Integramos el expediente con enfoque práctico: lo que necesita dirección para decidir, operación para ejecutar y la autoridad para revisar.</p>
        </div>
        <div class="service-checklist" data-aos-group="true">
          <?php foreach ($service['deliverables'] as $item): ?>
            <div class="service-checklist__item" data-aos="fade-up">
              <i class="fas fa-check-circle"></i>
              <span><?= $e($item) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section id="proceso" class="service-section service-section--process">
      <div class="service-container">
        <div class="service-section-heading" data-aos="fade-up">
          <p class="service-section__eyebrow"><?= $e($service['process_heading']['eyebrow'] ?? 'Proceso') ?></p>
          <h2><?= $e($service['process_heading']['title'] ?? 'Acompañamiento por etapas, sin perder trazabilidad') ?></h2>
        </div>
        <div class="service-timeline" data-aos-group="true">
          <?php foreach ($service['process'] as $index => $step): ?>
            <article class="service-step" data-aos="fade-up">
              <span><?= $e($step['number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
              <h3><?= $e($step['title']) ?></h3>
              <?php if (!empty($step['body'])): ?>
                <p><?= $e($step['body']) ?></p>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <?php if (!empty($service['certificate_types'])): ?>
      <?php $certificateTypes = $service['certificate_types']; ?>
      <section id="certificados" class="service-section service-section--certificates">
        <div class="service-container">
          <div class="service-certificates-header" data-aos="fade-up">
            <div>
              <p class="service-section__eyebrow"><?= $e($certificateTypes['eyebrow'] ?? 'Certificados') ?></p>
              <h2><?= $e($certificateTypes['title'] ?? 'Tipos de Certificados Ambientales') ?></h2>
            </div>

            <?php if (!empty($certificateTypes['logos'])): ?>
              <div class="service-logo-strip" aria-label="Logos de certificación ambiental">
                <?php foreach ($certificateTypes['logos'] as $logo): ?>
                  <div class="service-logo-card">
                    <img src="<?= $e(asset_url($logo['image'])) ?>" alt="<?= $e($logo['alt'] ?? 'Logo de certificación ambiental') ?>" loading="lazy">
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="service-certificate-grid" data-aos-group="true">
            <?php foreach (($certificateTypes['items'] ?? []) as $index => $certificate): ?>
              <article class="service-certificate-card" data-aos="fade-up">
                <span class="service-certificate-card__number"><?= $e((string) ($index + 1)) ?></span>
                <h3><?= $e($certificate['title']) ?></h3>
                <?php if (!empty($certificate['description'])): ?>
                  <p><?= $e($certificate['description']) ?></p>
                <?php endif; ?>
                <?php if (!empty($certificate['points'])): ?>
                  <ul>
                    <?php foreach ($certificate['points'] as $point): ?>
                      <li>
                        <i class="fas fa-check"></i>
                        <span><?= $e($point) ?></span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <section id="cumplimiento" class="service-section service-section--compliance">
      <div class="service-container service-compliance">
        <div class="service-compliance__visual" role="img" aria-label="<?= $e($service['hero_alt'] ?? $service['title']) ?>" data-aos="fade-right">
          <img src="<?= $e($heroImage) ?>" alt="<?= $e($service['hero_alt'] ?? $service['title']) ?>" loading="lazy">
        </div>
        <div class="service-compliance__content" data-aos="fade-left">
          <p class="service-section__eyebrow">Cumplimiento operativo</p>
          <h2>Lo importante queda visible antes de una inspección</h2>
          <p>El resultado no es solo un trámite: es una carpeta de cumplimiento que ayuda a sostener la operación cuando hay auditorías, renovaciones, clientes o autoridades revisando.</p>
          <div class="service-tag-list">
            <?php foreach ($service['compliance'] as $item): ?>
              <span><?= $e($item) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <section class="service-cta">
      <div class="service-container service-cta__inner" data-aos="fade-up">
        <div>
          <p class="service-section__eyebrow">Siguiente paso</p>
          <h2><?= $e($service['cta']['title']) ?></h2>
          <p><?= $e($service['cta']['body']) ?></p>
        </div>
        <a href="<?= BASE_URL ?>/contacto?servicio=<?= $e($service['slug']) ?>" class="service-button service-button--light">
          Agendar revisión
          <i class="fas fa-calendar-check"></i>
        </a>
      </div>
    </section>

    <?php if (!empty($relatedServices)): ?>
      <section class="service-section service-section--related">
        <div class="service-container">
          <div class="service-section-heading" data-aos="fade-up">
            <p class="service-section__eyebrow">Servicios relacionados</p>
            <h2>Complementa tu ruta de cumplimiento</h2>
          </div>
          <div class="service-related-grid" data-aos-group="true">
            <?php foreach ($relatedServices as $related): ?>
              <a href="<?= BASE_URL ?>/servicios/<?= $e($related['slug']) ?>" class="service-related-card" data-aos="fade-up">
                <span><i class="<?= $e($related['icon']) ?>"></i></span>
                <strong><?= $e($related['title']) ?></strong>
                <small><?= $e($related['description']) ?></small>
                <em>Ver servicio <i class="fas fa-arrow-right"></i></em>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>
  </article>
<?php endif; ?>
