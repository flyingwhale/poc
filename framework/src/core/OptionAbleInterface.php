<?php

namespace POC\core;

interface OptionAbleInterface
{
  public function getOption($key);
  public function getOptions();
  public function setOptions($options);
  public function fillDefaults();
}
