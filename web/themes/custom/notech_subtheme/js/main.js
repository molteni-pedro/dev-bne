(function ($) {
    $(document).ready(function () {
      // Manejar el cambio en el select
      $('#combine-authors-level-select').change(function () {
        var selectedValue = $(this).val();
  
        // Ocultar todos los campos de entrada primero
        $('.filtro-input').hide();
  
        // Mostrar el campo de entrada correspondiente
        $('#' + selectedValue + '-input').show();
      });
    });
  })(jQuery);
  