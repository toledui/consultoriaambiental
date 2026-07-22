<div class="max-w-5xl mx-auto">
  <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="mb-6 p-4 rounded-lg border <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
      <div class="flex items-center gap-2">
        <i class="fas <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <span><?= htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
      </div>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Tabs -->
    <div class="border-b border-gray-200">
      <nav class="flex overflow-x-auto" id="tabNav">
        <button type="button" onclick="switchTab('content')" id="tab-content-btn" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap border-b-2 border-ca-green text-ca-green">
          <i class="fas fa-edit mr-2"></i>
          Contenido
        </button>
        <button type="button" onclick="switchTab('seo')" id="tab-seo-btn" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-gray-700">
          <i class="fas fa-search mr-2"></i>
          SEO
        </button>
        <button type="button" onclick="switchTab('jsonld')" id="tab-jsonld-btn" class="px-6 py-4 text-sm font-medium transition-colors whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-gray-700">
          <i class="fas fa-code mr-2"></i>
          JSON-LD
        </button>
      </nav>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/admin/blog/editar/<?= $post['id'] ?>" enctype="multipart/form-data" class="p-6">

      <!-- ===== TAB: CONTENIDO ===== -->
      <div id="tab-content" class="tab-panel space-y-6">
        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="title">T&iacute;tulo *</label>
          <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="title" name="title" type="text" required value="<?= htmlspecialchars($post['title']) ?>"/>
        </div>

        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="slug">Slug (URL)</label>
          <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="slug" name="slug" type="text" value="<?= htmlspecialchars($post['slug']) ?>"/>
          <p class="text-xs text-gray-400 mt-1">Si se deja vac&iacute;o, se generar&aacute; autom&aacute;ticamente a partir del t&iacute;tulo.</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="category_id">Categor&iacute;a</label>
          <div class="flex gap-2">
            <select class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition bg-white" id="category_id" name="category_id">
              <option value="">— Sin categor&iacute;a —</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <a href="<?= BASE_URL ?>/admin/blog/categorias" target="_blank" class="inline-flex items-center px-4 py-3 border border-gray-300 rounded-lg text-ca-green hover:bg-gray-50 transition-colors text-sm" title="Gestionar categor&iacute;as">
              <i class="fas fa-cog"></i>
            </a>
          </div>
          <p class="text-xs text-gray-400 mt-1">Clasifica el art&iacute;culo en una categor&iacute;a existente.</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="author_id">Usuario asignado</label>
          <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition bg-white" id="author_id" name="author_id">
            <option value="">&mdash; Sin usuario asignado &mdash;</option>
            <?php foreach ($users as $user): ?>
              <option value="<?= (int)$user['id'] ?>" <?= (int)($post['author_id'] ?? 0) === (int)$user['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($user['username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> (<?= htmlspecialchars($user['email'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <p class="text-xs text-gray-400 mt-1">Puedes asignar el art&iacute;culo a cualquier usuario administrador o dejarlo sin asignar.</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="excerpt">Extracto / Resumen</label>
          <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="excerpt" name="excerpt" rows="3"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
        </div>

        <!-- Featured Image -->
        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5">Imagen destacada</label>
          <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-ca-green transition-colors" id="featuredDropZone">
            <div id="featuredPreview" class="<?= empty($post['featured_image']) ? 'hidden' : '' ?> mb-4">
              <img id="featuredPreviewImg" src="<?= htmlspecialchars($post['featured_image'] ?? '') ?>" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-sm"/>
              <div class="mt-2 flex items-center justify-center gap-3">
                <button type="button" onclick="clearFeaturedImage()" class="text-red-500 hover:text-red-700 text-sm font-medium transition-colors">
                  <i class="fas fa-times mr-1"></i> Quitar imagen
                </button>
                <button type="button" onclick="document.getElementById('featuredImageInput').click()" class="text-ca-green hover:text-ca-navy text-sm font-medium transition-colors">
                  <i class="fas fa-folder-open mr-1"></i> Subir otra imagen
                </button>
                <button type="button" onclick="openMediaBrowserForFeatured()" class="text-ca-green hover:text-ca-navy text-sm font-medium transition-colors">
                  <i class="fas fa-photo-video mr-1"></i> Cambiar desde mediateca
                </button>
              </div>
            </div>
            <div id="featuredPlaceholder" class="<?= empty($post['featured_image']) ? '' : 'hidden' ?>">
              <i class="fas fa-image text-4xl text-ca-light-gray mb-3"></i>
              <p class="text-sm text-gray-500 mb-2">Arrastra una imagen aqu&iacute; o</p>
              <div class="flex items-center justify-center gap-3">
                <label for="featuredImageInput" class="inline-block bg-ca-green hover:bg-green-700 text-white text-sm font-bold py-2 px-5 rounded-lg transition-colors shadow-sm cursor-pointer">
                  <i class="fas fa-folder-open mr-1"></i> Subir imagen
                </label>
                <button type="button" onclick="openMediaBrowserForFeatured()" class="inline-block bg-ca-navy hover:bg-gray-800 text-white text-sm font-bold py-2 px-5 rounded-lg transition-colors shadow-sm">
                  <i class="fas fa-photo-video mr-1"></i> Mediateca
                </button>
              </div>
              <p class="text-xs text-gray-400 mt-3">JPG, PNG, GIF, WebP &mdash; M&aacute;x. 10 MB</p>
            </div>
          </div>
          <input type="file" name="featured_image" id="featuredImageInput" class="hidden" accept="image/jpeg,image/png,image/gif,image/webp,image/avif" onchange="previewFeaturedImage(this)"/>
          <!-- Hidden input to store selected media URL as featured image -->
          <input type="hidden" id="featuredMediaUrl" name="featured_media_url" value="<?= htmlspecialchars($post['featured_image'] ?? '', ENT_QUOTES) ?>"/>
        </div>

        <!-- Content (TinyMCE) -->
        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5">Contenido *</label>
          <textarea id="content" name="content" class="min-h-[400px]"><?= htmlspecialchars($post['content']) ?></textarea>
          <p class="text-xs text-gray-400 mt-1 flex items-center gap-2">
            <i class="fas fa-info-circle"></i>
            Usa el editor visual para dar formato al contenido. Las im&aacute;genes se pueden arrastrar o pegar directamente.
          </p>
        </div>

        <?php $publishedAtValue = !empty($post['published_at']) ? format_cdmx_datetime($post['published_at'], 'Y-m-d\TH:i') : ''; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
          <div class="flex items-center gap-3 pt-3">
            <input class="w-5 h-5 text-ca-green border-gray-300 rounded focus:ring-ca-green" id="published" name="published" type="checkbox" value="1" <?= $post['published'] ? 'checked' : '' ?>/>
            <label class="text-sm font-medium text-ca-dark-gray" for="published">Marcar como publicado</label>
          </div>
          <div>
            <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="published_at">Fecha y hora de publicaci&oacute;n (CDMX)</label>
            <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="published_at" name="published_at" type="datetime-local" value="<?= htmlspecialchars($publishedAtValue) ?>"/>
            <p class="text-xs text-gray-400 mt-1">Vac&iacute;o publica de inmediato. Si eliges una fecha futura, el post queda programado.</p>
          </div>
        </div>
      </div>

      <!-- ===== TAB: SEO ===== -->
      <div id="tab-seo" class="tab-panel hidden space-y-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
          <i class="fas fa-info-circle mr-1"></i>
          Configura los meta tags para mejorar el posicionamiento en buscadores (SEO).
        </div>

        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="meta_title">Meta T&iacute;tulo</label>
          <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="meta_title" name="meta_title" type="text" placeholder="T&iacute;tulo para SEO (si se deja vac&iacute;o, se usar&aacute; el t&iacute;tulo del art&iacute;culo)" maxlength="70" value="<?= htmlspecialchars($post['meta_title'] ?? '') ?>"/>
          <div class="flex justify-between mt-1">
            <p class="text-xs text-gray-400">M&aacute;ximo 70 caracteres recomendado.</p>
            <span id="metaTitleCount" class="text-xs text-gray-400"><?= strlen($post['meta_title'] ?? '') ?>/70</span>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="meta_description">Meta Descripci&oacute;n</label>
          <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition" id="meta_description" name="meta_description" rows="3" placeholder="Descripci&oacute;n para resultados de b&uacute;squeda" maxlength="160"><?= htmlspecialchars($post['meta_description'] ?? '') ?></textarea>
          <div class="flex justify-between mt-1">
            <p class="text-xs text-gray-400">M&aacute;ximo 160 caracteres recomendado.</p>
            <span id="metaDescCount" class="text-xs text-gray-400"><?= strlen($post['meta_description'] ?? '') ?>/160</span>
          </div>
        </div>

        <!-- Google Preview -->
        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
          <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">Vista previa Google</p>
          <div id="googlePreview">
            <p class="text-sm text-green-700 font-medium" id="previewUrl"><?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?></p>
            <p class="text-lg text-blue-700 font-medium hover:underline cursor-pointer" id="previewTitle"><?= htmlspecialchars($post['meta_title'] ?: $post['title']) ?></p>
            <p class="text-sm text-gray-600" id="previewDesc"><?= htmlspecialchars($post['meta_description'] ?: ($post['excerpt'] ?: 'Descripci&oacute;n que aparecer&aacute; en los resultados de b&uacute;squeda...')) ?></p>
          </div>
        </div>
      </div>

      <!-- ===== TAB: JSON-LD ===== -->
      <div id="tab-jsonld" class="tab-panel hidden space-y-6">
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-700">
          <i class="fas fa-info-circle mr-1"></i>
          <strong>Importante para GEO 2026:</strong> Inserta aqu&iacute; el JSON-LD Schema markup para que los motores de b&uacute;squeda entiendan mejor el contenido del art&iacute;culo.
          <a href="https://schema.org" target="_blank" class="underline font-medium">Ver documentaci&oacute;n de Schema.org</a>
        </div>

        <div>
          <label class="block text-sm font-medium text-ca-dark-gray mb-1.5" for="json_ld">Script JSON-LD</label>
          <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green outline-none transition font-mono text-sm" id="json_ld" name="json_ld" rows="16" placeholder="{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "T&iacute;tulo del art&iacute;culo",
  "description": "Descripci&oacute;n del art&iacute;culo"
}"><?= htmlspecialchars($post['json_ld'] ?? '') ?></textarea>
          <p class="text-xs text-gray-400 mt-1">
            <i class="fas fa-info-circle mr-1"></i>
            No incluyas las etiquetas <code>&lt;script&gt;</code>, solo el objeto JSON. Se insertar&aacute; autom&aacute;ticamente en el <code>&lt;head&gt;</code> de la p&aacute;gina.
          </p>
        </div>

        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
          <p class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">Ejemplo de Schema para BlogPost</p>
          <pre class="text-xs text-gray-600 overflow-x-auto"><code>{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "Gu&iacute;a de Gesti&oacute;n Ambiental",
  "description": "Descubre los aspectos clave...",
  "author": {
    "@type": "Organization",
    "name": "Consultor&iacute;a Ambiental"
  },
  "datePublished": "2026-05-29",
  "image": "https://ejemplo.com/imagen.jpg"
}</code></pre>
        </div>
      </div>

      <!-- Submit -->
      <div class="flex items-center gap-4 pt-6 border-t border-gray-200 mt-6">
        <button class="bg-ca-green hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg transition-colors shadow-sm" type="submit">
          <i class="fas fa-save mr-1"></i> Actualizar
        </button>
        <a href="<?= BASE_URL ?>/admin/blog" class="text-gray-500 hover:text-ca-navy font-medium transition-colors">
          Cancelar
        </a>
      </div>

    </form>
  </div>
</div>

<!-- Media Browser Modal -->
<div id="mediaBrowserModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
    <div id="mediaBrowserContent">
      <div class="p-6 text-center">
        <i class="fas fa-spinner fa-spin text-3xl text-ca-green"></i>
        <p class="text-gray-500 mt-2">Cargando mediateca...</p>
      </div>
    </div>
  </div>
</div>

<script>
// --- Tab Switching --------------------------------------------------
function switchTab(tab) {
  document.querySelectorAll('.tab-panel').forEach(function(el) {
    el.classList.add('hidden');
  });
  document.getElementById('tab-' + tab).classList.remove('hidden');
  document.querySelectorAll('#tabNav button').forEach(function(btn) {
    btn.classList.remove('border-ca-green', 'text-ca-green');
    btn.classList.add('border-transparent', 'text-gray-500');
  });
  var activeBtn = document.getElementById('tab-' + tab + '-btn');
  activeBtn.classList.remove('border-transparent', 'text-gray-500');
  activeBtn.classList.add('border-ca-green', 'text-ca-green');
}

// --- Featured Image Preview -----------------------------------------
function previewFeaturedImage(input) {
  var preview = document.getElementById('featuredPreview');
  var placeholder = document.getElementById('featuredPlaceholder');
  var img = document.getElementById('featuredPreviewImg');

  if (input.files && input.files[0]) {
    document.getElementById('featuredMediaUrl').value = '';
    var reader = new FileReader();
    reader.onload = function(e) {
      img.src = e.target.result;
      preview.classList.remove('hidden');
      placeholder.classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function clearFeaturedImage() {
  document.getElementById('featuredPreview').classList.add('hidden');
  document.getElementById('featuredPlaceholder').classList.remove('hidden');
  document.getElementById('featuredImageInput').value = '';
  document.getElementById('featuredMediaUrl').value = '';
}

// Drag & drop for featured image
var featuredZone = document.getElementById('featuredDropZone');
var featuredInput = document.getElementById('featuredImageInput');
if (featuredZone && featuredInput) {
  featuredZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('border-ca-green', 'bg-green-50');
  });
  featuredZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('border-ca-green', 'bg-green-50');
  });
  featuredZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-ca-green', 'bg-green-50');
    if (e.dataTransfer.files.length) {
      featuredInput.files = e.dataTransfer.files;
      previewFeaturedImage(featuredInput);
    }
  });
}

// --- SEO Character Counters -----------------------------------------
document.getElementById('meta_title').addEventListener('input', function() {
  var count = this.value.length;
  document.getElementById('metaTitleCount').textContent = count + '/70';
  updateGooglePreview();
});

document.getElementById('meta_description').addEventListener('input', function() {
  var count = this.value.length;
  document.getElementById('metaDescCount').textContent = count + '/160';
  updateGooglePreview();
});

document.getElementById('title').addEventListener('input', function() {
  updateGooglePreview();
});

function updateGooglePreview() {
  var title = document.getElementById('meta_title').value || document.getElementById('title').value || 'T&iacute;tulo del art&iacute;culo';
  var desc = document.getElementById('meta_description').value || 'Descripci&oacute;n que aparecer&aacute; en los resultados de b&uacute;squeda...';
  var slug = document.getElementById('slug').value || 'url-del-articulo';
  document.getElementById('previewTitle').textContent = title;
  document.getElementById('previewDesc').textContent = desc;
  document.getElementById('previewUrl').textContent = '<?= BASE_URL ?>/blog/' + slug;
}

// --- Media Browser --------------------------------------------------
function openMediaBrowser() {
  var modal = document.getElementById('mediaBrowserModal');
  modal.classList.remove('hidden');

  fetch('<?= BASE_URL ?>/admin/media/browse')
    .then(function(r) { return r.text(); })
    .then(function(html) {
      document.getElementById('mediaBrowserContent').innerHTML = html;
    })
    .catch(function(err) {
      document.getElementById('mediaBrowserContent').innerHTML = '<div class="p-6 text-center text-red-500">Error al cargar la mediateca.</div>';
    });
}

// Open media browser for featured image selection
function openMediaBrowserForFeatured() {
  // Temporarily override the callback to handle featured image
  window.mediaBrowserCallback = function(url, name) {
    // Set the featured image preview
    var preview = document.getElementById('featuredPreview');
    var placeholder = document.getElementById('featuredPlaceholder');
    var img = document.getElementById('featuredPreviewImg');
    img.src = url;
    preview.classList.remove('hidden');
    placeholder.classList.add('hidden');
    document.getElementById('featuredImageInput').value = '';
    // Store the URL in the hidden input
    document.getElementById('featuredMediaUrl').value = url;
    // Restore the default callback for next time
    window.mediaBrowserCallback = defaultMediaBrowserCallback;
  };
  openMediaBrowser();
}

// Default callback for inserting into TinyMCE editor
var defaultMediaBrowserCallback = function(url, name) {
  var editor = window.tinymceEditor;
  if (editor) {
    var ext = url.split('.').pop().toLowerCase();
    var imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (imageExts.indexOf(ext) !== -1) {
      editor.insertContent('<img src="' + url + '" alt="' + name + '" style="max-width:100%;height:auto;" />');
    } else {
      editor.insertContent('<a href="' + url + '">' + name + '</a>');
    }
  }
};

window.mediaBrowserCallback = defaultMediaBrowserCallback;

// Global functions for media browser modal
function selectMedia(url, name) {
  if (typeof window.mediaBrowserCallback === 'function') {
    window.mediaBrowserCallback(url, name);
  }
  closeMediaBrowser();
}

function closeMediaBrowser() {
  var modal = document.getElementById('mediaBrowserModal');
  if (modal) modal.classList.add('hidden');
}

// --- Auto-generate slug from title ----------------------------------
function slugifyText(value) {
  var normalized = value && value.normalize ? value.normalize('NFD').replace(/[\u0300-\u036f]/g, '') : value;
  return normalized
    .toLowerCase()
    .replace(/[^a-z0-9\s_-]/g, '')
    .replace(/[\s_]+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
}

document.getElementById('title').addEventListener('blur', function() {
  var slugField = document.getElementById('slug');
  if (!slugField.value) {
    slugField.value = slugifyText(this.value);
  }
});
</script>
