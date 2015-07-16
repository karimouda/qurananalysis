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
			  	
			  	
			  	//$phraseTerms = getNGrams(2);
			  		


			  	//echoN("COUNT:".count($phraseTerms));
			  	
			  	//preprint_r($phraseTerms);
			  	
			  	//exit;
			  	
			  	
			  	$GENERATE_CONCEPTS_SWITCH = FALSE;
			  	
			  	$GENERATE_TERMS = 	$GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_PHRASE_TERMS = $GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_PRONOUN_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_FINAL_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_ADJECTIVE_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  	
			  	
			  	$GENERATE_TAXONOMIC_RELATIONS = FALSE;
			  	$GENERATE_NONTAXONOMIC_RELATIONS = FALSE;
			  	
			  	
			  	$EXTRACT_NEWCONCEPTS_FROM_RELATIONS = TRUE;
			  	
			  	$ENRICH_CONCEPTS_METADATA = TRUE;
			  	$ENRICH_RELATIONS_METADATA = TRUE;
			  	
			  	

			  	$finalConcepts = array();
			  	$relationsArr = array();

					
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
			  				$alefWasla = "ٱ"; //U+0671
			  				
			  				//$imla2yWord = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];
			  				
			  				
			  				// this block is important since $LEMMA_TO_SIMPLE_WORD_MAP is not good for  non $superscriptAlef words
			  				// ex زيت lemma is converted to زيتها which spoiled the ontology concept list results
			  				if(mb_strpos($segmentWordLema, $superscriptAlef) !==false
							   || mb_strpos($segmentWordLema, $alefWasla) !==false )
			  				{
			  					
			  					$imla2yWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$segmentWordLema];
			  					
			  					if (empty($imla2yWord))
			  					{
			  						$imla2yWord = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];
			  					}
			  					
			  				}
			  				else
			  				{
			  					$imla2yWord = shallowUthmaniToSimpleConversion($segmentWordLema);//$segmentWordNoTashkeel;
			  				}
			  				
			  				
			  				
			  				/// in case the word was not found after removing tashkeel, try the lema mappign table
			  				$termWeightArr = $MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'][$imla2yWord];
			  					
			  			
			  				
			  				// NOT WORKIGN BECAUSE LEMMAS WILL NOT BE IN SIMPLE WORDS LIST و الصابيئن =>صَّٰبِـِٔين
			  				// if the word after removing tashkeel is not found in quran simple words list, then try lemma table
			  				/*if (!isset($MODEL_CORE['WORDS_FREQUENCY']['WORDS'][$imla2yWord]) )
			  				{
			  					$imla2yWord = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];
			  					
			  					if ( empty($imla2yWord) )
			  					{
			  						echoN($segmentWordLema);
			  						echoN($imla2yWord);
			  						preprint_r($LEMMA_TO_SIMPLE_WORD_MAP);
			  						preprint_r($MODEL_CORE['WORDS_FREQUENCY']['WORDS']);
			  						exit;
			  					}
			  				}*/
			  					
			  				
			  				if ( empty($termWeightArr))
			  				{
			  					//only for weight since the lema table decrease qurana matching 
			  					$imla2yWordForWeight = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];
			  					$termWeightArr = $MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'][$imla2yWordForWeight];
			  					
			  					
			  				}
			  				
			  				$termWeight = $termWeightArr['TFIDF'];
			  				////////////////////////////////////////////
			  	
			  				$termWord = $segmentWordLema;//$imla2yWord;//"|$segmentWord| ".$imla2yWord ." - $location:$segmentId - $wordIndex=$imla2yWordIndex";
			  				
			  				if ( !isset($finalTerms[$termWord]))
			  				{
			  					$finalTerms[$termWord] = array("LEM"=>$segmentWordLema,"FREQ"=>0, 
			  							"POS"=>$POS,"SEG"=>array(),"SIMPLE_WORD"=>$imla2yWord,
			  							"ROOT"=>$segmentWordRoot,"WEIGHT"=>$termWeight,"ASA"=>array(),"ENG_TRANSLATION"=>"",
			  							"DBPEDIA_LINK"=>"", "DESC_AR"=>"");
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
						
						//echoN("Stop Words Excluded");
						foreach ($finalTerms as $term => $termArr )
						{
							$simpleWord = $termArr['SIMPLE_WORD'];
							
							
							if ( isset($MODEL_CORE['STOP_WORDS'][$simpleWord] ) )
							{
								//echoN("$term");
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
									<?=round($termArr['WEIGHT'],2)?>
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
							
							$wordsInfoArr = array();
							foreach ($MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'] as $wordLabel => $wordFreqArr )
							{
							
								$wordsInfoArr[$wordLabel] = getWordInfo($wordLabel, $MODEL_CORE, $MODEL_SEARCH, $MODEL_QAC,true);
							}
							
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
									
									$singleWordInfoArr = $wordsInfoArr[$singleWord];
									
									
									$posTagsArr = $singleWordInfoArr['POS'];
									
									
									
								
									
								
									
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
									//&& $singleWordInfoArr["BUCKWALTER"]!="{l~a*iyna" )

									
									
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
							
						/*foreach ($filteredBiGrams as $biGram=>$freq)
							{
								echoN("$biGram: $freq | "); //$filteredBiGramsPOS[$biGram]
							
								
							}*/
								
							
							//preprint_r($allPOSTags);
							$topPOS = array_count_values($allPOSTags);
							
							arsort($topPOS);
							
							preprint_r($topPOS);
							
							arsort($allPOSCombinations);
							preprint_r($allPOSCombinations);
								
					}		
					
					
					
					/*$qacConcepts  = file("/home/karimo/Documents/SelfManagment/2013-LifeChangeStuff/Masters/Leeds/Semester_2/Project/QE/Data/Existing ontologies/QAC.clean.arabic",FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);
					
					$myConcepts1 = array_keys($filteredBiGrams);
					$myConcepts2 = array();
					
					foreach ($finalTerms as $term => $termArr )
					{
						
							
						$simpleWord = $termArr['SIMPLE_WORD'];
						$myConcepts2[] = $simpleWord;
					}
					
					$intersection1 = array_intersect($qacConcepts,$myConcepts1);
					$intersection2 = array_intersect($qacConcepts,$myConcepts2);
					
					$intersection = array_merge($intersection1,$intersection2);
					
					//preprint_r($finalTerms);
					preprint_r($qacConcepts);
					preprint_r($myConcepts1);
					preprint_r($myConcepts2);
					preprint_r($intersection);
					echoN(count($intersection));
					exit;
					*/
					
					
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
								
								$quranaArWord = $conceptArr['AR'];
								
								if ( $quranaArWord=="null" || empty($quranaArWord) ) continue;
								
								$conceptWordsArr = preg_split("/ /", $quranaArWord);
								// 1 for concepts, 2 for bigrams
								if ( count($conceptWordsArr) ==1 )
								{
									
									
								
									$mySimpleWordWithDet = "ال"."$mySimpleWord";
									
								
							
							
							
									if ( ( $mySimpleWord==$quranaArWord || $mySimpleWordWithDet==$quranaArWord ) && 
										 (!isset($commonConceptsWithQurana[$quranaArWord])  ) 
									 )
									{
										//echoN($arWord);
										
										if ( $quranaArWord!=$mySimpleWord)
										{
											$termArr['ASA'][] = $quranaArWord;
										}
										
										// $mySimpleWord to avoid duplicate words one without ال
										$commonConceptsWithQurana[$mySimpleWord]=$termArr;
											
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
					
					if ( $GENERATE_ADJECTIVE_CONCEPTS)
					{
						$counter = 0;
						foreach ($finalTerms as $lemaUthmani=>$termArr)
						{
							
							$pos = $termArr['POS'];
							
						
							
							if ( $pos=="ADJ")
							{
								if ( $counter++> 100) break;
								
								$simpleWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$lemaUthmani];
							
									
								if ( !empty($simpleWord))
								{
									$mergedWord = $simpleWord;
								}
								else
								{
									$uthmaniWordNoTashkeel = shallowUthmaniToSimpleConversion($lemaUthmani);
									$mergedWord = $uthmaniWordNoTashkeel;
								}
									
								
								echoN("$lemaUthmani|$simpleWord|$uthmaniWordNoTashkeel");
									
									
								if ( !isset($finalConcepts[$mergedWord]))
								{
								
									
								
									
									$finalConcepts[$mergedWord]=array("CONCEPT_TYPE"=>"A-BOX","EXTRACTION_PHASE"=>"ADJECTIVE","FREQ"=>$termArr['FREQ'],"EXTRA"=>$termArr);
								
								
								
								
								}
							}
						}
					}
					
					//preprint_r($finalConcepts);exit;
					
					
					if ( $GENERATE_FINAL_CONCEPTS )
					{
						
					
						
						$amxConceptFreq = -99;
						//$finalTerms
						foreach ($commonConceptsWithQurana as $concept=>$termArr)
						{
							$finalConcepts[$concept]=array("CONCEPT_TYPE"=>"A-BOX","EXTRACTION_PHASE"=>"TERM","FREQ"=>$termArr['FREQ'],"EXTRA"=>$termArr);
							
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
							
							$finalConcepts[$biGramConcept]=array("CONCEPT_TYPE"=>"A-BOX","EXTRACTION_PHASE"=>"PHRASE","FREQ"=>$freq,"EXTRA"=>$extra);
						
						}
	
						rsortBy($finalConcepts,"FREQ");
						
						echoN("FINAL CONCEPTS COUNT:".count($finalConcepts));
					
						//preprint_r($finalConcepts);
						
						file_put_contents("../data/ontology/temp.final.concepts", serialize($finalConcepts));
						file_put_contents("../data/ontology/temp.all.terms", serialize($finalTerms));
					}
					
						
						$finalConcepts = unserialize(file_get_contents("../data/ontology/temp.final.concepts"));
						
						
						
						//preprint_r($finalConcepts);
						
						
					
					if ( $GENERATE_TAXONOMIC_RELATIONS )
					{
						
						////////////////////////// ADJECTIVE HYPERNYMS ////////////////////////////////
						$adjName = "صفة";
						
						$finalConcepts[$adjName]=array("CONCEPT_TYPE"=>"A-BOX","EXTRACTION_PHASE"=>"TAX-RELATIONS","FREQ"=>1,"EXTRA"=>array("ASA"=>array()));
						
						
						// ADJ PARENT + relations
						foreach($finalConcepts as $concept => $coneptArr)
						{
							$exPhase = $coneptArr['EXTRACTION_PHASE'];
							
							
							if ( $exPhase=="ADJECTIVE")
							{
								$type = "TAXONOMIC";
								addNewRelation($relationsArr,$type,$concept,"هى",$adjName,"ADJ");
							}
						}
						
						
					
						/////////////////////////////////////////////////////////////////////
				
						// DIDN'T DO THIS BECAUSE IT NEEDS CONTEXT قُرْءَانًا أَعْجَمِيًّا لَّ
						//$triGrams2 = getPoSNGrams("ACC PN ADJ");
						
						
						///////// PROPER NOUNS AJDECTIVES ////////////////////////
						$triGrams4 = getPoSNGrams("PN ADJ ADJ");
						preprint_r($triGrams4);
						
						// ADJ PARENT + relations
						foreach($triGrams4 as $bigram => $freq)
						{
							$biGramWords = preg_split("/ /",$bigram);
								
							$concept = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$biGramWords[0]];
							$adj1 = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$biGramWords[1]];
							$adj2 = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$biGramWords[2]];
							
							
								if ( isset($finalConcepts[$concept]) && isset($finalConcepts[$adj1]) )
								{
										$hasQuality = "من صفاتة";
										$type = "TAXONOMIC";
										addNewRelation($relationsArr,$type,$concept,$hasQuality,$adj1,"ADJ");
									
								}
								
								if ( isset($finalConcepts[$concept]) && isset($finalConcepts[$adj2]) )
								{
									$hasQuality = "من صفاتة";
									$type = "TAXONOMIC";
									addNewRelation($relationsArr,$type,$concept,$hasQuality,$adj2,"ADJ");
										
								}
								
							
							
						}
						
						/////////////// PHRASE CONCEPTS HYPERNYMS (PARENT-CHILD) ///////////////
					
						foreach($finalConcepts as $concept => $conceptArr)
						{
							
							$type = $conceptArr['EXTRACTION_PHASE'];
							$pos = $conceptArr['EXTRA']['POS'];
							
							if ( $type=="PHRASE")
							{
								$biGramWords = preg_split("/ /",$concept);
							
								
								$parentConcept = $biGramWords[0];
								
								$wordInfoArr = getWordInfo($parentConcept, $MODEL_CORE, $MODEL_SEARCH, $MODEL_QAC,true);
								
								$parentPosArr = $wordInfoArr['POS'];
								
								
								
								if ( !isset($parentPosArr['PN']) && !isset($parentPosArr['N']) && !isset($parentPosArr['ADJ']) ) continue;
								
								$subclassConcept = $concept;
								
								if (!isset($finalConcepts[$parentConcept]))
								{
									$finalConcepts[$parentConcept]=array("CONCEPT_TYPE"=>"A-BOX","EXTRACTION_PHASE"=>"TBOX","FREQ"=>1,"EXTRA"=>array("ASA"=>array()));
								}
					
						
									$hasQuality = "نوع من";
									$type = "TAXONOMIC";
									addNewRelation($relationsArr,$type,$subclassConcept,$hasQuality,$parentConcept,"$pos");
										
							
							}
						
								
								
						}


						///////////////////////////////////////////////////////////////////
						
						file_put_contents("../data/ontology/temp.final.concepts", serialize($finalConcepts));
						file_put_contents("../data/ontology/temp.final.relations", serialize($relationsArr));
						
					}	
						
						
					if ( $GENERATE_NONTAXONOMIC_RELATIONS)
					{
						
						$MODEL_CORE_UTH = loadUthmaniDataModel();
					
						
						
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
							
							
									  $verseLemmas = array();
									  $versePrevWords = array();
									  $versePrevPatterns = array();
									  
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
										 	 $allSegments = "";
									
										 	 foreach($qacWordSegmentsArr as $segmentIndex=> $segmentArr)
										 	 {
											 	 $lemma = $qacWordSegmentsArr[$segmentIndex]['FEATURES']['LEM'];
											 	 
											 	 $segmentAR = $qacWordSegmentsArr[$segmentIndex]['FORM_AR'];
											 	 $newTag = $qacWordSegmentsArr[$segmentIndex]['TAG'];;
											 	 if ( $newTag=="PN" || $newTag=="N" || $newTag=="ADJ")
											 	 {
											 		 $allSegments.=$segmentAR;
											 	 }
												 $pos = $pos ." ". $newTag;
												 
												 $verseLemmas[$simpleWord]=$lemma;
												 
												
										 	 }
										 	 
										 	 $verseSegments[$simpleWord]=$allSegments;
								
										 	// echoN("$pos|$simpleWord|$lemma|$qacLocation|$uthmaniWord");
										 	 
										 	 
										 	 $triplePatternArr['CONCEPTS'][]=$simpleWord;
										 	 $triplePatternArr['PATTERN'][]=trim($pos);
										 	 
										 	
										 	 
										 	 
										 	 	
										 	 	$joinedPattern = join(" ",array_values($triplePatternArr['PATTERN']));
										 	 	
										 	 	
										 	 	
										 	 	$concept1 = $triplePatternArr['CONCEPTS'][0];
										 	 	$concept2 = $triplePatternArr['CONCEPTS'][2];
										 	 	$verb = $triplePatternArr['CONCEPTS'][1];
										 	 
										 	 	
										 	 	if (  
													( 
														//الله -> يحب -> المتقين
 													     ($joinedPattern=="PN V DET N" && $triplePatternArr['CONCEPTS'][1]!="قال")
													  || ($joinedPattern=="PN NEG V DET N" )
														//الله -> مع -> المتقين
									  				  || $joinedPattern=="PN LOC DET N" 
													    //ٱللَّهَ ٱصْطَفَىٰٓ ءَادَمَ
									  				  || $joinedPattern=="PN V PN"
													    //مُّحَمَّدٌ رَّسُولُ ٱللَّهِ
													  || ( $joinedPattern=="PN N PN" && !isset($versePrevWords['قالت']) && !isset($versePrevWords['وقالت']) )
													    // NOTE: NEG يخلف[1] => الله[2] => عهده
													    //عَصَىٰٓ ءَادَمُ رَبَّ هُ
													  || $joinedPattern=="V PN N PRON"
									  				)
													&& 
													( $concept1!= $concept2 )
												)
										 	 	{
										 	 		
										 	 		//preprint_r($triplePatternArr);
										 	 		
										 	 		if ( $joinedPattern=="V PN N PRON")
										 	 		{
										 	 			$concept1Temp = $concept1;
										 	 			$concept1 = $triplePatternArr['CONCEPTS'][1];
										 	 			$concept2 = $triplePatternArr['CONCEPTS'][2];
										 	 			$verb = $concept1Temp;
										 	 			
										 	 		}
										 
										 	 		$concept1Segment = $verseSegments[$concept1];
										 	 		$concept2Segment = $verseSegments[$concept2];
										 	 		
										 	 		$concept1Lemma = $verseLemmas[$concept1];
										 	 		$concept2Lemma = $verseLemmas[$concept2];
										 	 		

										 	 		if ( (isset($finalConcepts[$concept1]) || ($concept1=getConceptByLemma($finalConcepts,$concept1Lemma)) )
														&& (isset($finalConcepts[$concept2]) || ($concept2=getConceptByLemma($finalConcepts,$concept2Lemma)) )
									  				 )
										 	 		{
										 	 			echoN("####");
										 	 			
										 	 				$type = "NON-TAXONOMIC";
										 					addNewRelation($relationsArr,$type,$concept1,$verb,$concept2,$joinedPattern);
										 				
										 					
										 	 			
										 	 		}
										 	 		//pattern found but concepts not found in concept list
										 	 		else
										 	 		{
										 	 			echoN("__________");
										 	 			echoN("|$concept1Lemma|$concept2Lemma");
										 	 			preprint_r($finalConcepts[$concept2]);
										 	 			preprint_r($triplePatternArr);
										 	 			echoN($joinedPattern);
										 	 		}
										 	 		
										 	 		$triplePatternArr = array();
										 	 		
										 	 	}
										 	 	else 
										 	 	{
										 	 	
										 	 		if ( count($triplePatternArr['CONCEPTS'])==3  )
										 	 		{
										 	 			
										 	 			$droppedConcept = array_slice($triplePatternArr['CONCEPTS'], 1,2);;
										 	 			$droppedPattern = array_slice($triplePatternArr['PATTERN'], 1,2);;
										 	 			$versePrevWords[$droppedConcept]=1;
										 	 			$versePrevPatterns[$droppedPattern]=1;
										 	 			
										 	 			$triplePatternArr['CONCEPTS'] = $droppedConcept;
												 	 	$triplePatternArr['PATTERN'] = $droppedPattern;
										 	 		 }
										 	 	}
										 	 	
										 	
										 	 	
										 	 	
										 	 	
										 	 
									  	//}
									 	 
									 }
									 
									
									 
									
							}
							
						
						
						preprint_r($relationsArr);
						
						
						echoN("FINAL NONTAXONOMIC RELATIONS :".count($relationsArr));
							
						
						file_put_contents("../data/ontology/temp.final.relations", serialize($relationsArr));
						
					}
					
					$poTaggedSubsentences = getPoSTaggedSubsentences();
					
					//preprint_r($poTaggedSubsentences);exit;
					
					//echoN("SubSentences Count:".addCommasToNumber(count($poTaggedSubsentences)));
					
					function addRelation(&$relationsArr, $subject,$verb,$object,$joinedPattern)
					{
						global  $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS;
					
					
						$subject = shallowUthmaniToSimpleConversion($subject);
						$object = shallowUthmaniToSimpleConversion($object);
					
						echoN("---SUBJ:<b>{$subject}</b> VERB:$verb OBJ:<b>{$object}</b>");
					
						addNewRelation($relationsArr,"NON-TAXONOMIC",$subject,$verb,$object,$joinedPattern);
					}
					
					function resolvePronouns($qacLocation)
					{
						global $MODEL_QURANA;
						$pronArr = array();
						$index=0;
						//echoN($qacLocation);
						//if ( $qacLocation=="3:146:11")
							//preprint_r($MODEL_QURANA['QURANA_PRONOUNS']);
						foreach($MODEL_QURANA['QURANA_PRONOUNS'][$qacLocation] as $coneptArr)
						{
								
							$coneptId = $coneptArr['CONCEPT_ID'];
							$conceptName = $MODEL_QURANA['QURANA_CONCEPTS'][$coneptId]['AR'];
						
							echoN($conceptName);
						
							// qurana null concept
							//if ( $conceptName=="null") continue;
						
							$pronArr[$index++]=$conceptName;
						}
						
						return $pronArr;
					}
					
					$ssPoSAggregation = array();
					$ssPoSAggregationCorrespondingSent = array();
					
					//$METHOD = "GENERAL_RULE";
					$METHOD = "LCS_RULES";
					
					if ( $METHOD=="GENERAL_RULE")
					{

					
					

					
							
		
							/*$targetWord = "الله";
							$handledVerses = array();
							
							foreach ($MODEL_SEARCH['INVERTED_INDEX'][$targetWord] as   $documentArrInIndex)
							{
									
							
							
								$SURA = $documentArrInIndex['SURA'];
								$AYA = $documentArrInIndex['AYA'];
		
		
							
								$qacLocation = ($SURA+1).":".($AYA+1);
								
								
								
								
								//multiple pronouns for same verse
								if (isset($handledVerses[$qacLocation])) continue;
								//echoN($qacLocation);
								
								$handledVerses[$qacLocation]=1;
								
								$subSentenceIndex=1;
								
								$currentArrayItem = $poTaggedSubsentences[$qacLocation."-$subSentenceIndex"];
								while(!empty($currentArrayItem) )
								{
									
									//preprint_r($currentArrayItem);
									
									$subSentenceLocation = $qacLocation."-$subSentenceIndex";
					
									$wordsArr = $poTaggedSubsentences[$subSentenceLocation]['WORDS'];
									$posArr = $poTaggedSubsentences[$subSentenceLocation]['POS_TAGS'];
										
									$ssPoSPattern = join(", ",$posArr);
										
									$subSentenceStr = join(" ",$wordsArr);
									
									
									
									
										$ssPoSAggregation[$ssPoSPattern]++;
									
										$ssPoSAggregationCorrespondingSent[$subSentenceLocation] = array($ssPoSPattern,$subSentenceStr);
									
									
									$subSentenceIndex++;
									
									$subSentenceLocation = $qacLocation."-$subSentenceIndex";
									
									$currentArrayItem = $poTaggedSubsentences[$subSentenceLocation];
									
									
								}
								
								
								
							}
							
							*/
							foreach($poTaggedSubsentences as $location => $dataArr)
							{
								$wordsArr = $poTaggedSubsentences[$location]['WORDS'];
								$posArr = $poTaggedSubsentences[$location]['POS_TAGS'];
								
								$ssPoSPattern = join(", ",$posArr);
								
								$subSentenceStr = join(" ",$wordsArr);
									
				
								$ssPoSAggregationCorrespondingSent[$location] = array($ssPoSPattern,$subSentenceStr);
									
							
									
								
							}
							
					
							function flushProperRelations(&$relationsArr,&$conceptsArr,&$verb,&$lastSubject,$ssPoSPattern,&$filledConcepts)
							{
								
								
								if ( count($conceptsArr)>=2   )
								{
										
									if (empty($verb))
									{
										$verb = "n/a";
									}
									
									
										
									if ( $conceptsArr[0]!=$conceptsArr[1])
									{
										addRelation($relationsArr, $conceptsArr[0],$verb,$conceptsArr[1],$ssPoSPattern);
										
										if ( count($conceptsArr)>2 )
										{
											addRelation($relationsArr, $conceptsArr[1],"n/a",$conceptsArr[2],$ssPoSPattern);
											addRelation($relationsArr, $conceptsArr[0],"n/a",$conceptsArr[2],$ssPoSPattern);
										}
									}
									
									$conceptsArr=array();
									$verb = null;
									$filledConcepts=0;
								}
									
									
								if ( count($conceptsArr)==1 && !empty($verb) && !empty($lastSubject) && $conceptsArr[0]!=$lastSubject)
								{
										
									//echoN("||||".$conceptsArr[0]."|".$lastSubject);
										
										
										
										
									$temp = $conceptsArr[0];
									$conceptsArr[0] = $lastSubject;
									$conceptsArr[1] = $temp;
								
		
									// many problems
									if ( $conceptsArr[0]!=$conceptsArr[1])
									{
										addRelation($relationsArr, $conceptsArr[0],$verb,$conceptsArr[1],$ssPoSPattern);
									}
									
									
								
									
									$conceptsArr=array();
									$verb = null;
		
									$filledConcepts=0;
								}
							}
							
							
							function isNounPhrase($posPattern)
							{
								return ( $posPattern=="N" || $posPattern=="PN" || $posPattern=="DET N"
									);
									//REMOVED || $posPattern=="N PRON"  نصيبك
							}
							
							
							
							 $lastVerseId = null;
							 
							 foreach ($ssPoSAggregationCorrespondingSent as $ssLocation => $ssArray)
							 {
		
							 	//	echoN("________");
							 	$ssPoSPattern = $ssArray[0];
							 	$subSentenceStr = $ssArray[1];
								//echoN("<b>$subSentenceStr</b>");
								
								//echoN("$ssPoSPattern");
								
								
								$verseId = substr($ssLocation, 0, strlen($ssLocation)-2);
								
								$patternArr = preg_split("/,/",$ssPoSPattern);
								$WordsArr = preg_split("/ /",$subSentenceStr);
								
								$verb = $lastPosPattern = $lastSubject = null;
								$conceptsArr = array();
								$filledConcepts = 0;
								$flushAndResetFlag = false;
								
								if ( $lastVerseId!=$verseId)
								{
									$qacVerseIndex=1;
									$lastVerseId = $verseId;
								}
								
								$lastWord = null;
								
								foreach($patternArr as $index => $posPattern)
								{
									$posPattern = trim($posPattern);
									
									//echoN($posPattern);
									
									$currentWord = current($WordsArr);
									
									$qacLocation = substr($ssLocation, 0, strlen($ssLocation)-2) .":".($qacVerseIndex);
									
							
									//echoN($qacLocation);
									
									$qacWordSegmentsArr = $MODEL_QAC['QAC_MASTERTABLE'][$qacLocation];
							
									$featuresArr = array();
									foreach($qacWordSegmentsArr as $segmentIndex=> $segmentArr)
									{
										$features = $qacWordSegmentsArr[$segmentIndex]['FEATURES'];
										
										$featuresArr = array_merge($featuresArr,$features);
									}
		
									//preprint_r($featuresArr);
									
									
									
										//echoN($featuresArr['ROOT']);
										
										if ( $index > 0 && strpos($posPattern,"ACC")!==false ||
											 strpos($posPattern,"REM")!==false ||
											strpos($posPattern,"SUB")!==false ||
											strpos($posPattern,"COND")!==false ||
											strpos($posPattern,"CONJ P PRON")!==false ||
		
											$featuresArr['ROOT']=="قول"
										)
										{
											
											
											flushProperRelations($relationsArr,$conceptsArr,$verb,$lastSubject,$ssPoSPattern,$filledConcepts);
											
											if ( $featuresArr['ROOT']=="قول" || strpos($posPattern,"COND")!==false)
											{
												$qacVerseIndex += (count($patternArr)-($index+1))+1;
												break;
											}
										}
										
										if ( strpos($posPattern,"V")!==false && !isset($featuresArr['PASS'] ) )//MAGHOOL
										{
											if ( isset($featuresArr['IMPF']))
											{
												$verbRepresentation = $currentWord;
											}
											else
											{
												$verbRepresentation = $featuresArr['LEM'];
											}
											
											//echoN("VERB:$verb|$verbRepresentation|$posPattern|");
											//echoN($lastPosPattern);
										
												if ( strpos($ssPoSPattern,"NEG")!==false || strpos($ssPoSPattern,"PRO")!==false )
												{
													$verb = $verb." ".$verbRepresentation;
												}
												else
												{
													$verb = $verbRepresentation;
												}
												
												
												if ( strpos($lastPosPattern,"NEG")!==false || strpos($lastPosPattern,"PRO")!==false )
												{
													$verb = $lastWord." ".$verb;
												}
											
										}else 								
										if ( strpos($posPattern,"P")!==false &&  strpos($lastPosPattern,"V")!==false)
										{
											
											
											if ( !empty($verb) && strpos($verb, " ")===false)
											{
												$verb = $verb." ".$currentWord;
											}
										}
									
										if ( isNounPhrase($posPattern))
										{
											//echoN("==$currentWord==$posPattern");
											//preprint_r($conceptsArr[$filledConcepts-1]);
											if ( isNounPhrase($lastPosPattern))
											{
												$conceptsArr[$filledConcepts-1]=$conceptsArr[$filledConcepts-1]." ".$currentWord;
											}
											else
											{
												$conceptsArr[$filledConcepts++]=$currentWord;
											}
											
										
											
										}
										
										if ( strpos($posPattern,"PRON")!==false  )
										{
											//echoN($ssLocation);
											
											
											
											//echoN($qacLocation);
											//preprint_r($MODEL_QURANA['QURANA_PRONOUNS'][$qacLocation]);
											
											foreach($MODEL_QURANA['QURANA_PRONOUNS'][$qacLocation] as $coneptArr)
											{
											
												$coneptId = $coneptArr['CONCEPT_ID'];
												$conceptName = $MODEL_QURANA['QURANA_CONCEPTS'][$coneptId]['AR'];
												
												//echoN($conceptName);
												
												// qurana null concept
												if ( $conceptName=="null") break;
												
												$conceptsArr[$filledConcepts++]=$conceptName;
											}
											
											if ($posPattern=="N PRON"  )
											{
												
												$nounSegment =  $featuresArr['LEM'];
												$tempConceptArr = array();
												$tempConceptArr[0] = $conceptsArr[$filledConcepts-1];
												$tempConceptArr[1] = $nounSegment;
												
												$tempVerb = "يملك";
												flushProperRelations($relationsArr,$tempConceptArr,$tempVerb,$nounSegment,$ssPoSPattern,$filledConcepts);
											}
											
											//echoN(">--- INPRON --");
										//	preprint_r($conceptsArr);
											//echoN("V:".$verb);
											//echoN("Last Sub:".$lastSubject);
											flushProperRelations($relationsArr,$conceptsArr,$verb,$lastSubject,$ssPoSPattern,$filledConcepts);
											//echoN("<--- INPRON --");
											
									
											
											
										}
										
										
										// last item in loop, check any pending relations
										if ( $index+1 >= count($patternArr))
										{
											//echoN("DOWN");
											flushProperRelations($relationsArr,$conceptsArr,$verb,$lastSubject,$ssPoSPattern,$filledConcepts);
										}
										
										$lastPosPattern = $posPattern;
									
									
									//echoN("$currentWord|$posPattern");
									
										$qacVerseIndex++;
										
										$lastWord = $currentWord;
									next($WordsArr);
								}
								
								
								
								
								
								
							 }
					}
					else
					if ( $METHOD=="LCS_RULES")
					{
						//preprint_r($poTaggedSubsentences);
						function getVerbSegment($qacWordSegmentsArr, $sentPos)
						{
							foreach($qacWordSegmentsArr as $segmentIndex=>$segmentArr)
							{
								$segmentPos = $segmentArr['TAG'];
									
								if ( $sentPos==$segmentPos )
								{
									return $segmentArr;
								}
						
							}
						}
						
						function trimVerb($verb)
						{
							
							return preg_replace("/وَ|فَ/um", "", $verb);
						}
						
						/*
						$rules = file("../data/ontology/lcs.postags.2plus.all.rules",FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);
						
						preprint_r($rules);
						
						$filteredRules = array();
						$counter =0;
						foreach ($rules as $rulePatternAndFreq)
						{
							
							
							$lineArr = preg_split("/\|/", $rulePatternAndFreq);
							$rulePattern = $lineArr[0];
							if ( strpos($rulePattern, "V")!==false )
							{
								if ( $counter++ > 100 ) break;
								
								$filteredRules[$rulePattern]=preg_replace("/,/", "", $rulePattern);
							}
						}
						echoN(count($filteredRules));
						//preprint_r($filteredRules);
						*/
						
						
						$filteredRules["V PRON, P, N, PN"]=1;
						$filteredRules["V PRON PRON"]=1;
						
						$filteredRules["V PRON, DET N"]=1;
						$filteredRules["V PRON, N PRON"]=1;
						
						
						foreach( $filteredRules as $rulePattern=>$strippedRulePattern)
						{
							//$rule = "V PRON, P, N, PN";
							//$rule = "V PRON PRON";
							$rule = $rulePattern;
							
							foreach($poTaggedSubsentences as $location => $dataArr)
							{
									$wordsArr = $poTaggedSubsentences[$location]['WORDS'];
									$posArr =   $poTaggedSubsentences[$location]['POS_TAGS'];
									$qacIndexes = $poTaggedSubsentences[$location]['QAC_WORD_INDEXES'];
									
									/*preprint_r($posArr);
									preprint_r($wordsArr);
									preprint_r($qacIndexes);
									*/
									
									$ssPoSPattern = join(", ",$posArr);
									//echoN("---$ssPoSPattern");
									
									$subSentenceStr = join(" ",$wordsArr);
									
									
										
					
									if ( strstr($ssPoSPattern, $rule)!==false)
									{
										echoN($subSentenceStr);
										//echoN($location);
										echoN($ssPoSPattern);
										echoN($rule);
										$startOfRule= strpos($ssPoSPattern, $rule);
										//echoN("SP:".$startOfRule);
										$prePatternStr = substr($ssPoSPattern, 0,$startOfRule);
										$numberOfWordsPrePattern = preg_match_all("/,/", $prePatternStr);
										$numberOfWordsInRule = (preg_match_all("/,/", $rule)+1);
										//echoN("# of words prepattern:".$numberOfWordsPrePattern);	
										//echoN("# of words in rule:".$numberOfWordsInRule);
										
										$startArrayIndexOfPattern = $numberOfWordsPrePattern;
										
										$verseId = substr($location, 0, strlen($location)-2);
						
										
					
	
										
										$qacStartWordIndexInVerse = $qacIndexes[$startArrayIndexOfPattern];
										$qacBaseLocation = $verseId;
										//echoN($qacLocation);
										
										
										// preventive TAGS
										if ( preg_match("/VOC|COND/",$prePatternStr) )
										{
											continue;
										}
										
										
										//preprint_r($qacWordSegmentsArr);
										$prevPoS = $prevWord= null;
										if ($startArrayIndexOfPattern-1 >= 0  )
										{
											$prevPoS = $posArr[$startArrayIndexOfPattern-1];
											$prevWord  = $wordsArr[$startArrayIndexOfPattern-1];
										}
										
										
										switch($rule)
										{
											case "V PRON PRON":
												
												$qacWordLocation = $qacBaseLocation .":".($qacStartWordIndexInVerse);
													
												$pronounConceptArr = resolvePronouns($qacWordLocation);
												$subject = $pronounConceptArr[0];
												$object = $pronounConceptArr[1];
												
												//qurana bug
												if ( empty($pronounConceptArr)) continue;
												if ( $subject=="null" || $object=="null") continue;
												
												
												$qacWordSegmentsArr = $MODEL_QAC['QAC_MASTERTABLE'][$qacWordLocation];
													
												$verbSegmentArr = getVerbSegment($qacWordSegmentsArr,"V");
												$verbLemma = $verbSegmentArr['FORM_AR'];
												//preprint_r($verbSegmentArr);
												//echoN($verbLemma);
												//preprint_r($pronounConceptArr);
												
												$isImperative = isset($verbSegmentArr['FEATURES']['IMPV']);
													
												// null concept
												if ( $subject=="null" ) continue;
												if ( $isImperative) continue;
												if ( empty($subject) || empty($object) ) continue;
													
													
												$verb = $verbLemma;
													
												if ( preg_match("/NEG|PRO([ ]|\,)/", $prevPoS))
												{
													$verb = $prevWord." ".$verb;
												}
													
												
												addRelation($relationsArr, $subject, $verb, $object, $rule);
												
											break;
											
											case "V PRON, P, N, PN":
												
											
												$qacWordLocation = $qacBaseLocation .":".($qacStartWordIndexInVerse);
												
												$pronounConceptArr = resolvePronouns($qacWordLocation);
												$subject = $pronounConceptArr[0];
									
												//qurana bug
												if ( empty($pronounConceptArr)) continue;
												
	
												$qacWordSegmentsArr = $MODEL_QAC['QAC_MASTERTABLE'][$qacWordLocation];
												
												$verbSegmentArr = getVerbSegment($qacWordSegmentsArr,"V");
												$verbLemma = $verbSegmentArr['FEATURES']['LEM'];
												$isImperative = isset($verbSegmentArr['FEATURES']['IMPV']);
												
												//preprint_r($verbSegmentArr);
												
												// null concept
												if ( $subject=="null" ) continue;
												if ( $isImperative) continue;
												
												
												$verb = $verbLemma." ".$wordsArr[$startArrayIndexOfPattern+1];
												
												if ( preg_match("/NEG|PRO/", $prevPoS))
												{
													$verb = $prevWord." ".$verb;
												}
												
												$object = $wordsArr[$startArrayIndexOfPattern+2]." ".$wordsArr[$startArrayIndexOfPattern+3];
												
												if ( empty($subject) || empty($object) ) continue;
												
												addRelation($relationsArr, $subject, $verb, $object, $rule);
											break;
											//
											//يَغْفِرْ لَ كُمْ ذُنُوبَ كُمْ
											case "V PRON, DET N":
											case "V PRON, N PRON":
												$qacWordLocation = $qacBaseLocation .":".($qacStartWordIndexInVerse);
													
												$pronounConceptArr = resolvePronouns($qacWordLocation);
												$subject = $pronounConceptArr[0];
							
												if ( $rule=="V PRON, N PRON")
												{
													// remove PRON chars from word
													$qacWordLocationForSecondWord = $qacBaseLocation .":".($qacStartWordIndexInVerse+1);
													$qacWordSegmentsArr = $MODEL_QAC['QAC_MASTERTABLE'][$qacWordLocationForSecondWord];
													$nounSegmentArr = getVerbSegment($qacWordSegmentsArr,"N");
													$nounSegment = $nounSegmentArr['FEATURES']['LEM'];
													/////
													$object = $nounSegment;
												}
												else
												{
													$object = $wordsArr[$startArrayIndexOfPattern+1];;
												}
												
												//echoN($startArrayIndexOfPattern);
												//preprint_r($wordsArr);
												
												//qurana bug
												if ( empty($pronounConceptArr)) continue;
												if ( empty($subject) || empty($object) ) continue;
												if ( $subject=="null" || $object=="null") continue;
												
												
												$qacWordSegmentsArr = $MODEL_QAC['QAC_MASTERTABLE'][$qacWordLocation];
													
												$verbSegmentArr = getVerbSegment($qacWordSegmentsArr,"V");
												$verbLemma = $verbSegmentArr['FORM_AR'];
												//preprint_r($verbSegmentArr);
												//echoN($verbLemma);
												//preprint_r($pronounConceptArr);
												
												$isImperative = isset($verbSegmentArr['FEATURES']['IMPV']);
													
												// null concept
												if ( $subject=="null" ) continue;
												if ( $isImperative) continue;
													
													
												$verb = trimVerb($wordsArr[$startArrayIndexOfPattern]);//$verbLemma;
													
												if ( preg_match("/NEG|PRO([ ]|\,)/", $prevPoS))
												{
													$verb = $prevWord." ".$verb;
												}
													
												
												addRelation($relationsArr, $subject, $verb, $object, $rule);
											break;
											
										}
	
										//$ruleBoundry = ($numberOfCommasInPattern+$numberOfWordsInRule);
										/*for($i=$numberOfCommasInPattern;$i<$ruleBoundry;$i++)
										{
											echoN("I:".$i);
											echoN($posArr[$i]);
											echoN($wordsArr[$i]);
											
											//$qacLocation = $verseId .":".($qacVerseIndex);
											//$qacWordSegmentsArr = $MODEL_QAC['QAC_MASTERTABLE'][$qacLocation];
											
											
										}*/
										
										
										
									}
										
									
										
									
							}
						}
					}
				
					
	
					preprint_r($relationsArr);
					file_put_contents("../data/ontology/temp.final.relations", serialize($relationsArr));
					
					
					}
					
					function getTermArrBySimpleWord($finalTerms, $sentSimpleWord)
					{
						
						
						foreach ($finalTerms as $lemaUthmani=>$termArr)
						{
							
							$mySimpleWord = $termArr['SIMPLE_WORD'];
							
							//echoN("$sentSimpleWord==$mySimpleWord");
							
							if ( $sentSimpleWord==$mySimpleWord)
							{
								return $termArr;
							}
							
						}
						
						return false;
					}
					
					 
					if ( $EXTRACT_NEWCONCEPTS_FROM_RELATIONS )
					{
						$relationArr = unserialize(file_get_contents("../data/ontology/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("../data/ontology/temp.final.concepts"));
						$finalTerms =  unserialize(file_get_contents("../data/ontology/temp.all.terms"));
						
						
						
						$notInCounceptsCounter = 0;
						$handled = array();
						foreach($relationArr as $hash => $relationsArr)
						{
							$relationsType = $relationsArr['TYPE'];
							
							if ( $relationsType =="NON-TAXONOMIC")
							{
								$subject = 	$relationsArr['SUBJECT'];
								$object = $relationsArr['OBJECT'];
								
								if ( !isset($finalConcepts[$subject]) && !isset($handled[$subject]))
								{
									echoN("NOT IN CONCEPTS:S:$subject");
									$notInCounceptsCounter++;
									
									$handled[$subject]=1;
									
									$termsArr = getTermArrBySimpleWord($finalTerms,$subject);
									
									$freq = $termsArr['FREQ'];
									
									$extra = array("POS"=>"SUBJECT","WEIGHT"=>$termsArr['WEIGHT']);
									$finalConcepts[$subject]=array("CONCEPT_TYPE"=>"A-BOX","EXTRACTION_PHASE"=>"POPULATION_FROM_RELATIONS","FREQ"=>$freq,"EXTRA"=>$extra);
										
								}
								if ( !isset($finalConcepts[$object]) && !isset($handled[$object]))
								{
									echoN("NOT IN CONCEPTS:O:$object");
									$notInCounceptsCounter++;
									
									$handled[$object]=1;
									
									$termsArr = getTermArrBySimpleWord($finalTerms,$object);
									
									$freq = $termsArr['FREQ'];
									
									$extra = array("POS"=>"OBJECT","WEIGHT"=>$termsArr['WEIGHT']);
									$finalConcepts[$object]=array("CONCEPT_TYPE"=>"A-BOX","EXTRACTION_PHASE"=>"POPULATION_FROM_RELATIONS","FREQ"=>$freq,"EXTRA"=>$extra);
									
									
									

									
								}
								
							}
							
							
							//file_put_contents("../data/ontology/temp.final.concepts", serialize($finalConcepts));
						}
						
						echoN("Concepts Added: $notInCounceptsCounter");
						
						preprint_r($finalConcepts);
						
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







