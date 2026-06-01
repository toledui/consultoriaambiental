<div class="max-w-3xl mx-auto">
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold text-ca-navy">Editar Categoría</h3>
          <p class="text-sm text-gray-500">Modifica los datos de la categoría.</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/blog/categorias" class="border border-gray-300 hover:bg-gray-50 text-ca-dark-gray text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
          <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
      </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/admin/blog/categorias/editar/<?= $category['id'] ?>" class="p-6 space-y-6">
      <div>
        <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="name">Nombre *</label>
        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="name" name="name" type="text" required value="<?= htmlspecialchars($category['name']) ?>"/>
      </div>

      <div>
        <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="slug">Slug (URL)</label>
        <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="slug" name="slug" type="text" value="<?= htmlspecialchars($category['slug']) ?>"/>
        <p class="text-xs text-gray-400 mt-1">Si se deja vacío, se generará automáticamente a partir del nombre.</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="description">Descripción</label>
        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="description" name="description" rows="3" placeholder="Breve descripción de la categoría"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
      </div>

      <div class="flex items-center gap-4 pt-6 border-t border-gray-200">
        <button class="bg-ca-green hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm" type="submit">
          <i class="fas fa-save mr-1"></i> Actualizar
        </button>
        <a href="<?= BASE_URL ?>/admin/blog/categorias" class="text-gray-500 hover:text-ca-navy font-medium transition-colors">
          Cancelar
        </a>
      </div>
    </form>
  </div>
</div>
