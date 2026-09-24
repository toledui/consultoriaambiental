(function () {
  'use strict';

  var SIZE_STYLES = {
    small: '.55rem 1rem',
    medium: '.78rem 1.25rem',
    large: '1rem 1.65rem'
  };

  var RADIUS_STYLES = {
    square: '0px',
    soft: '8px',
    rounded: '16px',
    pill: '9999px'
  };

  var modalState = {
    editor: null,
    button: null,
    bookmark: null,
    previousOverflow: ''
  };

  function closestButton(editor, node) {
    return editor.dom.getParent(node || editor.selection.getNode(), 'a.ca-editor-button');
  }

  function readButtonData(editor, button) {
    var row = button ? editor.dom.getParent(button, '.ca-button-row') : null;

    return {
      text: button ? button.textContent.trim() : 'Más información',
      url: button ? (button.getAttribute('href') || '') : '',
      backgroundColor: button ? (button.getAttribute('data-ca-bg') || '#2E7D32') : '#2E7D32',
      textColor: button ? (button.getAttribute('data-ca-color') || '#FFFFFF') : '#FFFFFF',
      size: button ? (button.getAttribute('data-ca-size') || 'medium') : 'medium',
      radius: button ? (button.getAttribute('data-ca-radius') || 'soft') : 'soft',
      alignment: button ? (button.getAttribute('data-ca-align') || (row && row.style.textAlign) || 'left') : 'left',
      newTab: Boolean(button && button.getAttribute('target') === '_blank')
    };
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function normalizeUrl(value) {
    var url = String(value || '').trim();
    if (/^[\w.-]+\.[a-z]{2,}(?:[\/?#]|$)/i.test(url)) {
      url = 'https://' + url;
    }

    return /^(https?:\/\/|mailto:|tel:|\/|\.\/|\.\.\/|#|\?)/i.test(url) ? url : '';
  }

  function buttonAttributes(data) {
    var target = data.newTab ? ' target="_blank" rel="noopener noreferrer"' : '';
    var style = '--ca-button-bg:' + data.backgroundColor
      + ';--ca-button-color:' + data.textColor
      + ';--ca-button-padding:' + SIZE_STYLES[data.size]
      + ';--ca-button-radius:' + RADIUS_STYLES[data.radius] + ';';

    return 'href="' + escapeHtml(data.url) + '" class="ca-editor-button"'
      + ' data-ca-bg="' + escapeHtml(data.backgroundColor) + '"'
      + ' data-ca-color="' + escapeHtml(data.textColor) + '"'
      + ' data-ca-size="' + escapeHtml(data.size) + '"'
      + ' data-ca-radius="' + escapeHtml(data.radius) + '"'
      + ' data-ca-align="' + escapeHtml(data.alignment) + '"'
      + ' style="' + escapeHtml(style) + '"' + target;
  }

  function field(form, name) {
    return form.elements.namedItem(name);
  }

  function setError(message) {
    var error = document.getElementById('ca-button-modal-error');
    error.textContent = message || '';
    error.hidden = !message;
  }

  function closeModal() {
    var modal = document.getElementById('ca-button-modal');
    if (!modal || modal.hidden) return;

    modal.hidden = true;
    document.body.style.overflow = modalState.previousOverflow;
    setError('');

    if (modalState.editor) {
      modalState.editor.focus();
    }

    modalState.editor = null;
    modalState.button = null;
    modalState.bookmark = null;
  }

  function submitButton(event) {
    event.preventDefault();

    var editor = modalState.editor;
    var form = event.currentTarget;
    if (!editor) {
      closeModal();
      return;
    }

    var data = {
      text: String(field(form, 'text').value || '').trim(),
      url: normalizeUrl(field(form, 'url').value),
      backgroundColor: field(form, 'backgroundColor').value,
      textColor: field(form, 'textColor').value,
      size: field(form, 'size').value,
      radius: field(form, 'radius').value,
      alignment: field(form, 'alignment').value,
      newTab: field(form, 'newTab').checked
    };

    if (!data.text) {
      setError('Escribe el texto que mostrará el botón.');
      field(form, 'text').focus();
      return;
    }

    if (!data.url) {
      setError('Escribe un enlace válido, por ejemplo https://sitio.com o /contacto.');
      field(form, 'url').focus();
      return;
    }

    editor.undoManager.transact(function () {
      var attributes = buttonAttributes(data);
      var currentButton = modalState.button;

      if (currentButton && currentButton.isConnected) {
        var holder = editor.getDoc().createElement('div');
        holder.innerHTML = '<a ' + attributes + '>' + escapeHtml(data.text) + '</a>';
        var updatedButton = holder.firstElementChild;
        var row = editor.dom.getParent(currentButton, '.ca-button-row');

        currentButton.parentNode.replaceChild(updatedButton, currentButton);
        if (row) {
          editor.dom.setStyle(row, 'text-align', data.alignment);
        }
        editor.selection.select(updatedButton);
      } else {
        if (modalState.bookmark) {
          editor.selection.moveToBookmark(modalState.bookmark);
        }
        editor.insertContent(
          '<p class="ca-button-row" style="text-align:' + escapeHtml(data.alignment) + '">'
          + '<a ' + attributes + '>' + escapeHtml(data.text) + '</a></p><p>&nbsp;</p>'
        );
      }
    });

    editor.nodeChanged();
    closeModal();
  }

  function createModal() {
    var modal = document.getElementById('ca-button-modal');
    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'ca-button-modal';
    modal.className = 'ca-button-modal';
    modal.hidden = true;
    modal.innerHTML = [
      '<div class="ca-button-modal__backdrop" data-ca-close></div>',
      '<section class="ca-button-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="ca-button-modal-title">',
        '<header class="ca-button-modal__header">',
          '<h2 id="ca-button-modal-title">Insertar botón</h2>',
          '<button type="button" class="ca-button-modal__close" data-ca-close aria-label="Cerrar">&times;</button>',
        '</header>',
        '<form id="ca-button-form" class="ca-button-modal__form">',
          '<div class="ca-button-modal__body">',
            '<label>Texto del botón<input type="text" name="text" maxlength="120" required></label>',
            '<label>Enlace<input type="text" name="url" placeholder="https://sitio.com o /contacto" required></label>',
            '<div class="ca-button-modal__grid">',
              '<label>Color de fondo<input type="color" name="backgroundColor"></label>',
              '<label>Color del texto<input type="color" name="textColor"></label>',
              '<label>Tamaño<select name="size"><option value="small">Pequeño</option><option value="medium">Mediano</option><option value="large">Grande</option></select></label>',
              '<label>Esquinas<select name="radius"><option value="square">Cuadradas</option><option value="soft">Suaves</option><option value="rounded">Redondeadas</option><option value="pill">Píldora</option></select></label>',
              '<label>Alineación<select name="alignment"><option value="left">Izquierda</option><option value="center">Centro</option><option value="right">Derecha</option></select></label>',
            '</div>',
            '<label class="ca-button-modal__checkbox"><input type="checkbox" name="newTab"> Abrir en una pestaña nueva</label>',
            '<p id="ca-button-modal-error" class="ca-button-modal__error" role="alert" hidden></p>',
          '</div>',
          '<footer class="ca-button-modal__footer">',
            '<button type="button" class="ca-button-modal__cancel" data-ca-close>Cancelar</button>',
            '<button type="submit" class="ca-button-modal__submit">Insertar botón</button>',
          '</footer>',
        '</form>',
      '</section>'
    ].join('');

    document.body.appendChild(modal);
    modal.querySelector('#ca-button-form').addEventListener('submit', submitButton);
    modal.querySelectorAll('[data-ca-close]').forEach(function (control) {
      control.addEventListener('click', closeModal);
    });
    modal.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        event.preventDefault();
        closeModal();
      }
    });

    return modal;
  }

  function openModal(editor, requestedButton) {
    var modal = createModal();
    var button = requestedButton || closestButton(editor);
    var data = readButtonData(editor, button);
    var form = modal.querySelector('#ca-button-form');

    modalState.editor = editor;
    modalState.button = button;
    modalState.bookmark = button ? null : editor.selection.getBookmark(2, true);
    modalState.previousOverflow = document.body.style.overflow;

    field(form, 'text').value = data.text;
    field(form, 'url').value = data.url;
    field(form, 'backgroundColor').value = data.backgroundColor;
    field(form, 'textColor').value = data.textColor;
    field(form, 'size').value = data.size;
    field(form, 'radius').value = data.radius;
    field(form, 'alignment').value = data.alignment;
    field(form, 'newTab').checked = data.newTab;

    modal.querySelector('#ca-button-modal-title').textContent = button ? 'Editar botón' : 'Insertar botón';
    modal.querySelector('.ca-button-modal__submit').textContent = button ? 'Guardar cambios' : 'Insertar botón';
    setError('');
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
    window.setTimeout(function () { field(form, 'text').focus(); }, 0);
  }

  window.setupBlogButtonTool = function (editor) {
    editor.ui.registry.addButton('botonpost', {
      text: 'Botón',
      tooltip: 'Insertar o editar botón',
      onAction: function () { openModal(editor); }
    });

    editor.on('dblclick', function (event) {
      var button = closestButton(editor, event.target);
      if (button) {
        event.preventDefault();
        openModal(editor, button);
      }
    });
  };
}());
