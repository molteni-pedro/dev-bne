(function($) {
  // Argument passed from InvokeCommand.
  $.fn.resourcesUpdateData = function(arguments) {
    console.log('resourcesUpdateData is called.');
    // Set textfield's value to the passed arguments.
    $('input#edit-title-0-value').attr('value', arguments['title']);
    $('input#edit-field-url-img-resources-0-uri').attr('value', arguments['small_image_id']);
    $('input#edit-field-url-img-resources-h-0-uri').attr('value', arguments['big_image_id']);
    $('img#small_image').attr('src', arguments['small_image_id']);
    $('img#big_image').attr('src', arguments['big_image_id']);
    $('input#edit-field-url-resources-0-uri').attr('value', arguments['viewer_url']);
  };
})(jQuery);
