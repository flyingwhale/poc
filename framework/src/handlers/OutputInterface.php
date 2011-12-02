<?php
interface OutputInterface {

function getLevel();
function startBuffer($callbackFunctname);
function stopBuffer();
function header($header);

}
