<?php
class FlexVariable extends HasValue {
  
  function setValue() {
    return eval("return $this->flexResource;");
  }
  
}