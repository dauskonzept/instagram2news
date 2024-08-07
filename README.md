[![StandWithUkraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)
[![TYPO3 12](https://img.shields.io/badge/TYPO3-12-orange.svg)](https://get.typo3.org/version/12)

TYPO3 Extension "instagram2news"
=================================

## What does it do?

Imports instagram posts via the official Instagram API
as [EXT:news](https://github.com/georgringer/news)
"News" entities.

**Summary of features**

* Integrates with [EXT:news](https://github.com/georgringer/news) to import
  instagram posts as News entities
* Provides command to regularly import new/update already imported posts
* Adds a new subtype for EXT:news: "Instagram"

## Installation
The recommended way to install the extension is by using [Composer](https://getcomposer.org/). In your Composer based TYPO3 project root, just do:
<pre>composer require dskzpt/instagram2news</pre>

## Setup
1. todo

__Recommended__:

* Add a cronjob/scheduler task to import the posts on a regular basis

## Compatibility
| Version | TYPO3       | PHP                  | Support/Development                  |
|---------|-------------|----------------------|--------------------------------------|
| 1.x     | 12.4        | >= 8.1.0 <= 8.3.99 ️ | Features, Bugfixes, Security Updates |
| 0.x     | 10.4 - 11.5 | 7.4 - 8.0️           | Bugfixes                             |

## Funtionalities

### Automatic import of posts
This extension comes with a command to import (new) posts of a given instagram
user.
It is recommended to set this command up to run regularly - e.g. once a day.

<pre>instagram2news:import-posts {username} {storagePid} [limit|25]</pre>

__Arguments:__

| Name       | Description                                                          |
|------------|----------------------------------------------------------------------|
| username   | The instagram username to import posts for                           |
| storagePid | The PID to save the imported posts                                   |
| limit      | The maximum number of latest posts to import (Optional. Default: 25) |

## Extending

### Local path to save downloaded files
By default all images/videos in imported posts are saved in <code>/public/fileadmin/instagram2news</code>
You can change this path via the Extensions settings <code>local_file_storage_path</code> option.

## Contributing
Please refer to the [contributing](CONTRIBUTING.md) document included in this repository.

## Testing
This Extension comes with a testsuite for coding styles and unit/functional
tests.
To run the tests simply use the provided composer script:

<pre>composer ci:test</pre>
