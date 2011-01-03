<?php

class Pob {
  
  function callback($buffer)
  {
    echo"ZIZIZIZIIZZI";
    $b= (str_replace("A", "a", $buffer));
    return (str_replace("B", "b", $b));
  }
  
  function __construct() {
    ob_start('SELF::callback');
  }

  function __destruct() {
       ob_flush();
       //phpinfo();
  }

}

