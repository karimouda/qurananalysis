<?php 
require_once("../global.settings.php");
include_once("../libs/core.lib.php");


$lang = "AR";


if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core",$lang);

$text = $_GET['text'];

$text = html_entity_decode($text);


if ( empty($text)) exit;



if ( isArabicString($text))
{

	$translation  = arabicToBuckwalter($text);
	?>

		<div class='buckwalter-trans-result-header'>Buckwalter Transliteration: <b><?= $translation?></b> </div>
	<?php 
}
else
{

	try 
	{
	
		$arabicTranslation  = buckwalterReverseTransliteration($text);
		?>
			<div class='buckwalter-trans-result-header'>Arabic Presentation: <b><?= $arabicTranslation?></b> </div>
	
		<?php 
	}
	catch(Exception $e)
	{
		showTechnicalError("Invalid Buckwalter segment [$text] !");	
		exit;
	}
}



?>
	



