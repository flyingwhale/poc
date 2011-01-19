<?php

  include('lib/LoremIpsum.class.php');
  $generator = new LoremIpsumGenerator;

  echo $generator->getContent(600, 'plain');
?>
 Consectetur adipiscing elit.
 