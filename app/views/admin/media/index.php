<div class="max-w-6xl mx-auto">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-ca-navy">Mediateca</h1>
      <p class="text-gray-500 text-sm mt-1">Administra tus imágenes y documentos</p>
    </div>
    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg transition-colors shadow-sm">
      <i class="fas fa-upload mr-1"></i> Subir archivo
    </button>
  </div>

  <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="mb-6 p-4 rounded-lg <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' ?>">
      <div class="flex items-center gap-2">
        <i class="fas <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <span><?= $_SESSION['flash_message'] ?></span>
      </div>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <?php if (empty($files)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
      <i class="fas fa-photo-video text-5xl text-ca-light-gray mb-4"></i>
      <p class="text-gray-500 text-lg mb-4">No hay archivos en la mediateca.</p>
      <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="inline-block bg-ca-green hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm">
        <i class="fas fa-upload mr-1"></i> Subir primer archivo
      </button>
    </div>
  <?php else: ?>
    <!-- Media Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
      <?php foreach ($files as $file): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group hover:shadow-md transition-shadow cursor-pointer" onclick="openFileDetail(<?= $file['id'] ?>)">
          <!-- Thumbnail -->
          <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
            <?php $thumbUrl = \App\Models\MediaFile::getThumbnailUrl($file); ?>
            <?php if ($thumbUrl): ?>
              <img src="<?= htmlspecialchars($thumbUrl) ?>" alt="<?= htmlspecialchars($file['alt_text'] ?? $file['original_name']) ?>" class="w-full h-full object-cover"/>
            <?php else: ?>
              <i class="<?= \App\Models\MediaFile::getFileIcon($file) ?> text-4xl"></i>
            <?php endif; ?>
          </div>
          <!-- Info -->
          <div class="p-3">
            <p class="text-xs font-medium text-ca-dark-gray truncate" title="<?= htmlspecialchars($file['original_name']) ?>">
              <?= htmlspecialchars($file['original_name']) ?>
            </p>
            <p class="text-[10px] text-gray-400 mt-0.5">
              <?= \App\Models\MediaFile::formatSize($file['size']) ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-bold text-ca-navy">Subir archivo</h3>
      <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/admin/media/subir" enctype="multipart/form-data" class="space-y-4">
      <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-ca-green transition-colors" id="dropZone">
        <i class="fas fa-cloud-upload-alt text-4xl text-ca-light-gray mb-3"></i>
        <p class="text-sm text-gray-500 mb-2">Arrastra un archivo aquí o</p>
        <label class="inline-block bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2 px-5 rounded-lg transition-colors shadow-sm cursor-pointer">
          <i class="fas fa-folder-open mr-1"></i> Seleccionar archivo
          <input type="file" name="file" id="fileInput" class="hidden" required onchange="updateFileName(this)"/>
        </label>
        <p class="text-xs text-gray-400 mt-3">JPG, PNG, GIF, WebP, SVG, PDF, DOC, DOCX, XLS, XLSX — Máx. 10 MB</p>
        <p id="fileName" class="text-sm text-ca-green font-medium mt-2 hidden"></p>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-gray-500 hover:text-ca-navy font-medium transition-colors text-sm px-4 py-2">
          Cancelar
        </button>
        <button type="submit" class="bg-ca-green hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-sm text-sm">
          <i class="fas fa-upload mr-1"></i> Subir
        </button>
      </div>
    </form>
  </div>
</div>

<!-- File Detail Modal -->
<div id="fileDetailModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-bold text-ca-navy" id="detailTitle">Detalle del archivo</h3>
      <button onclick="document.getElementById('fileDetailModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    <div class="flex flex-col md:flex-row gap-6">
      <div class="w-full md:w-1/2">
        <div class="bg-gray-50 rounded-xl flex items-center justify-center aspect-square overflow-hidden" id="detailPreview">
        </div>
      </div>
      <div class="w-full md:w-1/2 space-y-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Nombre</label>
          <p class="text-sm font-medium text-ca-dark-gray break-all" id="detailName"></p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Tamaño</label>
          <p class="text-sm text-gray-600" id="detailSize"></p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Tipo</label>
          <p class="text-sm text-gray-600" id="detailType"></p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">URL</label>
          <div class="flex gap-2">
            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs bg-gray-50" id="detailUrl" readonly onclick="this.select()"/>
            <button onclick="copyUrl()" class="text-ca-green hover:text-ca-navy transition-colors text-sm px-2" title="Copiar URL">
              <i class="fas fa-copy"></i>
            </button>
          </div>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Texto alternativo (alt)</label>
          <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" id="detailAlt" placeholder="Describe la imagen"/>
          <button onclick="saveAltText()" class="mt-2 text-xs text-ca-green hover:text-ca-navy font-medium transition-colors">
            <i class="fas fa-save mr-1"></i> Guardar alt text
          </button>
        </div>
        <div class="pt-2 border-t border-gray-200">
          <form method="POST" action="" id="deleteForm" onsubmit="return confirm('¿Eliminar este archivo permanentemente?')">
            <input type="hidden" name="_method" value="DELETE"/>
            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium transition-colors">
              <i class="fas fa-trash-alt mr-1"></i> Eliminar archivo
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Drag & drop support
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

if (dropZone && fileInput) {
  dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('border-ca-green', 'bg-green-50');
  });
  dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('border-ca-green', 'bg-green-50');
  });
  dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-ca-green', 'bg-green-50');
    if (e.dataTransfer.files.length) {
      fileInput.files = e.dataTransfer.files;
      updateFileName(fileInput);
    }
  });
}

function updateFileName(input) {
  const nameEl = document.getElementById('fileName');
  if (input.files && input.files.length > 0) {
    nameEl.textContent = '📄 ' + input.files[0].name;
    nameEl.classList.remove('hidden');
  }
}

// File detail data store
const fileData = <?= json_encode(array_map(function($f) {
    return [
        'id' => $f['id'],
        'name' => $f['original_name'],
        'url' => \App\Models\MediaFile::getUrl($f),
        'thumbnailUrl' => \App\Models\MediaFile::getThumbnailUrl($f),
        'size' => \App\Models\MediaFile::formatSize($f['size']),
        'type' => $f['type'],
        'mime' => $f['mime_type'],
        'alt' => $f['alt_text'] ?? '',
        'icon' => \App\Models\MediaFile::getFileIcon($f),
    ];
}, $files)) ?>;

let currentFileId = null;

function openFileDetail(id) {
  currentFileId = id;
  const f = fileData.find(item => item.id === id);
  if (!f) return;

  document.getElementById('detailTitle').textContent = f.name;
  document.getElementById('detailName').textContent = f.name;
  document.getElementById('detailSize').textContent = f.size;
  document.getElementById('detailType').textContent = f.mime;
  document.getElementById('detailUrl').value = f.url;
  document.getElementById('detailAlt').value = f.alt;

  const preview = document.getElementById('detailPreview');
  preview.innerHTML = '';
  if (f.thumbnailUrl) {
    preview.innerHTML = '<img src="' + f.thumbnailUrl + '" alt="' + f.name + '" class="w-full h-full object-contain"/>';
  } else {
    preview.innerHTML = '<i class="' + f.icon + ' text-5xl"></i>';
  }

  document.getElementById('deleteForm').action = '<?= BASE_URL ?>/admin/media/eliminar/' + id;
  document.getElementById('fileDetailModal').classList.remove('hidden');
}

function copyUrl() {
  const urlInput = document.getElementById('detailUrl');
  urlInput.select();
  document.execCommand('copy');
  // Visual feedback
  const btn = event.currentTarget;
  btn.innerHTML = '<i class="fas fa-check"></i>';
  setTimeout(function() { btn.innerHTML = '<i class="fas fa-copy"></i>'; }, 1500);
}

function saveAltText() {
  if (!currentFileId) return;
  const alt = document.getElementById('detailAlt').value;
  const formData = new FormData();
  formData.append('id', currentFileId);
  formData.append('alt_text', alt);

  fetch('<?= BASE_URL ?>/admin/media/alt/actualizar', {
    method: 'POST',
    body: formData
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      // Update local data
      const f = fileData.find(item => item.id === currentFileId);
      if (f) f.alt = alt;
      alert('Texto alternativo guardado.');
    }
  })
  .catch(function(err) { console.error(err); });
}
</script>
