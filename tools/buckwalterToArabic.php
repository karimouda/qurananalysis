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
include_once("../global.settings.php");
include_once("../core.lib.php");

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analysis Tools | Arabic To Buckwalter </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quran Analysis Arabic To Buckwalter Tools">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/qe/favicon.png">
      	 
	<script type="text/javascript">
	</script>


  </head>
  <body >
  <div style="text-align:center">
  <br>
<form action="#" method="GET" >
<input type="text" name="word" />
<input type="submit" value="Submit" />
</form>

<?php
$word = $_GET['word'];

$word = html_entity_decode($word);


if ( empty($word)) exit;





$arabicTranslation  = buckwalterReverseTransliteration($word);



?>
<h1>
Arabic Translation
</h1>
<h2><?= $arabicTranslation?></h2>
</div>
</body>
</html>