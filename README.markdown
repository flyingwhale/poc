# POC

[![Build Status](https://secure.travis-ci.org/php-output-cache/poc.png?branch=master)](http://travis-ci.org/php-output-cache/poc)

This is the root directory of the
POC that stands for PHP Output Caching.

The website is:
    POC: http://www.phpcache.org

## Description

The aim of this project is to create an easy to use but really flexible generic
output caching component for  PHP applications. The framework is plugin based,
so it is really easy to extend and use.

## Features

 * Caching of the output on certain circumstances that you define
 * Cache invalidation by TTL (of course)
 * Blacklisting caches by application state.

 * For caching it utilizes many interface, those are:
   * Memcached
   * Redis
   * MongoDb
   * It's own filesystem based engine.

 * Plugins:
   * Blacklisting by Output content (plugin)
   * Html output minification (plugin)
   * Logging with monolog (plugin)

   * Cache tagging
     * For this feature we utilize Doctrine2, Mysql and Sqlite is supported at the
       moment
     * Cache Invalidation by tags
     * Minimal overhead on the performance
     * Easy (one line) to turn off/on
     * Controls headers

Even more features are coming, so stay tuned.

## Examples ##
You can download/see the https://github.com/php-output-cache/poc-sandbox project and
install to your web directory (composer is your friend there as well:) ).

## Installation ##

The project uses the composer to download it's dependencies and we already added
a script what you can execute by typing ./bin/get_composer scrip. It downloads
the composer.

As the projet is psr-0 compliant it is really easy to map it to your project.
To download the dependencies please run the "./bin/get_composer" file from the
root of the project or download the composer.phar for yourself.
After run "bin/composer.phar install" script.

If you want to run unittests or want to improve or extend the framework then
use the following command:
"bin/composer.phar install --dev"

### Key-Value databases

Of course you are not forced to use any external key-value databases,
because it also supports the file system caching. But to make your application
more scalable you can user some more scalable solutions.

For instance You can install the redis-server, also  mongod (Mongodb) or
of course Memcached server to your environment and the PHP module as well.

### SQL ###

At the moment we only support Mysql, for the tagging feature, but as we
implemented it in Doctrine2 this is really likely that we will add more sql DBs
To this list in the close future.
So Install Mysql for the tagging feature.

##Unittesting##

The Project already have got 75% code coverage. Of course we work hard on to
improve it and make it better. Also the unittests can be nice source of examples.
For instance this is highly recommended to check out the PocTest.php file to get
more insight on the POC.

### Shortcuts ###

The project also contains a recent version of the PHPUnit framework. So you
don't need to prepare your environment to be suitable for this. By executing the
vendor/bin/phpunit file you can run the tests.

### Configuration ###

All PHPUnit configuration data can be found at the phpunit.xml.dist file.
The Mysql Database specific information also resides here for the tests If you
want to specify your own database. just copy this file to the phpunit.xml
and modify the database specific parts.


## COPYRIGHT ##

### Copyright [2013] [Imre Toth <tothimre at gmail>] ###

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License

