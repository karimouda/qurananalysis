<?php 
require_once("../global.settings.php");

require_once("../libs/search.lib.php");
require_once("../libs/graph.lib.php");


require_once("query.handling.common.php");



// IF NOT PHRASE OF QUESTION SEARCH, EXTEND QUERY BY ADDING DERVIATION OF THE QUERY WORDS
if ( $lang=="AR" && $isPhraseSearch==false && $isQuestion==false)
{
	//
	$extendedQueryWordsArr = extendQueryByExtractingWordDerviations($extendedQueryWordsArr);
}


// SEARCH INVERTED INDEX FOR DOCUMENTS
$scoringTable = getScoredDocumentsFromInveretdIndex($extendedQueryWordsArr,$query,$isPhraseSearch,$isQuestion,$isColumnSearch,$columnSearchKeyValParams);




// NOT RESULTS FOUND
handleEmptyResults($scoringTable,$extendedQueryWordsArr,$query);


///// GET STATS BY SCORING TABLE

$resultStatsArr = getStatsByScoringTable($scoringTable);
?>

<div id='search-results-summary'>
<span> Searched for <?php echo join(" ",$queryWordsArr)?> </span>, 
<span>  <?php echo $resultStatsArr['VERSES_COUNT']?> verses found </span>
</div>

<?php 
//// PRINT RESULT VERSES
printResultVerses($scoringTable,$lang,$direction,$query,$isPhraseSearch,$isQuestion,$script);
?>




