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

        // Construir la URL utilizando Drupal.url() para la solicitud AJAX.
        var ajaxUrl = Drupal.url('bne_resources/ajax/update_taxonomy/' + selectedTIDs.join(','));

        // Mostrar un mensaje de depuración con los TIDs seleccionados.
        console.log('TIDs seleccionados: ' + selectedTIDs.join(', ')); // Usar console.log en lugar de alert para depuración.

        // Realizar una petición AJAX.
        if (selectedTIDs.length > 0) {
          // Crear una nueva instancia de Drupal.ajax.
          var ajaxSettings = {
            url: ajaxUrl,
            // Ajusta las opciones de la solicitud según tus necesidades.
          };
          var ajaxObject = new Drupal.ajax(false, $('#edit-field-subject-resources', context)[0], ajaxSettings);
          ajaxObject.eventResponse($(this), context, ajaxSettings);
        }

        // Mostrar los TID seleccionados en el campo de "markup."
        $('#custom-selected-terms', context).html(selectedTIDs.join(', '));
      });
    }
  };
})(jQuery);
