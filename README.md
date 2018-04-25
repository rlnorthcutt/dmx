# DMX

DMX is a suite of modules designed to enhance the Drupal marketer experience. Each module is centered on fulfilling common marketer user stories, for example:

> As a marketer, I would like to see all the tasks, content and assets related to a project gathered in a single place so that I can effectively manage the project.

![DMX Project overview](http://johnmoney.github.io/files/projects/dmx/dmx-project-overview.gif)


### Installation

DMX uses a number of external libraries which can be installed with [Composer](https://getcomposer.org/):

```bash
$ composer update
```

DMX requires a [patch to Date Range Formatter](https://www.drupal.org/files/issues/2018-04-17/2961280-date_range_formatter-optional_end_date.patch) to support optional end dates. This will need to be applied manually.


### Compatibility

DMX has only been tested with Drupal 8.4 and [Material Admin](https://www.drupal.org/project/material_admin) administration theme.


### Components

####DMX Core####
Provides core functionality for DMX.

__Drupal modules__
- config_rewrite
- lightning_media
- twig_tweak

__External libraries__
- [jQuery Timeago](http://timeago.yarp.com/) plugin to create dynamically updating "time ago" dates
- [jQuery initial.js](http://judelicio.us/initial.js/) plugin to create team member images without a profile picture

####DMX Team####
Enables marketing team to communicate and collaborate.

__Drupal modules__
- image
- lightning_core
- message
- user

####DMX Project####
Enables tasks, content, assets and links to be related for the planning and launch of a project. Depends on DMX Core and DMX Team.

__Drupal modules__
- datetime
- datetime_range
- date_range_formatter
- entity_browser
- entity_reference
- inline_entity_form
- lightning_media
- link
- node
- optional_end_date
- options
