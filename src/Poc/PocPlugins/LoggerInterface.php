<?php
namespace Poc\PocPlugins;

interface LoggerInterface {
  public function getLogger($eventName);
  public function setLog($eventName, $output);
}
