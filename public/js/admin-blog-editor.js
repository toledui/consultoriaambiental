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

  function closestButton(editor) {
    return editor.dom.getParent(editor.selection.getNode(), 'a.ca-editor-button');
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

  function validUrl(value) {
    return /^(https?:\/\/|mailto:|tel:|\/|\.\/|\.\.\/|#)/i.test(value);
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
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

  function openDialog(editor) {
    var currentButton = closestButton(editor);
    var initialData = readButtonData(editor, currentButton);

    editor.windowManager.open({
      title: currentButton ? 'Editar botón' : 'Insertar botón',
      size: 'normal',
      body: {
        type: 'panel',
        items: [
          { type: 'input', name: 'text', label: 'Texto del botón' },
          { type: 'urlinput', name: 'url', label: 'Enlace' },
          { type: 'colorinput', name: 'backgroundColor', label: 'Color de fondo' },
          { type: 'colorinput', name: 'textColor', label: 'Color del texto' },
          {
            type: 'selectbox', name: 'size', label: 'Tamaño', items: [
              { text: 'Pequeño', value: 'small' },
              { text: 'Mediano', value: 'medium' },
              { text: 'Grande', value: 'large' }
            ]
          },
          {
            type: 'selectbox', name: 'radius', label: 'Esquinas', items: [
              { text: 'Cuadradas', value: 'square' },
              { text: 'Suaves', value: 'soft' },
              { text: 'Redondeadas', value: 'rounded' },
              { text: 'Píldora', value: 'pill' }
            ]
          },
          {
            type: 'selectbox', name: 'alignment', label: 'Alineación', items: [
              { text: 'Izquierda', value: 'left' },
              { text: 'Centro', value: 'center' },
              { text: 'Derecha', value: 'right' }
            ]
          },
          { type: 'checkbox', name: 'newTab', label: 'Abrir en una pestaña nueva' }
        ]
      },
      initialData: initialData,
      buttons: [
        { type: 'cancel', text: 'Cancelar' },
        { type: 'submit', text: currentButton ? 'Guardar cambios' : 'Insertar', primary: true }
      ],
      onSubmit: function (api) {
        var data = api.getData();
        data.text = String(data.text || '').trim();
        data.url = String(data.url || '').trim();

        if (!data.text) {
          editor.notificationManager.open({ text: 'Escribe el texto del botón.', type: 'error' });
          return;
        }

        if (!data.url || !validUrl(data.url)) {
          editor.notificationManager.open({
            text: 'Usa un enlace válido que comience con https://, /, #, mailto: o tel:.',
            type: 'error'
          });
          return;
        }

        editor.undoManager.transact(function () {
          var attributes = buttonAttributes(data);

          if (currentButton && currentButton.isConnected) {
            var holder = editor.dom.create('div', {}, '<a ' + attributes + '>' + escapeHtml(data.text) + '</a>');
            var updatedButton = holder.firstChild;
            var row = editor.dom.getParent(currentButton, '.ca-button-row');

            currentButton.replaceWith(updatedButton);
            if (row) {
              editor.dom.setStyle(row, 'text-align', data.alignment);
            } else {
              editor.dom.setAttrib(updatedButton, 'data-ca-align', data.alignment);
            }
            editor.selection.select(updatedButton);
          } else {
            editor.insertContent(
              '<p class="ca-button-row" style="text-align:' + escapeHtml(data.alignment) + '">'
              + '<a ' + attributes + '>' + escapeHtml(data.text) + '</a></p><p>&nbsp;</p>'
            );
          }
        });

        editor.nodeChanged();
        api.close();
      }
    });
  }

  window.setupBlogButtonTool = function (editor) {
    editor.ui.registry.addToggleButton('botonpost', {
      text: 'Botón',
      tooltip: 'Insertar o editar botón',
      onAction: function () { openDialog(editor); },
      onSetup: function (api) {
        function updateState() {
          api.setActive(Boolean(closestButton(editor)));
        }
        editor.on('NodeChange', updateState);
        return function () { editor.off('NodeChange', updateState); };
      }
    });

    editor.ui.registry.addMenuItem('botonpost', {
      text: 'Botón de llamada a la acción',
      onAction: function () { openDialog(editor); }
    });
  };
}());
