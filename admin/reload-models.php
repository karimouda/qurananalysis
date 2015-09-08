<?php 
require("../global.settings.php");
require_once(dirname(__FILE__)."/../libs/core.lib.php");

if ( isDevEnviroment())
{
	printHTMLPageHeader();
}
$cacheInfo = apc_cache_info('user');

echoN("CACHE MEM BEFROE:".$cacheInfo['mem_size']);
apc_clear_cache();

$cacheInfo = apc_cache_info('user');

echoN("CACHE MEM AFTER CLEAR:".$cacheInfo['mem_size']);

loadModels("core,search,qac,qurana,wordnet","EN");

$cacheInfo = apc_cache_info('user');
echoN("CACHE MEM AFTER RELOAD:".$cacheInfo['mem_size']);

preprint_r($cacheInfo);


echoN("DONE");

?>




