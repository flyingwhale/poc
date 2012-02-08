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

As the projet is psr-0 compilant it is really easy to map it to your project. To download the dependencies please run the "./bin/get_composer" file from the root of the project or dowload the composer.phar for yourself and run the "composer.phar install"


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

### Copyright [2012] [Imre Toth <tothimre at gmail>] ###

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License
