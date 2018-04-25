(function ($) {
  Drupal.behaviors.initialjs = {
    attach: function (context) {
      $('.initial-image').initial();
    }
  };
})(jQuery);
