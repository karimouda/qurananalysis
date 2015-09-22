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

}
else
{
	loadModels("core","EN");
}

$similarWordsArr = getSimilarWords($lang,array($word));

?>

<br>
<b>Similar Words</b>
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



