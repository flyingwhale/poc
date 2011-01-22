<?php
abstract class Evaluatable extends HasPattern{
  
  const EQUALATION = 1;
  const PREGMATCH = 2;
  const NOT_EMPTY=3;
  const _EMPTY=4;
  const _SET=5;
  const NOT_SET=6;
  
  var $negation = false;
  
  var $conditonArray = array();

  function __construct()  {
    $this->conditonArray[] = $this;
    return $this;
  }

  function _and (Evaluatable $evaluatable) {
    $this->conditonArray[] = 'and';
    $this->conditonArray[] = $evaluatable;
    return $this;
  }

  function _or (Evaluatable $evaluatable) {
    $this->conditonArray[] = 'or';
    $this->conditonArray[] = $evaluatable;
    return $this;
  }

  function _xor (Evaluatable $evaluatable) {
    $this->conditonArray[] = 'xor';
    $this->conditonArray[] = $evaluatable;
    return $this;
  }
  
  function evaluate() {
    $eval = null;
    if(sizeof($this->conditonArray) == 1) {
      $eval = $this->conditonArray[0]->selfEvaluate();
    } else {
      for($i = 0; $i < sizeof($this->conditonArray); $i++)
      {
        if($eval == null) {
         $left = $this->evaluation($this->conditonArray[$i]);
        } else {
          $i--;
          $left = $eval;          
        }
        $op = $this->conditonArray[++$i];
        $right = $this->evaluation($this->conditonArray[++$i]);               

        if ($op == 'and') {
          $eval = $left AND $right;
        }
        else if ($op == 'or') {
          $eval = $left OR $right;        
        }
        else if ($op == 'xor') {
           $eval = $left XOR $right;
        }
      }
    }
    return $eval;
  }

  function negate($value=true) {
    $negation = $value;
  }
  
  private function evaluation($object) {

    if($object === $this) {
      return $object->selfEvaluate();
    } else {
      return $object->evaluate();
    }
  }
}
