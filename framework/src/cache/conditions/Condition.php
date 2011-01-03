<?php

class Condition implements Evaluatable{
  
  var $conditonTree;
  

  function and_ (Evaluatable $evaluatable) {
    
  }
    
  function or_ (Evaluatable $evaluatable) {
    
  }
  
  function xor_ (Evaluatable $evaluatable) {
    
  }
  
  function set (Evaluatable $evaluatable) {
    //$this->conditonTree = array();
    $this->conditonTree[] = $evaluatable;
  }
  

  function evaluate() {
    return $this->conditonTree[0]->evaluate();
  }

}