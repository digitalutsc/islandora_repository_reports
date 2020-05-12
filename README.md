# Islandora Repository Reports

## Introduction

A Drupal 8 module that provides a of graphical reports on various aspects of and Islandora repository. Reports included in this modul are:

* Number of nodes, grouped by content type
* Number of nodes, grouped by Islandora model
* Number of nodes, grouped by genre
* Number of nodes, grouped created by month
* Number of media, grouped by MIME type

Submodules are included that add a report of media by [PRONOM PUID](https://en.wikipedia.org/wiki/PRONOM) (if Islandora FITS is installed), and three sample reports, "Flavors" and one each for generating random pie and bar chart data.

## Overview

Users with "View Repository Reports" permission can visit the reports page from Drupal's Reports list, or, if they don't have permission to view the Reporst list, they can link to it directly at `admin/reports/islandora_repository_reports`. Selecting one of the available reports, and then clicking on the "Go" button, will produce a chart, like this one for MIME type:

![MIME type report](docs/images/islandora_repo_reports.png)

Checking the "Generate a CSV file of this data" box before clicking on the "Go" button will provide a link to download the CSV file.

## Configuration

There is only one global configuration option, whether or not to cache report data. If you select this option, you should periodically pregenerate report data as described below.

Users will need to have "View Repository Reports" permission to view the report page.
	
## Pregenerating report data

This module comes with a set of Drush commands that generates the data used in the reports and caches it:

1. To list the enabled services that generate report data: `drush islandora_repository_reports:list_report_types`
1. To pregenerate the data for the 'model' report: `drush islandora_repository_reports:build_cache model`
1. To delete the data for the 'mimetype' report: `islandora_repository_reports:delete_cache mimetype`

## Requirements

* [Islandora 8](https://github.com/Islandora/islandora)
* [Islandora FITS](https://github.com/roblib/islandora_fits) is required if you want to generate the PUID report.

## Installation

1. Clone this repo into your Islandora's `drupal/web/modules/contrib` directory.
1. Enable the module either under the "Admin > Extend" menu or by running `drush en -y islandora_repository_reports`.

## Writing custom data source plugins

Data for most report are taken from Drupal's database, but data source plugins can get their data from Solr, Blazegraph, or even a source external to Islandora.

The `modules` subdirectory contains two sample data source plugin. The minimum requirements for a data source plugin are:

1. a .info.yml file
1. a .services.yml file
   * Within the .services.yml file, the service ID must be in the form `islandora_repository_reports.datasource.xxx`, where `xxx` is specific to the plugin. This pattern ensures that the plugin will show up in the list of media formats reports in the select list in the reports form.
1. a plugin class file that implements the `MediaFormatsReportsDataSourceInterface` interface.
   * The plugin's `getData()` method needs to return an associative array containing formatname => count members.
1. Optionally, a .module file containing any standard Drupal hook implementations. For example, data source plugins can add form elements to the report selector form. See the comments in the random data source plugin's .module file for more information.

## Current maintainer

* [Mark Jordan](https://github.com/mjordan)

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
