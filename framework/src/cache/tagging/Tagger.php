<?php
abstract class Tagger {

  private $evaluateable;
  private $tags;

  function __construct($condition, $tags, Evaluatable $evaluateable) {
    if($condition){
      $this->tags = $tags;
      $this->evaluateable = $evaluateable;
    }
  }

  function tagCache(){
    $this->evaluateable 
  }

  function invalidateCache(){
    $this->evaluateable
  }
}