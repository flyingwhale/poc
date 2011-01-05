<?php
  apc_clear_cache();
  include ("../framework/src/autoload.php");
  
  $url  = new Url('/dev/pob/test/basic2.php');
  
  $pob  = new Pob(new FileCache($url,'/tmp/'),100000);

  
?>

dsfkjhdsjkfh a
sdaf sdf df 
dsf ds fd ff
dsfd f sfd fdsf
dsf dsf dsf dsf df d
sf fd sdsf dsf ds fsdf ds
f 
sf sf df sf df sf sfd fdfdsf 

