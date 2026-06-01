<!-- Hero Section -->
<section class="home-hero" aria-labelledby="homeHeroTitle">
  <img
    class="home-hero__image"
    src="<?= BASE_URL ?>/images/impacto%20ambiental%20imagen3.webp"
    alt=""
    width="1920"
    height="1080"
    fetchpriority="high"
    decoding="async"
  />
  <div class="home-hero__shade"></div>
  <div class="home-hero__grid" aria-hidden="true"></div>

  <div class="container mx-auto px-4 md:px-8 home-hero__inner">
    <div class="home-hero__content">
      <div class="home-hero__eyebrow">
        <span></span>
        <b>Gesti&oacute;n y cumplimiento ambiental</b>
      </div>

      <h1 id="homeHeroTitle" class="home-hero__title">
        Consultor&iacute;a Ambiental
        <span><em>para Empresas</em> <em>e Industrias</em></span>
        en M&eacute;xico
      </h1>

      <p class="home-hero__copy">
        Gestionamos permisos, estudios ambientales, residuos, emisiones, COA, LAU, MIA y atenci&oacute;n a inspecciones PROEPA/PROFEPA para reducir riesgos regulatorios y mantener tu operaci&oacute;n en regla.
      </p>

      <div class="home-hero__actions">
        <a class="home-hero__button home-hero__button--primary" href="<?= BASE_URL ?>/contacto">
          Solicitar diagn&oacute;stico
        </a>
        <a class="home-hero__button home-hero__button--secondary" href="<?= BASE_URL ?>/servicios">
          Ver servicios
        </a>
      </div>

      <div class="home-hero__proof" aria-label="Indicadores de experiencia">
        <div>
          <strong>10+</strong>
          <span>A&ntilde;os de experiencia</span>
        </div>
        <div>
          <strong>60+</strong>
          <span>MIA aprobadas</span>
        </div>
        <div>
          <strong>140+</strong>
          <span>Inspecciones atendidas</span>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .home-hero {
    position: relative;
    min-height: clamp(680px, 94svh, 900px);
    display: flex;
    align-items: center;
    overflow: hidden;
    color: #ffffff;
    background: #102936;
    isolation: isolate;
  }

  .home-hero * {
    box-sizing: border-box;
    font-family: inherit;
  }

  .home-hero__image {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transform: scale(1.07);
    animation: heroImageSettle 1400ms cubic-bezier(.2, .8, .2, 1) forwards;
    z-index: -4;
  }

  .home-hero__shade {
    position: absolute;
    inset: 0;
    background:
      linear-gradient(90deg, rgba(8, 25, 34, .94) 0%, rgba(8, 25, 34, .82) 37%, rgba(8, 25, 34, .42) 68%, rgba(8, 25, 34, .28) 100%),
      linear-gradient(180deg, rgba(0, 0, 0, .34) 0%, rgba(0, 0, 0, .06) 42%, rgba(8, 25, 34, .82) 100%);
    z-index: -3;
  }

  .home-hero__grid {
    position: absolute;
    inset: 0;
    opacity: .18;
    background-image:
      linear-gradient(rgba(255,255,255,.16) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,.16) 1px, transparent 1px);
    background-size: 72px 72px;
    mask-image: linear-gradient(90deg, #000 0%, transparent 76%);
    z-index: -2;
  }

  .home-hero__inner {
    position: relative;
    width: 100%;
    padding-top: 7.5rem;
    padding-bottom: 4rem;
  }

  .home-hero__content {
    max-width: 1120px;
    min-width: 0;
  }

  .home-hero__eyebrow,
  .home-hero__title,
  .home-hero__copy,
  .home-hero__actions,
  .home-hero__proof {
    opacity: 0;
    transform: translateY(22px);
    animation: heroReveal 740ms cubic-bezier(.2, .8, .2, 1) forwards;
  }

  .home-hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .65rem;
    max-width: 100%;
    padding: .55rem .9rem;
    margin-bottom: 1.35rem;
    border: 1px solid rgba(145, 214, 128, .42);
    border-radius: 999px;
    background: rgba(102, 187, 106, .15);
    color: #bdf3a8;
    font-size: .84rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    backdrop-filter: blur(14px);
    white-space: normal;
  }

  .home-hero__eyebrow span {
    flex-shrink: 0;
    width: .55rem;
    height: .55rem;
    background: #7be06f;
    box-shadow: 0 0 0 6px rgba(123, 224, 111, .14);
  }

  .home-hero__eyebrow b {
    min-width: 0;
    font: inherit;
    overflow-wrap: break-word;
  }

  .home-hero__title {
    margin: 0;
    max-width: 1120px;
    color: #ffffff;
    font-size: clamp(2.6rem, 4.8vw, 4.45rem);
    font-weight: 900;
    line-height: 1.03;
    letter-spacing: 0;
    text-wrap: balance;
    text-shadow: 0 4px 32px rgba(0, 0, 0, .72);
    animation-delay: 110ms;
  }

  .home-hero__title span {
    display: block;
    color: #8be071;
    text-shadow: 0 0 34px rgba(102, 187, 106, .42), 0 4px 28px rgba(0, 0, 0, .7);
  }

  .home-hero__title em {
    font-style: normal;
  }

  .home-hero__copy {
    max-width: 740px;
    margin: 1.55rem 0 0;
    color: rgba(255, 255, 255, .9);
    font-size: clamp(1.04rem, 1.8vw, 1.28rem);
    line-height: 1.7;
    text-shadow: 0 3px 18px rgba(0, 0, 0, .65);
    animation-delay: 210ms;
  }

  .home-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: .9rem;
    margin-top: 2.15rem;
    animation-delay: 310ms;
  }

  .home-hero__button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .65rem;
    min-height: 3.35rem;
    padding: .95rem 1.45rem;
    border-radius: 999px;
    font-weight: 900;
    line-height: 1;
    transition: transform .22s ease, background-color .22s ease, border-color .22s ease, color .22s ease, box-shadow .22s ease;
  }

  .home-hero__button:hover {
    transform: translateY(-2px);
  }

  .home-hero__button--primary {
    background: #66bb6a;
    color: #0e2732;
    box-shadow: 0 18px 36px rgba(20, 112, 45, .36);
  }

  .home-hero__button--primary:hover {
    background: #7bd77f;
  }

  .home-hero__button--primary::after {
    content: "\2192";
    font-size: 1.05rem;
    line-height: 1;
    transition: transform .22s ease;
  }

  .home-hero__button--primary:hover::after {
    transform: translateX(3px);
  }

  .home-hero__button--secondary {
    border: 1px solid rgba(255, 255, 255, .58);
    color: #ffffff;
    background: rgba(255, 255, 255, .08);
    backdrop-filter: blur(12px);
  }

  .home-hero__button--secondary:hover {
    background: #ffffff;
    color: #1b3a4b;
    border-color: #ffffff;
  }

  .home-hero__proof {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    margin-top: 2.2rem;
    animation-delay: 410ms;
  }

  .home-hero__proof div {
    min-width: 10.5rem;
    padding: .9rem 1rem;
    border-left: 3px solid #66bb6a;
    border-radius: .75rem;
    background: rgba(255, 255, 255, .09);
    backdrop-filter: blur(14px);
  }

  .home-hero__proof strong {
    display: block;
    color: #ffffff;
    font-size: 1.45rem;
    line-height: 1;
  }

  .home-hero__proof span {
    display: block;
    margin-top: .35rem;
    color: rgba(255, 255, 255, .76);
    font-size: .82rem;
    font-weight: 700;
  }

  @keyframes heroReveal {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes heroImageSettle {
    to {
      transform: scale(1);
    }
  }

  @media (max-width: 760px) {
    .home-hero {
      min-height: 800px;
      align-items: flex-end;
    }

    .home-hero__shade {
      background:
        linear-gradient(180deg, rgba(8, 25, 34, .58) 0%, rgba(8, 25, 34, .86) 45%, rgba(8, 25, 34, .97) 100%),
        linear-gradient(90deg, rgba(8, 25, 34, .82) 0%, rgba(8, 25, 34, .45) 100%);
    }

    .home-hero__grid {
      background-size: 48px 48px;
      mask-image: linear-gradient(180deg, transparent 0%, #000 34%, #000 100%);
    }

    .home-hero__inner {
      padding-top: 7rem;
      padding-bottom: 3rem;
    }

    .home-hero__content,
    .home-hero__copy,
    .home-hero__actions,
    .home-hero__proof,
    .home-hero__eyebrow {
      max-width: 360px;
    }

    .home-hero__eyebrow {
      display: grid;
      grid-template-columns: auto 1fr;
      font-size: .72rem;
      line-height: 1.35;
      width: 100%;
    }

    .home-hero__title {
      font-size: clamp(2.05rem, 8.35vw, 2.55rem);
      line-height: 1.04;
    }

    .home-hero__title em {
      display: block;
    }

    .home-hero__actions {
      display: grid;
      grid-template-columns: 1fr;
    }

    .home-hero__proof {
      display: grid;
      grid-template-columns: 1fr;
    }

    .home-hero__proof div {
      min-width: 0;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .home-hero__image,
    .home-hero__eyebrow,
    .home-hero__title,
    .home-hero__copy,
    .home-hero__actions,
    .home-hero__proof {
      animation: none;
      opacity: 1;
      transform: none;
    }
  }
</style>
