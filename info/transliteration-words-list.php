<!--
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
-->
<?php 
require_once("../global.settings.php");
require_once("../libs/core.lib.php");
require_once("../libs/search.lib.php");

$TRANSLITERATION_WORDS_MAP = apc_fetch("WORDS_TRANSLITERATION");

$finalTransliteratedWords = array();
foreach($TRANSLITERATION_WORDS_MAP as $wordUthmani=>$wordTransliteration)
{
	$finalTransliteratedWords[] = cleanTransliteratedText($wordTransliteration);
}

asort($finalTransliteratedWords);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Words Transliterated | Quran Analysis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quran Transliterated arabic words sorted alphabetically">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/favicon.png">
      	 
	<script type="text/javascript">
	</script>


  </head>
  <body>
  
  <div id='header'>
			  	
     <?php 
		require("../header.php");
	 ?>
  		
  </div>
  
  			<div id='main-container'>
			  <h1>Transliterated Arabic Words in the Quran</h1>
			  <table id='transliteration-table' >
				  <?php 
				  	foreach($finalTransliteratedWords as $index=>$wordTransliteration):
				  ?>
					  <tr>
					  	<td >
					  		<?php echo $wordTransliteration?>
					  	</td>
					  </tr>
				  <?php 
				  endforeach;
				  ?>
			  </table>
			</div>
  </body>
</html>







