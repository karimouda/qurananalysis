<?php 

require_once("../global.settings.php");
require_once("../libs/core.lib.php");
require_once("../libs/microsoft.translator.api.lib.php");
require_once("../libs/pos.tagger.lib.php");
require_once("../libs/ontology.lib.php");
require_once("../libs/custom.translation.table.lib.php");

require_once("../libs/owllib/OWLLib.php");
require_once("../libs/owllib/reader/OWLReader.php");
require_once("../libs/owllib/memory/OWLMemoryOntology.php");
require_once("../libs/owllib/writer/OWLWriter.php");


$ONTOLOGY_EXTRACTION_FOLDER = "../data/ontology/extraction/";





$lang = "AR";





if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core,search,qac,qurana,wordnet",$lang);


$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = loadUthmaniToSimpleMappingTable();


$UTHMANI_TO_SIMPLE_LOCATION_MAP = apc_fetch("UTHMANI_TO_SIMPLE_LOCATION_MAP");

$LEMMA_TO_SIMPLE_WORD_MAP = loadLemmaToSimpleMappingTable();

$pauseMarksArr = getPauseMarksArrByFile($pauseMarksFile);







$WORDS_TRANSLATIONS_EN_AR = apc_fetch("WORDS_TRANSLATIONS_EN_AR");

$WORDS_TRANSLATIONS_AR_EN = apc_fetch("WORDS_TRANSLATIONS_AR_EN");

$WORDS_TRANSLITERATION = apc_fetch("WORDS_TRANSLITERATION");




	
$CUSTOM_TRANSLATION_TABLE_EN_AR = loadTranslationTable();




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
			  	$GENERATE_ADJECTIVE_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_FINAL_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  				  	
			  	
			 
			  	$GENERATE_NONTAXONOMIC_RELATIONS = FALSE;
			  	$EXTRACT_NEWCONCEPTS_FROM_RELATIONS = FALSE;
			  	$GENERATE_TAXONOMIC_RELATIONS = FALSE;
			  	
			  	
			  	$ENRICH_CONCEPTS_METADATA_TRANSLATION_TRANSLITERATION = FALSE;
			  	$ENRICH_CONCEPTS_METADATA_DBPEDIA = TRUE;
			  	$ENRICH_CONCEPTS_METADATA_WORDNET = TRUE;
			  	
			  	$EXCLUDE_CONCEPTS_AND_RELATIONS = TRUE;
			  	
			  	
			  	$FINAL_POSTPROCESSING = TRUE;
			  	
			  	$GENERATE_OWL_FILE = TRUE;
			  	
			
			  	
			  	$wordsInfoArr = unserialize(file_get_contents("../data/cache/words.info.all"));
			  		
			  	// not cahed yet
			  	if  ( empty($wordsInfoArr) )
			  	{
			  		foreach ($MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'] as $wordLabel => $wordFreqArr )
			  		{
			  	
			  			$wordsInfoArr[$wordLabel] = getWordInfo($wordLabel, $MODEL_CORE, $MODEL_SEARCH, $MODEL_QAC,true);
			  		}
			  	
			  		file_put_contents("../data/cache/words.info.all", serialize($wordsInfoArr));
			  	
			  	}
			  	
			  	

			  	$finalConcepts = array();
			  	$relationsArr = array();

					
			if ( $GENERATE_TERMS == true )
			{
			  	
			
			  		
			  	
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
											$termArr['AKA']['AR']['QURANA'] = $quranaArWord;
										}
										
										$termArr['EXTRA']['TRANSLATION_EN']=$conceptArr['EN'];
										
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
								
									
								
									
							
									addNewConcept($finalConcepts, $mergedWord, "A-BOX", "ADJECTIVE", $termArr['FREQ'], "");
									$finalConcepts[$mergedWord]['EXTRA']=$termArr;
								
								
								
								}
							}
						}
					}
					
					
					
		
					if ( $GENERATE_FINAL_CONCEPTS )
					{
						
						$quranaConceptsListArr  = $MODEL_QURANA['QURANA_CONCEPTS'];
						
						// "Thing" Concept
						addNewConcept($finalConcepts, $thing_class_name_ar, "T-BOX", "MANUAL", 0, $thing_class_name_en);
						
						
						$amxConceptFreq = -99;
						//$finalTerms
						foreach ($commonConceptsWithQurana as $concept=>$termArr)
						{

							
							addNewConcept($finalConcepts, $concept, "A-BOX", "TERM", $termArr['FREQ'], $engTranslation);
							$finalConcepts[$concept]['EXTRA']=$termArr;

					
							
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
							
							$quranaConceptArr = getQuranaConceptEntryByARWord($biGramConcept);
				
						
							$engTranslation = ucfirst($quranaConceptArr['EN']);
							
							addNewConcept($finalConcepts, $biGramConcept, "A-BOX", "PHRASE", $freq, $engTranslation);
							$finalConcepts[$biGramConcept]['EXTRA']['POS']=$pos;
							$finalConcepts[$biGramConcept]['EXTRA']['WEIGHT']=$weight;
							
							
						}
	
						rsortBy($finalConcepts,"FREQ");
						
						echoN("FINAL CONCEPTS COUNT:".count($finalConcepts));
					
						//preprint_r($finalConcepts);
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage1", serialize($finalConcepts));
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.all.terms", serialize($finalTerms));
					}
					
						
						
						
			
						
						
						
				
						
											
					if ( $GENERATE_NONTAXONOMIC_RELATIONS)
					{
						
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage1"));
						
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
										 					addRelation($relationsArr,$type,$concept1,$verb,$concept2,$joinedPattern);
										 				
										 					
										 	 			
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
							
						
						
					
					}
					
			
					
					$poTaggedSubsentences = getPoSTaggedSubsentences();
					
					//preprint_r($poTaggedSubsentences);exit;
					
					//echoN("SubSentences Count:".addCommasToNumber(count($poTaggedSubsentences)));
					
					
					
					
					
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
					
						
				
						
						/*
						$rules = file("$ONTOLOGY_EXTRACTION_FOLDER/lcs.postags.2plus.all.rules",FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);
						
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
													
												$verbSegmentArr = getQACSegmentByPos($qacWordSegmentsArr,"V");
												$verbLemma = $verbSegmentArr['FORM_AR'];
												//preprint_r($verbSegmentArr);
												//echoN($verbLemma);
												//preprint_r($pronounConceptArr);
												
												$isImperative = isset($verbSegmentArr['FEATURES']['IMPV']);
													
												// null concept
												if ( $subject=="null" ) continue;
												if ( $isImperative) continue;
												if ( empty($subject) || empty($object) ) continue;
													
													
												$verb = $wordsArr[$startArrayIndexOfPattern];;//$verbLemma;
													
												if ( preg_match("/NEG|PRO([ ]|\,)/", $prevPoS))
												{
													$verb = $prevWord." ".$verb;
												}
													
												$type = "NON-TAXONOMIC";
												addRelation($relationsArr,$type, $subject, $verb, $object, $rule);
												
											break;
											
											case "V PRON, P, N, PN":
												
											
												$qacWordLocation = $qacBaseLocation .":".($qacStartWordIndexInVerse);
												
												$pronounConceptArr = resolvePronouns($qacWordLocation);
												$subject = $pronounConceptArr[0];
									
												//qurana bug
												if ( empty($pronounConceptArr)) continue;
												
	
												$qacWordSegmentsArr = $MODEL_QAC['QAC_MASTERTABLE'][$qacWordLocation];
												
												$verbSegmentArr = getQACSegmentByPos($qacWordSegmentsArr,"V");
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
												
												$type = "NON-TAXONOMIC";
												
												addRelation($relationsArr, $type,$subject, $verb, $object, $rule);
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
													$nounSegmentArr = getQACSegmentByPos($qacWordSegmentsArr,"N");
													$nounSegment = $nounSegmentArr['FEATURES']['LEM'];
													
													/*echoN($qacWordLocationForSecondWord);
													preprint_r($qacWordSegmentsArr);
													preprint_r($nounSegmentArr);
													echoN($nounSegment);
													*/
													
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
													
												$verbSegmentArr = getQACSegmentByPos($qacWordSegmentsArr,"V");
												$verbLemma = $verbSegmentArr['FORM_AR'];
												//preprint_r($verbSegmentArr);
												//echoN($verbLemma);
												//preprint_r($pronounConceptArr);
												
												$isImperative = isset($verbSegmentArr['FEATURES']['IMPV']);
													
												// null concept
												if ( $subject=="null" ) continue;
												if ( $isImperative) continue;
													
													
												$verb = $verbLemma;//trimVerb($wordsArr[$startArrayIndexOfPattern]);//$verbLemma;
													
												if ( preg_match("/NEG|PRO([ ]|\,)/", $prevPoS))
												{
													$verb = $prevWord." ".$verb;
												}
													
											
												
												$type = "NON-TAXONOMIC";
												addRelation($relationsArr,$type, $subject, $verb, $object, $rule);
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
						
						
						echoN("FINAL NONTAXONOMIC RELATIONS :".count($relationsArr));
							
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($relationsArr));
						
					
					}
					
					

					
					
					
					 
					if ( $EXTRACT_NEWCONCEPTS_FROM_RELATIONS )
					{
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage1"));
						$finalTerms =  unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.all.terms"));
						
			
						echoN("BA-AA:".count($finalConcepts));
						
						$notInCounceptsCounter = 0;
						$handled = array();
						foreach($relationsArr as $hash => $relationsArr)
						{
							$relationsType = $relationsArr['TYPE'];
							
							if ( $relationsType =="NON-TAXONOMIC")
							{
								$subject = 	$relationsArr['SUBJECT'];
								$object = $relationsArr['OBJECT'];
								$verb = $relationsArr['VERB'];
								
								if ( !isset($finalConcepts[$subject]) && !isset($handled[$subject]))
								{
									echoN("NOT IN CONCEPTS:S:$subject");
									$notInCounceptsCounter++;
									
									$handled[$subject]=1;
									
									$termsArr = getTermArrBySimpleWord($finalTerms,$subject);
									
									$freq = $termsArr['FREQ'];
									
									
									
									if( isMultiWordStr($subject))
									{
										$quranaConceptArr = getQuranaConceptEntryByARWord($subject);
										
										
										$engTranslation = ucfirst($quranaConceptArr['EN']);
									}
									else 
									{
										$uthmaniWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$subject];
										$engTranslation = ucfirst(cleanEnglishTranslation($WORDS_TRANSLATIONS_AR_EN[$uthmaniWord]));
									}
									
									addNewConcept($finalConcepts, $subject, "A-BOX", "POPULATION_FROM_RELATIONS", $freq, $engTranslation);
									$finalConcepts[$subject]['EXTRA']['POS']="SUBJECT";
									$finalConcepts[$subject]['EXTRA']['WEIGHT']=$termsArr['WEIGHT'];
									
									
										
								}
								if ( !isset($finalConcepts[$object]) && !isset($handled[$object]))
								{
									echoN("NOT IN CONCEPTS:O:$object");
									$notInCounceptsCounter++;
									
									$handled[$object]=1;
									
									$termsArr = getTermArrBySimpleWord($finalTerms,$object);
									
									$freq = $termsArr['FREQ'];
									

									
										
										
									if( isMultiWordStr($object))
									{
										$quranaConceptArr = getQuranaConceptEntryByARWord($object);
									
									
										$engTranslation = ucfirst($quranaConceptArr['EN']);
									}
									else
									{
										$uthmaniWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$object];
										$engTranslation = ucfirst(cleanEnglishTranslation($WORDS_TRANSLATIONS_AR_EN[$uthmaniWord]));
									}
									
									
									
									addNewConcept($finalConcepts, $object, $conceptType, "POPULATION_FROM_RELATIONS", $freq, $engTranslation);
									$finalConcepts[$object]['EXTRA']['POS']="SUBJECT";
									$finalConcepts[$object]['EXTRA']['WEIGHT']=$termsArr['WEIGHT'];
									
									

									
								}
								
							}
							
							
							file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage2", serialize($finalConcepts));
						}
						
						echoN("Concepts Added: $notInCounceptsCounter");
						
						//preprint_r($finalConcepts);
					
						
						echoN("Final Concepts Count:". count($finalConcepts));
						
					}
					
					
					/*
					 * ONE NON-TAX IS STILL INSIDE
					* // TODO:TO BE MOVED or RMEOVED
					* // MOVED AFTER RELATION AEXTRACTION AND RELATION CONCEPT EXTRACTION SO WE CAN INFER HYPERNYMS
					*/
					if ( $GENERATE_TAXONOMIC_RELATIONS )
					{
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage2"));
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
							
						$countOfRelationsBefore = 	count($relationsArr);
							
						echoN("BA-B:".count($finalConcepts));
					
						////////////////////////// ADJECTIVE HYPERNYMS ////////////////////////////////
						$adjName = "صفة";
							
						$finalConcepts[$adjName]=array("CONCEPT_TYPE"=>"T-BOX","EXTRACTION_PHASE"=>"TAX-RELATIONS","FREQ"=>1,"EXTRA"=>generateEmptyConceptMetadata());
						$finalConcepts[$adjName]['EXTRA']['ENG_TRANSLATION']='Attribute';
							
							
						// ADJ PARENT + relations
						foreach($finalConcepts as $concept => $coneptArr)
						{
							$exPhase = $coneptArr['EXTRACTION_PHASE'];
					
					
							if ( $exPhase=="ADJECTIVE")
							{
								$type = "TAXONOMIC";
								addRelation($relationsArr,$type,$concept,"$is_a_relation_name_ar",$adjName,"ADJ","$is_a_relation_name_en");
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
								$hasAttribute = "من صفاتة";
								$type = "NON-TAXONOMIC";
								addNewRelation($relationsArr,$type,$concept,$hasAttribute,$adj1,"ADJ","has attribute");
									
									
							}
								
							if ( isset($finalConcepts[$concept]) && isset($finalConcepts[$adj2]) )
							{
								$hasAttribute = "من صفاتة";
								$type = "NON-TAXONOMIC";
								addNewRelation($relationsArr,$type,$concept,$hasAttribute,$adj2,"ADJ","has attribute");
									
									
							}
								
					
					
						}
							
						/////////////// PHRASE CONCEPTS HYPERNYMS (PARENT-CHILD) ///////////////
							
						foreach($finalConcepts as $concept => $conceptArr)
						{
					
							$type = $conceptArr['EXTRACTION_PHASE'];
							$pos = $conceptArr['EXTRA']['POS'];
					
							
							//if ( $type=="PHRASE")
							if ( preg_match_all("/ /", $concept)==1)
							{
								$biGramWords = preg_split("/ /",$concept);
									
									
								$parentConcept = $biGramWords[0];
									
								$wordInfoArr = $wordsInfoArr[$parentConcept];//getWordInfo($parentConcept, $MODEL_CORE, $MODEL_SEARCH, $MODEL_QAC,true);
									
									
								$parentPosArr = $wordInfoArr['POS'];
									
									
								//echoN("%%2:$parentConcept:".preprint_r($parentPosArr,true));
								// if the is a quanic word it has to be PN, N or ADJ
								if ( !empty($parentPosArr) && !isset($parentPosArr['PN']) && !isset($parentPosArr['N']) && !isset($parentPosArr['ADJ']) ) continue;
									
								$subclassConcept = $concept;
									
								if (!isset($finalConcepts[$parentConcept]))
								{
									$finalConcepts[$parentConcept]=array("CONCEPT_TYPE"=>"T-BOX","EXTRACTION_PHASE"=>"TAX-RELATIONS","FREQ"=>1,"EXTRA"=>generateEmptyConceptMetadata());
									$finalConcepts[$parentConcept]['EXTRA']['ENG_TRANSLATION']=cleanEnglishTranslation($WORDS_TRANSLATIONS_AR_EN[$parentConcept]);
								}
								else
								{
									// SHOULD SWITCH TO T-BOX SINCE IT IS A PARENT CLASS NOW - FOR OWL SERIALIZATION BUGS
									$finalConcepts[$parentConcept]['CONCEPT_TYPE']='T-BOX';
								}
									
									
								$hasType = "$is_a_relation_name_ar";
								$type = "TAXONOMIC";
								addRelation($relationsArr,$type,$subclassConcept,$hasType,$parentConcept,"$pos","$is_a_relation_name_en");
									
									
									
							}
								
								
								
						}
							
							
						///////////////////////////////////////////////////////////////////
						echoN("FINAL TAXONOMIC RELATIONS :".(count($relationsArr)-$countOfRelationsBefore));
					
						echoN("BA-A:".count($finalConcepts));
						//preprint_r($finalConcepts);exit;
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage3", serialize($finalConcepts));
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($relationsArr));
							
					}
					
				
					
					
					if ( $ENRICH_CONCEPTS_METADATA_TRANSLATION_TRANSLITERATION)
					{
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage3"));
						
			
				
						foreach($finalConcepts as $concept => $coneptArr)
						{
							
							$currentEnglishTranslation = $coneptArr['EXTRA']['TRANSLATION_EN'];
							$uthmaniWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$concept];
							
							if ( empty($currentEnglishTranslation))
							{
							
								
								
								echoN($uthmaniWord);
								
								$engTranslationB4Cleaning = $WORDS_TRANSLATIONS_AR_EN[$uthmaniWord];
								
								if ( empty($engTranslationB4Cleaning))
								{
									//try adding ال
									$conceptWithAL = "ال".$concept;
									//echoN($conceptWithAL);
									$uthmaniWordForTranslation = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$conceptWithAL];
									//echoN($uthmaniWordForTranslation);
									
									$engTranslationB4Cleaning = $WORDS_TRANSLATIONS_AR_EN[$uthmaniWordForTranslation];
								}
								//echoN($engTranslationB4Cleaning);
							
								
								$englishTranslation = cleanEnglishTranslation($engTranslationB4Cleaning);
								
								$finalConcepts[$concept]['EXTRA']['TRANSLATION_EN'] = $englishTranslation;
								//echoN("$uthmaniWord|$englishTranslation|$engTranslationB4Cleaning");
							}
							
							$englishTransliteration = $WORDS_TRANSLITERATION[$uthmaniWord];
							
							
							
							$finalConcepts[$concept]['EXTRA']['TRANSLITERATION_EN'] = $englishTransliteration;
								
							
						}
						
				
						
						preprint_r($finalConcepts);
						echoN(count($finalConcepts));
						//exit;
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage4", serialize($finalConcepts));

					}
					
					

					
				
					
					
					if ( $ENRICH_CONCEPTS_METADATA_DBPEDIA)
					{
						$newConcepts = array();
						$dbpediaCacheArr = array();
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage4"));
						$finalTerms =  unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.all.terms"));
						
						$dbpediaCacheArr = unserialize(file_get_contents("../data/cache/dbpedia.resources"));
						
						
					
						
						
						
						$typeNS = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
						$resourceIDTemplate = "http://live.dbpedia.org/resource/{NAME}";
						$urlTemplate = "http://live.dbpedia.org/data/{NAME}.json";
						$wikipediaIDTemplate = "http://en.wikipedia.org/wiki/{NAME}";
						
						
					
						$stopWordsArr = getStopWordsArrByFile($englishStopWordsFile);
						
						$typesArr = array();
						$conceptsFiltered=0;
						$conceptsEnriched=0;
						$newConceptsAdded = 0;
						$newRelationsAdded=0;
						
						$enrichedFinalConcepts = $finalConcepts;
						foreach($finalConcepts as $concept => $coneptArr)
						{
							
							
							
							$conceptNameEn  = $coneptArr['EXTRA']['TRANSLATION_EN'];
							$conceptNameAr  = $concept;;//$coneptArr['EXTRA']['SIMPLE_WORD'];
							
						
							
							
							if ( !empty($conceptNameEn) &&
								 preg_match("/ /",$conceptNameEn) == 0 &&
								!isset($stopWordsArr[strtolower($conceptNameEn)]) )
							{
								echoN("NOT FILTERED:$conceptNameEn|$conceptNameAr");
								$conceptsFiltered++;
							
							
							
								  $conceptName  = $conceptNameEn;
								  $url = str_replace("{NAME}", $conceptName, $urlTemplate);
								  $resourceID = str_replace("{NAME}", $conceptName, $resourceIDTemplate);
								  $wikipediaID = str_replace("{NAME}", $conceptName, $wikipediaIDTemplate);
								  
								  
								  if ( !isset($dbpediaCacheArr[$conceptName]))
								  {
								 
										
										
										
										echoN($url);
										echoN($resourceID);
										echoN($wikipediaID);
										
										$jsonText = file_get_contents($url);
										
										$jsonArr = json_decode($jsonText,true);
										
										
										if ( !empty($jsonArr))
										{
											// add to cache
											$dbpediaCacheArr[$conceptName]=$jsonArr;
										}
								  }
								  // get from cache
								  else
								  {
									 $jsonArr =  $dbpediaCacheArr[$conceptName];
								  }
											
											
									$typesArr = $jsonArr[$resourceID][$typeNS];
									$abstract = $jsonArr[$resourceID]["http://live.dbpedia.org/ontology/abstract"][0]['value'];
								
									
									
									if ( empty($typesArr) || empty($abstract))
									{
										echoN("--NO TYPE OR ABSTRACT");
									}
									else
									{
										
										$typesArrFiltered = array();
											
										foreach($typesArr as $index => $typeArr)
										{
											$typeValue = $typeArr['value'];
										
											
											
											if ( strpos($typeValue, "schema.org")!==false ||
											strpos($typeValue, "dbpedia.org/ontology")!==false ||
											strpos($typeValue, "xmlns.com/foaf")!==false ||
											strpos($typeValue, "umbel.org")!==false ||

											strpos($typeValue, "yago/Person")!==false	)
											{
												
												
												if ( strpos($typeValue, "yago/Person")!==false )
												{
													$typeName = "Person";
												}
												else
												{
													$typeName = substr($typeValue, strrpos($typeValue, "/")+1);
												}
												
												if ( $typeName== "Agent" || $typeName=="BiologicalLivingObject") continue;
												
									
												
												$typesArrFiltered[$typeName]= $typeValue;
											}
										}
											
										$thumbnail = $jsonArr[$resourceID]["http://live.dbpedia.org/ontology/thumbnail"][0]['value'];
										$depiction = $jsonArr[$resourceID]["http://xmlns.com/foaf/0.1/depiction"][0]['value'];
										
										$finalImage = $thumbnail;
										
										if ( empty($finalImage) )
										{
											$finalImage = $depiction;
										}
										
										
											
										$namesArr = $jsonArr[$resourceID]["http://xmlns.com/foaf/0.1/name"];
										
										$filteredNames = array();
										foreach($namesArr as $index => $nameArr)
										{
											$filteredNames[]=$nameArr['value'];
										}
										
										echoN("Names:".join(",",$filteredNames));
										echoN("Image:".$finalImage);
										echoN("Types:".join(",",array_keys($typesArrFiltered)));
										echoN("Abstract:".$abstract);
										echoN("URL:".$url);
										
										//preprint_r($typesArr);
										//preprint_r($jsonArr);
										//exit;
										
										$parentClassConceptsArr = array();
										
										foreach($typesArrFiltered as $type=>$url)
										{
											$parentClassConceptsArr[$type]=1;
										}
										 
										
										$conceptType = getConceptTypeFromDescriptionText($abstract);
										
										if ( !empty($conceptType))
										{
											$conceptType = ucfirst(strtolower($conceptType));
											
											$parentClassConceptsArr[$conceptType]=1;
										}
									
										//preprint_r($parentClassConceptsArr);
										
										
										///////// enrich concept 
										
										$enrichedFinalConcepts[$concept]['EXTRA']['DBPEDIA_LINK']=$resourceID;
										$enrichedFinalConcepts[$concept]['EXTRA']['WIKIPEDIA_LINK']=$wikipediaID;
										$enrichedFinalConcepts[$concept]['EXTRA']['IMAGES']['DBPEDIA']=$finalImage;
										
										$enrichedFinalConcepts[$concept]['EXTRA']['DESC_EN']['DBPEDIA']=$abstract;
										//$enrichedFinalConcepts[$concept]['EXTRA']['ASA']=array_merge($filteredNames,$finalConcepts[$concept]['EXTRA']['ASA']);
										
										
										
										//preprint_r($enrichedFinalConcepts);
										///////////////////////
										
										///////// add parent concepts
										
										
										
										
										preprint_r($parentClassConceptsArr);
										foreach($parentClassConceptsArr as   $parentConceptEN=>$dummy)
										{
											$exPhase = "ENRICHMENT_DBPEDIA";
											$parentConceptEN = ucfirst($parentConceptEN);
											
											
											//////////// NEW DBPEDIA CONCEPT HANDLING
											$parentConceptName = $parentConceptEN;
											
											if (!isFoundInTranslationTable($parentConceptEN))
											{
												//echoN("|$parentConceptEN|");
												$tentitaveTranslation = translateText($parentConceptEN);
												
												
												
												addTranslationEntry($parentConceptEN, "CONCEPT",$tentitaveTranslation );
												
												$parentConceptName = $tentitaveTranslation;
											}
											else 
											{
												$customTranslationEntry = getTranlationEntryByEntryKeyword($parentConceptEN);
													
												
												$parentConceptName  = $customTranslationEntry['AR_TEXT'];
											}
									
											
											if ( !isset($enrichedFinalConcepts[$parentConceptName]))
											{
												$enrichedFinalConcepts[$parentConceptName]=array("CONCEPT_TYPE"=>"T-BOX","EXTRACTION_PHASE"=>$exPhase,"FREQ"=>1,"EXTRA"=>array("AKA"=>array(),"TRANSLATION_EN"=>$parentConceptEN));
												$newConceptsAdded++;
												
												$newConcepts[$parentConceptName]=1;
												
											}
											else 
											{
												/*
												 * WILL NOT DO IT HERE SINCE SOME RELATIONS ARE EXCLUDED LATER, SO OPERATIONS DONE HERE
												* CAN'T BE REVERTED BACK, WILL BE MOVED AFTER EXCLUSION INSTEAD
												*/
												// SHOULD SWITCH TO T-BOX SINCE IT IS A PARENT CLASS NOW - FOR OWL SERIALIZATION BUGS
												//$enrichedFinalConcepts[$parentConceptName]['CONCEPT_TYPE']='T-BOX';
											}
												
											
											
												$type = "TAXONOMIC";
												addRelation($relationsArr,$type,$concept,"$is_a_relation_name_ar",$parentConceptName,"","$is_a_relation_name_en");
												
											//////////////////////////////////////////////////////
											
										}
										
							
										
										echoN("--------------------------<BR>ENRICHED:$conceptNameEn|$conceptNameAr");
											
										$conceptsEnriched++;
										
										
										
									}

											
											
											
											
											
							}
										
										
										
										
						  
							
							
					}
					
					
					/*$url = "https://ar.wikipedia.org/w/api.php?action=query&titles=الأرض&prop=revisions&rvprop=content&format=json";
							
					$jsonText = file_get_contents($url);
										
					$jsonArr = json_decode($jsonText,true);
					
					preprint_r($jsonArr);
					*/
										
							//preprint_r($relationsArr);exit;
							
							//$typesArr = $jsonArr[$resourceID][$typeNS];
							
							//preprint_r($typesArr);
							
							//preprint_r($jsonArr);
							
							//exit;
							
						preprint_r($newConcepts);
								
								
							
						
						
						echoN("Concepts Filtered:$conceptsFiltered");
						echoN("Concepts Enriched from (DBPEDIA):$conceptsEnriched");
						
						echoN("New Concepts Added:$newConceptsAdded");
						
						
						file_put_contents("../data/cache/dbpedia.resources", serialize($dbpediaCacheArr));
						
					
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage5", serialize($enrichedFinalConcepts));
						
			
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($relationsArr));
					}
					
					
					if ( $ENRICH_CONCEPTS_METADATA_WORDNET)
					{
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage5"));
						$finalTerms =  unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.all.terms"));

						
						
						
						
					
					
					
						
						
						$conceptsEnriched=0;
						$newConceptsAdded = 0;
						$newRelationsAdded=0;
						
						$newConceptsAddedArr = array();
						$enrichedFinalConcepts = $finalConcepts;
						////// ENRICJ ALL CONCEPT USING WORDNET MODEL
						foreach($finalConcepts as $concept => $coneptArr)
						{
								
						
								
							$conceptNameEn  = $coneptArr['EXTRA']['TRANSLATION_EN'];
							$conceptNameAr  = $concept;;//$coneptArr['EXTRA']['SIMPLE_WORD'];
										
							$conceptNameEn = removeStopwordsAndTrim($conceptNameEn,"EN");
							
					
							$lexicoSemanticCategories = apc_fetch("WORDNET_LEXICO_SEMANTIC_CATEGORIES");
							
							$wordnetInfoArr = getWordnetEntryByWordString($conceptNameEn);
							
							if ( empty($wordnetInfoArr))
							{
							
								if ( $conceptNameEn[(strlen($conceptNameEn)-1)]=="s")
								{
									echoN($conceptNameEn);
									echoN("=============".substr($conceptNameEn,0,-1));
									//try removing plural (s)
									$wordnetInfoArr = getWordnetEntryByWordString(substr($conceptNameEn,0,-1));
								}
							}
							
							/*if ($conceptNameEn=="Land")
							{
								preprint_r($wordnetInfoArr);
								$wordnetIndex  = apc_fetch("WORDNET_INDEX");
								
								$lexicoSemanticCategories = apc_fetch("WORDNET_LEXICO_SEMANTIC_CATEGORIES");
								
								$dataArr = apc_fetch("WORDNET_DATA");
								
								$lowerName  = strtolower($conceptNameEn);
								
								//preprint_r($wordnetIndex[$lowerName]);
								
								foreach($wordnetIndex[$lowerName]['noun']['SYNSETS'] as $index => $pointer)
								{
									
									//preprint_r($dataArr['noun'][$pointer]);
									$semantic = $dataArr['noun'][$pointer]['SEMANTIC_CATEGORY_ID'];
									$wordsStr = join(",",array_keys($dataArr['noun'][$pointer]['WORDS']));
								
									
									echoN("$pointer|$semantic|$wordsStr");
									
									foreach($dataArr['noun'][$pointer]['POINTERS'] as $index => $arr)
									{
										if ( $arr['SYMBOL_DESC']!="Hypernym")
										{
											continue;
										}
										
										echoN($arr['SYNSET_OFFSET']."-".$arr['SYMBOL_DESC']);
									}
									
								}
								
							}*/
							
							// DATA FOUND IN WORDNET FOR THE CURRENT CONCEPT
							if ( !empty($wordnetInfoArr) )
							{
								$conceptsEnriched++;
								
								$qacPOS = $coneptArr['EXTRA']['POS'];
								$wordnetPOS = mapQACPoSToWordnetPoS($qacPOS);
								
								
								
								
								preprint_r($coneptArr);
								
								$wordnetInfoArrayForConcept = $wordnetInfoArr;
								
								preprint_r($wordnetInfoArrayForConcept);
								
							

								
								$conceptMeaningEN = getGlossaryFirstPart($wordnetInfoArr['GLOSSARY'][$wordnetPOS]);
								
								$conceptMeaningAR = "";
								
								//////////// MEANING TRANSLATION 
								if (!isFoundInTranslationTable($conceptMeaningEN))
								{
									echoN("|$conceptMeaningEN|");
									$tentitaveTranslation = translateText($conceptMeaningEN);
									addTranslationEntry($conceptMeaningEN, "DESC",$tentitaveTranslation );
									
									$conceptMeaningAR = $tentitaveTranslation;
								}
								else
								{
									$customTranslationEntry = getTranlationEntryByEntryKeyword($conceptMeaningEN);
										
								
									$conceptMeaningAR  = $customTranslationEntry['AR_TEXT'];
								}
								/////////////////////////////////////////////
								
								
								////////////////////// MEANING ENRICHMENT 
								$enrichedFinalConcepts[$concept]['EXTRA']['MEANING_EN']['WORDNET']=$conceptMeaningEN;
								$enrichedFinalConcepts[$concept]['EXTRA']['MEANING_AR']['WORDNET']=$conceptMeaningAR;
								////////////////////////////////////////
								
								$handledSemanticTypes = array();
								
								///////////////// ENRICH CONCEPT BY WORDNET SEMANTIC TYPES
								foreach($wordnetInfoArr['SEMANTIC_TYPES'][$wordnetPOS] as $dummy => $semanticType)
								{
									
									if ( isset($handledSemanticTypes[$semanticType])) continue;
									
									if ( isExcludableSemanticType($semanticType)) continue;
									
									$handledSemanticTypes[$semanticType] = 1;
									

									$englishConceptName = $semanticType;
									$finalConceptName = $englishConceptName;
									
									if (isFoundInTranslationTable($finalConceptName))
									{
										$customTranslationEntry = getTranlationEntryByEntryKeyword($finalConceptName);
									
										$finalConceptName = $customTranslationEntry['AR_TEXT'];
									}
									else
									{
										$tentitaveTranslation = translateText($finalConceptName);
										
										
										
										addTranslationEntry($englishConceptName, "CONCEPT",$tentitaveTranslation );
										
										$finalConceptName = $tentitaveTranslation;
									}
									
									// DIDN'T FIND NEITHER ARABIC OR ENGLISH CONCEPTS IN FINAL CONCEPTS LIST
									if ( !isset($finalConcepts[$finalConceptName]))
									{
											
									
										
										$exPhase = "ENRICHMENT_WORDNET";
										
										//$parentConceptEN = ucfirst($parentConceptEN);
										$conceptType = "T-BOX";
										
									
										
										
										$res = addNewConcept($enrichedFinalConcepts,$finalConceptName,$conceptType,$exPhase,1 ,$englishConceptName);
										
										
										
										if ( $res==true)
										{
											echoN("$finalConceptName|$concept|$englishConceptName");
											$newConceptsAdded++;
											
											$newConceptsAddedArr[$finalConceptName]=1;
										}
										
										
										
										///////////////////// ENRICH NEWLY ADDED CONCEPT
										
										$semanticTypeWordInfoArr = getWordnetEntryByWordString($englishConceptName);
										
										
										
										$conceptMeaningEN = getGlossaryFirstPart($semanticTypeWordInfoArr['GLOSSARY'][$wordnetPOS]);
										
										if (isFoundInTranslationTable($glossary))
										{
											$customTranslationEntry = getTranlationEntryByEntryKeyword($glossary);
										
											$glossaryAR = $customTranslationEntry['AR_TEXT'];
										}
										else
										{
											$tentitaveTranslation = translateText($glossary);
										
										
										
											addTranslationEntry($glossary, "DESC",$tentitaveTranslation );
												
											$glossaryAR = $tentitaveTranslation;
										}
										
										
										$enrichedFinalConcepts[$finalConceptName]['EXTRA']['MEANING_EN']['WORDNET']=$glossary;
										$enrichedFinalConcepts[$finalConceptName]['EXTRA']['MEANING_AR']['WORDNET']=$glossaryAR;
											
										$synonymsArr = trim($semanticTypeWordInfoArr['SYNONYMS'][$wordnetPOS]);
										
										foreach($synonymsArr as  $synonym => $dummy)
										{
												
											if ( $synonym!=$finalConceptName)
											{
												$enrichedFinalConcepts[$finalConceptName]['EXTRA']['AKA']['EN']['WORDNET']=cleanWordnetCollocation($synonym);
										
											}
										
										
										}
										
										//////////////////////////////////////////////////////
										
									}
									else 
									{
									
										/*
										 * WILL NOT DO IT HERE SINCE SOME RELATIONS ARE EXCLUDED LATER, SO OPERATIONS DONE HERE
										* CAN'T BE REVERTED BACK, WILL BE MOVED AFTER EXCLUSION INSTEAD
										*/
										
										// avoid ALLAH is a ALLAH cases
										/*if ( $concept!=$finalConceptName)
										{
											echoN("##$finalConceptName|T-BOX");
											
											echoN("$concept,$is_a_relation_name_ar,$finalConceptName");
											
											$enrichedFinalConcepts[$finalConceptName]['CONCEPT_TYPE']='T-BOX';
										}
										*/
									}
									
											
										$relationType = "TAXONOMIC";
										$res = addRelation($relationsArr,$relationType,$concept,"$is_a_relation_name_ar",$finalConceptName,"$is_a_relation_name_en");
										
										if ( $res==true)
										{
											$newRelationsAdded++;
										}
										
									
									
								}
								
								$synonymsArr = $wordnetInfoArr['SYNONYMS'][$wordnetPOS];
								
								foreach($synonymsArr as $index => $synonym)
								{
									//echoN("------$synonym!=$conceptNameEn");
									if ( $synonym!=$conceptNameEn)
									{
										$enrichedFinalConcepts[$concept]['EXTRA']['AKA']['EN']['WORDNET']=cleanWordnetCollocation($synonym);
										
									}
									
									
								}
								
								
								foreach($wordnetInfoArr['RELATIONSHIPS'][$wordnetPOS] as $relIndex => $relArr)
								{
										$relType = $relArr['RELATION'];
										
										if ( stripos($relType,"Hypernym")!==false)
										{
											$wordsArr = $relArr['WORDS'];
											$semanticTypeID = $relArr['SEMANTIC_CATEGORY_ID'];
											$semanticType = $lexicoSemanticCategories[$semanticTypeID];
												
											$semanticType = ucfirst(substr($semanticType, strpos($semanticType, ".")+1));
											

											
											$glossary = getGlossaryFirstPart($relArr['GLOSSARY']);
											
											$hypernym = key($wordsArr);
											
											echoN("==|$hypernym|");
											
												if ( !isset($handledSemanticTypes[$hypernym]))
												{
													
													$handledSemanticTypes[$hypernym] = 1;
														
													$englishConceptName = cleanWordnetCollocation($hypernym);
													
													$finalConceptName = $englishConceptName;
														
													if (isFoundInTranslationTable($englishConceptName))
													{
														$customTranslationEntry = getTranlationEntryByEntryKeyword($englishConceptName);
													
														$finalConceptName = $customTranslationEntry['AR_TEXT'];
													}
													else
													{
														$tentitaveTranslation = translateText($englishConceptName);
													
													
													
														addTranslationEntry($englishConceptName, "CONCEPT",$tentitaveTranslation );
														
														$finalConceptName = $tentitaveTranslation;
													}
													
													
													// DIDN'T FIND NEITHER ARABIC OR ENGLISH CONCEPTS IN FINAL CONCEPTS LIST
													if ( !isset($finalConcepts[$finalConceptName]))
													{
															
															
													
														
														
														$exPhase = "ENRICHMENT_WORDNET";
													
														//$parentConceptEN = ucfirst($parentConceptEN);
														$conceptType = "T-BOX";
													
														
														
												
													
														$res = addNewConcept($enrichedFinalConcepts,$finalConceptName,$conceptType,$exPhase,1 ,$englishConceptName);
													
														if ( $res==true)
														{
															
															$newConceptsAdded++;
																
															$newConceptsAddedArr[$finalConceptName]=1;
														}
														
														$enrichedFinalConcepts[$finalConceptName]['EXTRA']['MEANING_EN']['WORDNET']=$glossary;
														
														
														if (isFoundInTranslationTable($glossary))
														{
															$customTranslationEntry = getTranlationEntryByEntryKeyword($glossary);
														
															$glossaryAR = $customTranslationEntry['AR_TEXT'];
														}
														else
														{
															$tentitaveTranslation = translateText($glossary);
																
																
																
															addTranslationEntry($glossary, "DESC",$tentitaveTranslation );
															
															$glossaryAR = $tentitaveTranslation;
														}
														
														
														
														$enrichedFinalConcepts[$finalConceptName]['EXTRA']['MEANING_AR']['WORDNET']=$glossaryAR;
													
														
														foreach($wordsArr as  $synonym => $dummy)
														{
															
															if ( $synonym!=$parentConceptName)
															{
																$enrichedFinalConcepts[$finalConceptName]['EXTRA']['AKA']['EN']['WORDNET']=cleanWordnetCollocation($synonym);
														
															}
																
																
														}
														
													}
													else
													{

														
														//echoN("##$finalConceptName|T-BOX");
														//echoN("$concept,$is_a_relation_name_ar,$finalConceptName");
														/*
														 * WILL NOT DO IT HERE SINCE SOME RELATIONS ARE EXCLUDED LATER, SO OPERATIONS DONE HERE
														 * CAN'T BE REVERTED BACK, WILL BE MOVED AFTER EXCLUSION INSTEAD
														 */
														
													
														//$enrichedFinalConcepts[$finalConceptName]['CONCEPT_TYPE']='T-BOX';
													}
														
														
													$relationType = "TAXONOMIC";
													$res = addRelation($relationsArr,$relationType,$concept,"$is_a_relation_name_ar",$finalConceptName,"$is_a_relation_name_en");
													
													if ( $res==true)
													{
														$newRelationsAdded++;
													}
												}
											
											
										}
										
									
								}
								
								
								
								
								
								
								
								
							}
							//if ( $conceptsEnriched>3) break;
						
						
							
						}
						
						echoN("Concepts Enriched from (WORDNET):$conceptsEnriched");
						
						echoN("New Concepts Added:$newConceptsAdded");
						echoN("New Relations Added:$newRelationsAdded");
						
						
						
						preprint_r($newConceptsAddedArr);
						preprint_r($enrichedFinalConcepts);
				
						
						
							
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage6", serialize($enrichedFinalConcepts));
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($relationsArr));
						
					}
					
					
					if ($EXCLUDE_CONCEPTS_AND_RELATIONS)
					{
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage6"));

						//preprint_r($relationsArr);
						//preprint_r($finalConcepts);
						
						$conceptsRemoved=0;
						$relationsRemoved = 0;

						$filteredConcepts = array();
						

						
						$EXCLUDED_CONCEPTS = loadExcludedConceptsArr();
						
						preprint_r($EXCLUDED_CONCEPTS);
						
						/// CONCEPTS
						foreach($finalConcepts as $concept => $coneptArr)
						{
								
						
								
							$conceptNameEn  = $coneptArr['EXTRA']['TRANSLATION_EN'];
							$conceptNameAr  = $concept;
							
							echoN("$conceptNameAr|$conceptNameEn|".isset( $EXCLUDED_CONCEPTS[$conceptNameAr]));
							
							if (isset( $EXCLUDED_CONCEPTS[$conceptNameAr]) || isset( $EXCLUDED_CONCEPTS[$conceptNameEn]) )
							{
								
								$conceptsRemoved++;
								continue;
							}

							
							
							$filteredConcepts[$concept]=$coneptArr;
							
							
						}
						
						
						/// RELATIONS FILTER
						
						$RELATIONS_EXCLUSION_RULES = array();
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"ابن","OBJECT"=>"الله");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"الله","VERB"=>"*","OBJECT"=>"الشخص");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"قال","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"إنسان","VERB"=>"$is_a_relation_name_ar","OBJECT"=>"حيوان");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"ناس","VERB"=>"$is_a_relation_name_ar","OBJECT"=>"حيوان");
						//DBpedia mess
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"صيد","VERB"=>"*","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"أنثى","VERB"=>"$is_a_relation_name_ar","OBJECT"=>"حيوان");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"مرء","VERB"=>"$is_a_relation_name_ar","OBJECT"=>"حيوان");
						
						
						
						
						$filteredRelationsArr = array();
						
						foreach($relationsArr as $hash => $relationArr)
						{
							$relationsType = $relationArr['TYPE'];
							
							$subject = 	$relationArr['SUBJECT'];
							$object = $relationArr['OBJECT'];
							$verbSimple = $relationArr['VERB'];
							
							
							if ( $subject==$object)
							{
								$relationsRemoved++;
								continue;
							}
							
						
							//echoN("$subject|$object|".isset($EXCLUDED_CONCEPTS[$subject]));
							
							// IF CONCEPTS ARE EXCLUDED, RELATIONS ARE ALSO EXSCLUDED
							if ( isset($EXCLUDED_CONCEPTS[$subject]) || isset($EXCLUDED_CONCEPTS[$object]))
							{
								$relationsRemoved++;
								continue;
							}
								
							
							$ruleFlag = false;
							foreach($RELATIONS_EXCLUSION_RULES as $index=>$ruleArr)
							{
								if ( 
									($ruleArr["SUBJECT"]=="*" || $ruleArr["SUBJECT"]==$subject) &&
									($ruleArr["VERB"]=="*" || $ruleArr["VERB"]==$verbSimple) &&
									($ruleArr["OBJECT"]=="*" || $ruleArr["OBJECT"]==$object) 
						           )
								   {
								      $relationsRemoved++;
								      $ruleFlag = true;
									  break;
								   }
								   
									// NOT USED
								   // CONJUNCTION SUBJECT IN IS-A RELATION
								   if ( $verbSimple==$is_a_relation_name_ar &&
										mb_strpos($subject, " و")!==false)
								   {
								   	
									   	$relationsRemoved++;
									   	$ruleFlag = true;
									   	break;
								   }
							}
							
							if ( $ruleFlag == true)
							{
								$relationsRemoved++;
								continue;
							}
							
							
							
							$filteredRelationsArr[$hash] = $relationArr;
							
						}
						
						
						
						
								
						
						echoN("Concepts Removed:$conceptsRemoved");
						echoN("Relations Removed:$relationsRemoved");

						echoN(count($filteredRelationsArr));
						
						
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage7", serialize($filteredConcepts));
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($filteredRelationsArr));
						
					}
					
					
					if ($FINAL_POSTPROCESSING)
					{
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage7"));
							
						
						foreach($relationsArr as $hash => $relationArr)
						{
							$relationsType = $relationArr['TYPE'];
								
							$subject = 	$relationArr['SUBJECT'];
							$object = $relationArr['OBJECT'];
							$verbAR = $relationArr['VERB'];
							
							// IF IT IS AN IS-A RELATION
							if ( $verbAR==$is_a_relation_name_ar)
							{
								// MAKE SURE THE PARENT IS A T-BOX ( CLASS NOT INSTANCE )
								$finalConcepts[$object]['CONCEPT_TYPE']='T-BOX';
							}
							
							
							
						}
						
						foreach($finalConcepts as $concept => $coneptArr)
						{
						
						
						
							$conceptNameEn  = $coneptArr['EXTRA']['TRANSLATION_EN'];
							$conceptNameAr  = $concept;
							$conceptType = $coneptArr['CONCEPT_TYPE'];
							
							// FIX CONCEPT TYPE FOR ANY CONCEPT WITH EXCLUDED RELATIONS
							if ( $conceptNameAr!=$thing_class_name_ar && $conceptType=="T-BOX" && !conceptHasSubclasses($relationsArr,$conceptNameAr) )
							{
								$finalConcepts[$concept]['CONCEPT_TYPE']='A-BOX';
							}
							
						}
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage8", serialize($finalConcepts));
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($relationsArr));
							
					}
					
					
					
					
					
					//////////// COPY/FI FILIZE FINAL CONCEPTS /////////////////////////////
					//persistTranslationTable();
					
					$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage8"));
					
					file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.final", serialize($finalConcepts));
					
					////////////////////////////////////////////////////////////////////////
					
					
					if ($GENERATE_OWL_FILE)
					{
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.final"));
			
						preprint_r($finalConcepts);
					
						
						///////// ONTOLOGY MODEL INIT
						$writer = new OWLWriter();
						$ontology = new OWLMemoryOntology();
						$thingClassName = $thing_class_name_ar;
						//$thingClassID = "$qaOntologyNamespace#$thingClassName";
						$ontology->setNamespace($qaOntologyNamespace);
						////////////////////////////
					
					
						///////// ADD COMMON PROPERTIES

						////////////////////////////
					
						///////// ADD ANNOTATION PROPERTIES
						$ontology->createAnnotationProperty("frequency");
						$ontology->createAnnotationProperty("pos");
						$ontology->createAnnotationProperty("weight");
						$ontology->createAnnotationProperty("transliteration");
						$ontology->createAnnotationProperty("meaning_wordnet_en");
						$ontology->createAnnotationProperty("meaning_wordnet_translated_ar");
						$ontology->createAnnotationProperty("meaning_wordnet_translated_ar");
						
						
						$ontology->createAnnotationProperty("lemma");
						$ontology->createAnnotationProperty("root");
						$ontology->createAnnotationProperty("dbpedia_link");
						$ontology->createAnnotationProperty("wikipedia_link");
						$ontology->createAnnotationProperty("image_url");
						$ontology->createAnnotationProperty("long_description_en");
						$ontology->createAnnotationProperty("long_description_ar");
						
						$ontology->createAnnotationProperty("synonym_wordnet_1");
						$ontology->createAnnotationProperty("synonym_qa_1");
						
						
						/////////////////////////////
						
						//////////  Things class
						//$ontology->createClass($thingClassName);
						
						
						//preprint_r($finalConcepts);
						
						$counter++;
						foreach($finalConcepts as $concept => $coneptArr)
						{
							
							
							$conceptType  = $coneptArr['CONCEPT_TYPE'];
							$conceptNameEn  = removeBasicEnglishStopwordsNoNegation($coneptArr['EXTRA']['TRANSLATION_EN']);
							$conceptNameAr  = $concept;
							
							$classID = getXMLFriendlyString($conceptNameAr);
							
							$classOrInstanceName = $classID;
							if ( $conceptType=="T-BOX")
							{
								
									
								$ontology->createClass($classOrInstanceName);
							}
							else
							{
								$classOrInstanceName = $classID;
								
								$ontology->addInstance($classOrInstanceName, $thingClassName , $properties);
							}
							
							
							$ontology->addLabel($classOrInstanceName, "AR", $conceptNameAr);
							$ontology->addLabel($classOrInstanceName, "EN", $conceptNameEn);
							
					
							
							$conceptType  = $coneptArr['CONCEPT_TYPE'];
							$extractionPhase  = $coneptArr['EXTRACTION_PHASE'];
							$frequency  = $coneptArr['FREQ'];
							
							$transliteration = $coneptArr['EXTRA']['TRANSLITERATION_EN'];
							$meaningArArr  = $coneptArr['EXTRA']['MEANING_AR'];
							$meaningEnArr  = $coneptArr['EXTRA']['MEANING_EN'];
							$pos  = $coneptArr['EXTRA']['POS'];
							$weight  = $coneptArr['EXTRA']['WEIGHT'];
							
							
							$lemma = $coneptArr['EXTRA']['LEM'];
							$root = $coneptArr['EXTRA']['ROOT'];
							
							$dbPediaLink = $coneptArr['EXTRA']['DBPEDIA_LINK'];
							
							$wikipediaLink = $coneptArr['EXTRA']['WIKIPEDIA_LINK'];
							
							$imageURL =  $coneptArr['EXTRA']['IMAGES']['DBPEDIA'];
							$lonDescEN = $coneptArr['EXTRA']['DESC_EN']['DBPEDIA'];
							$lonDescAR = $coneptArr['EXTRA']['DESC_AR']['DBPEDIA'];
							
							$alsoKnownAsENArr = $coneptArr['EXTRA']['AKA']['EN'];
							$alsoKnownAsARArr = $coneptArr['EXTRA']['AKA']['AR'];
							
							
							$ontology->addAnnotation($classOrInstanceName,"EN","frequency",$frequency);
							$ontology->addAnnotation($classOrInstanceName,"EN","weight",$weight);
							$ontology->addAnnotation($classOrInstanceName,"EN","pos",$pos);
							$ontology->addAnnotation($classOrInstanceName,"EN","transliteration",htmlspecialchars($transliteration));
							
							$ontology->addAnnotation($classOrInstanceName,"AR","lemma",$lemma);
							$ontology->addAnnotation($classOrInstanceName,"AR","root",$root);
							
							
							$ontology->addAnnotation($classOrInstanceName,"EN","meaning_wordnet_en",$meaningEnArr['WORDNET']);
							$ontology->addAnnotation($classOrInstanceName,"AR","meaning_wordnet_translated_ar",$meaningArArr['WORDNET']);
							
							$ontology->addAnnotation($classOrInstanceName,"EN","dbpedia_link",$dbPediaLink);
							$ontology->addAnnotation($classOrInstanceName,"EN","wikipedia_link",$wikipediaLink);
							$ontology->addAnnotation($classOrInstanceName,"EN","image_url",$imageURL);
							$ontology->addAnnotation($classOrInstanceName,"EN","long_description_en",$lonDescEN);
							$ontology->addAnnotation($classOrInstanceName,"AR","long_description_ar",$lonDescAR);
							
							$counter = 0;
							foreach($alsoKnownAsENArr as $source=>$synonym)
							{
								$counter++;
								$ontology->addAnnotation($classOrInstanceName,"EN","synonym_$source_$counter",$synonym);
							}
							$counter = 0;
							foreach($alsoKnownAsARArr as $source=>$synonym)
							{
								$counter++;
								$ontology->addAnnotation($classOrInstanceName,"AR","synonym_$source_$counter",$synonym);
							}
							
							
							
						}
						
						$ontologyRelationsCount= 0;
						
						echoN("--".count($relationsArr));
						foreach($relationsArr as $hash => $relationArr)
						{
							$relationsType = $relationArr['TYPE'];
								
							$subject = 	$relationArr['SUBJECT'];
							$object = $relationArr['OBJECT'];
							$verbSimple = $relationArr['VERB'];
							
							$subjectID = getXMLFriendlyString($subject);
							$objectID = getXMLFriendlyString($object);
							$verbID = getXMLFriendlyString($verbSimple);
							
							
							
							if ( $relationsType=="TAXONOMIC")
							{
								
								
								
								
								
								$ontology->addInstance($subjectID, $objectID, null);
								$ontologyRelationsCount++;
							}
							else
							{
								//echoN("!!! $subjectID|$verbSimple|$objectID|$relationsType");
								
								//add object and data properties
								
								$propertyObj = $ontology->getProperty($verbID);
								
								if ( $propertyObj==null)
								{
									$propertyObj = $ontology->createProperty($verbID, "", "", false);
								}
								
								$relFreq = $relationArr['FREQ'];
								$verbEnglishTranslation= $relationArr['VERB_ENG_TRANSLATION'];
								$verbUthmani = $relationArr['VERB_UTHMANI'];
								
								$relationMetaData = array("frequency"=>$relFreq,"verb_translation_en"=>$verbEnglishTranslation,"verb_uthmani"=>$verbUthmani);
								
								
								$properties = array($verbID=>array($objectID),"RELATION_META"=>$relationMetaData);
	
								if ( $finalConcepts[$subjectID]['CONCEPT_TYPE']=="T-BOX")
								{
									$ontology->addProperty($subjectID,$properties,"CLASS");
								}
								else
								{
									$ontology->addProperty($subjectID,$properties);
								}
							
								
								$ontologyRelationsCount++;
							
								
							}
							
							
							
							
							
							
						}
						
						echoN("INSTANCES COUNT:".count($ontology->{'owl_data'}['instances']));
						echoN("CLASSES COUNT:".count($ontology->{'owl_data'}['classes']));
						echoN("PROPERTIES COUNT:".count($ontology->{'owl_data'}['properties']));;
						
						echoN("ONTOLOGY RELATIONS COUNT - INCLUDING INSTACE PROP-:".$ontologyRelationsCount);;

				
					
						$writer->writeToFile($qaOntologyFile, $ontology, "QA Ontology - www.qurananalysis.com","1.0");
					
					
						//echo htmlentities(file_get_contents($qaOntologyFile));
						//var_dump($ontology->getAllClasses());
					
					
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







