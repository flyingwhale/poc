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
