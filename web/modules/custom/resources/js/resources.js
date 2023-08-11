(function ($, Drupal) {
  // Argument passed from InvokeCommand.
  $.fn.resourcesUpdateData = function(arguments) {
    console.log('resourcesUpdateData is called.');
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
  };

  Drupal.behaviors.resourcesUpdateCKEditor = {
    attach: function (context, settings) {
      // Función para actualizar CKEditor con el contenido HTML.
      window.updateCKEditorContent = function (content) {
        const editorElement = document.querySelector('[data-ckeditor-editable]');
        console.log(editorElement);
        if (editorElement && editorElement.editor) {
          editorElement.editor.setData(content);
        }
      };
    }
  };

})(jQuery, Drupal);
