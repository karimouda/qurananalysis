<?php
require_once("../global.settings.php");
require_once("../libs/core.lib.php");

$type = $_GET['type'];
$value = $_GET['value'];

//echo($type);

//echo($value);

$baseDir = dirname(__FILE__)."/../data/ontology/extraction/cleaner/";
//echoN($baseDir);
$resBytes = file_put_contents("$baseDir/excluded.$type", "$value\n", FILE_APPEND);

if ( empty($resBytes))
{
	echoN($resBytes);
}

?>