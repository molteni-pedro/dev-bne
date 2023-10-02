(function ($) {
  Drupal.behaviors.myCustomCHSBehavior = {
    attach: function (context, settings) {
      // Escucha cambios en el campo CHS con el evento 'cschierarchyupdate'.
      $('#field-resource-level', context).once('my-custom-chs-behavior').on('cschierarchyupdate', function () {
        // Tu código JavaScript personalizado aquí.
        // Por ejemplo, muestra un mensaje de alerta.
        alert('Seleccionaste un valor en el campo CHS');
      });
    }
  };
})(jQuery);
