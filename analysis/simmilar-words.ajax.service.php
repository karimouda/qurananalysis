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
require_once("../global.settings.php");
require_once("../libs/core.lib.php");
require_once("../libs/search.lib.php");



$lang = "AR";


if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}



$word = $_GET['word'];

$word = html_entity_decode($word);


if ( empty($word)) exit;



if ( isArabicString($word))
{
	loadModels("core","AR");
	$lang = "AR";

}
else
{
	loadModels("core","EN");
	$lang = "EN";
}

$similarWordsArr = getSimilarWords($lang,array($word));

?>

<br>
<b>Similar Words (Character Similarity)</b>
<ul>
<?php 

$i=0;
foreach($similarWordsArr as $similarWord =>$score)
{
	if ($i++ > 20 ) break;
?>
<li><?=$similarWord?></<li>
<?php 
}
?>
</ul>



