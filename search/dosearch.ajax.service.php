<?php 
require_once("../global.settings.php");

require_once("../libs/search.lib.php");
require_once("../libs/graph.lib.php");


require_once("query.handling.common.php");






?>

<?php require_once('search.result.statement.inc.php')?>

<?php 
//// PRINT RESULT VERSES
printResultVerses($scoringTable,$lang,$direction,$query,$isPhraseSearch,$isQuestion,$script);
?>




