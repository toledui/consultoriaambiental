<form method="POST" action="<?= BASE_URL ?>/admin/settings/brand/guardar" enctype="multipart/form-data" class="space-y-6">
  <div>
    <h3 class="text-lg font-semibold text-ca-navy mb-4">Información de la empresa</h3>
    <div class="grid grid-cols-1 gap-4">
      <div>
        <label for="brand_company_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la empresa</label>
        <input type="text" id="brand_company_name" name="brand_company_name" value="<?= htmlspecialchars($settings['brand_company_name'] ?? 'Gestoría Ambiental') ?>" placeholder="Gestoría Ambiental" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
      </div>
    </div>
  </div>

  <hr class="border-gray-200">

  <div>
    <h3 class="text-lg font-semibold text-ca-navy mb-4">Logo de la empresa</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="brand_logo" class="block text-sm font-medium text-gray-700 mb-1">Subir logo</label>
        <input type="file" id="brand_logo" name="brand_logo" accept="image/png,image/jpeg,image/gif,image/svg+xml,image/webp,image/avif" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-ca-green file:text-white hover:file:bg-ca-navy">
        <p class="text-xs text-gray-500 mt-1">Uso: encabezado, pie de página y panel. Formatos: PNG, JPG, GIF, SVG, WebP o AVIF. Máximo: 5 MB.</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Logo actual</label>
        <?php if (!empty($settings['brand_logo'])): ?>
          <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($settings['brand_logo']) ?>" alt="Logo actual" class="h-20 w-auto object-contain">
            <p class="text-xs text-gray-500 mt-2"><?= htmlspecialchars(basename($settings['brand_logo'])) ?></p>
          </div>
        <?php else: ?>
          <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center h-20">
            <span class="text-gray-400 text-sm">No hay logo subido</span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <hr class="border-gray-200">

  <div>
    <h3 class="text-lg font-semibold text-ca-navy mb-1">Favicon del sitio</h3>
    <p class="text-sm text-gray-500 mb-4">Es el icono cuadrado que muestran el navegador y Google junto al nombre del sitio. Es independiente del logo y de la imagen para redes sociales.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="brand_favicon" class="block text-sm font-medium text-gray-700 mb-1">Subir favicon</label>
        <input type="file" id="brand_favicon" name="brand_favicon" accept="image/png" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-ca-green file:text-white hover:file:bg-ca-navy">
        <div class="mt-2 rounded-lg border border-ca-green/20 bg-ca-green/5 px-3 py-2 text-xs text-gray-600">
          <strong class="text-ca-navy">Tamaño exacto: 512×512 px.</strong><br>
          Formato PNG, relación 1:1, máximo 5 MB. Usa solamente el símbolo de la marca, sin texto pequeño.
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon actual</label>
        <?php $faviconPreview = !empty($settings['brand_favicon']) ? BASE_URL . '/' . ltrim($settings['brand_favicon'], '/') : BASE_URL . '/favicon.svg'; ?>
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
          <img src="<?= htmlspecialchars($faviconPreview) ?>" alt="Favicon actual" class="h-20 w-20 object-contain rounded-lg">
          <p class="text-xs text-gray-500 mt-2">
            <?= !empty($settings['brand_favicon']) ? htmlspecialchars(basename($settings['brand_favicon'])) : 'favicon.svg (predeterminado)' ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <hr class="border-gray-200">

  <div>
    <h3 class="text-lg font-semibold text-ca-navy mb-1">Imagen para compartir en redes sociales</h3>
    <p class="text-sm text-gray-500 mb-4">Se usa como <code>og:image</code> y <code>twitter:image</code> en la portada y como respaldo cuando una página no tiene una imagen propia.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="brand_og_image" class="block text-sm font-medium text-gray-700 mb-1">Subir imagen Open Graph</label>
        <input type="file" id="brand_og_image" name="brand_og_image" accept="image/jpeg,image/png" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-ca-green file:text-white hover:file:bg-ca-navy">
        <div class="mt-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-gray-600">
          <strong class="text-ca-navy">Tamaño exacto: 1200×630 px.</strong><br>
          Formato JPG o PNG, relación 1.91:1, máximo 5 MB. Incluye un titular legible y, si aplica, un CTA breve; manténlos alejados de los bordes.
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Imagen social actual</label>
        <?php if (!empty($settings['brand_og_image'])): ?>
          <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($settings['brand_og_image']) ?>" alt="Imagen Open Graph actual" class="w-full object-cover rounded" style="aspect-ratio: 1200 / 630;">
            <p class="text-xs text-gray-500 mt-2"><?= htmlspecialchars(basename($settings['brand_og_image'])) ?></p>
          </div>
        <?php else: ?>
          <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 min-h-24 flex items-center justify-center text-center">
            <span class="text-amber-700 text-sm">No hay una imagen social configurada. Se usará temporalmente el logo predeterminado.</span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="flex justify-end pt-4 border-t border-gray-200">
    <button type="submit" class="px-6 py-2.5 bg-ca-green text-white font-medium rounded-lg hover:bg-ca-navy transition-colors text-sm">
      <i class="fas fa-save mr-2"></i>
      Guardar configuración de marca
    </button>
  </div>
</form>
