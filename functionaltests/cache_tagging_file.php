<?php
/*Copyright 2011 Imre Toth <tothimre at gmail>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

use POC\Poc;
use POC\handlers\ServerOutput;
use POC\cache\PocCache;
use POC\cache\cacheimplementation\FileCache;

use POC\cache\filtering\Hasher;
use POC\cache\filtering\Filter;
  
use POC\cache\header\HeaderManipulator;
  
use POC\cache\tagging\driver\mySQL\CacheTable;
use POC\cache\tagging\MysqlTagging;

use POC\cache\filtering\OutputFilter;
  
include ("../framework/autoload.php");

$hasher = new Hasher();
$filter = new Filter();
$hasher->addDistinguishVariable($_GET);

$cache = new FileCache($hasher, $filter, 5, new MysqlTagging);

if(isset($_GET)){
  if(isset($_GET['delcache'])){
    if($_GET['delcache']){
        $cache->addCacheInvalidationTags(true,'user');
    }
  }
}

$cache->addCacheAddTags(true,"user,customer");

//$cache->addCacheAddTags(true,"Karacsonyfa,Mezesmadzag,csicsa");
$poc  = new Poc($cache, new ServerOutput(), new HeaderManipulator(), new OutputFilter(), true);
$poc->start();
//$poc->addCacheInvalidationTags($_GET,"Mezesmadzag,csicsa");
//print_r($sqlite3Tagging->addCacheToTags('zizi,yuyu,aa,bb,ggg,fufu,fufufu,dict,sztaki,hu,dsaj,adsf,sdaf,adsf,asdf,sadf,dafgfdsg,ghrt,qw,we,er,rt,ty,yu,uii,io,as,sd,df,fg,gh,hj,jk,kl,zx,xc,v,cb,vn,bm,fh,df,sd,ad,qe,wr,e,t,ry,ru,,ueu,i,dj,sd,ssdf,sdf,sd,fsd,f,sdf,sd,f,sdf,sd,f,dfg,rewt,yu,ghj,sdfg,bv,gfh,rew,tq,etr,hdsg,hjsj,wu,djdj,sh,wy,ry,hfh,fh,d,gd,g,dgssdfg,sdf,g,ty,t,yhf,ghb,cvhgf,hg,fh,gfj,gfh,sdfg,dfhb,gfn,v,bnb,n,sfh,y,hh,oyoy,pdpdp,zlzl,al,bbbb,wweewe,rtrtrt,tytyty,yuyu,zxzxzx,xcxcxc,cvcvcv,vbvbvb,bnbn,ghghgh,fgfgfg,dfdfsfd,1,2,3,4,5,6,7,8,9,01'));
include('lib/text_generator.php');
