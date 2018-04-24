(function ($) {
  Drupal.behaviors.timeago = {
    attach: function (context) {
      $('abbr.timeago, span.timeago, time.timeago', context).timeago();
    }
  };
})(jQuery);
