<?php 
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



