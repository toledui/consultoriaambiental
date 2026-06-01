<!-- Media Browser Modal Content -->
<div class="p-6">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-ca-navy">Seleccionar archivo multimedia</h2>
    <button type="button" onclick="closeMediaBrowser()" class="text-gray-400 hover:text-gray-600 transition-colors">
      <i class="fas fa-times text-xl"></i>
    </button>
  </div>

  <?php if (empty($files)): ?>
    <div class="text-center py-12">
      <i class="fas fa-photo-video text-4xl text-ca-light-gray mb-3"></i>
      <p class="text-gray-500">No hay archivos en la mediateca.</p>
      <p class="text-sm text-gray-400 mt-1">Sube archivos desde la sección Mediateca primero.</p>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
      <?php foreach ($files as $file): ?>
        <?php
        $thumbUrl = \App\Models\MediaFile::getThumbnailUrl($file);
        $fileUrl = \App\Models\MediaFile::getUrl($file);
        $fileIcon = \App\Models\MediaFile::getFileIcon($file);
        ?>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md hover:border-ca-green transition-all cursor-pointer group" onclick="selectMedia('<?= htmlspecialchars($fileUrl, ENT_QUOTES) ?>', '<?= htmlspecialchars($file['original_name'], ENT_QUOTES) ?>')">
          <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
            <?php if ($thumbUrl): ?>
              <img src="<?= htmlspecialchars($thumbUrl) ?>" alt="<?= htmlspecialchars($file['alt_text'] ?? $file['original_name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform"/>
            <?php else: ?>
              <i class="<?= $fileIcon ?> text-4xl"></i>
            <?php endif; ?>
          </div>
          <div class="p-2">
            <p class="text-xs font-medium text-ca-dark-gray truncate"><?= htmlspecialchars($file['original_name']) ?></p>
            <p class="text-[10px] text-gray-400"><?= \App\Models\MediaFile::formatSize($file['size']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Note: selectMedia() and closeMediaBrowser() are defined globally in create.php / edit.php -->
