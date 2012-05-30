<?php
namespace Poc\PocPlugins\Output;

use Poc\PocEvents\PocEventNames;
use Poc\Poc;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Poc\Events\BaseEvent;
use Poc\Core\PluginSystem\Plugin;

class MinifyHtmlOutput extends \Poc\Core\PluginSystem\Plugin
{

    /**
     *
     * @var PocDispatcher
     */
    private $dispatcher;

//    public function init (Poc $poc);   
    public function init (Poc $poc)
    {
        parent::init($poc);
        $poc->getPocDispatcher()->addListener(PocEventNames::BEFORE_STORE_OUTPUT,
                                                    array($this, 'minifyHtml'));
    }

    public function minifyHtml (BaseEvent $event)
    {
        $search =
        array(

        '/ {2,}/',
        '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s',

        '/\>[^\S ]+/s', //strip whitespaces after tags, except space
        '/[^\S ]+\</s', //strip whitespaces before tags, except space
        '/(\s)+/s',  // shorten multiple whitespace sequences

        );            // shorten multiple whitespace sequences
        $replace =
        array(

        ' ',
        ' ',

         '>',
        '<',
        '\\1',

        );
        $event->getEvent()->setOutput(
        preg_replace($search, $replace, $event->getEvent()->getOutput()));
    }
}
