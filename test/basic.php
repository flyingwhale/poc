<?php
  apc_clear_cache();
  include ("../framework/src/autoload.php");
  
  $url  = new Url('/dev/pob/test/basic.php');
  
  $pob  = new Pob(new ApcCache($url),15);

  //$cond = new Condition();
  //$cond->set($url);
  
  
  echo ("AAAAAAAAAAAAAAAAAAAAAAABBBB"); ?>
  sdaf sda
  sda
  f 
  sdaf 
  sd
  f sad
  f 
  sd f
  
  BBBBBBBBBBBBBBBBBBBBBBBBBBB
  <?php
  
  echo "cucc"; 
  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();
  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();  phpinfo();

  
