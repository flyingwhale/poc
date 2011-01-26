<?php
abstract class Evaluateable extends HasValue {
  
  const OP_EQUALATION = 1;
  const OP_PREGMATCH = 2;

  var $negation = false;
  var $conditonArray = array();
  var $operation;
  var $key;
  var $variablesArray;
  
  protected $pattern;

  function __construct($pattern, $operation=self::EQUALATION)  {
    $this->conditonArray[] = $this;
    $this->pattern = $pattern;
    $this->value = $this->setValue();
    $this->opertation = $operation;
    $this->setKey();
  }

  function _and (Evaluateable $evaluateable) {
    $this->conditonArray[] = 'and';
    $this->conditonArray[] = $evaluateable;
    $this->setKey();
    return $this;
  }

  function _or (Evaluateable $evaluateable) {
    $this->conditonArray[] = 'or';
    $this->conditonArray[] = $evaluateable;
    $this->setKey();
    return $this;
  }

  function _xor (Evaluateable $evaluateable) {
    $this->conditonArray[] = 'xor';
    $this->conditonArray[] = $evaluateable;
    $this->setKey();
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
  
  function selfEvaluate() {
    if($this->opertation == self::OP_EQUALATION) {
      return ($this->pattern == $this->value);
    }

    elseif($this->opertation == self::OP_PREGMATCH) {
      return preg_match($this->pattern, $this->value);
    }
    
  }

  function getKey() {
    return $this->key;
  }
  
  function setKey() {
    $this->key = md5($this->toString());
  } 
  
  function addVariable(FlexVariable $var) {
    $this->variablesArray[] = $var;
    $this->setKey();
  }
  
  
}

