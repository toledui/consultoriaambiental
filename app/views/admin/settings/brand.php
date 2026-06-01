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
        <p class="text-xs text-gray-500 mt-1">Formatos: PNG, JPG, GIF, SVG, WebP. Tamaño máximo recomendado: 2MB.</p>
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

  <div class="flex justify-end pt-4 border-t border-gray-200">
    <button type="submit" class="px-6 py-2.5 bg-ca-green text-white font-medium rounded-lg hover:bg-ca-navy transition-colors text-sm">
      <i class="fas fa-save mr-2"></i>
      Guardar configuración de marca
    </button>
  </div>
</form>
