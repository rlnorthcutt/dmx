(function ($, Drupal) {
    Drupal.behaviors.dmxProjectTaskList = {
        attach: function (context, settings) {
            $('.view-dmx-project-task input.form-checkbox', context).once('taskStatus').each(function () {
                if ($(this).attr('checked')) {
                    $(this).parents('tr').addClass('task-status-complete');
                }
            });
        }
    };
})(jQuery, Drupal);