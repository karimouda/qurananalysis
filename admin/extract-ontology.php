<?php 
#   PLEASE DO NOT REMOVE OR CHANGE THIS COPYRIGHT BLOCK
#   ====================================================================
#
#    Quran Analysis (www.qurananalysis.com). Full Semantic Search and Intelligence System for the Quran.
#    Copyright (C) 2015  Karim Ouda
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#    You can use Quran Analysis code, framework or corpora in your website
#	 or application (commercial/non-commercial) provided that you link
#    back to www.qurananalysis.com and sufficient credits are given.
#
#  ====================================================================
require_once("../global.settings.php");
require_once("../libs/core.lib.php");
require_once("../libs/microsoft.translator.api.lib.php");
require_once("../libs/pos.tagger.lib.php");
require_once("../libs/ontology.lib.php");
require_once("../libs/custom.translation.table.lib.php");
require_once("../libs/graph.lib.php");

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






//$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = loadUthmaniToSimpleMappingTable();

//preprint_r($UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS);exit;


//$UTHMANI_TO_SIMPLE_LOCATION_MAP = apc_fetch("UTHMANI_TO_SIMPLE_LOCATION_MAP");

//$LEMMA_TO_SIMPLE_WORD_MAP = loadLemmaToSimpleMappingTable();

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
    <title>Quran Analytics | Ontology Extraction </title>
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
			  	
			  	
			  	$GENERATE_CONCEPTS_SWITCH = TRUE;
			  	
			  	$GENERATE_TERMS = 	$GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_PHRASE_TERMS = $GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_PRONOUN_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_ADJECTIVE_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  	$GENERATE_FINAL_CONCEPTS = $GENERATE_CONCEPTS_SWITCH;
			  				  	
			  	
			 
			  	$GENERATE_NONTAXONOMIC_RELATIONS = TRUE;;
			  	$EXTRACT_NEWCONCEPTS_FROM_RELATIONS = TRUE;
			  	$GENERATE_TAXONOMIC_RELATIONS = TRUE;
			  	
			  	
			  	$ENRICH_CONCEPTS_METADATA_TRANSLATION_TRANSLITERATION = TRUE;
			  	$ENRICH_CONCEPTS_METADATA_DBPEDIA = TRUE;
			  	$ENRICH_CONCEPTS_METADATA_WORDNET = TRUE;
			  	
			  	$EXCLUDE_CONCEPTS_AND_RELATIONS = TRUE;
			  	
			  	
			  	$FINAL_POSTPROCESSING = TRUE;
			  	
			  	$GENERATE_OWL_FILE = TRUE;
			  	
			
				$WORDS_FREQUENCY = getModelEntryFromMemory($lang, "MODEL_CORE", "WORDS_FREQUENCY", "");
				
			  	
			  	$wordsInfoArr = unserialize(file_get_contents("../data/cache/words.info.all"));
			  		
			  	// not cahed yet
			  	if  ( empty($wordsInfoArr) )
			  	{
			  		foreach ($WORDS_FREQUENCY['WORDS_TFIDF'] as $wordLabel => $wordFreqArr )
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
								echoN("$term");
								unset($finalTerms[$term]);
							}
						}
						

						
						echoN("Terms Count After SW Exclusion:<b>".count($finalTerms)."</b>");
						
						
						
						//exit;
						
						
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
						
							$phraseTerms = getNGrams("AR",2);
							
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
										
										// NO qURANA TRANSLATIONS FOR 1 WORD CONCEPTS //EX: "PROPHEt MUHAMMAD INSTEAD OF MUHAMAD"
										//$termArr['TRANSLATION_EN']=$conceptArr['EN'];
										
										
										
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
									
								$simpleWord = getModelEntryFromMemory("AR", "OTHERS", "UTHMANI_TO_SIMPLE_WORD_MAP",$lemaUthmani);
									
									
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
										
					
									// try to get translation from uthmani translation table
									$engTranslation = $WORDS_TRANSLATIONS_AR_EN[$lemaUthmani];
									
					
									addNewConcept($finalConcepts, $mergedWord, "A-BOX", "ADJECTIVE", $termArr['FREQ'], $engTranslation);
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
								$weight += floatval($WORDS_FREQUENCY['WORDS_TFIDF'][$biGramTerm]['TFIDF']);
							}
							
							$weight = ($weight/2);
							//////
							
							
							//$weight = round($freq/$maxConceptFreq,2);
							
							$quranaConceptArr = getQuranaConceptEntryByARWord($biGramConcept);
				
							// ADD QURANA TRANSLATION FOR QURANA BIGRAMS
							$engTranslation = ucfirst($quranaConceptArr['EN']);
							
							addNewConcept($finalConcepts, $biGramConcept, "A-BOX", "PHRASE", $freq, $engTranslation);
							$finalConcepts[$biGramConcept]['EXTRA']['POS']=$pos;
							$finalConcepts[$biGramConcept]['EXTRA']['WEIGHT']=$weight;
							
						
							$finalConcepts[$biGramConcept]['EXTRA']['IS_QURANA_NGRAM_CONCEPT'] = true;
						
							
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
						
						//preprint_r($finalConcepts);exit;
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
									  // WORDS LOOP
									  foreach($uthmaniWordsArr as $index => $uthmaniWord)
									  {
							
									  	// USINF SIMPLE  WORDS FOR SIMPLER LEXICAL CONDISTIONS
									  	$simpleWord = getModelEntryFromMemory("AR", "OTHERS", "UTHMANI_TO_SIMPLE_WORD_MAP",$uthmaniWord);
									  	
									  	
									 
									  //	echoN("|$uthmaniWord|$simpleWord");
									  	
									  	//if ( isset($finalConcepts[$simpleWord]))
									  	//{
									  		 $qacLocation = ($s+1).":".($a+1).":".($index+1);
									  		 
										 	 $qacWordSegmentsArr = getModelEntryFromMemory($lang,"MODEL_QAC","QAC_MASTERTABLE",$qacLocation);
	
										 	 $pos="";
										 	 $allSegments = "";
									
										 	 // GET POS TAGS, LEMMAS AND SEGMENTS FOR THIS CONTEXT, PUT IN ARRAY
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
										 	 
										 	 // ASSUNME A WORD IS A CONCEPTS AND ADD ITS POS
										 	 $triplePatternArr['CONCEPTS'][]=$simpleWord;
										 	 $triplePatternArr['PATTERN'][]=trim($pos);
										 	 
										 	
										 	 
										 	 
										 	 	
										 	 	$joinedPattern = join(" ",array_values($triplePatternArr['PATTERN']));
										 	 	
										 	 	
										 	 	
										 	 	$concept1 = $triplePatternArr['CONCEPTS'][0];
										 	 	$concept2 = $triplePatternArr['CONCEPTS'][2];
										 	 	$verb = $triplePatternArr['CONCEPTS'][1];
										 	 
										 	 	
										 	 	//removed  || ($joinedPattern=="PN NEG V DET N" )
										 	 	
										 	 	// IF THE UNIT PATTERN MATCHES 
										 	 	if (  
													( 
														//الله -> يحب -> المتقين
 													     ($joinedPattern=="PN V DET N" && $triplePatternArr['CONCEPTS'][1]!="قال")
														//الله -> مع -> المتقين
									  				  || ($joinedPattern=="PN LOC DET N" && $triplePatternArr['CONCEPTS'][1]=="مع")
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
										 	 		
										 	 		if ( $joinedPattern=="PN NEG V DET N")
										 	 		{
										 	 			
										 	 			$concept1 = $triplePatternArr['CONCEPTS'][0];
										 	 			$concept2 = $triplePatternArr['CONCEPTS'][3];
										 	 			$verb = $triplePatternArr['CONCEPTS'][1]." ".$triplePatternArr['CONCEPTS'][2];
										 	 			
										 	 		}
								
										 
										 	 		$concept1Segment = $verseSegments[$concept1];
										 	 		$concept2Segment = $verseSegments[$concept2];
										 	 		
										 	 		$concept1Lemma = $verseLemmas[$concept1];
										 	 		$concept2Lemma = $verseLemmas[$concept2];
										 	 		

										 	 		if ( (isset($finalConcepts[$concept1]) || ($concept1=getConceptByLemma($finalConcepts,$concept1Lemma)) )
														&& (isset($finalConcepts[$concept2]) || ($concept2=getConceptByLemma($finalConcepts,$concept2Lemma)) )
									  				 )
										 	 		{
										 	 				//echoN("####");
										 	 			
										 	 				$type = "NON-TAXONOMIC";
										 					addRelation($relationsArr,$type,$concept1,$verb,$concept2,$joinedPattern,"",$verb);
										 				
										 					
										 	 			
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
										 	 		
										 	 		// RESET TRIGRAMS UNIT ARRAY
										 	 		$triplePatternArr = array();
										 	 		
										 	 	}
										 	 	// PATTERN DIDN'T MATCH
										 	 	else 
										 	 	{
										 	 
										 	 	
										 	 		$numberOWordsInUnit = 3;
										 	 		// REMOVE THE FIRST CONCEPT AND POS TO GIVE SPACE FOR a new word in the trigram concept
										 	 		if ( count($triplePatternArr['CONCEPTS'])==$numberOWordsInUnit )
										 	 		{
										 	 			
										 	 			// GET ONLY SECOND AND THIRD ENTRIES
										 	 			$bigramFromTrigramArr = array_slice($triplePatternArr['CONCEPTS'], 1,($numberOWordsInUnit-1) ) ;;
										 	 			$bigramPatternFromTrigramArr = array_slice($triplePatternArr['PATTERN'], 1, ($numberOWordsInUnit-1));;
										 	 			
										 	 			$versePrevWords[$triplePatternArr['CONCEPTS'][0]]=1;
										 	 			$versePrevPatterns[$triplePatternArr['PATTERN']]=1;
										 	 			
										 	 			// GET ONLY SECOND AND THIRD ENTRIES
										 	 			$triplePatternArr['CONCEPTS'] = $bigramFromTrigramArr;
												 	 	$triplePatternArr['PATTERN'] = $bigramPatternFromTrigramArr;
										 	 		 }
										 	 	}
										 	 	
										 	
										 	 	
										 	 
										 	 	
										 	 
									  	//}
									  	
										 	 	
									 	 
									 }
									 
									 
									
									 
									
							}
							
						
						
					
					}
					
					echoN("NON-TAX SYNTATIC PATTERNS RELATIONS COUNT:".count($relationsArr));
					//preprint_r($relationsArr);
					//exit;
					
				
					
					$poTaggedSubsentences = getPoSTaggedSubsentences();
					
					//preprint_r($poTaggedSubsentences);exit;
					
					//echoN("SubSentences Count:".addCommasToNumber(count($poTaggedSubsentences)));
					
					
					
					
					
					$ssPoSAggregation = array();
					$ssPoSAggregationCorrespondingSent = array();
					
					//$METHOD = "GENERAL_RULE";
					$METHOD = "LCS_RULES";
					
					if ( $METHOD=="GENERAL_RULE")
					{

					
					

					
							
		
		
						
							// CONVERT POS SUBSENTENCES FROM ARRAYS TO STRINGS
							foreach($poTaggedSubsentences as $location => $dataArr)
							{
								$wordsArr = $poTaggedSubsentences[$location]['WORDS'];
								$posArr = $poTaggedSubsentences[$location]['POS_TAGS'];
								
								$ssPoSPattern = join(", ",$posArr);
								
								$subSentenceStr = join(" ",$wordsArr);
									
				
								$ssPoSAggregationCorrespondingSent[$location] = array($ssPoSPattern,$subSentenceStr);
									
							
									
								
							}
							
					
							
							
							
							
							 $lastVerseId = null;
							 
							 // LOOP ON SUBSENTCES
							 foreach ($ssPoSAggregationCorrespondingSent as $ssLocation => $ssArray)
							 {
		
							 	//	echoN("________");
							 	$ssPoSPattern = $ssArray[0];
							 	$subSentenceStr = $ssArray[1];
								//echoN("<b>$subSentenceStr</b>");
								
								//echoN("$ssPoSPattern");
								
								
								$verseId = substr($ssLocation, 0, strlen($ssLocation)-2);
								
								// RETUSN STRINGS ARRAY AGAIN ! CHANGE THIS
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
								
								// LOOP ON PATTERNS IN CURRENT SUBSENTCE
								foreach($patternArr as $index => $posPattern)
								{
									$posPattern = trim($posPattern);
									
									echoN($posPattern);
									
									$currentWord = current($WordsArr);
									
									$qacLocation = substr($ssLocation, 0, strlen($ssLocation)-2) .":".($qacVerseIndex);
									
							
									//echoN($qacLocation);
									
									$qacWordSegmentsArr = getModelEntryFromMemory($lang,"MODEL_QAC","QAC_MASTERTABLE",$qacLocation);
							
									$featuresArr = array();
									foreach($qacWordSegmentsArr as $segmentIndex=> $segmentArr)
									{
										$features = $qacWordSegmentsArr[$segmentIndex]['FEATURES'];
										
										$featuresArr = array_merge($featuresArr,$features);
									}
		
									preprint_r($featuresArr);
									
									
									
										//echoN($featuresArr['ROOT']);
										
										// RESET CONTEXT WHEN WE FIND CONJ THE FOLLOWING POS TAGS - SUCH AS CONJUCTIONS
										if ( $index > 0 && strpos($posPattern,"ACC")!==false ||
											 strpos($posPattern,"REM")!==false ||
											strpos($posPattern,"SUB")!==false ||
											strpos($posPattern,"COND")!==false ||
											strpos($posPattern,"CONJ P PRON")!==false ||
		
											$featuresArr['ROOT']=="قول"
										)
										{
											
											
											// ADD ANY PENDING RELATIONS
											flushProperRelations($relationsArr,$conceptsArr,$verb,$lastSubject,$ssPoSPattern,$filledConcepts);
											
											// SKIP ANY VERSE WHICH INCLUDES CONVERSATION OR CONDITION
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
							 
							 echoN("GENERAL RULES COUNT:".count($relationsArr));
							 preprint_r($relationsArr);
							// exit;
							 
					}
					// USING STATISTICALLY SIGNIFICANT RULES ( SUCH AS RULES EXTRACTED FROM LONGEST COMMON SUBSTRINGS ALGORITHM )
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
						
						// LOOP ON EACH RULE OF THE ABOVE
						foreach( $filteredRules as $rulePattern=>$strippedRulePattern)
						{
							//$rule = "V PRON, P, N, PN";
							//$rule = "V PRON PRON";
							$rule = $rulePattern;
							
							// LOOP ON SUB-VERSES
							foreach($poTaggedSubsentences as $location => $dataArr)
							{
									$wordsArr = $poTaggedSubsentences[$location]['WORDS'];
									$posArr =   $poTaggedSubsentences[$location]['POS_TAGS'];
									$qacIndexes = $poTaggedSubsentences[$location]['QAC_WORD_INDEXES'];
									
									/*preprint_r($posArr);
									preprint_r($wordsArr);
									preprint_r($qacIndexes);
									*/
									
									
									// GET PATTERN/WORD STRINGS FROM ARRAYS
									$ssPoSPattern = join(", ",$posArr);
									$subSentenceStr = join(" ",$wordsArr);
									
									
										
									// RULE IS FOUND IN SUB-VERSE  PATTERN
									if ( strstr($ssPoSPattern, $rule)!==false)
									{
										echoN($subSentenceStr);
										//echoN($location);
										echoN($ssPoSPattern);
										echoN($rule);
										$startOfRule= strpos($ssPoSPattern, $rule);
										//echoN("SP:".$startOfRule);
										
										// GET ALL POS WHICH IS FOUND BEFORE THE PATTERN IN THE PATTERN STRING
										$prePatternStr = substr($ssPoSPattern, 0,$startOfRule);
										
										// SOME STATISTICS
										$numberOfWordsPrePattern = preg_match_all("/,/", $prePatternStr);
										$numberOfWordsInRule = (preg_match_all("/,/", $rule)+1);
										//echoN("# of words prepattern:".$numberOfWordsPrePattern);	
										//echoN("# of words in rule:".$numberOfWordsInRule);
										
										$startArrayIndexOfPattern = $numberOfWordsPrePattern;
										
										$verseId = substr($location, 0, strlen($location)-2);
						
										
					
	
										// QAC INDEX OF FIRST CORRSPONDING WORD IN THE PATTERN
										$qacStartWordIndexInVerse = $qacIndexes[$startArrayIndexOfPattern];
										$qacBaseLocation = $verseId;
										//echoN($qacLocation);
										
										
										// IF THE SUBVERSE CONTAIN CONDITIONS OR VOCATIVES, IGNORE THE WHOLE SUBVERSE
										if ( preg_match("/VOC|COND|INTG/",$prePatternStr) 
												|| mb_strpos(removeTashkeel($subSentenceStr), "قال") !==false
												 )
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
												
												// RESOLVE PRONOUNS
												$qacWordLocation = $qacBaseLocation .":".($qacStartWordIndexInVerse);
													
												$pronounConceptArr = resolvePronouns($qacWordLocation);
												$subject = $pronounConceptArr[0];
												$object = $pronounConceptArr[1];
												
												//qurana bug OR NULL CONCEPTS
												if ( empty($pronounConceptArr)) continue;
												if ( $subject=="null" || $object=="null") continue;
												
												/// VERB LEMMA
												$qacWordSegmentsArr = getModelEntryFromMemory($lang,"MODEL_QAC","QAC_MASTERTABLE",$qacWordLocation);
													
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
												addRelation($relationsArr,$type, $subject, $verb, $object, $rule,"",$verb);
												
											break;
											
											case "V PRON, P, N, PN":
												
											
												$qacWordLocation = $qacBaseLocation .":".($qacStartWordIndexInVerse);
												
												$pronounConceptArr = resolvePronouns($qacWordLocation);
												$subject = $pronounConceptArr[0];
									
												//qurana bug
												if ( empty($pronounConceptArr)) continue;
												
	
												$qacWordSegmentsArr =  getModelEntryFromMemory($lang,"MODEL_QAC","QAC_MASTERTABLE",$qacWordLocation);
												
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
												
												addRelation($relationsArr, $type,$subject, $verb, $object, $rule,"",$wordsArr[$startArrayIndexOfPattern]);
											break;
											//
											//يَغْفِرْ لَ كُمْ ذُنُوبَ كُمْ
											case "V PRON, DET N":
											case "V PRON, N PRON":
												$qacWordLocation = $qacBaseLocation .":".($qacStartWordIndexInVerse);
												$qacWordLocation2 = $qacBaseLocation .":".($qacStartWordIndexInVerse+1);
												
												// FIRST PRONOUN
												$pronounConceptArr = resolvePronouns($qacWordLocation);
												//SECOND PRONOUN
												$pronounConceptArr2 = resolvePronouns($qacWordLocation2);
												
												$subject = $pronounConceptArr[0];
							
											
												if ( $rule=="V PRON, N PRON")
												{
													preprint_r($pronounConceptArr);
													preprint_r($pronounConceptArr2);
												
													// remove PRON chars from word
													$qacWordLocationForSecondWord = $qacBaseLocation .":".($qacStartWordIndexInVerse+1);
													$qacWordSegmentsArr = getModelEntryFromMemory($lang,"MODEL_QAC","QAC_MASTERTABLE",$qacWordLocationForSecondWord);
													$nounSegmentArr = getQACSegmentByPos($qacWordSegmentsArr,"N");
													$nounSegment = $nounSegmentArr['FEATURES']['LEM'];
													
													//preprint_r($nounSegmentArr['FEATURES']);
													/*echoN($qacWordLocationForSecondWord);
													preprint_r($qacWordSegmentsArr);
													preprint_r($nounSegmentArr);
													echoN($nounSegment);
													*/
													
													if ( isset($nounSegmentArr['FEATURES']['NOM']) )
													{
														$object = $pronounConceptArr[0];
														$subject = $nounSegment;
													}
													// NOUN SEGMENT FEATURE HAS ACC OR OTHER
													else 
													{
														/////
														$object = $nounSegment;
													}
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
												
												
												$qacWordSegmentsArr = getModelEntryFromMemory($lang,"MODEL_QAC","QAC_MASTERTABLE",$qacWordLocation);
													
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
												addRelation($relationsArr,$type, $subject, $verb, $object, $rule,"",$wordsArr[$startArrayIndexOfPattern]);
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
				
					
					
						
						
					///////// PROPER NOUNS AJDECTIVES ////////////////////////
					$triGrams4 = getPoSNGrams("PN ADJ ADJ");
					preprint_r($triGrams4);
					// DIDN'T DO THIS BECAUSE IT NEEDS CONTEXT قُرْءَانًا أَعْجَمِيًّا لَّ
					//$triGrams2 = getPoSNGrams("ACC PN ADJ");
						
					// ADJ PARENT + relations
					foreach($triGrams4 as $bigram => $freq)
					{
						$biGramWords = preg_split("/ /",$bigram);
					
						// CONVERT ALL WORDS TO SIMPLE
						$concept = getModelEntryFromMemory("AR", "OTHERS", "UTHMANI_TO_SIMPLE_WORD_MAP",$biGramWords[0]);
						$adj1 = getModelEntryFromMemory("AR", "OTHERS", "UTHMANI_TO_SIMPLE_WORD_MAP",$biGramWords[1]);
						$adj2 = getModelEntryFromMemory("AR", "OTHERS", "UTHMANI_TO_SIMPLE_WORD_MAP",$biGramWords[2]);
					
					
						$hasAttribute = "من صفاتة";
						$type = "NON-TAXONOMIC";
					
						// HANDLE ADJECTIVE 1
						// BOTH SUBJECT AND OBJECT EXISTS IN THE CONCEPTS LIST
						if ( isset($finalConcepts[$concept]) && isset($finalConcepts[$adj1]) )
						{
					
							//ADD RELATION: CONCEPT( PN ) has attribute ($adj1)
							addNewRelation($relationsArr,$type,$concept,$hasAttribute,$adj1,"ADJ","has attribute");
								
								
						}
					
						// HANDLE ADJECTIVE 2
						if ( isset($finalConcepts[$concept]) && isset($finalConcepts[$adj2]) )
						{
					
							//ADD RELATION: CONCEPT ( PN ) has attribute ($adj2)
							addNewRelation($relationsArr,$type,$concept,$hasAttribute,$adj2,"ADJ","has attribute");
								
								
						}
					
					
						/* produced 13 relations*/
					}
					
						preprint_r($relationsArr);
						
						
						echoN("FINAL NONTAXONOMIC RELATIONS :".count($relationsArr));
							
						
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($relationsArr));
						
				
					
					}
					
					

					
					
					

					if ( $EXTRACT_NEWCONCEPTS_FROM_RELATIONS )
					{
						// LOAD CACHED RESULTS FROM LAST STAGE
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage1"));
						$finalTerms =  unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.all.terms"));
						
			
						echoN("BA-AA:".count($finalConcepts));
						
						$notInCounceptsCounter = 0;
						$handled = array();
						
						$statsUniqueSubjects = array();
						$statsUniqueObjects = array();
						
						// LOOP ON ALL RELATIONS
						foreach($relationsArr as $hash => $relationsArr)
						{
							$relationsType = $relationsArr['TYPE'];
							
							// NO TAXNOMICAL ONLY
							if ( $relationsType =="NON-TAXONOMIC")
							{
								$subject = 	$relationsArr['SUBJECT'];
								$object = $relationsArr['OBJECT'];
								$verb = $relationsArr['VERB'];
								
					
								
								handleNewConceptFromRelation($finalConcepts,$subject,"SUBJECT",$notInCounceptsCounter,$statsUniqueSubjects);
								
								handleNewConceptFromRelation($finalConcepts,$object,"OBJECT",$notInCounceptsCounter,$statsUniqueSubjects);
								
								
							}
							

						}
						
						echoN("Concepts Added: $notInCounceptsCounter");
						echoN("UNIQIE SUBJECTS:".count($statsUniqueSubjects));
						echoN("UNIQIE OBJECTS:".count($statsUniqueObjects));
						
						//preprint_r($statsUniqueSubjects);
					
						
						echoN("Final Concepts Count:". count($finalConcepts));
						
						preprint_r($finalConcepts);
						
			
							
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage2", serialize($finalConcepts));
						
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
						$countOfRelationsFirst = $countOfRelationsBefore;
							
						//echoN("BA-B:".count($finalConcepts));
					
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
							
							
						echoN("TAXONOMIC RELATIONS - ADJ:".(count($relationsArr)-$countOfRelationsBefore));
						$countOfRelationsBefore = 	count($relationsArr);
						
						/////////////////////////////////////////////////////////////////////
							
				
						
					
							
						/////////////// PHRASE CONCEPTS HYPERNYMS (PARENT-CHILD) ///////////////
							
						foreach($finalConcepts as $concept => $conceptArr)
						{
					
							$type = $conceptArr['EXTRACTION_PHASE'];
							$pos = $conceptArr['EXTRA']['POS'];
					
							
							//if ( $type=="PHRASE")
								
							// ONLY HANDLE BIGRAMS
							if ( preg_match_all("/ /", $concept)==1)
							{
								//SPLIT PHRASE ON SPACE
								$biGramWords = preg_split("/ /",$concept);
									
									
								// FIRST WORD IS PARENT
								$parentConcept = $biGramWords[0];
									
								// GET ALL INFO ABOUT THIS WORD - INCLUDING POS TAG
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
							
						

						echoN("TAXONOMIC RELATIONS - BIGRAM PARENT :".(count($relationsArr)-$countOfRelationsBefore));
							
						///////////////////////////////////////////////////////////////////
						echoN("FINAL TAXONOMIC RELATIONS :".(count($relationsArr)-$countOfRelationsFirst));
					
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
							$uthmaniWord = getModelEntryFromMemory("AR", "OTHERS", "UTHMANI_TO_SIMPLE_WORD_MAP",$concept);
							
							//echoN("/$concept/");
							if ( empty($currentEnglishTranslation))
							{
								$finalTranslation = null;
								
								
								//echoN($uthmaniWord);
								
								$finalTranslation = $WORDS_TRANSLATIONS_AR_EN[$uthmaniWord];
								
								//echoN($finalTranslation);
								
								// WORD TRANSLATION NOT FOUND - TRY AGAIN WITH DETERMINDER 'ALEF+LAM'
								if ( empty($finalTranslation))
								{
								   /*
									* REMOVED TRANSLATION BY NEAREST DERIVATION BECAUSE IT CHANGES THE MEANING
									* زوج = kind 
									*
									*/
									
									/*if ( startsWithAL($concept))
									{
										
										//try adding ال
										$conceptDerivation = mb_substr($concept, 2);
									}
									else
									{
										//try adding ال
										$conceptDerivation = "ال".$concept;
									}
									
									echoN("**** ".$conceptDerivation);
									
									$uthmaniWordForTranslation = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$conceptDerivation];
									echoN($uthmaniWordForTranslation);
									
									$engTranslationB4Cleaning = $WORDS_TRANSLATIONS_AR_EN[$uthmaniWordForTranslation];
									echoN($engTranslationB4Cleaning);
									*/
									
									
									if (!isFoundInTranslationTable($concept))
									{
										//echoN("|$parentConceptEN|");
										$tentitaveTranslation = translateText($concept,"ar","en");
									
									
									
										addTranslationEntry($tentitaveTranslation, "CONCEPT",$concept,"AR" );
									
										$finalTranslation = $tentitaveTranslation;
									}
									else
									{
										$customTranslationEntry = getTranlationEntryByEntryKeyword($concept);
											
									
										$finalTranslation  = $customTranslationEntry['EN_TEXT'];
									}
								}
								
								//echoN($finalTranslation);
							
								
								// REPLACE BRACKETS AND SPECIAL CHARS WITH SPACES
								$englishTranslation = cleanEnglishTranslation($finalTranslation);
								
								//SET IT IN CONCEPT METADATA
								$finalConcepts[$concept]['EXTRA']['TRANSLATION_EN'] = $englishTranslation;
								//echoN("$uthmaniWord|$englishTranslation|$engTranslationB4Cleaning");
								
								preprint_r($finalConcepts[$concept]);
							}
							
							
							// TODO: may need to do the same as above for transliteration
							$englishTransliteration = $WORDS_TRANSLITERATION[$uthmaniWord];
							
							
							
							$finalConcepts[$concept]['EXTRA']['TRANSLITERATION_EN'] = $englishTransliteration;
								
							
						}
						
				
						
						//preprint_r($finalConcepts);exit;
						//echoN(count($finalConcepts));
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
								//echoN("NOT FILTERED:$conceptNameEn|$conceptNameAr");
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
										
										
										
										
										//preprint_r($parentClassConceptsArr);
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
						
						
						//preprint_r($enrichedFinalConcepts);exit;
						
						file_put_contents("../data/cache/dbpedia.resources", serialize($dbpediaCacheArr));
						
					
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage5", serialize($enrichedFinalConcepts));
						
			
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($relationsArr));
					
						
					}
					
					
					if ( $ENRICH_CONCEPTS_METADATA_WORDNET)
					{
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage5"));
						$finalTerms =  unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.all.terms"));

						
						$lexicoSemanticCategories = apc_fetch("WORDNET_LEXICO_SEMANTIC_CATEGORIES");
				
					
					
						
						
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
							
					
							
							
							$wordnetInfoArr = getWordnetEntryByWordString($conceptNameEn);
							
							echoN($conceptNameEn."-".$wordnetInfoArr);
							
							echoN("^^^^ ".count($wordnetInfoArr));
							
							
							
							
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
							

							
							// DATA FOUND IN WORDNET FOR THE CURRENT CONCEPT
							if ( !empty($wordnetInfoArr) )
							{
								$conceptsEnriched++;
								
								// MAP QAC POS TO WORDNET POS
								$qacPOS = $coneptArr['EXTRA']['POS'];
								$wordnetPOS = mapQACPoSToWordnetPoS($qacPOS);
								
							
								echoN("Wordnet Enriching [$concept] ...");
								
								//preprint_r($coneptArr);
								
								$wordnetInfoArrayForConcept = $wordnetInfoArr;
								
								//preprint_r($wordnetInfoArr);
								
							

								
								$conceptMeaningEN = getGlossaryFirstPart($wordnetInfoArr['GLOSSARY'][$wordnetPOS]);
								
								
								$conceptMeaningAR = "";
								
								//////////// MEANING TRANSLATION 
								if (!isFoundInTranslationTable($conceptMeaningEN,"DESC"))
								{
									//echoN("|$conceptMeaningEN|");
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
								
								
								
								//echoN("|$wordnetPOS|");
							
								
								///////////////// ENRICH CONCEPT BY WORDNET SEMANTIC TYPES
								foreach($wordnetInfoArr['SEMANTIC_TYPES'][$wordnetPOS] as $dummy => $semanticType)
								{
									
									
									if ( empty($semanticType) ) continue;
									
									if ( isset($handledSemanticTypes[$semanticType])) continue;
									
									// EXCLUDED SEMANTIC TYPE
									if ( isExcludableSemanticType($semanticType)) continue;
									
									$handledSemanticTypes[$semanticType] = 1;
									

									$englishConceptName = $semanticType;
									$finalConceptName = $englishConceptName;
									
									// GET FROM CUSTOM TRANSLATION TABLE OT TRANSLATE SEMANTIC TYPE
									if (isFoundInTranslationTable($finalConceptName))
									{
										$customTranslationEntry = getTranlationEntryByEntryKeyword($finalConceptName);
									
										$finalConceptName = $customTranslationEntry['AR_TEXT'];
									}
									else
									{
										$tentitaveTranslation = translateText($finalConceptName);
										
										
										
										addTranslationEntry($englishConceptName, "CONCEPT",$tentitaveTranslation);
										
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
										
										
										/////////  ENRIUCHMENT AND TRANSLATION FOR THE NEW CONCEPT
										$conceptMeaningEN = getGlossaryFirstPart($semanticTypeWordInfoArr['GLOSSARY'][$wordnetPOS]);
										
										if (isFoundInTranslationTable($glossary,"DESC"))
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
									// SEMANTIC TYPE IS ALREADY A CONCEPT
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
									
										echoN("XPP: 1  $finalConceptName");
										
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
								
								
								/// HANDLIGN WORD RELATIONS
								foreach($wordnetInfoArr['RELATIONSHIPS'][$wordnetPOS] as $relIndex => $relArr)
								{
										$relType = $relArr['RELATION'];
										
										// ONLY HANDLE HYPERENYMS - PARENTS
										if ( stripos($relType,"Hypernym")!==false)
										{
											$wordsArr = $relArr['WORDS'];
											$semanticTypeID = $relArr['SEMANTIC_CATEGORY_ID'];
											$semanticType = $lexicoSemanticCategories[$semanticTypeID];
												
											$semanticType = ucfirst(substr($semanticType, strpos($semanticType, ".")+1));
											

											
											$glossary = getGlossaryFirstPart($relArr['GLOSSARY']);
											
											$hypernym = key($wordsArr);
											
							
											
												if ( !empty($hypernym) && !isset($handledSemanticTypes[$hypernym]))
												{
													
													echoN("==|$hypernym|");
													
													$handledSemanticTypes[$hypernym] = 1;
														
													$englishConceptName = cleanWordnetCollocation($hypernym);
													
													$finalConceptName = $englishConceptName;
														
													
													// TRANSLATION
													if (isFoundInTranslationTable($englishConceptName))
													{
														$customTranslationEntry = getTranlationEntryByEntryKeyword($englishConceptName);
													
														
														
														$finalConceptName = $customTranslationEntry['AR_TEXT'];
														
														echoN("FOUND:$finalConceptName");
													}
													else
													{
														$tentitaveTranslation = translateText($englishConceptName);
													
													
													
														addTranslationEntry($englishConceptName, "CONCEPT",$tentitaveTranslation );
														
														$finalConceptName = $tentitaveTranslation;
													}
													
													echoN("finalConceptName:$finalConceptName");
													
													
													
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
														
														
														if (isFoundInTranslationTable($glossary,"DESC"))
														{
															$customTranslationEntry = getTranlationEntryByEntryKeyword($glossary);
														
															$glossaryAR = $customTranslationEntry['AR_TEXT'];
														}
														else
														{
															$tentitaveTranslation = translateText($glossary);
																
														/*	echoN($glossary);
															echoN("==".("(plural) any group of human beings (men or women or children) collectively"==$glossary));
															//showHiddenChars(removeUnacceptedChars(cleanAndTrim("(plural) any group of human beings (men or women or children) collectively")),"EN");
															//showHiddenChars("someone who leads you to believe something that is not true","EN");
															isFoundInTranslationTable($glossary,"DESC");
															preprint_r($CUSTOM_TRANSLATION_TABLE_EN_AR["(plural) any group of human beings (men or women or children) collectively"]);
															preprint_r($CUSTOM_TRANSLATION_TABLE_EN_AR);
															exit;*/
																
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
													// CONCEPT WAS ALREAD IN THE CONCEPTS LIST
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
														
													echoN("XPP: 2  $finalConceptName");
														
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
						//preprint_r($enrichedFinalConcepts);exit;
				
						
						
							
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage6", serialize($enrichedFinalConcepts));
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($relationsArr));
						
					}
					
					
					
					if ($EXCLUDE_CONCEPTS_AND_RELATIONS)
					{
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage6"));

						//preprint_r($relationsArr);
						//preprint_r($finalConcepts);exit;

						$imagesToBeRemoved = array();
						$englishLongDescriptionsToBeRemoved = array();
						$arabicLongDescriptionsToBeRemoved = array();
						
						$synonymsToBeRemoved = array();
						
						
						//////////// DATA ATTRIBUTES TO BE REMOVED
						
						
		
						
						
						$imagesToBeRemoved = loadExcludesByType('images');
						$linksToBeRemoved = loadExcludesByType('links');
						

						
						$synonymsToBeRemovedArr = loadExcludesByType('synonyms');
						
						
				
						
						//////////////////////////////////////////
						
						
						$conceptsRemoved=0;
						$relationsRemoved = 0;
						$removedLongDesc = 0;
						$removedShortDesc = 0;
						$removedImages = 0;
						$removedLinks = 0;
						$removedSynonyms =0;

						$filteredConcepts = array();
						
						
						$excludedRelationsFromFileArr = loadExcludesByType('relations');
						$excludedShortDescArr = loadExcludesByType('shortdesc');
						$excludedLongDescConceptsArr  = loadExcludesByType('longdesc');
						
						
						//also used down in relation filtering
						$excludedConceptsArr = loadExcludesByType('concepts');
						$isAddedBeforeArr = array();
					
						echoN("^^^^");
						preprint_r($synonymsToBeRemovedArr);
						
						/// CONCEPTS
						foreach($finalConcepts as $concept => $conceptArr)
						{
								
						
								
							$conceptNameEn  = $conceptArr['EXTRA']['TRANSLATION_EN'];
							$conceptNameAr  = $concept;
							
							echoN("$conceptNameAr|$conceptNameEn|".isset( $excludedConceptsArr[$conceptNameAr]));
							
							if (isset( $excludedConceptsArr[$conceptNameAr]) || isset( $excludedConceptsArr[$conceptNameEn]) )
							{
								
								$conceptsRemoved++;
								echoN("IGNORING:$concept");
								continue;
							}
							
							
							////////// ATTRIBUTE CLEANING
					
							//echoN("::".isset($englishLongDescriptionsToBeRemoved[$concept])." ".$concept);
							//showHiddenChars($concept,"AR",false);
							if ( isset($excludedLongDescConceptsArr[$concept]) )
							{
								$descArr = $conceptArr['EXTRA']['DESC_EN'];
								
								//echoN("Removing EN Translation:$concept");
								
								foreach($descArr as $source => $desc)
								{
									//echoN("$source");
									
									$conceptArr['EXTRA']['DESC_EN'][$source]="";
								}
								
								$removedLongDesc++;
							}
							
							if ( isset($excludedLongDescConceptsArr[$concept]) )
							{
								$descArr = $conceptArr['EXTRA']['DESC_AR'];
							
								foreach($descArr as $source => $desc)
								{
									$conceptArr['EXTRA']['DESC_AR'][$source]="";
								}
								
								$removedLongDesc++;
							
							}
							
							

							

							$shortDescAR = trim($conceptArr['EXTRA']['MEANING_AR']['WORDNET']);
								
							if ( isset($excludedShortDescArr[$shortDescAR]) )
							{
								
								$descArr = $conceptArr['EXTRA']['MEANING_AR'];
									
								foreach($descArr as $source => $desc)
								{
									$conceptArr['EXTRA']['MEANING_AR'][$source]="";
								}
									
								$removedShortDesc++;
							}
							
							$shortDescEN = trim($conceptArr['EXTRA']['MEANING_EN']['WORDNET']);
							
							//echoN("|the force of workers available|");
							///echoN("|$shortDescEN|");
							
							if ( isset($excludedShortDescArr[$shortDescEN]) )
							{
							
								$descArr = $conceptArr['EXTRA']['MEANING_EN'];
									
								foreach($descArr as $source => $desc)
								{
									$conceptArr['EXTRA']['MEANING_EN'][$source]="";
								}
									
								$removedShortDesc++;
							}
							
							
							
							$imageURLsArr   = $conceptArr['EXTRA']['IMAGES'];
							
							foreach($imageURLsArr as $source => $url)
							{
								//echoN("IMURL:$url|$source");
								
								if ( !empty($url) && isset($imagesToBeRemoved[$url]) )
								{
									//echoN("removed:$url");
									
									//using $conceptArr since it is assigned after the loop
									$conceptArr['EXTRA']['IMAGES'][$source]="";
									
									$removedImages++;
									
								}
							}
							
							
							
							$synonymsArr   = $conceptArr['EXTRA']['AKA'];
							
							foreach($synonymsArr as $lang => $langArr)
							{
								foreach($langArr as $source => $synonym)
								{
									$synonym = trim($synonym);
									
								
									
									if (!isset($isAddedBeforeArr[$synonym]))
									{
										$isAddedBeforeArr[$synonym]=1;
									}
									
									echoN("::".$synonym);
									
									
									if ( !empty($synonym) && 
									( isset($synonymsToBeRemovedArr[$synonym]) || $conceptNameAr==$synonym || $conceptNameEn==$synonym ) )
									{
										//echoN("removed:$synonym");
											
										//using $conceptArr since it is assigned after the loop
										$conceptArr['EXTRA']['AKA'][$lang][$source]="";
											
											
										$removedSynonyms++;
									}
								}
							}
								
							
							////// LINK
							$wpLink = $conceptArr['EXTRA']['WIKIPEDIA_LINK'];
							$dbPediaLink = $conceptArr['EXTRA']['DBPEDIA_LINK'];
							
							echoN($wpLink);
							echon($dbPediaLink);
							
							if (  (!empty($wpLink) && isset($linksToBeRemoved[$wpLink])) ||
								  (!empty($dbPediaLink) && isset($linksToBeRemoved[$dbPediaLink])) )
							{
								$conceptArr['EXTRA']['WIKIPEDIA_LINK']="";
								$conceptArr['EXTRA']['DBPEDIA_LINK']="";
								$removedLinks++;
							}
							
							
							
							
							//////////////////////////////

							echoN("ADDING:$concept");
							
							$filteredConcepts[$concept]=$conceptArr;
							
							
						}
						
					
						
						echoN("Unique Synonyms:".count($isAddedBeforeArr));
						//preprint_r($isAddedBeforeArr);
						
						/// RELATIONS FILTER
						
						$RELATIONS_EXCLUSION_RULES = array();
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"ابن","OBJECT"=>"الله");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"الله","VERB"=>"*","OBJECT"=>"الشخص");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"قال","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"قالت","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"يقول","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"إنسان","VERB"=>"$is_a_relation_name_ar","OBJECT"=>"حيوان");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"ناس","VERB"=>"$is_a_relation_name_ar","OBJECT"=>"حيوان");
						//DBpedia mess
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"صيد","VERB"=>"*","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"أنثى","VERB"=>"$is_a_relation_name_ar","OBJECT"=>"حيوان");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"مرء","VERB"=>"$is_a_relation_name_ar","OBJECT"=>"حيوان");
						
						//this verb has no significant meaning by its own -> addition words should be added to the verb to be meaningful
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"فجعلناهم","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"جعلناهم","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"وجعلناهم","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"فجعلناهم","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"وجعلناها","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"جعلناه","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"جعلناها","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"وجعلناه","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"جعلناك","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"فجعلناهن","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"جعل","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"تجعلونه","OBJECT"=>"*");
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"جعلناكم","OBJECT"=>"*");
						
						
						//object vague
						$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>"*","VERB"=>"*","OBJECT"=>"بعض");
						
						//preprint_r($excludedRelationsFromFileArr);
						
						
						foreach($excludedRelationsFromFileArr as $relationTripleText => $dummy)
						{
							$subject = convertConceptIDtoGraphLabel($relationTripleArr[0]);
							$verb = ($relationTripleArr[1]);
							$object = convertConceptIDtoGraphLabel($relationTripleArr[2]);
							
							$relationTripleArr = explode(",",$relationTripleText);
							$RELATIONS_EXCLUSION_RULES[]=array("SUBJECT"=>$subject,
												 "VERB"=>$verb,"OBJECT"=>$object);
							
						}
						
						//preprint_r($RELATIONS_EXCLUSION_RULES);
						
					
						
						
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
							
						
							echoN("$subject|$object|".isset($excludedConceptsArr[$subject]));
							
							// IF CONCEPTS ARE EXCLUDED, RELATIONS ARE ALSO EXSCLUDED
							if ( isset($excludedConceptsArr[$subject]) || isset($excludedConceptsArr[$object]))
							{
								$relationsRemoved++;
								continue;
							}
							
					
								
							
							$ruleFlag = false;
							foreach($RELATIONS_EXCLUSION_RULES as $index=>$ruleArr)
							{
								if ( 
									($ruleArr["SUBJECT"]=="*" || $ruleArr["SUBJECT"]==$subject ) &&
									($ruleArr["VERB"]=="*" || $ruleArr["VERB"]==$verbSimple) &&
									($ruleArr["OBJECT"]=="*" || $ruleArr["OBJECT"]==$object ) 
						           )
								   {
								   	echoN("removed");
								      $relationsRemoved++;
								      $ruleFlag = true;
									  break;
								   }
								   
								   
								   // REMOVE WHEN THE RELATION IS VARIANT OF AN EXCLUDED RELATION
								   if (
								   ( $ruleArr["SUBJECT"]=="*" || $ruleArr["SUBJECT"]==addAlefLam($subject) || $ruleArr["SUBJECT"]==removeAlefLamFromBegining($subject)) &&
								     ($ruleArr["VERB"]=="*" || $ruleArr["VERB"]==$verbSimple) &&
								     ($ruleArr["OBJECT"]=="*" || $ruleArr["OBJECT"]==addAlefLam($object) || $ruleArr["OBJECT"]==removeAlefLamFromBegining($object))
								   )
								   {
								   	echoN("//////////// RELATION REMOVED BY LAZY ASSUMPTION");
								   	preprint_r($ruleArr);
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
						
						echoN("Short Desc Removed:$removedShortDesc");
						echoN("Long Desc Removed:$removedLongDesc");
						
						echoN("Links Removed:$removedLinks");
						echoN("Images Removed:$removedImages");
						echoN("Synonyms Removed:$removedSynonyms");
		

						// final concepts and relations
						echoN(count($filteredConcepts));
						echoN(count($filteredRelationsArr));
						
						
						
					
						
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage7", serialize($filteredConcepts));
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($filteredRelationsArr));
						
					}
					
					
					if ($FINAL_POSTPROCESSING)
					{
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage7"));
							
					
						
						
						
						$TRANSLATION_FIXER_LOOKUP_TABLE = array();
						$TRANSLATION_FIXER_LOOKUP_TABLE['المسجد']='the mosque';
						$TRANSLATION_FIXER_LOOKUP_TABLE['عمل']='deed';
						$TRANSLATION_FIXER_LOOKUP_TABLE['شهر']='month';
						$TRANSLATION_FIXER_LOOKUP_TABLE['بني']='children of';
						$TRANSLATION_FIXER_LOOKUP_TABLE['كفار']='disbelievers';
						$TRANSLATION_FIXER_LOOKUP_TABLE['سورة']='chapter';
						$TRANSLATION_FIXER_LOOKUP_TABLE['جنود']='soldiers';
						$TRANSLATION_FIXER_LOOKUP_TABLE['زمرة']='group';
						$TRANSLATION_FIXER_LOOKUP_TABLE['خير']='good';
						$TRANSLATION_FIXER_LOOKUP_TABLE['ظالم']='unjust';
						$TRANSLATION_FIXER_LOOKUP_TABLE['غفور']='often forgiving';
						$TRANSLATION_FIXER_LOOKUP_TABLE['ابن']='son';
						$TRANSLATION_FIXER_LOOKUP_TABLE['ميت']='dead';
						$TRANSLATION_FIXER_LOOKUP_TABLE['بشر']='man';
						$TRANSLATION_FIXER_LOOKUP_TABLE['مرسل']='sent';
						$TRANSLATION_FIXER_LOOKUP_TABLE['ظلمات']='darkness';
						$TRANSLATION_FIXER_LOOKUP_TABLE['جن']='jinn';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['مؤمنات']='faithful women';
						$TRANSLATION_FIXER_LOOKUP_TABLE['صبر']='patience';
						$TRANSLATION_FIXER_LOOKUP_TABLE['تواب']='often returning';
						$TRANSLATION_FIXER_LOOKUP_TABLE['فقير']='needy';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['فصل']='decisive';
						$TRANSLATION_FIXER_LOOKUP_TABLE['هدي']='guidance';
						$TRANSLATION_FIXER_LOOKUP_TABLE['حديد']='iron';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['صيد']='hunt';
						$TRANSLATION_FIXER_LOOKUP_TABLE['برق']='the lightning';						
						$TRANSLATION_FIXER_LOOKUP_TABLE['حواريون']='the disciples';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['المستهزئين']='mockers';
						$TRANSLATION_FIXER_LOOKUP_TABLE['المساجد']='mosques';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['القبلة']='prayer direction';
						$TRANSLATION_FIXER_LOOKUP_TABLE['الأبصار']='vission';
						$TRANSLATION_FIXER_LOOKUP_TABLE['السبل']='paths';
						$TRANSLATION_FIXER_LOOKUP_TABLE['الحر']='heat';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['التكاثر']='competition to increase';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['ولد']='baby';
						$TRANSLATION_FIXER_LOOKUP_TABLE['وزر']='burden';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['آدم']='adam';
						$TRANSLATION_FIXER_LOOKUP_TABLE['أسر']='forms';
						$TRANSLATION_FIXER_LOOKUP_TABLE['الأشهر']='months';
						$TRANSLATION_FIXER_LOOKUP_TABLE['يأجوج']='gog';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['عقب']='descendents';
						$TRANSLATION_FIXER_LOOKUP_TABLE['الجلود']='skins';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['منشور']='Wide open';
						$TRANSLATION_FIXER_LOOKUP_TABLE['بحيرة']='bahirah';
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['أول']='first';
						
						
						$TRANSLATION_FIXER_LOOKUP_TABLE['علي']='on me';
						
						
						
						
						
						
						//preprint_r($TRANSLATION_FIXER_LOOKUP_TABLE);
						
						
						echoN("Concepts b4 PP:".count($finalConcepts));
						
						//echoN("DUPLICATES:");
						$duplicateCounter = 0;
						$filteredFinalConcepts = $finalConcepts;
						foreach($finalConcepts as $concept => $coneptArr)
						{
						
						
						
						
							$conceptNameEn  = $coneptArr['EXTRA']['TRANSLATION_EN'];
							$conceptNameAr  = $concept;
							$conceptType = $coneptArr['CONCEPT_TYPE'];
							
							
							//thing shouldnot be altered or removed
							if ( $conceptNameAr==$thing_class_name_ar)
							{
								//echoN("$conceptNameAr $thing_class_name_ar");
								continue;
							}
								
							
							if ( startsWithAL($conceptNameAr)  )
							{
								$conceptNameArNoAl = mb_substr($conceptNameAr, 2);
								
								//thing shouldnot be altered or removed
								if ( $conceptNameArNoAl==$thing_class_name_ar)
								{
									//echoN("$conceptNameAr $thing_class_name_ar");
									continue;
								}
								
								if (isset($finalConcepts[$conceptNameArNoAl]))
								{
									$duplicateConceptArr = $finalConcepts[$conceptNameArNoAl];
									
									
									
									$duplicateCounter++;
									
									$concept1RichnessScore = getConceptRichnessScore($coneptArr);
									$duplicateConceptRichnessScore = getConceptRichnessScore($duplicateConceptArr);
									
									
									echoN("ORIGINAL :[$conceptNameAr][$concept1RichnessScore]");
									echoN("DUPLICATE:[$conceptNameArNoAl][$duplicateConceptRichnessScore]");
									
									$toBeRemovedConcept = null;
									if ($duplicateConceptRichnessScore < $concept1RichnessScore )
									{
										$toBeRemovedConcept = $conceptNameArNoAl;
										updateNameInAllRelations($relationsArr,$conceptNameArNoAl,$conceptNameAr);
										
									}
									else
									{
										$toBeRemovedConcept = $conceptNameAr;
										updateNameInAllRelations($relationsArr,$conceptNameAr,$conceptNameArNoAl);
									}
									
									//echoN("$toBeRemovedConcept REMOVED");
									
									unset($filteredFinalConcepts[$toBeRemovedConcept]);
									
									
									
									
									//preprint_r($relationsArr);exit;

									
								}
							}
						}
						
						$finalConcepts = $filteredFinalConcepts;
						
						echoN("Duplicates:".$duplicateCounter);
						
						
						$relationsArrCopy = $relationsArr;
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
							
							if ( $subject==$object)
							{
								unset($relationsArrCopy[$hash]);
							}
							
						}
						
						$relationsArr = $relationsArrCopy;
						
						foreach($finalConcepts as $concept => $coneptArr)
						{
						
						
						
							$conceptNameEn  = $coneptArr['EXTRA']['TRANSLATION_EN'];
							$conceptNameAr  = $concept;
							$conceptType = $coneptArr['CONCEPT_TYPE'];
							
							
							////////// FIX ENGLISH TRANSLATION, SINCE WORD-TO-WORD FILE DOES NOT GIVE
							////////// PROPER ONTOLOGY TRANSLATIONS FOR THE VALUES IN THE LOOKUP TABLE
							if ( isset($TRANSLATION_FIXER_LOOKUP_TABLE[$conceptNameAr]))
							{
								$finalConcepts[$concept]['EXTRA']['TRANSLATION_EN'] = $TRANSLATION_FIXER_LOOKUP_TABLE[$conceptNameAr];
								//echoN($finalConcepts[$concept]['TRANSLATION_EN']);
								
							}
							/////////////////////////////////////////
							//ECHOn("___ $conceptNameAr | $conceptNameEn");
							
							// FIX CONCEPT TYPE FOR ANY CONCEPT WITH EXCLUDED RELATIONS
							if ( $conceptNameAr!=$thing_class_name_ar && $conceptType=="T-BOX" && !conceptHasSubclasses($relationsArr,$conceptNameAr) )
							{
								$finalConcepts[$concept]['CONCEPT_TYPE']='A-BOX';
							}
							
						}
						
						
						echoN("Concepts after PP:".count($finalConcepts));
						
						//exit;
						//preprint_r($relationsArr);exit;
						
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage8", serialize($finalConcepts));
						file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations", serialize($relationsArr));
							
					}
					
					
					
					
					
					//////////// COPY/FI FILIZE FINAL CONCEPTS /////////////////////////////
					persistTranslationTable();
					
					$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.stage8"));
					
					file_put_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.final", serialize($finalConcepts));
					
					////////////////////////////////////////////////////////////////////////
					
				
					
				
					if ($GENERATE_OWL_FILE)
					{
						$relationsArr = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.relations"));
						$finalConcepts = unserialize(file_get_contents("$ONTOLOGY_EXTRACTION_FOLDER/temp.final.concepts.final"));
			
						//preprint_r($finalConcepts);
						//exit;
						preprint_r($relationsArr);
						
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
						
						
						$ontology->createAnnotationProperty("is_qurana_ngram_concept");
					
						
						
						/////////////////////////////
						
						//////////  Things class
						//$ontology->createClass($thingClassName);
						
						//////// PUT THING AS THE FIRST CLASS SINCE OTHERS DEPEND ON, SO IT SHOULD BE 
						//////// READ FIRST IN DESERIALIZATION
						
						$updatedFinalConcept = array();
						$thingArr = $finalConcepts[$thing_class_name_ar];
						
						$updatedFinalConcept[$thing_class_name_ar]=$thingArr;
						unset($finalConcepts[$thing_class_name_ar]);
						$updatedFinalConcept = array_merge($updatedFinalConcept,$finalConcepts);
						
						$finalConcepts = $updatedFinalConcept;
						
						//////////////////////////////////////////
						
						
						//preprint_r($finalConcepts);exit;
						
						$counter++;
						foreach($finalConcepts as $concept => $coneptArr)
						{
							
							
							$conceptType  = $coneptArr['CONCEPT_TYPE'];
							$conceptNameEn  = ($coneptArr['EXTRA']['TRANSLATION_EN']);
							
							//echoN($concept." ".$conceptNameEn." ".$coneptArr['EXTRA']['IS_QURANA_NGRAM_CONCEPT']);
							
							// Qurana concept should be left as is to match concepts in inverted index
							if ( $coneptArr['EXTRA']['IS_QURANA_NGRAM_CONCEPT']!==true )
							{
							//	echoN($coneptArr['EXTRA']['IS_QURANA_NGRAM_CONCEPT']);
								//echoN($conceptNameEn);
								$conceptNameEn = removeBasicEnglishStopwordsNoNegation($conceptNameEn);
								
								//echoN($conceptNameEn);
							}
							else
							{
								$conceptNameEn = strtolower($conceptNameEn);
							}
							
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
								
								if ( !conceptHasParentClasses($relationsArr,$classOrInstanceName))
								{
									$ontology->addInstance($classOrInstanceName, $thingClassName , $properties);
								}
								else
								{
									$ontology->createClass($classOrInstanceName);
								}
								
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
							
							
							
							$isQurana = $coneptArr['EXTRA']['IS_QURANA_NGRAM_CONCEPT'];
							
							
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
							
							$ontology->addAnnotation($classOrInstanceName,"EN","is_qurana_ngram_concept",$isQurana);
							
							$counter = 0;
							foreach($alsoKnownAsENArr as $source=>$synonym)
							{
								$counter++;
								$ontology->addAnnotation($classOrInstanceName,"EN","synonym_$counter",$synonym);
							}
						
							foreach($alsoKnownAsARArr as $source=>$synonym)
							{
								$counter++;
								$ontology->addAnnotation($classOrInstanceName,"AR","synonym_$counter",$synonym);
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
									//echoN("!!! $subjectID|$verbSimple|$objectID|$relationsType");
									
									if ( $ontology->getClass($subjectID)!=null)
									{
										$ontology->addProperty($subjectID,$properties,"CLASS");
									}
									else
									{
										//PROPERTY IN INSTANCE
										$ontology->addProperty($subjectID,$properties);
									}
									
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







