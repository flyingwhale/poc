<?php
namespace Poc\Toolsets;

abstract class  CaptureIntrerface implements CaptureIntrerface 
{
    
    const NAME_START="CaptureInterface";
    
    private $poc;
    
    public function init($pluginContainer)
    {
        $this->poc = $pluginContainer;
    }
    
    public function getName(){
        $rnd = rand(0, 1);
        return self::NAME_START.$rnd;
    }
    
    public function isMultipleInstanced()
    {
        return false;
    }
}
