<?php
namespace Poc\PocPlugins\CacheInvalidationProtection;

interface CIAProtectorEventNames
{

    const CONSULT_STARTED = 'consult';

    const CONSULT_STARTED_NOT_FIRST = "consultnf";

    const CONSULT_STARTED_FIRST = "consultf";

    const CONSULT_SLEEP = "consultslp";
}
