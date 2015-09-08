<?php 
require_once("../global.settings.php");
require_once("query.handling.common.php");







?>
<div id='search-results-options'>
	Sort by
	<select  id='qa-sort-select' onchange="clientSortResults('result-aya-container')">
		<option value='relevance' selected sortorder='desc'>Relevance</option>
		<option value='order' sortorder='asc'>Order</option>
	</select>
	
	<?php if ($lang=="AR"):?>
	Script
	<select  id='qa-script-select' onchange="changeDefaultQuranScript()">
		<option value='simple' selected>Simple</option>
		<option value='uthmani'>Uthmani</option>
	</select>
		<?php endif;?>

</div>


<?php 

// SHOW SUGGESTIONS
showSuggestions($postResultSuggestionArr);


if ($isQuestion )
{
?>
<div id='question-answering-area' <?=returnDirectionStyle($lang)?>>

	<?php if (!empty($userQuestionAnswerConceptsArr)  || !empty($userQuestionAnswerVersesArr)):?>
	<span id='question-answering-area-answer-title'><?=$MODEL_CORE['RESOURCES']['ANSWER']?></span> 
	<span>
	<?php 

		
		if ( !empty($userQuestionAnswerConceptsArr) )
		{
			array_walk($userQuestionAnswerConceptsArr, function(&$val)
			{
				$val = ucfirst($val);
			});
			echoN( join(", ",$userQuestionAnswerConceptsArr) );
		}
		else
		if ( !empty($userQuestionAnswerVersesArr) )
		{
			
			$firstAnswerVerseArr = current($userQuestionAnswerVersesArr);
			//preprint_r($firstAnswerVerseArr);
			
			$verseText = getVerseTextBySuraAndAya($firstAnswerVerseArr['SURA']+1, $firstAnswerVerseArr['AYA']+1);
			
			echoN($verseText);
		}
		
		
	?>
	<?php else:?>
		<span id='question-answering-area-noanswer-msg'>
			Didn't find clear answer for your Question, but you may find clues in labels and graphs
		</span> 	
	<?php endif;?>
	</span>
</div>
<?php 
}


?>



<div id="result-verses-area" >

<!-- <h1><?=$MODEL_CORE['RESOURCES']['RESULTS']?></h1> -->

<?php require_once('search.result.statement.inc.php')?>
<?php 

$searchResultsTextArr = printResultVerses($scoringTable,$lang,$direction,$query,$isPhraseSearch,$isQuestion,$script,$significantCollocationWords);


?>


</div>
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
				<!-- WORDCLOUD AREA -->
				<div id="result-wordcloud-area">
					<?php 
						$wordCloudArr = searchResultsToWordcloud($searchResultsTextArr,$lang,50);
						
						//shuffle_assoc($wordCloudArr);
						
						//preprint_r($wordCloudArr,1);
						
				
							
						$i=0;
						foreach ($wordCloudArr as $wordLabel => $wordFreq )
						{
								
							$freq = $wordFreq;//log($wordFreq,2);
							$i++;
								
								
							?>
							<a class='search-wordcloud-item'  
							 href="javascript:scrollToTop();showResultsForQueryInSpecificDiv('<?=$wordLabel?> <?=$query?>','result-verses-area');;"  rel="<?=($freq)?>" title="<?=$freq?> "><?=$wordLabel?></a>
						<?php
						
						}
					?>
				</div>
			</td>
		</tr>
<tr>
	<td>
		<!-- ONTOLOGY GRAPH AREA -->
		<div  id="result-stats-chart-area">

			<div id="results-stats-table" <?=returnDirectionStyle($lang)?> >
					

				
					 <table>
					 	<tr>
					 		<th><?=$MODEL_CORE['RESOURCES']['CHAPTERS']?></th><td><?=$resultStatsArr['CHAPTERS_COUNT']?></td>
					 		<th><?=$MODEL_CORE['RESOURCES']['VERSES']?></th><td><?=$resultStatsArr['VERSES_COUNT']?></td>
					  		<!--  <th><?=$MODEL_CORE['RESOURCES']['REPETITION']?></th><td><?=$resultStatsArr['UNIQUE_REP']?></td>		-->
					
					 	</tr>
					 </table>
					
				</div>
				<div id="results-chart-area" >
					
				</div>
			</div>
		</td>
		</tr>
		

</table>
<!-- END CHART/STAT table -->
</div>
<?php



//$extendedQueryWordsArr WAS $queryWordsArr
$graphObj = ontologyTextToD3Graph($MODEL_QA_ONTOLOGY,"QUERY_TERMS",$extendedQueryWordsArr,0,array(600,400),$lang,false,$isPhraseSearch,$isQuestion,$query);



if ( empty($graphObj['nodes']))
{
	$graphObj = ontologyTextToD3Graph($MODEL_QA_ONTOLOGY,"SEARCH_RESULTS_TEXT_ARRAY",$searchResultsTextArr,0,array(600,400),$lang,true,$isPhraseSearch,$isQuestion,$query);
}



//preprint_r($graphObj);

$graphNodesJSON = json_encode($graphObj['nodes']);
$graphLinksJSON = json_encode($graphObj["links"]);



$wordDistributionChartJSON = getDistributionChartData($scoringTable);




?>



<script>


<?php if (!empty($graphObj['nodes'])) : ?>
drawGraph(<?="$graphNodesJSON" ?>,<?="$graphLinksJSON"?>,600,400,"#result-graph-area",<?="'$lang'"?>,"result-verses-area");
<?php else: ?>
$("#result-graph-area").hide();
$("#result-wordcloud-area").css("margin-top","0px");
<?php endif; ?>

drawChart(<?=$wordDistributionChartJSON?>,600,400,0,<?=$numberOfSuras?>,'#results-chart-area',"Chapter Number","Word Repetition",function(d){return "Chapter Number:" + d[0]+ "<br/>Repetition: "+d[1]} );


drawSearchWordCloud("result-wordcloud-area");
</script>
