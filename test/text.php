<?php

  include('lib/LoremIpsum.class.php');
  $generator = new LoremIpsumGenerator;

  echo $generator->getContent(300, 'plain');
?>
 Consectetur adipiscing elit.