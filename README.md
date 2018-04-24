# DMX

DMX is a suite of modules designed to enhance the Drupal marketer experience. Each module is centered on fulfilling common marketer user stories, for example:

> As a marketer, I would like to see all the content and assets related to a project gathered in a single place so that I can effectively manage the project.

## Installation

DMX uses the [jQuery Timeago](http://timeago.yarp.com/) plugin to create dynamically updating "time ago" dates. To get the library, run composer update.

DMX requires a [patch to Date Range Formatter](https://www.drupal.org/files/issues/2018-04-17/2961280-date_range_formatter-optional_end_date.patch) to support optional end dates. This will need to be applied manually.

## Compatibility

DMX has only been tested with Drupal 8.5 and [Material Admin](https://www.drupal.org/project/material_admin) administration theme.
