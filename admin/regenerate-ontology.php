<?php 
require_once("../global.settings.php");
require_once("../libs/core.lib.php");

$lang = "AR";



if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core,search,qac,qurana",$lang);

$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = loadUthmaniToSimpleMappingTable();


$UTHMANI_TO_SIMPLE_LOCATION_MAP = apc_fetch("UTHMANI_TO_SIMPLE_LOCATION_MAP");

$LEMMA_TO_SIMPLE_WORD_MAP = loadLemmaToSimpleMappingTable();

$pauseMarksArr = getPauseMarksArrByFile($pauseMarksFile);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analytics | Ontology </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Semantic Ontology for the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>


				
  <div id='main-container'>
			  	

			  
			  	<h1 id='ontology-title'>Quran Ontology Terms</h1>
			  	<div id=''>
			  	
			  	
			  	
			  	<?php
			  	
			  	$GENERATE_CONCEPTS_SWITCH = TRUE;
			  	
			  	$GENERATE_TERMS = 	$GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_PHRASE_TERMS = $GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_PRONOUN_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_FINAL_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  	
			  	
			  	
			  	$GENERATE_NONTAXONOMIC_RELATIONS = TRUE;
			  	

					
			if ( $GENERATE_TERMS == true )
			{
			  	
				/** Returns words from QAC by PoS tags - grouped by lemma **/
			  	function getWordsByPos(&$finalTerms,$POS)
			  	{
			  		global $MODEL_QAC,$MODEL_CORE,$UTHMANI_TO_SIMPLE_LOCATION_MAP,$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS;
			  		global $LEMMA_TO_SIMPLE_WORD_MAP;
			  		
			  		
			  		// Get all segment in QAC for that PoS
			  		foreach($MODEL_QAC['QAC_POS'][$POS] as $location => $segmentId)
			  		{

			  			// get Word, Lema and root
			  			$segmentWord = $MODEL_QAC['QAC_MASTERTABLE'][$location][$segmentId-1]['FORM_AR'];
			  			$segmentWordLema = $MODEL_QAC['QAC_MASTERTABLE'][$location][$segmentId-1]['FEATURES']['LEM'];
			  			$segmentWordRoot = $MODEL_QAC['QAC_MASTERTABLE'][$location][$segmentId-1]['FEATURES']['ROOT'];
			  			$verseLocation = substr($location,0,strlen($location)-2);
			  			//$segmentWord = removeTashkeel($segmentWord);
			  	
			  			
			  			// get word index in verse
			  			$wordIndex = (getWordIndexFromQACLocation($location));
			  	

			  			//$segmentFormARimla2y = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$segmentWord];
			  	
			  			// get simple version of the word index
			  			$imla2yWordIndex = getImla2yWordIndexByUthmaniLocation($location,$UTHMANI_TO_SIMPLE_LOCATION_MAP);
			  	

			  			// get verse text 
			  			$verseText = getVerseByQACLocation($MODEL_CORE,$location);
			  				
			  				//$imla2yWord = getWordFromVerseByIndex($MODEL_CORE,$verseText,$imla2yWordIndex);
			  				
			  				
			  				//echoN("|$segmentWord|$imla2yWord");
			  				$segmentWordNoTashkeel = removeTashkeel($segmentWordLema);
			  				
			  				$superscriptAlef = json_decode('"\u0670"');
			  				
			  				
			  				$imla2yWord = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];
			  				
			  				/*if(mb_strpos($segmentWordLema, $superscriptAlef) !==false)
			  				{
			  					
			  					$imla2yWord = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];
			  					
			  				}
			  				else
			  				{
			  					$imla2yWord = $segmentWordNoTashkeel;
			  				}*/
			  				
			  				
			  				
			  	
			  				$termWord = $segmentWordLema;//$imla2yWord;//"|$segmentWord| ".$imla2yWord ." - $location:$segmentId - $wordIndex=$imla2yWordIndex";
			  				
			  				if ( !isset($finalTerms[$termWord]))
			  				{
			  					$finalTerms[$termWord] = array("LEM"=>$segmentWordLema,"FREQ"=>0, "POS"=>$POS,"SEG"=>array(),"SIMPLE_WORD"=>$imla2yWord,"ROOT"=>$segmentWordRoot);
			  				}
			  		
		  					$finalTerms[$termWord]["FREQ"]=$finalTerms[$termWord]["FREQ"]+1;
		  					
		  					if ( !isset($finalTerms[$termWord]["SEG"][$segmentWord]) )
		  					{
		  						$finalTerms[$termWord]["SEG"][$segmentWord]=$imla2yWord;
		  					
		  					}
		  					
		  					if ( !isset($finalTerms[$termWord]["POSES"][$POS]))
		  					{
		  						$finalTerms[$termWord]["POSES"][$POS]=1;
		  					}
		  					
		  					
		  			
			  				
			  	
			  	
			  	
			  	
			  				 
			  			}
			  			 
			  			return $finalTerms;
			  		}
			  		
			  	
			  	 	$finalTerms = array();
			  	 	
			  	 	
		
					
			  		getWordsByPos($finalTerms,"PN");
			  		echoN("PN:<b>".(count($finalTerms))."</b>");
			  		$last=count($finalTerms);
			  		
			  		getWordsByPos($finalTerms,"ADJ");
			  		echoN("ADJ:<b>".(count($finalTerms)-$last)."</b>");
			  		$last=count($finalTerms);
			  		
			  		getWordsByPos($finalTerms,"N");
			  		echoN("N:<b>".(count($finalTerms)-$last)."</b>");
			  		
					
					
					/*function filterFunc($v)
					{
						return	isSimpleQuranWord($v);
					} 
			  	
			  		$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = array_filter($UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS, "filterFunc" );

			  		//echoN( count($UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS) );exit;
			  		
			  		// FILL EMPTY IMLA2Y WORDS - by getting the common repeated imla2y segment
			  		foreach ($finalTerms as $term => $termArr )
			  		{
			  			if ( empty($termArr['SIMPLE_WORD']))
			  			{
			  				//echoN($term);
			  				foreach( $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS as $mapTermKey => $mapTermVal)
			  				{
			  					
			  					if ( $mapTermKey[0]==$term[0] && myLevensteinEditDistance($term, $mapTermKey)==1)
			  					{
			  						//echoN($mapTermKey."-".myLevensteinEditDistance($term, $mapTermKey));
			  						
			  						$finalTerms[$term]['SIMPLE_WORD']= $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$mapTermKey];
			  						
			  					}
			  				}
			  				
			  				
			  			}
			  			
			  		}
						
					*/
					
					
					
					?>
					
				
					<?php 
					
						rsortBy($finalTerms,"FREQ");
						
						echoN("<hr>");
						
						echoN("Terms Count:<b>".count($finalTerms)."</b>");
					
						$PRESENTATION = "TABLE";
						//$PRESENTATION = "BLOCKS";
						
						if ( $PRESENTATION=="TABLE")
						{
							echo "<TABLE>";
						}
						
						
						
						//$finalTerms = array_slice($finalTerms, 0,70);
						
						echoN("Terms Count Capped:<b>".count($finalTerms)."</b>");
						
						foreach ($finalTerms as $term => $termArr )
						{
							$simpleWord = $termArr['SIMPLE_WORD'];
							
							
							if ( isset($MODEL_CORE['STOP_WORDS'][$simpleWord] ) )
							{
								unset($finalTerms[$term]);
							}
						}
						
						echoN("Terms Count After SW Exclusion:<b>".count($finalTerms)."</b>");
						
						echoN("<hr>");
						
						$tableIndex =0;
						foreach ($finalTerms as $term => $termArr )
						{
							$tableIndex++;
							
							$simpleWord = $termArr['SIMPLE_WORD'];

							$termWeightArr = $MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'][$simpleWord];
							
							$termWeight = $termWeightArr['TFIDF'];
							
							$finalTerms[$term]['WEIGHT']=$termWeight;
							
							if ( $PRESENTATION=="TABLE")
							{
								
								$allImla2ySegments = array_values($termArr['SEG']);
						?>
							<tr>
								<td>
									<b><?=$tableIndex?></b>
								</td>
								<td>
									<b><?=$term?></b>
								</td>
								<td>
									<?=$termArr['FREQ']?>
								</td>
								<td>
									<?=$termArr['POS']?>
								</td>
								<td>
									<?=$simpleWord;//findSmallestWordInArray($allImla2ySegments);?>
								</td>
								<td>
									<?=$termArr['LEM']?>
								</td>
								<td>
									<?=$termArr['ROOT']?>
								</td>
								<td>
									<?=round($finalTerms[$term]['WEIGHT'],2)?>
								</td>
								<td>
									<?= join(",", array_keys($termArr['POSES']))?>
								</td>								
								<td>
									<?= join(",", array_keys($termArr['SEG']))?>
									<br>
									<?= join(",", $allImla2ySegments )?>
									<br>
								</td>
							</tr>
							
							
							
						<?php
							
								
							}
							else
							{
								
								
					?>
						<span style="padding:10px;margin:2px;display:inline-block" 
						onclick="document.getElementById('<?=$term?>-disc-info').style.display='block'"
						onmouseout="document.getElementById('<?=$term?>-disc-info').style.display='none'">
							<b <?php if ( mb_strlen(removeTashkeel($term)) ==2 ) echo "style='color:red'" ?>><?=$term?></b><br>
						</span>
						<span id='<?=$term?>-disc-info' style="padding:10px;margin:2px;display:none">
							<?= preprint_r($termArr) ?> 
						</span>
						
					<?php
							}
					
						} 
						
						if ( $PRESENTATION=="TABLE")
						{
							echo "</TABLE>";
						}
						
						//preprint_r($UTHMANI_TO_SIMPLE_LOCATION_MAP);
						//preprint_r($UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS);
						
						
					}
						
						
					////### EXTRACTING PHRASE TERMS USING NGRAMS
						
					if ( $GENERATE_PHRASE_TERMS)
					{
						
							$phraseTerms = getNGrams(2);
							
							//$phraseTermsHIST = histogramFromArray($phraseTerms);
							
							//echoN( arrayToCSV($phraseTermsHIST));
							
							//preprint_r($phraseTermsHIST);
							//preprint_r($phraseTerms);
							echoN("COUNT B4 FILTER:".count($phraseTerms));
							
							$filteredBiGrams = array();
							$filteredBiGramsPOS = array();
							
							
							$allPOSTags = array();
							$allPOSCombinations = array();
							
							$counter =0;
							foreach($phraseTerms as $biGram=>$freq)
							{
								$wordsArr = preg_split("/ /",$biGram);
								
								//if ( $counter++ > 300 ){ break;}
								
								$posTagsForCurrentBiGram = "";
								$allPosTagsForCurrentBiGramArr = array();
								$filterFlag =0;
								$wordIndex = 0;
								
								foreach ($wordsArr as $singleWord)
								{
									$wordIndex++;
									
									//STOP WORDS FILTER
									/*if ( isset($MODEL_CORE['STOP_WORDS'][$singleWord]) ) 
									{
										$filterFlag = 1;
										break;
									}*/
									
									$wordsInfoArr = getWordInfo($singleWord, $MODEL_CORE, $MODEL_SEARCH, $MODEL_QAC,true);
									
									$posTagsArr = $wordsInfoArr['POS'];
									
									
									
								
									
								
									
									$posTagsForCurrentBiGram  = $posTagsForCurrentBiGram." ".join(" ",array_keys($posTagsArr));
									
									$allPosTagsForCurrentBiGramArr = array_merge($allPosTagsForCurrentBiGramArr,$posTagsArr);
									
							
									
									// POS FILTER //|| isset($posTagsArr['V'])
									if ( isset($posTagsArr['CONJ'])
									
									|| isset($posTagsArr['ACC'])
									|| isset($posTagsArr['CERT'])
									|| isset($posTagsArr['P'])
									|| isset($posTagsArr['NEG'])
									|| isset($posTagsArr['COM'])
									|| isset($posTagsArr['SUB'])
									|| isset($posTagsArr['RES'])
									|| isset($posTagsArr['EXP'])		

									|| isset($posTagsArr['CIRC'])
									|| isset($posTagsArr['REM'])
									
									|| (isset($posTagsArr['COND']) )
									//|| ( isset($posTagsArr['REL']) )
									
									|| isset($posTagsArr['T'] )
									
									|| isset($posTagsArr['LOC'] )
									|| isset($posTagsArr['RSLT'] )
									|| isset($posTagsArr['INTG'] )
									
									|| isset($posTagsArr['SUP'] )
									|| isset($posTagsArr['SUB'] )
									
									
									
									|| isset($posTagsArr['VOC'] )
									|| isset($posTagsArr['DEM'] )
									|| isset($posTagsArr['RET'] )
									
									|| isset($posTagsArr['EMPH'] )
									
									
									//|| isset($posTagsArr['PRON'] )
									//&& $wordsInfoArr["BUCKWALTER"]!="{l~a*iyna" )

									
									
									)
									{
										$filterFlag=1;
										break;
									}
									
									
								}
								
								//preprint_r($posTagsForCurrentBiGram);exit;
								
								// ACCEPTED PATTERNS
								if ( strpos($posTagsForCurrentBiGram,"REL V PRON")!==false )
								{
									$filterFlag=0;
								}
								else 
								{
									if ( isset($allPosTagsForCurrentBiGramArr['REL']) ||
										 isset($allPosTagsForCurrentBiGramArr['PRON']) || 
										 isset($allPosTagsForCurrentBiGramArr['V']) ||

										 ( strpos($posTagsForCurrentBiGram,"PN N")!==false ) ||
										 ( strpos($posTagsForCurrentBiGram,"ADJ ADJ")!==false )
										
										
									)
									{
										$filterFlag=1;
										
									}
								}
						
							
								
								if ( $filterFlag==0)
								{
									$filteredBiGrams[$biGram]=$freq;
									$filteredBiGramsPOS[$biGram]=$posTagsForCurrentBiGram;
									$allPOSCombinations[trim($posTagsForCurrentBiGram)]++;
									$allPOSTags = array_merge($allPOSTags ,array_keys($posTagsArr));
									
									//echoN("|$biGram|$posTagsForCurrentBiGram");
								}
							}
							
							echoN("COUNT AFTER FILTER:".count($filteredBiGrams));
							
								
							//preprint_r($filteredBiGrams);
							
							foreach ($filteredBiGrams as $biGram=>$freq)
							{
								//echoN("$biGram: $freq | "); //$filteredBiGramsPOS[$biGram]
							
								
							}
								
							
							//preprint_r($allPOSTags);
							$topPOS = array_count_values($allPOSTags);
							
							arsort($topPOS);
							
							preprint_r($topPOS);
							
							arsort($allPOSCombinations);
							preprint_r($allPOSCombinations);
								
					}		
					
					
					if ( $GENERATE_PRONOUN_CONCEPTS)
					{
						
						$quranaConceptsMatch = 0;
						$quranaConcepts = 0;
						$conceptsListArr  = $MODEL_QURANA['QURANA_CONCEPTS'];
						
						//preprint_r($conceptsListArr);
						
						$commonBiGramsConceptsWithQurana = array();
						
						foreach ($conceptsListArr as $key=>$conceptArr)
						{
						
							$arWord = $conceptArr['AR'];
							
							if ( $arWord=="null" || empty($arWord) ) continue;
							
							$conceptWordsArr = preg_split("/ /", $arWord);
							// 1 for concepts, 2 for bigrams
							if ( count($conceptWordsArr) ==2 )
							{
								//echoN($arWord);
								$quranaConcepts++;
								
								if ( isset($filteredBiGrams[$arWord]) )
								{
									//echoN($arWord);
									$quranaConceptsMatch++;
									
									$commonBiGramsConceptsWithQurana[$arWord]=$filteredBiGrams[$arWord];
								}
								else
								{
									//echoN($arWord);
								}
								
							
								
							}
							
							
						}
						
							echoN("QURANA BIGRAMS=$quranaConcepts");
						echoN("MATCHING QURANA BIGRAMS=$quranaConceptsMatch");
						
						
						
						
						$QuranaCommonBiGramsPos = array();
						
						foreach($commonBiGramsConceptsWithQurana as $biGram=>$dummy)
						{
							$QuranaCommonBiGramsPos[trim($filteredBiGramsPOS[$biGram])]++;
							
							
							
						}
						
						preprint_r($commonBiGramsConceptsWithQurana);
						
						arsort($QuranaCommonBiGramsPos);
						preprint_r($QuranaCommonBiGramsPos);
						
						
						$quranaConceptsMatch = 0;
						$quranaConcepts = 0;
						
						// NORMAL CONCEPT MATCHING
						foreach ($conceptsListArr as $key=>$conceptArr)
						{
							$arWord = $conceptArr['AR'];
							
							
							
							$conceptWordsArr = preg_split("/ /", $arWord);
							// 1 for concepts, 2 for bigrams
							if ( count($conceptWordsArr) ==1 )
							{
									
									
								$quranaConcepts++;
							}
							
						}
						
						$commonConceptsWithQurana = array();
						
						foreach ($finalTerms as $lemaUthmani=>$termArr)
						{
						
							$mySimpleWord = $termArr['SIMPLE_WORD'];
								
							
							// 1 for concepts, 2 for bigrams
							foreach ($conceptsListArr as $key=>$conceptArr)
							{
								
								$arWord = $conceptArr['AR'];
								
								if ( $arWord=="null" || empty($arWord) ) continue;
								
								$conceptWordsArr = preg_split("/ /", $arWord);
								// 1 for concepts, 2 for bigrams
								if ( count($conceptWordsArr) ==1 )
								{
									
									
								
							
							
							
							
									if ( $mySimpleWord==$arWord && !isset($commonConceptsWithQurana[$arWord]))
									{
										//echoN($arWord);
										
										$commonConceptsWithQurana[$arWord]=$termArr;
											
										$quranaConceptsMatch++;
										break;
									}
								}
								
						
							}
								
								
						}
						
						echoN("QURANA 1-word Concepts=$quranaConcepts");
						echoN("MATCHING QURANA 1-word Concepts=$quranaConceptsMatch");
						
						preprint_r($commonConceptsWithQurana);
						
						
					
					}
					
					
					if ( $GENERATE_FINAL_CONCEPTS )
					{
						
						/// SELECTING FINAL LIST OF CONCEPTS
						$finalConcepts = array();
						
						$amxConceptFreq = -99;
						//$finalTerms
						foreach ($commonConceptsWithQurana as $concept=>$termArr)
						{
							$finalConcepts[$concept]=array("TYPE"=>"TERM","FREQ"=>$termArr['FREQ'],"EXTRA"=>$termArr);
							
							if ( $termArr['FREQ'] > $amxConceptFreq)
							{
								$amxConceptFreq = $termArr['FREQ'];
							}
							
						}
						
						$maxConceptFreq  = $amxConceptFreq;//max(array_values($commonBiGramsConceptsWithQurana));
						foreach ($commonBiGramsConceptsWithQurana as $biGramConcept=>$freq)
						{
							$pos = $filteredBiGramsPOS[$biGramConcept];
							
							
							// phrase weight = average weight of inner terms
							$biGramWords = preg_split("/ /", $biGramConcept);
							
							$weight=0;
							foreach($biGramWords as $biGramTerm)
							{
								$weight += floatval($MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'][$biGramTerm]['TFIDF']);
							}
							
							$weight = ($weight/2);
							//////
							
							
							//$weight = round($freq/$maxConceptFreq,2);
							
							$extra = array("POS"=>$pos,"WEIGHT"=>$weight);
							
							$finalConcepts[$biGramConcept]=array("TYPE"=>"PHRASE","FREQ"=>$freq,"EXTRA"=>$extra);
						
						}
	
						rsortBy($finalConcepts,"FREQ");
						
						echoN("FINAL CONCEPTS COUNT:".count($finalConcepts));
					
						preprint_r($finalConcepts);
						
						file_put_contents("../data/ontology/temp.final.concepts", serialize($finalConcepts));
					}
					
						
						$finalConcepts = unserialize(file_get_contents("../data/ontology/temp.final.concepts"));
						
						
						
						//preprint_r($finalConcepts);
						
						
						
					if ( $GENERATE_NONTAXONOMIC_RELATIONS)
					{
						
						$MODEL_CORE_UTH = loadUthmaniDataModel();
						$relationArr = array();
						
						
						/* SURA'S LOOP **/
						for ($s=0;$s<$numberOfSuras;$s++)
						{
							
							
							$suraSize = count($MODEL_CORE_UTH['QURAN_TEXT'][$s]);
							
							
										
							/* VERSES LOOP **/
							for ($a=0;$a<$suraSize;$a++)
							{
								
								$i++;
								$verseTextUthmani = $MODEL_CORE_UTH['QURAN_TEXT'][$s][$a];
				  				$uthmaniWordsArr = preg_split("/ /", $verseTextUthmani);
				  
							
							
							
									  $uthmaniWordsArr = removePauseMarksFromArr($pauseMarksArr,$uthmaniWordsArr);
							
							
									  $triplePatternArr = array();
									  foreach($uthmaniWordsArr as $index => $uthmaniWord)
									  {
							
									  	$simpleWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$uthmaniWord];
									  	
									  	
									 
									  //	echoN("|$uthmaniWord|$simpleWord");
									  	
									  	//if ( isset($finalConcepts[$simpleWord]))
									  	//{
									  		 $qacLocation = ($s+1).":".($a+1).":".($index+1);
									  		 
										 	 $qacWordSegmentsArr = $MODEL_QAC['QAC_MASTERTABLE'][$qacLocation];
	
										 	 $pos="";
										 	 foreach($qacWordSegmentsArr as $segmentIndex=> $segmentArr)
										 	 {
											 	 $lemma = $qacWordSegmentsArr[$segmentIndex]['FEATURES']['LEM'];
												 $pos = $pos ." ". $qacWordSegmentsArr[$segmentIndex]['TAG'];
										 	 }
								
										 	// echoN("$pos|$simpleWord|$lemma|$qacLocation|$uthmaniWord");
										 	 
										 	 $triplePatternArr['CONCEPTS'][]=$simpleWord;
										 	 $triplePatternArr['PATTERN'][]=trim($pos);
										 	 
										 	
										 	 
										 	 
										 	 	
										 	 	$joinedPattern = join(" ",array_values($triplePatternArr['PATTERN']));
										 	 	
										 	 	//preprint_r($triplePatternArr);
										 	 	
										 	 	if (   $joinedPattern=="PN V DET N"
									  				|| $joinedPattern=="PN LOC DET N" 
									  				|| $joinedPattern=="PN V PN")
										 	 	{
										 	 		
										 	 		//preprint_r($triplePatternArr);
										 	 		
										 	 		
										 	 		

										 	 		if ( isset($finalConcepts[$triplePatternArr['CONCEPTS'][0]]) 
														&& isset($finalConcepts[$triplePatternArr['CONCEPTS'][2]]) )
										 	 		{
										 	 			//echoN("####");
										 	 			
										 	 			$newRelation= array("TYPE"=>"NON-TAXONOMIC","SUBJECT"=>$triplePatternArr['CONCEPTS'][0],
										 	 					"VERB"=>$triplePatternArr['CONCEPTS'][1],
										 	 					"OBJECT"=>$triplePatternArr['CONCEPTS'][2],
										 	 					"posPattern"=>$joinedPattern);

														$relationHash = md5(join(",",array_values($newRelation)));

														if ( !isset($relationArr[$relationHash]))
														{
															$relationArr[$relationHash]=$newRelation;
														}
										 	 			
										 	 		}
										 	 		
										 	 		$triplePatternArr = array();
										 	 		
										 	 	}
										 	 	else 
										 	 	{
										 	 	
										 	 		if ( count($triplePatternArr['CONCEPTS'])==3  )
										 	 		{
										 	 			$triplePatternArr['CONCEPTS'] = array_slice($triplePatternArr['CONCEPTS'], 1,2);
												 	 		$triplePatternArr['PATTERN'] = array_slice($triplePatternArr['PATTERN'], 1,2);
										 	 		 }
										 	 	}
										 	 	
										 	
										 	 	
										 	 	
										 	 	
										 	 
									  	//}
									 	 
									 }
									 
									
									 
									
							}
							
						}
						
						preprint_r($relationArr);
						
						
						echoN("FINAL NONTAXONOMIC RELATIONS :".count($relationArr));
							
						
						file_put_contents("../data/ontology/temp.final.relations", serialize($relationArr));
						
					}
					
					
				?>
				
					
				</div>
	
		  		
			
   </div>
   

	<script type="text/javascript">


				
		$(document).ready(function()
		{


		
		});


		
	</script>
		

	<?php 
		require("../footer.php");
	?>
	


  </body>
</html>







