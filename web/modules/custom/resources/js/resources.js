(function($) {
  // Argument passed from InvokeCommand.
  $.fn.resourcesUpdateData = function(arguments) {
    console.log('resourcesUpdateData is called.');
    // Set textfield's value to the passed arguments.
    $('input#edit-title-0-value').attr('value', arguments['title']);
  };
})(jQuery);
