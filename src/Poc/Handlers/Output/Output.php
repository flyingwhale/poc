<?php
namespace Poc\Handlers\Output;

use Poc\Poc;

abstract class Output implements OutputInterface
{

    /**
     *
     * @var Poc
     */
    protected $poc;

    public function setPoc (Poc $poc)
    {
        $this->poc = $poc;
    }

}
