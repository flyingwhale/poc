<?php

class Condition extends Evaluatable {

  var $conditonTree;


  function and_ (Evaluatable $evaluatable) {
  
  }

  function or_ (Evaluatable $evaluatable) {

  }

  function xor_ (Evaluatable $evaluatable) {

  }

  function set (Evaluatable $evaluatable) {
    $this->conditonTree[] = $evaluatable;
  }

  function evaluate() {
    return $this->conditonTree[0]->evaluate();
  }

  function getKey() {
    return md5(var_export($this->conditionTree,true));
  }

}