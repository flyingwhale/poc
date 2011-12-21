<?php

namespace POC\core;

interface OptionAble
{
  public function getOptions();
  public function getDefaultOptions();
  public function setOptions($options);
}
