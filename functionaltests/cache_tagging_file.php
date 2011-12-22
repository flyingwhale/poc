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
  use POC\cache\filtering\Evaluateable;
  use POC\Poc;

  include ("../framework/autoload.php");

  $sqliteTagging = new SqliteTagging();
  $eval = new Evaluateable('#.*#',$_SERVER["REQUEST_URI"], Evaluateable::OP_PREGMATCH);
  $eval->addCacheTags(true,'user,customer');
  $eval->addCacheTags(true,'invetntory,article');
  if(isset($_GET)){
    if(isset($_GET['delcache'])){
      if($_GET['delcache']){
        $eval->addCacheInvalidationTags(true,'user,customer');
        $eval->addCacheInvalidationTags(true,'invetntory,article');
      }
    }
  }

  $cache = new FileCache($eval, 5);

  //$apcCache->addCacheAddTags(true,"Karacsonyfa,Mezesmadzag,csicsa");

  $pobCache = new PocCache($cache);

  //$cache->addCacheAddTags(true,"Karacsonyfa,Mezesmadzag,csicsa");

  $pob  = new Poc($pobCache, new ServerOutput(),  true);

  //$pob->addCacheInvalidationTags($_GET,"Mezesmadzag,csicsa");
  //print_r($sqlite3Tagging->addCacheToTags('zizi,yuyu,aa,bb,ggg,fufu,fufufu,dict,sztaki,hu,dsaj,adsf,sdaf,adsf,asdf,sadf,dafgfdsg,ghrt,qw,we,er,rt,ty,yu,uii,io,as,sd,df,fg,gh,hj,jk,kl,zx,xc,v,cb,vn,bm,fh,df,sd,ad,qe,wr,e,t,ry,ru,,ueu,i,dj,sd,ssdf,sdf,sd,fsd,f,sdf,sd,f,sdf,sd,f,dfg,rewt,yu,ghj,sdfg,bv,gfh,rew,tq,etr,hdsg,hjsj,wu,djdj,sh,wy,ry,hfh,fh,d,gd,g,dgssdfg,sdf,g,ty,t,yhf,ghb,cvhgf,hg,fh,gfj,gfh,sdfg,dfhb,gfn,v,bnb,n,sfh,y,hh,oyoy,pdpdp,zlzl,al,bbbb,wweewe,rtrtrt,tytyty,yuyu,zxzxzx,xcxcxc,cvcvcv,vbvbvb,bnbn,ghghgh,fgfgfg,dfdfsfd,1,2,3,4,5,6,7,8,9,01'));

  include('lib/text_generator.php');
