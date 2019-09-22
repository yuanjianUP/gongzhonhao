<?php 

//var_dump(file_get_contents('php://input'));
$xml = file_get_contents('php://input');
$obj = simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
var_dump($obj);
var_dump($obj->stus->id);
 ?>