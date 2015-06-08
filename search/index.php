<?php 
require_once("../global.settings.php");

require_once("../libs/search.lib.php");
require_once("../libs/graph.lib.php");


$lang = "EN";
$direction = "ltr";



$query = $_GET['q'];

///DETEDCT LANGUAGE //LOCATION SIGNIFICANT
if (isArabicString($query))
{
	$lang = "AR";
	$direction = "rtl";
}



if ($lang=="EN" && !isDevEnviroment() )
{
	file_put_contents( dirname(__FILE__)."/../data/query.log.en", time().",".$query."\n",FILE_APPEND);
}
else if ($lang=="AR" && !isDevEnviroment())
{
	file_put_contents( dirname(__FILE__)."/../data/query.log.ar",  time().",".$query."\n",FILE_APPEND);
}


	//echoN(time());
	loadModels("core,search,qac",$lang);
	//echoN(time());
	
	
//preprint_r($MODEL_CORE);

$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = apc_fetch("UTHMANI_TO_SIMPLE_WORD_MAP");

$MODEL_CORE_OTHER_LANG = apc_fetch("MODEL_CORE[".toggleLanguage($lang)."]");

$UTHMANI_TO_SIMPLE_LOCATION_MAP = apc_fetch("UTHMANI_TO_SIMPLE_LOCATION_MAP");



//preprint_r($UTHMANI_TO_SIMPLE_LOCATION_MAP);

/// CLEANING
$query = cleanAndTrim($query);

$query = removeTashkeel($query);



// CASE HANDLING
if ($lang=="EN" )
{
	$query = strtolower($query);
}


$queryWordsArr = preg_split("/ /",$query);

//preprint_r($queryWords);




//////////////
$scoringTable = array();

$lastWord = null;

$extendedQueryWordsArr = array_fill_keys($queryWordsArr,1);

//preprint_r($extendedQueryWordsArr);

if ( $lang=="AR")
{
	/** GET ROOT/STEM FOR EACH QUERY WORD **/
	foreach ($queryWordsArr as $word)
	{
	
		//preprint_r($MODEL_SEARCH['INVERTED_INDEX'][$word]);exit;
		
		foreach ($MODEL_SEARCH['INVERTED_INDEX'][$word] as $documentArrInIndex)
		{
			
		
	
			$SURA = $documentArrInIndex['SURA'];
			$AYA = $documentArrInIndex['AYA'];
			$INDEX_IN_AYA_EMLA2Y = $documentArrInIndex['INDEX_IN_AYA_EMLA2Y'];
			$INDEX_IN_AYA_UTHMANI= $documentArrInIndex['INDEX_IN_AYA_UTHMANI'];
			$WORD_TYPE = $documentArrInIndex['WORD_TYPE'];
			$EXTRA_WORD_TYPE_INFO = $documentArrInIndex['EXTRA_INFO'];
		
			//echoN("|$INDEX_IN_AYA_EMLA2Y|");
			//$INDEX_IN_AYA_EMLA2Y = getImla2yWordIndexByUthmaniLocation(getQACLocationStr($SURA+1,$AYA+1,$INDEX_IN_AYA_EMLA2Y),$UTHMANI_TO_SIMPLE_LOCATION_MAP);
			//echoN("|$INDEX_IN_AYA_UTHMANI|");
		
			$qacLocation = getQACLocationStr($SURA+1,$AYA+1,$INDEX_IN_AYA_UTHMANI);
			
			
			
			//echoN($word);
			//echoN($WORD_TYPE);
			//preprint_r($documentArrInIndex);
			//preprint_r($MODEL_QAC['QAC_MATERTABLE'][$qacLocation]);
			
			// search QAC for roots and LEMMAS for this word
			foreach ( $MODEL_QAC['QAC_MATERTABLE'][$qacLocation] as $segmentIndex => $segmentDataArr)
			{
				$segmentFormAR = $segmentDataArr['FORM_AR'];
				$segmentFormARimla2y = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$segmentFormAR];
				
				
				//preprint_r($segmentDataArr);
				//echoN($segmentFormAR);
				//echoN($segmentFormARimla2y);
				//echoN($qacLocation);
				
			
				// the current query word has a ROOT in the current QAC segment
				if ($WORD_TYPE=="NORMAL_WORD" &&  isset($segmentDataArr['FEATURES']['STEM'])  )
				{
					// get QAC root and LEM for the current query word
					$rootOfQueryQord = $segmentDataArr['FEATURES']['ROOT'];
					$stemOfQueryWord = $segmentDataArr['FEATURES']['LEM'];
					
					
					
					/*if ( empty($stemOfQueryWord) || empty($rootOfQueryQord))
					{
						echoN($rootOfQueryQord);
						echoN($stemOfQueryWord);
						exit;
					}*/
					
					// add the STEMS to out extended query words
					if ( !isset($extendedQueryWordsArr[$rootOfQueryQord])) { $extendedQueryWordsArr[$rootOfQueryQord]=1;}
					if ( !isset($extendedQueryWordsArr[$stemOfQueryWord])) { $extendedQueryWordsArr[$stemOfQueryWord]=1;}
					
					
				}
				
			}
					
			
	
			
		}
	}
	
	/** GET EMLA2Y (SIMPLE) WORDS CORRESPONSING TO ANY QAC SEGMENT CONTAINING THE ROOT/STEMS IN THE EXTENDED QUERY WORD FROM INVERTED INDEX 
	 *  ADD TO EXTENDED QUERY WORDS
	 * **/
	foreach ($extendedQueryWordsArr as $word => $dummy)
	{
	
		//preprint_r($MODEL_SEARCH['INVERTED_INDEX'][$word]);exit;
	
		foreach ($MODEL_SEARCH['INVERTED_INDEX'][$word] as $documentArrInIndex)
		{
			$SURA = $documentArrInIndex['SURA'];
			$AYA = $documentArrInIndex['AYA'];
			$INDEX_IN_AYA_EMLA2Y = $documentArrInIndex['INDEX_IN_AYA_EMLA2Y'];
			$INDEX_IN_AYA_UTHMANI= $documentArrInIndex['INDEX_IN_AYA_UTHMANI'];
			$WORD_TYPE = $documentArrInIndex['WORD_TYPE'];
			$EXTRA_WORD_TYPE_INFO = $documentArrInIndex['EXTRA_INFO'];

			$qacLocation = getQACLocationStr($SURA+1,$AYA+1,$INDEX_IN_AYA_UTHMANI);
			
			//preprint_r($MODEL_QAC['QAC_MATERTABLE'][$qacLocation]);
			
			$verseText = getVerseByQACLocation($MODEL_CORE,$qacLocation);
			
			$wordFromVerse = getWordFromVerseByIndex($MODEL_CORE,$verseText,$INDEX_IN_AYA_EMLA2Y);
			
			
			if ( $WORD_TYPE=="PRONOUN_ANTECEDENT")
			{
				//echoN($wordFromVerse);
				
				// PRONOUNS SHOULD NOT BE ADDED TO THE QUERY BECAUSE THEY CAN REFER TO MANY THINGS
				// OTHER THAN THE ORIGINAL QUERY
				continue;
			}
			
			if ( !isset($extendedQueryWordsArr[$wordFromVerse])) 
			{ 
				
					$extendedQueryWordsArr[$wordFromVerse]=$qacLocation;
				
			}
		}
					
	
	}
}

//preprint_r($extendedQueryWordsArr);exit;
/**
 * GET ALL RESULT FORM INDEX USING EXTENDED QUERY WORD (WHICH INCLUDES ALL VARIATIONS AND PRONOUNS)
 */
foreach ($extendedQueryWordsArr as $word =>$targetQACLocation)
{	

	foreach ($MODEL_SEARCH['INVERTED_INDEX'][$word] as $documentArrInIndex)
	{	
		
		//echoN("$word ");
		//preprint_r($documentArrInIndex);exit;
		$SURA = $documentArrInIndex['SURA'];
		$AYA = $documentArrInIndex['AYA'];
		$INDEX_IN_AYA_EMLA2Y = $documentArrInIndex['INDEX_IN_AYA_EMLA2Y'];
		$INDEX_IN_AYA_UTHMANI= $documentArrInIndex['INDEX_IN_AYA_UTHMANI'];
		$WORD_TYPE = $documentArrInIndex['WORD_TYPE'];
		$EXTRA_INFO = $documentArrInIndex['EXTRA_INFO'];
		
		
		
		
		//echo getQACLocationStr($SURA,$AYA,$INDEX_IN_AYA_EMLA2Y);
		$qacLocation = getQACLocationStr($SURA+1,$AYA+1,$INDEX_IN_AYA_UTHMANI);
		
		//echoN("$qacLocation|$targetQACLocation|$word|$EXTRA_INFO|$WORD_TYPE");
		

		// incase of non normal word ( QAC/QURANA) .. transslate WordIndex from Uthmani script to Imla2y script
		/*if ( $WORD_TYPE!="NORMAL_WORD"   )
		{	
			//echoN("OLD:$INDEX_IN_AYA_EMLA2Y");
			$INDEX_IN_AYA_EMLA2Y = getImla2yWordIndexByUthmaniLocation($qacLocation,$UTHMANI_TO_SIMPLE_LOCATION_MAP);
			//echoN("NEW:$INDEX_IN_AYA_EMLA2Y");
		}*/
		
		//echoN($word);
		//preprint_r($documentArrInIndex);
		//preprint_r($MODEL_QAC['QAC_MATERTABLE'][$qacLocation]);
		
		if (!isset($scoringTable[$SURA.":".$AYA])) 
		{
			$scoringTable[$SURA.":".$AYA]=array();
			
			$scoringTable[$SURA.":".$AYA]['SCORE']=0;
			$scoringTable[$SURA.":".$AYA]['FREQ']=0;
			$scoringTable[$SURA.":".$AYA]['DISTANCE']=0;
			$scoringTable[$SURA.":".$AYA]['SURA']=$SURA;
			$scoringTable[$SURA.":".$AYA]['AYA']=$AYA;
			$scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS']=array();
			$scoringTable[$SURA.":".$AYA]['WORD_TYPE']=$WORD_TYPE;
			$scoringTable[$SURA.":".$AYA]['EXTRA_INFO']=$EXTRA_INFO;
			$scoringTable[$SURA.":".$AYA]['INDEX_IN_AYA_EMLA2Y']=$INDEX_IN_AYA_EMLA2Y;
			$scoringTable[$SURA.":".$AYA]['INDEX_IN_AYA_UTHMANI']=$INDEX_IN_AYA_UTHMANI;
			$scoringTable[$SURA.":".$AYA]['PRONOUNS']=array();
			
		}
		

		
		if ( !isset($scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS'][$word]) && in_array($word,$queryWordsArr) 
			&& $scoringTable[$SURA.":".$AYA]['FREQ']>0 )
		{
			// Raise the frequency (score) of ayas containing more than one of the query items
			$scoringTable[$SURA.":".$AYA]['FREQ']*=10;
		}
		else
		{
			$scoringTable[$SURA.":".$AYA]['FREQ']++;
		}
		
		

		/*$verseArr = preg_split("/ /",$MODEL_CORE['QURAN_TEXT'][$SURA][$AYA]);
			
		$verseArr = removePauseMarksFromArr($MODEL_CORE['TOTALS']['PAUSEMARKS'],$verseArr);
			
	
		$simpleWordFromText = $verseArr[$INDEX_IN_AYA_EMLA2Y-1];
		*/
		
		/*
		if ( empty($simpleWordFromText))
		{
			echoN($INDEX_IN_AYA_EMLA2Y);
			preprint_r($verseArr);
		}
	
		echoN($qacLocation);
		echoN($word);
		echoN($INDEX_IN_AYA_EMLA2Y);
		echoN($MODEL_CORE['QURAN_TEXT'][$SURA][$AYA]);
		echoN($simpleWordFromText);
		preprint_r($verseArr);
		*/
		
		


		
		// STEM or PRONOUN
		if ( $WORD_TYPE=="PRONOUN_ANTECEDENT"    )
		{
			$scoringTable[$SURA.":".$AYA]['PRONOUNS'][$EXTRA_INFO]=$INDEX_IN_AYA_EMLA2Y;
		}
		else if ( $WORD_TYPE=="ROOT" || $WORD_TYPE=="LEM"   )
		{
		
			// for non-normal words this will get the whole  segment
			$scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS'][$word]=$WORD_TYPE;
		
			// needed to fix root that are sometimes converted by uthmani/simple map below
			$scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS'][removeTashkeel($EXTRA_INFO)]=$WORD_TYPE;
			
			// try to convert QAC uthmani word to simpleimla2y using the MAP table with and withou tashkeel
			$wordInAya = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$EXTRA_INFO];
			
			if ( empty($wordInAya ) ) { $wordInAya = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[removeTashkeel($EXTRA_INFO)]; }
			
			if ( empty($wordInAya ) ) { $wordInAya = removeTashkeel($EXTRA_INFO); }
				
			/*if ( empty($wordInAya ) )
			{
				preprint_r($documentArrInIndex);
				echoN($EXTRA_INFO);
				echo"HERE";
				preprint_r($scoringTable[$SURA.":".$AYA]);exit;
			}*/
			
				
		
			//echoN("$word-$wordInAya-$EXTRA_INFO");
			$scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS'][$wordInAya]=$WORD_TYPE;
		}
		// NORMAL WORD
		else
		{
			// word was in original user query, not in our extended one
			///if ( in_array($word,$queryWordsArr))
			//{

			
				$scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS'][$word]=$WORD_TYPE;
			//}
		}


		
		
	}
}


//preprint_r($scoringTable);exit;

if ( empty($scoringTable))
{

	//preprint_r(array_keys($extendedQueryWordsArr));
	$suggestionsArr = getSimilarWords(array_keys($extendedQueryWordsArr));
	//preprint_r($suggestionsArr);
	
	?>
	
		<div class='search-error-message'>
			No results found !
		</div>
		<div class='search-word-suggestion'>
		<?php 
		
		if (!empty($suggestionsArr)) 
		{
			?>
			Do you Mean:
			<?php 
			
				$index =0;
				 foreach($suggestionsArr  as $suggestedWord => $dummyFlag)
				 {
				 	if ( $index++>5) break;
				 	
				 	?>
				 	<a href='?q=<?=urlencode($suggestedWord)?>'><?=$suggestedWord?></a>&nbsp;
				 	<?php
				 	
				 }
				 
			?>
		 
		</div>	
	<?php 
	  }
	exit;
	
	

}



?>
<div id="result-graph-area">

</div>

<div  id="result-stats-chart-area">


<div id="results-stats-table" style="direction:<?php echo ($lang=="AR")? "rtl":"ltr";?>">

<?php 


//// GENERATING DATA FOR CHART AND STATISTICS TABLE
$uniqueResultSuras = array();
$uniqueResultVerses = array();
$uniqueResultRepetitionCount = 0;

$frequencyPerSuraArr = array();

//preprint_r($scoringTable);exit;

foreach ($scoringTable as $verseID => $scoringArr)
{
	$suraID = $scoringArr['SURA']+1;
	$ayaID = $scoringArr['AYA'];
	$freq = $scoringArr['FREQ'];
	
	$uniqueResultSuras[$suraID]=1;
	

	$uniqueResultVerses[$verseID]=1;
	$uniqueResultRepetitionCount += 1;
	
	if ( !isset($frequencyPerSuraArr[$suraID]) ) $frequencyPerSuraArr[$suraID]=0;
	$frequencyPerSuraArr[$suraID]+=1;

	
}



//preprint_r($frequencyPerSuraArr);

$chartJSONArr = array();
foreach ($frequencyPerSuraArr as $suraID=>$repetition)
{
	$chartJSONArr[] = array($suraID,$repetition);
}

//preprint_r($chartJSONArr);

$wordDistributionChartJSON = json_encode($chartJSONArr);

//echoN($wordDistributionChartJSON);

$searchResultsChaptersCount = count($uniqueResultSuras);
$searchResultsVersesCount = count($uniqueResultVerses);

///////////////////////////////////////////////////////

?>
 <table>
 	<tr>
 		<th><?=$MODEL_CORE['RESOURCES']['CHAPTERS']?></th><td><?=$searchResultsChaptersCount?></td>
 		<th><?=$MODEL_CORE['RESOURCES']['VERSES']?></th><td><?=$searchResultsVersesCount?></td>
  		<th><?=$MODEL_CORE['RESOURCES']['REPETITION']?></th><td><?=$uniqueResultRepetitionCount?></td>		
 		<th><?=$MODEL_CORE['RESOURCES']['SENTIMENT']?></th><td>SOON</td>	
 	</tr>
 </table>

</div>
<div id="results-chart-area" >

</div>

</div>
<!-- END CHART/STAT table -->

<div id="result-verses-area" style="direction:<?php echo ($lang=="AR")? "rtl":"ltr";?>">

<h1><?=$MODEL_CORE['RESOURCES']['RESULTS']?></h1>

<?php 

foreach($scoringTable as $documentID => $documentScoreArr)
{
	$scoringTable[$documentID]['SCORE'] = $documentScoreArr['FREQ']+$documentScoreArr['DISTANCE'];
}

rsortBy($scoringTable, 'SCORE');

//preprint_r($scoringTable);exit;

$searchResultText = array();



foreach($scoringTable as $documentID => $documentScoreArr)
{
	//preprint_r($documentScoreArr);
	
	$SURA = $documentScoreArr['SURA'];
	$AYA = $documentScoreArr['AYA'];
	$TEXT = $MODEL_CORE['QURAN_TEXT'][$SURA][$AYA];
	$WORD_TYPE = $documentScoreArr['WORD_TYPE'];
	$EXTRA_INFO = ($documentScoreArr['EXTRA_INFO']);
	$INDEX_IN_AYA_EMLA2Y = $documentScoreArr['INDEX_IN_AYA_EMLA2Y'];
	$WORDS_IN_AYA = $documentScoreArr['POSSIBLE_HIGHLIGHTABLE_WORDS'];
	$PRONOUNS = $documentScoreArr['PRONOUNS'];

	
	$searchResultText[]=$TEXT;
	
	$TEXT_TRANSLATED = $MODEL_CORE_OTHER_LANG['QURAN_TEXT'][$SURA][$AYA];

	$SURA_NAME = $MODEL_CORE['META_DATA']['SURAS'][$SURA]['name_'.strtolower($lang)];

	$SURA_NAME_LATIN = $MODEL_CORE['META_DATA']['SURAS'][$SURA]['name_trans'];
	
	
	// وكذلك جلناكم امة وسطا 143/256 
	$TOTAL_VERSES_OF_SURA = $MODEL_CORE['TOTALS']['TOTAL_PER_SURA'][$SURA]['VERSES'];

	
	//preprint_r($MODEL['QURAN_TEXT']);
	
	$MATCH_TYPE="";
	
	if ( $WORD_TYPE=="PRONOUN_ANTECEDENT")
	{
	
		$MATCH_TYPE = "ضمير";
	
	}
	else if ( $WORD_TYPE=="ROOT" || $WORD_TYPE=="LEM")
	{
	
		$MATCH_TYPE = "تصريف / إشتقاق";
	
	}
	
	
	// empty in case of only pronouns
	if ( !empty($WORDS_IN_AYA))
	{
		// mark all POSSIBLE_HIGHLIGHTABLE_WORDS
		$TEXT = preg_replace("/(".join("|",array_keys($WORDS_IN_AYA)).")/mui", "<marked>\\1</marked>", $TEXT);
	}
	
	
	// mark PRONOUNS
	if ( $WORD_TYPE=="PRONOUN_ANTECEDENT")
	{
		foreach( $PRONOUNS as $pronounText => $PRONOUN_INDEX_IN_AYA_EMLA2Y)
		{
			$pronounText = removeTashkeel($pronounText);
		
	
			$TEXT = markSpecificWordInText($TEXT,($PRONOUN_INDEX_IN_AYA_EMLA2Y-1),$pronounText,"marked");
		
			//$TEXT = preg_replace("/(".$EXTRA_INFO.")/mui", "<marked>\\1</marked>", $TEXT);
		
		}
	}

	
	
	$documentID = preg_replace("/\:/", "-", $documentID);
	
	//preprint_r($documentScoreArr);
	
?>

	<div class='result-aya' style="direction:<?=$direction?>" id="<?=$documentID?>">
		<?=$TEXT?>
		
		<div id="<?=$documentID?>-translation" class='result-translated-text'>
			
			<?=$TEXT_TRANSLATED?>
		</div>
	</div>
	<div class='result-aya-info'>
	
		<span class='result-sura-info' style="direction:<?=$direction?>">
				<?=$SURA_NAME ?><?php if ( $lang=="EN") { echo " ($SURA_NAME_LATIN)"; } ?>  [<?=$AYA+1?>/<?=$TOTAL_VERSES_OF_SURA?>]
		</span>
		<span class='result-aya-showtranslation' >
		<?php 
			$showTransText = "Show Translation";
			
			if ( $lang=="EN")
			{
				$showTransText = "Show Origninal";
			}
		?>
			<a href="javascript:showTranslationFor('<?=$documentID?>')"><?=$showTransText?></a>
		</span>
		
		<span>
			<?php echo $MATCH_TYPE?>
		</span>
	</div>

<?php 
}


$graphObj = textToGraph($searchResultText,$MODEL_CORE['STOP_WORDS']);


$graphNodesArr = array();

foreach($graphObj["nodes"] as $word => $nodeArr)
{
	
	$graphNodesArr[] = $nodeArr;
	
}

//preprint_r($graphNodesArr);

$graphNodesJSON = json_encode($graphNodesArr);
$graphLinksJSON = json_encode($graphObj["links"]);

//echoN($graphNodesJSON);
//exit;
//echoN($graphLinksJSON);
?>
</div>
<!-- END verses section -->

<script>

drawGraph(<?php echo "$graphNodesJSON" ?>,<?php echo "$graphLinksJSON" ?>,960,400,"#result-graph-area",<?php echo $graphObj["capped"]?>);


drawChart(<?=$wordDistributionChartJSON?>,800,200,1,<?=$numberOfSuras?>,'#results-chart-area',"Chapter Number","Word Repetition",function(d){return "Chapter Number:" + d[0]+ "<br/>Repetition: "+d[1]} );

</script>
