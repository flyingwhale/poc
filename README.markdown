# POC
This is the root directory of the
POC that stands for PHP Output Caching.


## Description

The aim of this project is to create an easy to use generic output caching component for  PHP applications.

## Features
 * Caching of the output on certain circumstances that you define
 * Cache invalidation by TTL (of course)
 * Blacklisting / cache invalidation by application state
 * Blacklisting by Output content
 * For caching it utilizes many interface, those are:
   * Memcached
   * Redis
   * MongoDb
   * It's own filesystem based engine.
 * APC (experimental, performs and works well on a webserver, but unfortunately the cli interface is not behaves like it should and cannot be unit tested properly so I don't include it in the master)
 * Cache tagging
    * For this feature we utilize MySQL, but more are coming
    * Cache Invalidation by tags
    * Minimal overhead on the performance
    * Easy (one line) to turn off/on
    * Controls headers

Even more features are coming, so stay tuned.

## INSTALLATION

Just copy the framework library anywhere where it is reachable from your project and include the autoload.php file from it, just take a look to the functionaltests folder you can see how the basic usage of the framework goes.

## The directory structure you see:

* ./bin: At the moment contains a small script that generates the autoload file for the prject.
* ./framework: Here it is. Essentially you need the src in it folder if you are a user the tests folder is for unittests.

* ./functionaltests: this directory dontains some php files that can persent the basic functionality of the framework with the cache engines implemented.


## DEVELOPMENT

If you want to develop the project you should make shoure that the current tests are running with your changeset, and you have added more tests if it is a new feature.

PHP unit:
sudo pear channel-discover pear.phpunit.de
sudo pear install pear.phpunit.de/PHPUnit

/*
sudo pear upgrade PEAR
pear update-channels
pear config-set auto_discover 1
*/

More info:
http://pear.phpunit.de/


PHP AutoloadBuilder CLI
sudo pear channel-discover pear.netpirates.net
sudo pear channel-discover components.ez.no
sudo pear install theseer/Autoload

test:
phpab -v

More info:
https://github.com/theseer/Autoload


Install redis:

Simple on Ubuntu / Debian:
aptitude install redis-server

more info:
http://kevin.vanzonneveld.net/techblog/article/redis_in_php/


Install rediska:
sudo pear channel-discover pear.geometria-lab.net
sudo pear install geometria-lab/Rediska-beta

Install Mongod server to the localchost and the PHP module as well

Install Mysql (for the tagging)

After this you can run the unittests,

Also the functionaltests directory can help you by inspecting the behaviour of the framework in a server environment.

## COPYRIGHT ##

### Copyright [2011] [Imre Toth <tothimre at gmail>] ###

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License
