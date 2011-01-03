<?php

class Condition {
  
  var $conditonTree;
  

  function and_ (Resource $resource) {
    
  }
    
  function or_ (Resource $resource) {
    
  }
  
  function xor_ (Resource $resource) {
    
  }
  
  function set (Resource $resource) {
    $this->conditonTree = array();
    $this->conditonTree[] = $resource;
  }
  

  function evaluate() {
    
  }

}