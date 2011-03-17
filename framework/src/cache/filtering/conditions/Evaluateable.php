<?php
/*Copyright 2011 Imre Toth <tothimre at gmail>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
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
