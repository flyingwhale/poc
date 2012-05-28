<?php
namespace Poc\PocPlugins;

use Poc\PocEvents\PocEventNames;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Poc\Events\BaseEvent;

class MinifyHtmlOutput
{

    /**
     *
     * @var PocDispatcher
     */
    private $dispatcher;

    public function __construct (EventDispatcher $dispatcher)
    {
        $dispatcher->addListener(PocEventNames::BEFORE_STORE_OUTPUT,
                                                    array($this, 'minifyHtml'));
    }

    public function minifyHtml (BaseEvent $event)
    {
                                // got fromhttp://stackoverflow.com/questions/6225351/how-to-minify-php-page-html-output
        $search = 
        array(
        '/ {2,}/',
        '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'

        );            // shorten multiple whitespace sequences
        $replace = 
        array(       
        ' ',
        ''

        
        );
        $event->getEvent()->setOutput(
        preg_replace($search, $replace, $event->getEvent()->getOutput()));
    }
}
