# Snuze

**Snuze** is a PHP client interface for Reddit's API.

Snuze is designed to help you, a PHP developer, quickly build bots or other
applications that interact with the Reddit service, without having to learn
much about how Reddit's API works. Some of Snuze's key features include:

* Retrieve data about subreddits, submissions, and users (interactive [features](https://snuze.shaunc.com/#feature-roadmap) are on the way)
* Entity classes for working with Reddit "thing" objects: `Subreddit`, `Link`, `Account`, etc.
* Supports OAuth authentication for Reddit "script application" apps
* Automatic tracking of Reddit's API rate limit state; can auto pause as needed
* Includes a MySQL persistence layer to optionally store what you fetch

Snuze supports Reddit's "script application" app type. This means you need to
have a Reddit account and register a "script" app in its preferences.

This is a *preview release* of Snuze. The current version supports some common
read-only actions while the overall design is being stabilized. You can use this
release to spider Reddit data for analysis, scan your favorite subreddits
for new links, or other tasks that don't involve posting/submitting data.

### Requirements

Snuze has the following requirements:

* The [PHP](https://www.php.net/downloads.php) 7.2 or newer, capable of running from the command line
* [Composer](https://getcomposer.org/download/) to handle installation and create the autoloader
* A Reddit account

While not absolutely necessary, it's also useful to have:

* PHP's `pdo_sqlite` extension, version 3.20.1 or newer; this is enabled by default in modern PHP installs
* PHP's `pdo_mysqli` extension, *only if* you want to use the MySQL storage provider

### Documentation

Please see the external [Snuze documentation](https://snuze.shaunc.com/) for
information on how to install, configure, and use Snuze. There's simply too much
to include in one README.

### Getting Help

If you find a bug in Snuze, the best way to report it is to open a
[new issue on GitHub](https://github.com/snuze/snuze/issues) with a
description of what happened. If Snuze threw an exception, please include it
in your report, and if the bug can be reproduced, try to add a snippet of code
that will trigger it.

For general questions, suggestions, or other discussions, check out
[/r/snuze](https://reddit.com/r/snuze/) on Reddit.
