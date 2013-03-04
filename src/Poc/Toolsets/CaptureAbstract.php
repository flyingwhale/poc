<?php
namespace Poc\Toolsets;

use \Poc\Core\PocEvents\PocEventNames;

abstract class  CaptureAbstract implements CaptureIntrerface 
{
    
    const NAME_START="CaptureInterface";
    
    protected $poc;
    
    protected $pocDispatcher;
    
    /**
     * 
     * @param \Poc\Poc $pluginContainer
     */
    public function init($pluginContainer)
    {
        $this->poc = $pluginContainer;
        
        $this->pocDispatcher = $this->poc->getPocDispatcher();
 
        $this->pocDispatcher->addListener( PocEventNames::GET_OUTPUT_FROM_CACHE,
                                            array($this, 'getOutputFromCache'));

        $this->pocDispatcher->addListener(
                                PocEventNames::CAPTURE,array($this, 'capture'));

        $this->pocDispatcher->addListener(
                                PocEventNames::FUNCTION_FETCHCACHE_BEGINNING,
                                                    array($this, 'setObLevel'));
        
        $this->pocDispatcher->addListener(
                                PocEventNames::MONITOR,
                                                    array($this, 'monitor'));
        
        $this->pocDispatcher->addListener(
                                PocEventNames::END_OF_BUFFERING,
                                                array($this, 'endOfBuffering'));
    }
    
    public function getName()
    {
        $rnd = rand(0, 1);
        return self::NAME_START.$rnd;
    }
    
    public function isMultipleInstanced()
    {
        return false;
    }
}
