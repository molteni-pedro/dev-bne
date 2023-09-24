(function ($) {
  Drupal.behaviors.bneResources = {
    attach: function (context, settings) {
      // Detectar cambios en el campo field_subject_resources.
      $('#edit-field-subject-resources', context).once('bneResources').on('change', function () {
        // Obtener los valores seleccionados de los términos (TID) de las taxonomías.
        var selectedTIDs = [];
        $('#edit-field-subject-resources option:selected').each(function () {
          if ($(this).val() !== '_none') {
            selectedTIDs.push($(this).val());
          }
        });

        // Realizar una petición AJAX para pasar los TID seleccionados a PHP.
        if (selectedTIDs.length > 0) {
          Drupal.ajax({ url: 'bne_resources/update_taxonomy/' + selectedTIDs.join(',') }).execute();
        }

        // Mostrar los TID seleccionados en el campo de "markup."
        $('#custom-selected-terms', context).html(selectedTIDs.join(', '));
      });
    }
  };
})(jQuery);
