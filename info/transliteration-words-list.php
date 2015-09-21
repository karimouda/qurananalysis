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
    <title>Quran Analysis | Quran Words Transliterated </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quran Semantic-based Search, Analysis & Expert System">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/qe/favicon.png">
      	 
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
			  <h1>Transliterated Arabic Words of the Quran</h1>
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







