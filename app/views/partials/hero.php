<!-- Hero Section -->
<section class="home-hero" aria-labelledby="homeHeroTitle">
  <img
    class="home-hero__image"
    src="<?= BASE_URL ?>/images/imagen%20de%20background.webp"
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
      <div class="home-hero__eyebrow" data-aos="fade-up">
        <span></span>
        <b>Gesti&oacute;n y cumplimiento ambiental</b>
      </div>

      <h1 id="homeHeroTitle" class="home-hero__title" data-aos="fade-up" data-aos-delay="80">
        Consultor&iacute;a Ambiental
        <span><em>para Empresas</em> <em>e Industrias</em></span>
        en M&eacute;xico
      </h1>

      <p class="home-hero__copy" data-aos="fade-up" data-aos-delay="160">
        Gestionamos permisos, estudios ambientales, residuos, emisiones, COA, LAU, MIA y atenci&oacute;n a inspecciones PROEPA/PROFEPA para reducir riesgos regulatorios y mantener tu operaci&oacute;n en regla.
      </p>

      <div class="home-hero__actions" data-aos="fade-up" data-aos-delay="240">
        <a class="home-hero__button home-hero__button--primary" href="<?= BASE_URL ?>/contacto">
          Solicitar diagn&oacute;stico
        </a>
        <a class="home-hero__button home-hero__button--secondary" href="<?= BASE_URL ?>/servicios">
          Ver servicios
        </a>
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
    z-index: -4;
  }

  .home-hero__video {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    opacity: 0;
    transition: opacity 650ms ease;
    z-index: -4;
  }

  .home-hero__video.is-ready {
    opacity: 1;
  }

  .home-hero__shade {
    position: absolute;
    inset: 0;
    background:
      linear-gradient(90deg, rgba(8, 25, 34, .78) 0%, rgba(8, 25, 34, .62) 38%, rgba(8, 25, 34, .28) 70%, rgba(8, 25, 34, .12) 100%),
      linear-gradient(180deg, rgba(0, 0, 0, .2) 0%, rgba(0, 0, 0, .04) 46%, rgba(8, 25, 34, .48) 100%);
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
  }

  .home-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: .9rem;
    margin-top: 2.15rem;
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

  @media (max-width: 760px) {
    .home-hero {
      min-height: 800px;
      align-items: flex-end;
    }

    .home-hero__shade {
      background:
        linear-gradient(180deg, rgba(8, 25, 34, .22) 0%, rgba(8, 25, 34, .42) 48%, rgba(8, 25, 34, .68) 100%),
        linear-gradient(90deg, rgba(8, 25, 34, .5) 0%, rgba(8, 25, 34, .18) 100%);
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

  }

</style>

<script>
  (function () {
    var hero = document.querySelector('.home-hero');
    var poster = document.querySelector('.home-hero__image');
    var videoUrl = '<?= BASE_URL ?>/images/video%20background%20hero.mp4';

    if (!hero || !poster) return;
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (navigator.connection && navigator.connection.saveData) return;

    function loadHeroVideo() {
      var video = document.createElement('video');
      video.className = 'home-hero__video';
      video.muted = true;
      video.defaultMuted = true;
      video.loop = true;
      video.playsInline = true;
      video.autoplay = true;
      video.preload = 'metadata';
      video.poster = poster.currentSrc || poster.src;
      video.setAttribute('aria-hidden', 'true');
      video.setAttribute('muted', '');
      video.setAttribute('loop', '');
      video.setAttribute('playsinline', '');
      video.setAttribute('autoplay', '');

      var source = document.createElement('source');
      source.src = videoUrl;
      source.type = 'video/mp4';
      video.appendChild(source);

      function showWhenPlaying() {
        if (!video.paused && video.readyState >= 2) {
          video.classList.add('is-ready');
        }
      }

      function playHeroVideo() {
        var playPromise = video.play();
        if (playPromise && typeof playPromise.catch === 'function') {
          playPromise.then(showWhenPlaying).catch(function () {});
        } else {
          window.setTimeout(showWhenPlaying, 150);
        }
      }

      video.addEventListener('playing', showWhenPlaying);
      video.addEventListener('canplay', playHeroVideo, { once: true });
      document.addEventListener('visibilitychange', function () {
        if (!document.hidden && video.paused) {
          playHeroVideo();
        }
      });

      hero.insertBefore(video, poster.nextSibling);
      video.load();
    }

    window.addEventListener('load', function () {
      if ('requestIdleCallback' in window) {
        window.requestIdleCallback(loadHeroVideo, { timeout: 1800 });
      } else {
        window.setTimeout(loadHeroVideo, 700);
      }
    }, { once: true });
  })();
</script>
