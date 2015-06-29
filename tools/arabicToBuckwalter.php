<?php 
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




$translation  = arabicToBuckwalter($word);



?>
<h1>
Bucklwalter Translation
</h1>
<h2><?= $translation?></h2>

</div>
</body>
</html>