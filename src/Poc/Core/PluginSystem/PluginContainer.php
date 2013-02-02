<?php
namespace Poc\Core\PluginSystem;

interface PluginContainer {
    public function addPlugin($plugin);
    public function getPluginRegistry();
}