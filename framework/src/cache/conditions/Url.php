<?php

class Url extends Resource {

  function setValue (){ 
    return $_SERVER["REQUEST_URI"];
  }
}
