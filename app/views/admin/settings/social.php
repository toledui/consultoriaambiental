<form method="POST" action="<?= BASE_URL ?>/admin/settings/social/guardar" class="space-y-8">

  <?php
  // Decode JSON settings
  $socialHeader = [];
  $socialFooter = [];
  $socialContact = [];
  if (!empty($settings['social_header'])) {
      $decoded = json_decode($settings['social_header'], true);
      $socialHeader = is_array($decoded) ? $decoded : [];
  }
  if (!empty($settings['social_footer'])) {
      $decoded = json_decode($settings['social_footer'], true);
      $socialFooter = is_array($decoded) ? $decoded : [];
  }
  if (!empty($settings['social_contact'])) {
      $decoded = json_decode($settings['social_contact'], true);
      $socialContact = is_array($decoded) ? $decoded : [];
  }
  ?>

  <!-- ─── Header Social Networks ─────────────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-arrow-up text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Redes sociales — Header (Barra superior)</h3>
        <p class="text-sm text-gray-500">Estos iconos aparecen en la barra de navegación superior del sitio.</p>
      </div>
    </div>
    <div id="social-header-container" class="space-y-3">
      <?php foreach ($socialHeader as $item): ?>
      <div class="social-row flex items-center gap-3">
        <div class="flex-1">
          <select name="social_header[icon][]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm bg-white">
            <?php renderSocialIconOptions($item['icon'] ?? '') ?>
          </select>
        </div>
        <div class="flex-[2]">
          <input type="url" name="social_header[url][]" value="<?= htmlspecialchars($item['url'] ?? '') ?>" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <button type="button" class="remove-row-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <?php endforeach; ?>
      <?php if (empty($socialHeader)): ?>
      <div class="social-row flex items-center gap-3">
        <div class="flex-1">
          <select name="social_header[icon][]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm bg-white">
            <?php renderSocialIconOptions('') ?>
          </select>
        </div>
        <div class="flex-[2]">
          <input type="url" name="social_header[url][]" value="" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <button type="button" class="remove-row-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <?php endif; ?>
    </div>
    <button type="button" class="add-row-btn mt-3 px-4 py-2 bg-ca-navy/10 text-ca-navy rounded-lg hover:bg-ca-navy/20 transition-colors text-sm font-medium" data-container="social-header-container">
      <i class="fas fa-plus mr-1"></i> Agregar red social
    </button>
  </div>

  <hr class="border-gray-200">

  <!-- ─── Footer Social Networks ─────────────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-arrow-down text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Redes sociales — Footer (Pie de página)</h3>
        <p class="text-sm text-gray-500">Estos iconos aparecen en la sección "Síguenos" del pie de página.</p>
      </div>
    </div>
    <div id="social-footer-container" class="space-y-3">
      <?php foreach ($socialFooter as $item): ?>
      <div class="social-row flex items-center gap-3">
        <div class="flex-1">
          <select name="social_footer[icon][]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm bg-white">
            <?php renderSocialIconOptions($item['icon'] ?? '') ?>
          </select>
        </div>
        <div class="flex-[2]">
          <input type="url" name="social_footer[url][]" value="<?= htmlspecialchars($item['url'] ?? '') ?>" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <button type="button" class="remove-row-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <?php endforeach; ?>
      <?php if (empty($socialFooter)): ?>
      <div class="social-row flex items-center gap-3">
        <div class="flex-1">
          <select name="social_footer[icon][]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm bg-white">
            <?php renderSocialIconOptions('') ?>
          </select>
        </div>
        <div class="flex-[2]">
          <input type="url" name="social_footer[url][]" value="" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <button type="button" class="remove-row-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <?php endif; ?>
    </div>
    <button type="button" class="add-row-btn mt-3 px-4 py-2 bg-ca-navy/10 text-ca-navy rounded-lg hover:bg-ca-navy/20 transition-colors text-sm font-medium" data-container="social-footer-container">
      <i class="fas fa-plus mr-1"></i> Agregar red social
    </button>
  </div>

  <hr class="border-gray-200">

  <!-- ─── Contact Page Social Networks ───────────────────────── -->
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-ca-navy/10 p-2 rounded-lg">
        <i class="fas fa-phone text-ca-navy"></i>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-ca-navy">Redes sociales — Página de Contacto</h3>
        <p class="text-sm text-gray-500">Estos iconos aparecerán en la futura página de contacto.</p>
      </div>
    </div>
    <div id="social-contact-container" class="space-y-3">
      <?php foreach ($socialContact as $item): ?>
      <div class="social-row flex items-center gap-3">
        <div class="flex-1">
          <select name="social_contact[icon][]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm bg-white">
            <?php renderSocialIconOptions($item['icon'] ?? '') ?>
          </select>
        </div>
        <div class="flex-[2]">
          <input type="url" name="social_contact[url][]" value="<?= htmlspecialchars($item['url'] ?? '') ?>" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <button type="button" class="remove-row-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <?php endforeach; ?>
      <?php if (empty($socialContact)): ?>
      <div class="social-row flex items-center gap-3">
        <div class="flex-1">
          <select name="social_contact[icon][]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm bg-white">
            <?php renderSocialIconOptions('') ?>
          </select>
        </div>
        <div class="flex-[2]">
          <input type="url" name="social_contact[url][]" value="" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
        </div>
        <button type="button" class="remove-row-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <?php endif; ?>
    </div>
    <button type="button" class="add-row-btn mt-3 px-4 py-2 bg-ca-navy/10 text-ca-navy rounded-lg hover:bg-ca-navy/20 transition-colors text-sm font-medium" data-container="social-contact-container">
      <i class="fas fa-plus mr-1"></i> Agregar red social
    </button>
  </div>

  <div class="flex justify-end pt-4 border-t border-gray-200">
    <button type="submit" class="px-6 py-2.5 bg-ca-green text-white font-medium rounded-lg hover:bg-ca-navy transition-colors text-sm">
      <i class="fas fa-save mr-2"></i>
      Guardar configuración de redes sociales
    </button>
  </div>
</form>

<?php
/**
 * Helper function to render <option> tags for social icon selector.
 */
function renderSocialIconOptions(string $selected = ''): void
{
    $icons = [
        ''                => '— Seleccionar —',
        'facebook-f'      => 'Facebook',
        'twitter'         => 'X (Twitter)',
        'instagram'       => 'Instagram',
        'linkedin-in'     => 'LinkedIn',
        'youtube'         => 'YouTube',
        'tiktok'          => 'TikTok',
        'whatsapp'        => 'WhatsApp',
        'telegram'        => 'Telegram',
        'facebook-messenger' => 'Messenger',
        'snapchat'        => 'Snapchat',
        'pinterest'       => 'Pinterest',
        'threads'         => 'Threads',
        'discord'         => 'Discord',
        'spotify'         => 'Spotify',
        'soundcloud'      => 'SoundCloud',
        'vimeo'           => 'Vimeo',
        'behance'         => 'Behance',
        'dribbble'        => 'Dribbble',
        'github'          => 'GitHub',
        'gitlab'          => 'GitLab',
        'medium'          => 'Medium',
        'twitch'          => 'Twitch',
        'reddit'          => 'Reddit',
        'tumblr'          => 'Tumblr',
        'flickr'          => 'Flickr',
        'vine'            => 'Vine',
        'weibo'           => 'Weibo',
        'xing'            => 'Xing',
        'stack-overflow'  => 'Stack Overflow',
        'envelope'        => 'Email (fa-envelope)',
        'globe'           => 'Web (fa-globe)',
        'link'            => 'Link genérico (fa-link)',
    ];

    foreach ($icons as $value => $label) {
        $sel = ($value === $selected) ? ' selected' : '';
        $iconPreview = $value ? 'fab fa-' . $value : '';
        if (in_array($value, ['envelope', 'globe', 'link'])) {
            $iconPreview = 'fas fa-' . $value;
        }
        $dataIcon = htmlspecialchars($iconPreview);
        echo "<option value=\"" . htmlspecialchars($value) . "\" data-icon=\"{$dataIcon}\"{$sel}>{$label}</option>\n";
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add row functionality
    document.querySelectorAll('.add-row-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const containerId = this.dataset.container;
            const container = document.getElementById(containerId);
            const prefix = containerId.replace('social-', '').replace('-container', ''); // header, footer, contact
            
            // Get the first row's select HTML to clone the options
            const firstSelect = container.querySelector('.social-row select');
            const selectHTML = firstSelect ? firstSelect.outerHTML : '';
            
            const row = document.createElement('div');
            row.className = 'social-row flex items-center gap-3';
            row.innerHTML = `
                <div class="flex-1">
                    <select name="social_${prefix}[icon][]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm bg-white">
                        ${getIconOptions('')}
                    </select>
                </div>
                <div class="flex-[2]">
                    <input type="url" name="social_${prefix}[url][]" value="" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ca-green focus:border-ca-green text-sm">
                </div>
                <button type="button" class="remove-row-btn px-3 py-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(row);
            attachRemoveHandler(row.querySelector('.remove-row-btn'));
        });
    });

    // Remove row functionality
    document.querySelectorAll('.remove-row-btn').forEach(function(btn) {
        attachRemoveHandler(btn);
    });

    function attachRemoveHandler(btn) {
        btn.addEventListener('click', function() {
            const row = this.closest('.social-row');
            const container = row.parentElement;
            if (container.querySelectorAll('.social-row').length > 1) {
                row.remove();
            } else {
                // Clear fields instead of removing the last row
                row.querySelectorAll('input').forEach(function(input) { input.value = ''; });
                row.querySelector('select').selectedIndex = 0;
            }
        });
    }

    function getIconOptions(selected) {
        const icons = {
            '': '— Seleccionar —',
            'facebook-f': 'Facebook',
            'twitter': 'X (Twitter)',
            'instagram': 'Instagram',
            'linkedin-in': 'LinkedIn',
            'youtube': 'YouTube',
            'tiktok': 'TikTok',
            'whatsapp': 'WhatsApp',
            'telegram': 'Telegram',
            'facebook-messenger': 'Messenger',
            'snapchat': 'Snapchat',
            'pinterest': 'Pinterest',
            'threads': 'Threads',
            'discord': 'Discord',
            'spotify': 'Spotify',
            'soundcloud': 'SoundCloud',
            'vimeo': 'Vimeo',
            'behance': 'Behance',
            'dribbble': 'Dribbble',
            'github': 'GitHub',
            'gitlab': 'GitLab',
            'medium': 'Medium',
            'twitch': 'Twitch',
            'reddit': 'Reddit',
            'tumblr': 'Tumblr',
            'flickr': 'Flickr',
            'vine': 'Vine',
            'weibo': 'Weibo',
            'xing': 'Xing',
            'stack-overflow': 'Stack Overflow',
            'envelope': 'Email (fa-envelope)',
            'globe': 'Web (fa-globe)',
            'link': 'Link genérico (fa-link)'
        };
        let html = '';
        for (const [value, label] of Object.entries(icons)) {
            const sel = value === selected ? ' selected' : '';
            html += `<option value="${value}"${sel}>${label}</option>\n`;
        }
        return html;
    }
});
</script>
