(function ($, Drupal) {
  // Argument passed from InvokeCommand.
  $.fn.resourcesUpdateData = function(arguments) {
    // Set textfield's value to the passed arguments.
    $('input#edit-title-0-value').val(arguments['title']);
    $('input#edit-field-url-img-resources-0-uri').attr('value', arguments['small_image_id']);
    $('input#edit-field-url-img-resources-h-0-uri').attr('value', arguments['big_image_id']);
    $('img#small_image').attr('src', arguments['small_image_id']);
    $('img#big_image').attr('src', arguments['big_image_id']);
    $('input#edit-field-url-resources-0-uri').attr('value', arguments['viewer_url']);
    $('input#edit-field-physical-description-0-value').attr('value', arguments['physical_description']);
    $('input#edit-field-publication-1-year-0-value').attr('value', arguments['date_1']);
    $('input#edit-field-publication-2-year-0-value').attr('value', arguments['date_2']);

    // Verificar si el campo "body" tiene un editor CKEditor.
    const bodyTextarea = document.querySelector('textarea[name="body[0][value]"]');
    if (!bodyTextarea || !bodyTextarea.getAttribute('data-ckeditor5-id')) {
      // El campo "body" no tiene un editor asociado, llenarlo con el contenido.
      const scrappedData = arguments['description_notes'];
      if (bodyTextarea) {
        bodyTextarea.value = scrappedData;
      }
    }

    const editorContainer = document.querySelector('#edit-body-wrapper .ck-editor__editable');
    if (editorContainer) {
      let editorInstance = editorContainer.ckeditorInstance
      if (editorInstance && editorInstance.model && editorInstance.model.document) {
        editorInstance.setData(arguments['description_notes']);
      }
    }

  };

})(jQuery, Drupal);


(function ($, Drupal, CKEditorInspector) {
  Drupal.behaviors.mymoduleUpdateCKEditor = {
    attach: function (context, settings) {
      // Función para actualizar CKEditor 5 con el contenido HTML.
      function updateCKEditorContent(content) {
        const editorContainer = document.querySelector('.ck-editor__main'); // Selector para el contenedor del editor CKEditor 5
        if (editorContainer && CKEditorInspector.getFromElement) {
          const editorInstance = CKEditorInspector.getFromElement(editorContainer);
          if (editorInstance && editorInstance.model && editorInstance.model.document) {
            const document = editorInstance.model.document;
            document.setData(content);
          }
        }
      }

      // Verificar si el campo "body" tiene un editor CKEditor.
      const bodyTextarea = document.querySelector('textarea[name="body[0][value]"]');
      if (!bodyTextarea || !bodyTextarea.getAttribute('data-ckeditor5-id')) {
        // El campo "body" no tiene un editor asociado, llenarlo con el contenido.
        const scrapedData = '<p>Contenido de scrapping por AJAX</p>';
        if (bodyTextarea) {
          bodyTextarea.value = scrapedData;
        }
      }

      // Escuchar el evento personalizado para actualizar CKEditor.
      $(document).on('mymoduleUpdateCKEditorEvent', function (event, content) {
        updateCKEditorContent(content);
      });
    }
  };
})(jQuery, Drupal, CKEditorInspector);
