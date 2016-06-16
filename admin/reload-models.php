<?php 
#   PLEASE DO NOT REMOVE OR CHANGE THIS COPYRIGHT BLOCK
#   ====================================================================
#
#    Quran Analysis (www.qurananalysis.com). Full Semantic Search and Intelligence System for the Quran.
#    Copyright (C) 2015  Karim Ouda
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#    You can use Quran Analysis code, framework or corpora in your website
#	 or application (commercial/non-commercial) provided that you link
#    back to www.qurananalysis.com and sufficient credits are given.
#
#  ====================================================================
require("../global.settings.php");
require_once(dirname(__FILE__)."/../libs/core.lib.php");
//phpinfo();exit;
if ( isDevEnviroment())
{
	printHTMLPageHeader();
}
$cacheInfo = apcu_cache_info('user');

echoN("CACHE MEM BEFROE:".$cacheInfo['mem_size']);
apcu_clear_cache();

$cacheInfo = apcu_cache_info('user');

echoN("CACHE MEM AFTER CLEAR:".$cacheInfo['mem_size']);

loadModels("core,search,qac,qurana,wordnet","EN");

$cacheInfo = apcu_cache_info('user');
echoN("CACHE MEM AFTER RELOAD:".$cacheInfo['mem_size']);

preprint_r($cacheInfo);


echoN("DONE");

?>




