<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title><?= $title ?? 'Admin' ?> | <?= APP_NAME ?></title>
  
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- TinyMCE WYSIWYG Editor (CDN con API key) -->
  <script src="https://cdn.tiny.cloud/1/44lhcd5vz74lz90ljvg8mva2u03h83xnltnyxiggfmklyoxe/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
  
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'ca-green': '#2E7D32',
            'ca-light-green': '#66BB6A',
            'ca-navy': '#1B3A4B',
            'ca-dark-gray': '#263238',
            'ca-light-gray': '#90A4AE',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    /* TinyMCE custom styles */
    .tox-tinymce {
      border-radius: 0.5rem !important;
      border: 1px solid #d1d5db !important;
    }
    .tox .tox-toolbar__primary {
      background: #f9fafb !important;
    }
    .tox .tox-edit-area__iframe {
      background: #fff !important;
    }
    /* Hide the "This is a popup" notification from TinyMCE */
    .tox .tox-notification--in {
      display: none !important;
    }
    .tox .tox-statusbar__branding {
      display: none !important;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <?php if (isset($_SESSION['admin_id'])): ?>
    <!-- Sidebar -->
    <?php require VIEWS_DIR . '/partials/admin/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <?php require VIEWS_DIR . '/partials/admin/topbar.php'; ?>
      
      <main class="flex-1 p-6 overflow-y-auto">
        <?= $content ?>
      </main>
    </div>
  <?php else: ?>
    <!-- Login page - no sidebar -->
    <main class="flex-1 flex items-center justify-center">
      <?= $content ?>
    </main>
  <?php endif; ?>

  <!-- Global BASE_URL for JS -->
  <script>
    var BASE_URL = '<?= BASE_URL ?>';
  </script>

  <!-- TinyMCE Initialization -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var contentEl = document.getElementById('content');
    if (!contentEl || contentEl.tagName !== 'TEXTAREA') return;

    // Configure custom image upload handler
    function tinymceUploadHandler(blobInfo, progress) {
      return new Promise(function(resolve, reject) {
        var formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        formData.append('X-Requested-With', 'XMLHttpRequest');
        fetch(BASE_URL + '/admin/media/subir', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(result) {
          if (result.url) {
            resolve(result.url);
          } else {
            reject('Subida fallida');
          }
        })
        .catch(function(err) { reject(err); });
      });
    }

    // Initialize TinyMCE
    tinymce.init({
      selector: '#content',
      height: 600,
      language: 'es',
      promotion: false,
      branding: false,
      menubar: true,
      plugins: [
        // Core editing features
        'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
        // Premium features (free trial until Jun 12, 2026)
        'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'tinymceai', 'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
      ],
      toolbar: [
        'undo redo | tinymceai-chat tinymceai-quickactions tinymceai-review | blocks fontfamily fontsize',
        '|', 'bold italic underline strikethrough | link media table mergetags',
        '|', 'addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare',
        '|', 'align lineheight | checklist numlist bullist indent outdent',
        '|', 'emoticons charmap | removeformat',
        '|', 'mediateca'
      ],
      toolbar_mode: 'wrap',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Admin',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ],
      setup: function(editor) {
        // Store editor instance globally for media browser callback
        window.tinymceEditor = editor;

        // Add custom "Mediateca" button
        editor.ui.registry.addButton('mediateca', {
          text: '📁 Mediateca',
          tooltip: 'Abrir mediateca',
          onAction: function() {
            if (typeof openMediaBrowser === 'function') {
              openMediaBrowser();
            }
          }
        });
      },
      extended_valid_elements: 'i[class],span[class],div[class|data-*],section[class],main[class]',
      images_upload_handler: tinymceUploadHandler,
      automatic_uploads: true,
      file_picker_types: 'image media',
      file_picker_callback: function(callback, value, meta) {
        // Open media browser for file selection
        if (typeof openMediaBrowser === 'function') {
          window.tinymceFilePickerCallback = callback;
          openMediaBrowser();
        }
      },
      content_css: [
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
      ],
      content_style: `
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        *, ::before, ::after { --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-sticky: 0; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / 0.5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; }
        ::backdrop { --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-sticky: 0; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / 0.5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; }
        .container { width: 100%; }
        @media (min-width: 640px) { .container { max-width: 640px; } }
        @media (min-width: 768px) { .container { max-width: 768px; } }
        @media (min-width: 1024px) { .container { max-width: 1024px; } }
        @media (min-width: 1280px) { .container { max-width: 1280px; } }
        @media (min-width: 1536px) { .container { max-width: 1536px; } }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .md\\:px-8 { padding-left: 2rem; padding-right: 2rem; }
        .max-w-7xl { max-width: 80rem; }
        .max-w-6xl { max-width: 72rem; }
        .max-w-5xl { max-width: 64rem; }
        .max-w-4xl { max-width: 56rem; }
        .max-w-3xl { max-width: 48rem; }
        .w-full { width: 100%; }
        .w-24 { width: 6rem; }
        .w-14 { width: 3.5rem; }
        .w-1\\/3 { width: 33.333333%; }
        .w-2\\/3 { width: 66.666667%; }
        .h-1 { height: 0.25rem; }
        .h-14 { height: 3.5rem; }
        .h-24 { height: 6rem; }
        .h-auto { height: auto; }
        .min-h-screen { min-height: 100vh; }
        .flex { display: flex; }
        .inline-flex { display: inline-flex; }
        .grid { display: grid; }
        .hidden { display: none; }
        .flex-col { flex-direction: column; }
        .flex-row { flex-direction: row; }
        .flex-wrap { flex-wrap: wrap; }
        .items-start { align-items: flex-start; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .gap-2 { gap: 0.5rem; }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .gap-8 { gap: 2rem; }
        .gap-12 { gap: 3rem; }
        .space-y-2 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(0.5rem * calc(1 - var(--tw-space-y-reverse))); margin-bottom: calc(0.5rem * var(--tw-space-y-reverse)); }
        .space-y-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(1rem * calc(1 - var(--tw-space-y-reverse))); margin-bottom: calc(1rem * var(--tw-space-y-reverse)); }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .rounded { border-radius: 0.25rem; }
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-2xl { border-radius: 1rem; }
        .rounded-full { border-radius: 9999px; }
        .border { border-width: 1px; }
        .border-t { border-top-width: 1px; }
        .border-b-8 { border-bottom-width: 8px; }
        .border-gray-50 { border-color: #f9fafb; }
        .border-gray-100 { border-color: #f3f4f6; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-700 { border-color: #374151; }
        .border-ca-green { border-color: #2E7D32; }
        .border-ca-light-green\\/30 { border-color: rgb(102 187 106 / 0.3); }
        .border-white { border-color: #fff; }
        .bg-white { background-color: #fff; }
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-gray-800\\/50 { background-color: rgb(31 41 55 / 0.5); }
        .bg-ca-bg { background-color: #f8f9fa; }
        .bg-ca-navy { background-color: #1B3A4B; }
        .bg-ca-green { background-color: #2E7D32; }
        .bg-ca-light-green\\/20 { background-color: rgb(102 187 106 / 0.2); }
        .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)); }
        .from-ca-green { --tw-gradient-from: #2E7D32; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgb(46 125 50 / 0)); }
        .to-\\[\\#236026\\] { --tw-gradient-to: #236026; }
        .from-ca-navy\\/95 { --tw-gradient-from: rgb(27 58 75 / 0.95); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgb(27 58 75 / 0)); }
        .via-ca-navy\\/90 { --tw-gradient-to: rgb(27 58 75 / 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-via, rgb(27 58 75 / 0.9)), var(--tw-gradient-to); }
        .to-ca-navy\\/70 { --tw-gradient-to: rgb(27 58 75 / 0.7); }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .p-8 { padding: 2rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .px-8 { padding-left: 2rem; padding-right: 2rem; }
        .px-10 { padding-left: 2.5rem; padding-right: 2.5rem; }
        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .py-16 { padding-top: 4rem; padding-bottom: 4rem; }
        .py-20 { padding-top: 5rem; padding-bottom: 5rem; }
        .pt-4 { padding-top: 1rem; }
        .pt-32 { padding-top: 8rem; }
        .pb-20 { padding-bottom: 5rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mb-10 { margin-bottom: 2.5rem; }
        .mb-12 { margin-bottom: 3rem; }
        .mb-16 { margin-bottom: 4rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-4 { margin-top: 1rem; }
        .mr-2 { margin-right: 0.5rem; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-xs { font-size: 0.75rem; line-height: 1rem; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
        .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
        .text-2xl { font-size: 1.5rem; line-height: 2rem; }
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        .text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
        .text-6xl { font-size: 3.75rem; line-height: 1; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .font-extrabold { font-weight: 800; }
        .uppercase { text-transform: uppercase; }
        .tracking-widest { letter-spacing: 0.1em; }
        .leading-tight { line-height: 1.25; }
        .leading-relaxed { line-height: 1.625; }
        .text-white { color: #fff; }
        .text-gray-300 { color: #d1d5db; }
        .text-gray-400 { color: #9ca3af; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-700 { color: #374151; }
        .text-green-100 { color: #dcfce7; }
        .text-ca-navy { color: #1B3A4B; }
        .text-ca-dark-gray { color: #263238; }
        .text-ca-light-gray { color: #90A4AE; }
        .text-ca-green { color: #2E7D32; }
        .text-ca-light-green { color: #66BB6A; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
        .shadow-xl { box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); }
        .shadow-inner { box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.05); }
        .border-ca-light-green { border-color: #66BB6A; }
        .hover\\:border-ca-light-green:hover { border-color: #66BB6A; }
        .hover\\:shadow-md:hover { box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }
        .hover\\:shadow-xl:hover { box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); }
        .hover\\:bg-gray-100:hover { background-color: #f3f4f6; }
        .hover\\:bg-green-700:hover { background-color: #15803d; }
        .hover\\:bg-ca-green:hover { background-color: #2E7D32; }
        .hover\\:text-white:hover { color: #fff; }
        .hover\\:scale-105:hover { --tw-scale-x: 1.05; --tw-scale-y: 1.05; transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y)); }
        .hover\\:-translate-y-1:hover { --tw-translate-y: -0.25rem; }
        .group:hover .group-hover\\:bg-ca-green { background-color: #2E7D32; }
        .group:hover .group-hover\\:text-white { color: #fff; }
        .transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        .transition-colors { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        .transition-shadow { transition-property: box-shadow; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        .transform { transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y)); }
        .overflow-hidden { overflow: hidden; }
        .relative { position: relative; }
        .absolute { position: absolute; }
        .fixed { position: fixed; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .top-0 { top: 0; }
        .right-0 { right: 0; }
        .z-10 { z-index: 10; }
        .z-50 { z-index: 50; }
        .opacity-5 { opacity: 0.05; }
        .object-cover { object-fit: cover; }
        .bg-cover { background-size: cover; }
        .bg-center { background-position: center; }
        .bg-no-repeat { background-repeat: no-repeat; }
        .border-ca-green { border-color: #2E7D32; }
        .md\\:col-span-2 { grid-column: span 2 / span 2; }
        .lg\\:col-span-2 { grid-column: span 2 / span 2; }
        .lg\\:col-span-1 { grid-column: span 1 / span 1; }
        @media (min-width: 768px) {
          .md\\:flex-row { flex-direction: row; }
          .md\\:w-1\\/3 { width: 33.333333%; }
          .md\\:w-2\\/3 { width: 66.666667%; }
          .md\\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
          .md\\:text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
          .md\\:text-5xl { font-size: 3rem; line-height: 1; }
          .md\\:pt-40 { padding-top: 10rem; }
          .md\\:pb-28 { padding-bottom: 7rem; }
        }
        @media (min-width: 1024px) {
          .lg\\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
          .lg\\:text-6xl { font-size: 3.75rem; line-height: 1; }
        }
        @media (min-width: 640px) {
          .sm\\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        body {
          font-family: 'Inter', sans-serif;
          font-size: 15px;
          line-height: 1.7;
          color: #263238;
          background: #ffffff;
        }
        p { margin-bottom: 0.75em; }
        h2 { font-size: 1.5em; font-weight: 700; margin: 1em 0 0.5em; color: #1B3A4B; }
        h3 { font-size: 1.25em; font-weight: 600; margin: 0.8em 0 0.4em; color: #1B3A4B; }
        img { max-width: 100%; height: auto; border-radius: 0.5rem; }
        a, a:link, a:visited { text-decoration: none !important; color: #2E7D32; }
        a:hover { color: #1B5E20; }
      `
    });
  });
  </script>

</body>
</html>
