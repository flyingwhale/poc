<?php

class Url extends Resource {

  function setValue() {
    //echo($_SERVER["REQUEST_URI"]);
    return $_SERVER["REQUEST_URI"];
  }
}
