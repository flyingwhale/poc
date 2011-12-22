<?php

namespace POC\core;

interface OptionAbleInterface
{
  public function getOptions();
  public function getDefaultOptions();
  public function setOptions($options);
}
