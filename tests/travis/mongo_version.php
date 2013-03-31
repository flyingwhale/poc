<?php

$class = 'MongoClient'; 

if(!class_exists($class)){ 
    $class = 'Mongo'; 
}
$m = new $class();
echo 'Mongo driver version: '.$m::VERSION."\n";
