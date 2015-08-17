<?php 
require_once("../global.settings.php");

require_once("../libs/search.lib.php");
require_once("../libs/graph.lib.php");


require_once("query.handling.common.php");







?>
<div id='search-results-options'>
	Sort by
	<select  id='qa-sort-select' onchange="clientSortResults('result-aya-container')">
		<option value='relevance' selected sortorder='desc'>Relevance</option>
		<option value='order' sortorder='asc'>Order</option>
	</select>
	Script
	<select  id='qa-script-select' onchange="changeDefaultQuranScript()">
		<option value='simple' selected>Simple</option>
		<option value='uthmani'>Uthmani</option>
	</select>

</div>

<?php 
if ($isQuestion && !empty($conceptsFromTaxRelations))
{
?>
<div id='question-answering-area'>
	<span id='question-answering-area-answer-title'>Answer:</span> 
	<span>
	<?php 

	
		array_walk($conceptsFromTaxRelations, function(&$val)
		{
			$val = ucfirst($val);
		});
		echo join(", ",$conceptsFromTaxRelations);
		
		
	?>
	</span>
</div>
<?php 
}
?>
<div id="visualization-area" >

<table id='visualization-table'>
<tr>
	<td>
		<!-- ONTOLOGY GRAPH AREA -->
		<div id="result-graph-area">
		
		</div>
	</td>
</tr>
<tr>
	<td>
		<!-- ONTOLOGY GRAPH AREA -->
		<div  id="result-stats-chart-area">

			<div id="results-stats-table" style="direction:<?php echo ($lang=="AR")? "rtl":"ltr";?>">
					

				
					 <table>
					 	<tr>
					 		<th><?=$MODEL_CORE['RESOURCES']['CHAPTERS']?></th><td><?=$resultStatsArr['CHAPTERS_COUNT']?></td>
					 		<th><?=$MODEL_CORE['RESOURCES']['VERSES']?></th><td><?=$resultStatsArr['VERSES_COUNT']?></td>
					  		<th><?=$MODEL_CORE['RESOURCES']['REPETITION']?></th><td><?=$resultStatsArr['UNIQUE_REP']?></td>		
					
					 	</tr>
					 </table>
					
				</div>
				<div id="results-chart-area" >
					
				</div>
			</div>
		<tr>
	<td>

</table>
<!-- END CHART/STAT table -->
</div>

<div id="result-verses-area" >

<!-- <h1><?=$MODEL_CORE['RESOURCES']['RESULTS']?></h1> -->
<div id='search-results-summary'>
<span> Searched for <?php echo join(" ",$queryWordsArr)?> </span>, 
<span>  <?php echo $resultStatsArr['VERSES_COUNT']?> verses found </span>
</div>
<?php 

$searchResultsTextArr = printResultVerses($scoringTable,$lang,$direction,$query,$isPhraseSearch,$isQuestion,$script);
?>
</div>

<?php


$graphObj = ontologyTextToD3Graph($MODEL_QA_ONTOLOGY,$queryWordsArr,0,array(600),$lang,false,$isPhraseSearch,$isQuestion,$query);



if ( empty($graphObj['nodes']))
{
	$graphObj = ontologyTextToD3Graph($MODEL_QA_ONTOLOGY,$searchResultsTextArr,0,array(600,400),$lang,true,$isPhraseSearch,$isQuestion,$query);
}




$graphNodesJSON = json_encode($graphObj['nodes']);
$graphLinksJSON = json_encode($graphObj["links"]);



$wordDistributionChartJSON = getDistributionChartData($scoringTable);


?>



<script>



drawGraph(<?="$graphNodesJSON" ?>,<?="$graphLinksJSON"?>,600,400,"#result-graph-area",<?="'$lang'"?>,"result-verses-area");


drawChart(<?=$wordDistributionChartJSON?>,600,400,0,<?=$numberOfSuras?>,'#results-chart-area',"Chapter Number","Word Repetition",function(d){return "Chapter Number:" + d[0]+ "<br/>Repetition: "+d[1]} );

</script>
