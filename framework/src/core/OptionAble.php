<?php

namespace POC\core;

class OptionAble implements OptionAbleInterface
{

  public function getOptions(){
    return $this->options;
  }

  public function setOptions($options){
    $this->options = $options;
  }

  public function getDefaultOptions(){
    return $this->defaultOptions;
  }

}
