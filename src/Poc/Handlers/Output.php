<?php
namespace Poc\Handlers;

use Poc\Handlers\OutputInterface;

abstract class Output implements OutputInterface
{

    protected $poc;

    public function setPoc ($poc)
    {
        $this->poc = $poc;
    }

}
