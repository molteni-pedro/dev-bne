(function ($, Drupal) {
  Drupal.behaviors.customBehavior = {
    attach: function (context, settings) {
      var idsToStyle = [68, 90, 131, 114, 102, 75, 64, 159, 82];

      idsToStyle.forEach(function(id) {
        var optionElement = $('option[value="' + id + '"]', context);
        optionElement.css({
          'font-weight': 'bold',
          'font-size': '20px'
        });
        optionElement.attr('disabled', 'disabled');
      });
    }
  };
})(jQuery, Drupal);
