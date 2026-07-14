# TODO — Próximas mejoras

## 🚨 Alta prioridad

- [ ] **FOUC (Flash of Unstyled Content)** — Al cargar la página se ve HTML puro sin estilos porque Tailwind CSS se carga de forma no bloqueante (`preload` + `onload`). Solución: agregar un `<link rel="stylesheet">` bloqueante para Tailwind, o expandir los estilos críticos inline para cubrir todo el above-the-fold.

- [ ] **Favicon** — Agregar etiqueta `<link rel="icon">` en el `<head>` de `main.php`. Se necesita un archivo `favicon.ico` en `/public/`.

- [ ] **Open Graph / og:graph** — Verificar que las etiquetas OG se estén renderizando correctamente en producción. El `og:image` apunta a `/images/consultoria-ambiental-logo.png` — asegurarse de que la ruta sea correcta y la imagen exista en `public/images/`.

## 📋 Media prioridad

- [ ] **Verificar BASE_URL en producción** — Confirmar que `config/app.php` auto-detecta correctamente `https://ambiental.nexagro.com.mx`.

- [ ] **Verificar autoloader** — Confirmar que `config/init.php` encuentra todas las clases en el servidor Linux (case-sensitive).

- [ ] **CSS build en producción** — Asegurar que `public/css/tailwind.css` existe en el servidor (ejecutar `npm run build:css` y subir el archivo).

## 💡 Baja prioridad / Ideas

- [ ] **Cache Busting** — Agregar versión a los archivos CSS/JS para evitar caché del navegador después de actualizaciones (ej: `tailwind.css?v=1.0.1`).

- [ ] **Service Worker** — Implementar un service worker básico para mejorar la experiencia offline y velocidad de carga en visitas repetidas.

- [ ] **Lazy loading de imágenes** — Asegurar que todas las imágenes below-the-fold tengan `loading="lazy"`.

- [ ] **WebP conversion** — Convertir imágenes a WebP para reducir peso y mejorar Lighthouse.

- [x] **Sitemap XML y robots.txt** — Generados dinámicamente para mejorar el SEO.

- [ ] **RSS Feed** — Agregar un feed RSS para el blog.
