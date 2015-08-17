<?php 
require_once("../global.settings.php");

require_once("../libs/search.lib.php");
require_once("../libs/graph.lib.php");


require_once("query.handling.common.php");






?>

<div id='search-results-summary'>
<span> Searched for <?php echo join(" ",$queryWordsArr)?> </span>, 
<span>  <?php echo $resultStatsArr['VERSES_COUNT']?> verses found </span>
</div>

<?php 
//// PRINT RESULT VERSES
printResultVerses($scoringTable,$lang,$direction,$query,$isPhraseSearch,$isQuestion,$script);
?>




