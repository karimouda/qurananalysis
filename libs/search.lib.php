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
require_once(dirname(__FILE__)."/core.lib.php");
require_once(dirname(__FILE__)."/pos.tagger.lib.php");

/*
 * Get second level similarity distance by counting the common unique characters in both strings
 * The higher the distance the more both strings are similar
 */
function getDistanceByCommonUniqueChars($word1,$word2)
{
	$word1Arr =  preg_split('//u', $word1, -1, PREG_SPLIT_NO_EMPTY);
    $word2Arr =  preg_split('//u', $word2, -1, PREG_SPLIT_NO_EMPTY);

    
    $uniqueCommonChars = array_unique(array_intersect($word1Arr, $word2Arr));
    
    $commonChars = implode($uniqueCommonChars);
    
    $extraPoints = 0;
    

    //echoN("$word1,$word2 $commonChars");
    // if tword 1 mtches word2 in both first and last character then add more similarity score
    if ( current($uniqueCommonChars)==current($word2Arr) )
    {
    
    	$extraPoints = $extraPoints+1;
    }
    
    if ( end($uniqueCommonChars)==end($word2Arr) )
    {
    
    	$extraPoints = $extraPoints+1;
    }
    
    
 

    
    return (mb_strlen($commonChars)+$extraPoints);
}

function getSimilarWords($lang,$queryWords)
{

	$WORDS_FREQUENCY_WORDS = getModelEntryFromMemory($lang, "MODEL_CORE", "WORDS_FREQUENCY", "WORDS");
	
	

	$simmilarWords = array();
	
	
	foreach ($WORDS_FREQUENCY_WORDS as $wordFromQuran=>$one)
	{
		
		if ( $lang=="EN")
		{
			//to remove ":"
			$wordFromQuran = cleanAndTrim($wordFromQuran);
			
		}
		
		foreach ($queryWords as $wordFromQuery)
		{
			
			//echoN("abs(mb_strlen($wordFromQuran)-mb_strlen($wordFromQuery))=".(abs(mb_strlen($wordFromQuran)-mb_strlen($wordFromQuery))));
			
			// only one char len diff between words for not comparing all 14k words
			if ( abs(mb_strlen($wordFromQuran)-mb_strlen($wordFromQuery)) <=3 )
			{
				
				//echoN($wordFromQuran);
				
				$distance = getDistanceBetweenWords($wordFromQuran,$wordFromQuery);
				
				//echoN("$wordFromQuran $wordFromQuery | $distance");
				
				if ( $distance <=3 )
				{
					// compund score of both min edit distance and common unique chars
					$simmilarWords[$wordFromQuran] = (1/$distance)+getDistanceByCommonUniqueChars($wordFromQuran,$wordFromQuery);
					
					//echoN($simmilarWords[$wordFromQuran]);
				}
				
				
				
			}
			
		}
		
	}
	
	//sort words by simmilarity to query
	arsort($simmilarWords);
	
	//preprint_r($simmilarWords);
	
	return $simmilarWords;
	
}

function addToInvertedIndex(&$invertedIndexBatchApcArr,$lang,$word,$suraID,$verseID,$wordIndex,$wordType,$extraInfo=null)
{


	$indexInAyaName = "INDEX_IN_AYA_EMLA2Y";

	if ($wordType!="NORMAL_WORD")
	{
		$indexInAyaName = "INDEX_IN_AYA_UTHMANI";
	}


	$entryValueObj = array("SURA"=>$suraID,"AYA"=>$verseID,"$indexInAyaName"=>$wordIndex,"WORD_TYPE"=>"$wordType","EXTRA_INFO"=>$extraInfo);

	//addToMemoryModelList($lang,"MODEL_SEARCH","INVERTED_INDEX",$word,$entryValueObj);


	$apcMemoryEntryKey = "$lang/MODEL_SEARCH/INVERTED_INDEX/$word";
	
	$invertedIndexBatchApcArr[$apcMemoryEntryKey][]=$entryValueObj;
	//$INVERTED_INDEX[$word][] = $entryValueObj;

}



function posTagUserQuery($query, $lang)
{

	
	$taggedSignificantWords = array();
	
	if ( $lang=="EN")
	{
		$taggedWordsArr = posTagText($query);
	
		
		
		foreach($taggedWordsArr as $posArr)
		{
			$word = trim($posArr['token']);
			$tag  = trim($posArr['tag']);
			
			// no need for that, removed 'loves'(verb) which is not needed to search ontology verbs
			//if ( strpos($tag,"NN")!==false || strpos($tag,"NP")!==false )
			//{
				
				$taggedSignificantWords[$word] = $tag;
			//}
			
		}
	
		
	}
	else 
	{
	
		//$query = removeStopwordsAndTrim($query,$lang);
		
		$tempArr =  explode(" ", $query);
		
		foreach($tempArr as $index=>$word)
		{
			if ( isset($MODEL_CORE['STOP_WORDS'][$word]) )
			{
				///STOPWORD
				$taggedSignificantWords[$word] = "STW";
			}
			else 
			{
				$taggedSignificantWords[$word] = "NN";
			}
		}
		
	}
	
		return $taggedSignificantWords;
}
//TODO: ADD SYNONYMS ENRICHMENT
function extendQueryWordsByDerivations($taggedSignificantWords,$lang)
{

	
	foreach($taggedSignificantWords as $word => $posTag)
	{
		// avoid small words, will lead to too many iirelevant derivations
		if (mb_strlen($word) <=2 ) continue;
		
		if ( $lang=="EN")
		{
	
			if ($posTag=="NN")
			{
				$plural = $word."s";
				$taggedSignificantWords[$plural] = "$posTag"."S";
			}
			else
			if ($posTag=="NNS")
			{
			
				$single = substr($word, 0,-1);
				$taggedSignificantWords[$single] = substr($posTag, 0,-1);
			}
		}
		else
		{
		
			$simmlarWords = array();
			
			$qaOntologyConceptsIterator = getAPCIterator("ALL\/MODEL_QA_ONTOLOGY\/CONCEPTS\/.*");
			
			foreach($qaOntologyConceptsIterator as $conceptsCursor )
			{
				$conceptID = getEntryKeyFromAPCKey($conceptsCursor['key']);
			
				$mainConceptArr = $conceptsCursor['value'];
	
				
				$conceptLabelAR = $mainConceptArr['label_ar'];
				
			
			
				
				$dist = myLevensteinEditDistance($word, $conceptLabelAR);
				if ( $dist <=5 )
				{
					$dist = getDistanceByCommonUniqueChars($word, $conceptLabelAR);
					
					$simmlarWords[$conceptLabelAR]=$dist;
				}
				
				$i=1;
				while(isset($mainConceptArr['synonym_'.$i]) && isArabicString($mainConceptArr['synonym_'.$i]))
				{
					$synonym = $mainConceptArr['synonym_'.$i];
					
					$dist = myLevensteinEditDistance($word, $synonym);
					
					if ( $dist <=5 )
					{
						$dist = getDistanceByCommonUniqueChars($word, $synonym);
						$simmlarWords[$synonym]=$dist;
					}
					
					$i++;
				}
				
			}
			
			foreach($simmlarWords as $conceptWord => $distBySimChars)
			{
				$diff = mb_strlen($conceptWord)-mb_strlen($word);
				
				$absDiffSize = abs($diff);
				
				
				// $word is bigger
				if ( $diff < 0)
				{
					$absDiffSize = abs($diff);
					
					$diffStr = getStringDiff($conceptWord, $word);
					//echoN($diffStr);
					
					//حيوان => الحيوانات
					// the bigger word should not contain space الله => سبيل الله
					// $diffStr=="الات" for حيوان = الحياوانات
					if ( mb_strpos($word, $conceptWord)!==false && strpos($word," ")===false && ($diffStr=="ات" || $diffStr=="ال" || $diffStr=="الات" ) )
					{
						//echoN("$word, $conceptWord");
						
						
						
						/// convert word to noun plular
						$taggedSignificantWords[$word] = "NNS";
						
						// concept word is singular
						$taggedSignificantWords[$conceptWord] = "NN";
					}
					else if ($diff==1 )
					{
						$wordLastCharTrimmed = mb_substr($conceptWord, 0,-1);
					
						if ( $wordLastCharTrimmed."ات"==$word)
						{
							
							
							/// convert word to noun plular
							$taggedSignificantWords[$word] = "NNS";
							
							/// convert word to noun plular
							$taggedSignificantWords[$conceptWord] = "NN";
						}
					}

				}
				// $word is smaller
				else
				{
					$diffStr = getStringDiff($conceptWord, $word);
					
					
					//  الحيوانات => حيوان
					// the bigger word should not contain space الله => سبيل الله
					if ( $diff!=0 && mb_strpos($conceptWord,$word)!==false && strpos($conceptWord," ")===false && ($diffStr=="ات" || $diffStr=="ال" ||  $diffStr=="الات") )
					{
					
						//echoN("$word,$conceptWord");
						
						/// convert word to noun plular
						$taggedSignificantWords[$conceptWord] = "NNS";
					

					}
					else if ($diff==1 )
					{
						$wordLastCharTrimmed = mb_substr($word, 0,-1);
						
						if ( $wordLastCharTrimmed."ات"==$conceptWord)
						{
							/// convert word to noun plular
							$taggedSignificantWords[$conceptWord] = "NNS";
						}
					}
				}
				
				
			
			}
			
			// limit the number of derivations+original terms to 15
			$taggedSignificantWords = array_slice($taggedSignificantWords, 0,10);
			
			//arsort($simmlarWords);
			//preprint_r($taggedSignificantWords);
			//preprint_r($simmlarWords);
			//exit;
			
		}
			
	}
	
	//preprint_r($taggedSignificantWords);
	
	return $taggedSignificantWords;
}

function extendQueryWordsByConceptTaxRelations($extendedQueryArr,$lang,$isQuestion = false)
{
	global  $is_a_relation_name_ar,$thing_class_name_en,$thing_class_name_ar,$TRANSLATION_MAP_EN_TO_AR;
	
	$conceptsFromTaxRelations = array();
	
	
	if ( $lang=="EN")
	{
		$thing_class_name = strtolower($thing_class_name_en);
	}
	else
	{
		$thing_class_name = $thing_class_name_ar;
	}
	
	
	
	$questionIncludesVerb =  ($isQuestion && doesQuestionIncludesVerb($extendedQueryArr));



	
	foreach($extendedQueryArr as  $word=> $pos)
	{
	
		// ignore any tern which is not nound or verb
		// [0-9]+ to allow normal non pos tagged queries - incase of pharase search
		if ( !preg_match("/NN|VB|[0-9]+/", $pos)) continue;
		
		
		
		/*
		 * Differentiate between word and concept ID, in English 'word' is needed unmapped for 'verb' search
		 */
		
		if ( $lang=="EN")
		{
			//corresponding arabic Concept - only if it is a concept
			//$conceptIDStr = $MODEL_QA_ONTOLOGY['CONCEPTS_EN_AR_NAME_MAP'][$word];
			$conceptIDStr  = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "CONCEPTS_EN_AR_NAME_MAP", $word);
		}
		else 
		{
			$conceptIDStr = $word;
		}
	
	
		
		//!$questionIncludesVerb since if the question includes a verb then the user is not looking ofr is-a relation
		if ( !$questionIncludesVerb && modelEntryExistsInMemory("ALL", "MODEL_QA_ONTOLOGY", "CONCEPTS", $conceptIDStr) )
		{
			//$inboundRelationsArr = $MODEL_QA_ONTOLOGY['GRAPH_INDEX_TARGETS'][$conceptIDStr];
			
			$inboundRelationsArr = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "GRAPH_INDEX_TARGETS", $conceptIDStr);
				
			
			//$outboundRelationsArr = $MODEL_QA_ONTOLOGY['GRAPH_INDEX_SOURCES'][$conceptIDStr];
			
			$outboundRelationsArr = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "GRAPH_INDEX_SOURCES", $conceptIDStr);
			
		
		
			
			// FOR INBOUND IS-A RELATIONS EX: X IS AN ANIMAL($word)
			foreach($inboundRelationsArr as $index => $relationArr)
			{
				$subject = $relationArr['source'];
				$verbAR = $relationArr['link_verb'];
				
				$subjectConceptArr = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "CONCEPTS", $subject);
				
				
				if ( $lang=="EN")
				{
					$subject = trim(removeBasicEnglishStopwordsNoNegation(($subjectConceptArr['label_en'])));
				}
				
				/// CLEAN AND REPLACE CONCEPT
				$subject = cleanWordnetCollocation($subject);
				///////////////////////////
				
				//TODO: check if this is needed $subject!=$thing_class_name
				if ( $verbAR==$is_a_relation_name_ar && $subject!=$thing_class_name)
				{
					
					
					// ignore phrase parent concepts
					// عذاب + عذاب الله
					if ( strpos($subject,$conceptIDStr)===false)
					{
						$conceptsFromTaxRelations[]=$subject;
					}
					
				
					
					
				}
				
	
				
		
				
			}
			
			
	
			
			if ( $isQuestion )
			{
			
				
				// FOR OUTBOUND IS-A RELATIONS EX: X($word) IS A PERSON			
				foreach($outboundRelationsArr as $index => $relationArr)
				{
					
					$verbAR = $relationArr['link_verb'];
					$object = $relationArr['target'];
					
					$objectConceptArr = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "CONCEPTS", $object);
					
				
					if ( $lang=="EN")
					{
						$object = trim(removeBasicEnglishStopwordsNoNegation(($objectConceptArr['label_en'])));
					}
				
					/// CLEAN AND REPLACE CONCEPT
					$object = cleanWordnetCollocation($object);
					///////////////////////////
				
					
					
					if ( $verbAR==$is_a_relation_name_ar && $object!=$thing_class_name)
					{
						//echoN(" $object!=$thing_class_name");
						//echoN("|$thing_class_name|$object|");
						// ignore phrase parent concepts
						// عذاب + عذاب الله
						if ( strpos($object,$conceptIDStr)===false)
						{
							$conceptsFromTaxRelations[]=$object;
						}
						
			
						
					
					}
					
				
				
				}
			}
		}
		
		
		if ( !$isQuestion )
		{
		
			///////// add concept name to query if the current query word is found to be synonym to that concept
			if ( modelEntryExistsInMemory("ALL", "MODEL_QA_ONTOLOGY", "SYNONYMS_INDEX", $word) )
			{
				//$conceptNameAR = $MODEL_QA_ONTOLOGY['SYNONYMS_INDEX'][$word];
				$conceptNameAR = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "SYNONYMS_INDEX", $word);
				
				
				$finalConceptName = $conceptNameAR;
	
				if ( $lang=="EN")
				{
					$conceptArr = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "CONCEPTS", $conceptNameAR);
					$finalConceptName  = $conceptArr['label_en'];
				}
				$conceptsFromTaxRelations[]=$finalConceptName;
			}
			
			//////////////////////////////////////////////////////////////
		}
		

		//$lang=="AR" check since AR words are not PoS tagged yet
		if ( $isQuestion && ($lang=="AR" ||posIsVerb($pos)) )
		{
			if (  (($verbArr=getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "VERB_INDEX", $word))!=null) || ($verbArr = isWordPartOfAVerbInVerbIndex($word,$lang) ) )
			{
				
		
				foreach($verbArr as $index => $verbSTArr)
				{
					$subject = $verbSTArr['SUBJECT'];
					$object = $verbSTArr['OBJECT'];
					
					if ( $lang=="EN")
					{
						$subjectConceptArr = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "CONCEPTS", $wordConveretedToConceptID);
						$objectConceptArr = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "CONCEPTS", $wordConveretedToConceptID);
						
						$object = trim(removeBasicEnglishStopwordsNoNegation(($objectConceptArr['label_en'])));
						$subject = trim(removeBasicEnglishStopwordsNoNegation(($subjectConceptArr['label_en'])));
					}
					
					// we are not interested in X is_a Thing - does not add value
					if ( $object==$thing_class_name)
					{
						continue;
					}
					
					
					//echoN("-$subject>$word>$object");
				
					//echoN(" $object!=$thing_class_name");
					
					if ( isset($extendedQueryArr[$subject]))
					{
					
						$conceptsFromTaxRelations[]=$object;
						
						
						
					}
					else 
					if ( isset($extendedQueryArr[$object]))
					{
						
						$conceptsFromTaxRelations[]=$subject;
						
			
							
					}
				}
			}
		}
	
		
			
	}
	
	
	$conceptsFromTaxRelations = array_unique($conceptsFromTaxRelations);
	
	//preprint_r($conceptsFromTaxRelations);

	
	return $conceptsFromTaxRelations;
}

function extendQueryByExtractingQACDerviations($extendedQueryWordsArr)
{
	global $MODEL_SEARCH;




		/** GET ROOT/STEM FOR EACH QUERY WORD **/
		foreach ($extendedQueryWordsArr as $word=>$index)
		{
	
			//preprint_r($MODEL_SEARCH['INVERTED_INDEX'][$word]);exit;
			
			$invertedIndexEntryArr1 = getModelEntryFromMemory("AR","MODEL_SEARCH","INVERTED_INDEX",$word);
	
			foreach ($invertedIndexEntryArr1 as $documentArrInIndex)
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
				//preprint_r($MODEL_QAC['QAC_MASTERTABLE'][$qacLocation]);
				
				 $qacMasterTableEntryArr2 = getModelEntryFromMemory("AR","MODEL_QAC","QAC_MASTERTABLE",$qacLocation);
		  	
					
				// search QAC for roots and LEMMAS for this word
				foreach ($qacMasterTableEntryArr2 as $segmentIndex => $segmentDataArr)
				{
					$segmentFormAR = $segmentDataArr['FORM_AR'];
					$segmentFormARimla2y = getItemFromUthmaniToSimpleMappingTable($segmentFormAR);
	
	
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
							
							
						/*
						 if ( empty($stemOfQueryWord) || empty($rootOfQueryQord))
						 {
						preprint_r($MODEL_QAC['QAC_MASTERTABLE'][$qacLocation]);
						echoN($rootOfQueryQord);
						echoN($stemOfQueryWord);
						exit;
						}*/
							
							
							
						// add the STEMS to out extended query words
						if ( !empty($rootOfQueryQord) && !isset($extendedQueryWordsArr[$rootOfQueryQord])) { $extendedQueryWordsArr[$rootOfQueryQord]=1;}
						if ( !isset($extendedQueryWordsArr[$stemOfQueryWord])) { $extendedQueryWordsArr[$stemOfQueryWord]=1;}
							
							
					}
	
				}
					
					
	
					
			}
			
			////////// CUSTOM ROOT TABLE ///////////
			//TODO:
			$zawaga = "زوج";
			$CUSTOM_ROOTS_TABLE['الزواج']=$zawaga;
		
			if (isset($CUSTOM_ROOTS_TABLE[$word]))
			{
				$extendedQueryWordsArr[$CUSTOM_ROOTS_TABLE[$word]]=1;
				
			}
			
			////////////////////////////////////////
		}
	
	
	
		$QURAN_TEXT = getModelEntryFromMemory("AR", "MODEL_CORE", "QURAN_TEXT", "");
		$TOTALS = getModelEntryFromMemory("AR", "MODEL_CORE", "TOTALS", "");
		
		$PAUSEMARKS = $TOTALS['PAUSEMARKS'];
		
		/** GET EMLA2Y (SIMPLE) WORDS CORRESPONDING TO ANY QAC SEGMENT CONTAINING THE ROOT/STEMS IN THE EXTENDED QUERY WORD FROM INVERTED INDEX
		 *  ADD TO EXTENDED QUERY WORDS
		 *  TODO: recheck to remove this whole loop
		 * **/
		foreach ($extendedQueryWordsArr as $word => $dummy)
		{
	
			// ONLY UTHMANI SHOULD BE HANDLED
			if ( isSimpleQuranWord($word)) continue;
	
			$invertedIndexEntry = getModelEntryFromMemory("AR","MODEL_SEARCH","INVERTED_INDEX",$word);
			
			
			foreach ($invertedIndexEntry as $documentArrInIndex)
			{
				$SURA = $documentArrInIndex['SURA'];
				$AYA = $documentArrInIndex['AYA'];
				$INDEX_IN_AYA_EMLA2Y = $documentArrInIndex['INDEX_IN_AYA_EMLA2Y'];
				$INDEX_IN_AYA_UTHMANI= $documentArrInIndex['INDEX_IN_AYA_UTHMANI'];
				$WORD_TYPE = $documentArrInIndex['WORD_TYPE'];
				$EXTRA_WORD_TYPE_INFO = $documentArrInIndex['EXTRA_INFO'];
	
				$qacLocation = getQACLocationStr($SURA+1,$AYA+1,$INDEX_IN_AYA_UTHMANI);
					
				//preprint_r($MODEL_QAC['QAC_MASTERTABLE'][$qacLocation]);
					
				$verseText = getVerseByQACLocation($QURAN_TEXT,$qacLocation);
					
				$wordFromVerse = getWordFromVerseByIndex($PAUSEMARKS,$verseText,$INDEX_IN_AYA_EMLA2Y);
					
			
				if ( empty($wordFromVerse) ) continue;
					
					
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
		
	
	
	return $extendedQueryWordsArr;
}

function getScoredDocumentsFromInveretdIndex($extendedQueryWordsArr,$query,$isPhraseSearch,$isQuestion,$isColumnSearch,$columnSearchKeyValParams,$isConceptSearch,$lang,$isTransliterationSearch)
{
	global $MODEL_CORE,$MODEL_SEARCH;;

	
	
	$QURAN_TEXT = getModelEntryFromMemory($lang, "MODEL_CORE", "QURAN_TEXT", "");
	$TOTALS = getModelEntryFromMemory($lang, "MODEL_CORE", "TOTALS", "");
	
	$PAUSEMARKS = $TOTALS['PAUSEMARKS'];
	


	
	if ( $isColumnSearch)
	{
		$SURA = $columnSearchKeyValParams['KEY']-1;
		$isFullChapter = ($columnSearchKeyValParams['VAL']=="ALL");
		
		
		if ( $isFullChapter)
		{
			
			
			$suraSize = count($QURAN_TEXT[$SURA]);
			
			for($AYA=0;$AYA<$suraSize;$AYA++)
			{
			
				$scoringTable[$SURA.":".$AYA]=array();
				$scoringTable[$SURA.":".$AYA]['SCORE']=1;
				$scoringTable[$SURA.":".$AYA]['SURA']=$SURA;
				$scoringTable[$SURA.":".$AYA]['AYA']=$AYA;
			}
		}
		else
		{
			
			$AYA = $columnSearchKeyValParams['VAL']-1;
			
			// VERSE VALIDITY CHECK
			$qacLocation = getQACLocationStr($SURA+1,$AYA+1,0);
			
			$verseText = getVerseByQACLocation($QURAN_TEXT, $qacLocation);
			
			if ( empty($verseText)) 
			{
				return array();
			}
			//////////////////////
					
			$scoringTable[$SURA.":".$AYA]=array();
				
			$scoringTable[$SURA.":".$AYA]['SCORE']=1;
			$scoringTable[$SURA.":".$AYA]['SURA']=$SURA;
			$scoringTable[$SURA.":".$AYA]['AYA']=$AYA;
		
		}
		
		return $scoringTable;
	}


	

//	$MODEL_QURANA  = apc_fetch("MODEL_QURANA");
	//preprint_r($extendedQueryWordsArr);
	
	//$isOneWordQuery = preg_match("/ /", $query)==0;
	
	//preprint_r($extendedQueryWordsArr);
	
	/**
	 * GET ALL RESULT FORM INDEX USING EXTENDED QUERY WORD (WHICH INCLUDES ALL VARIATIONS AND PRONOUNS)
	 */
	foreach ($extendedQueryWordsArr as $word =>$targetQACLocation)
	{
		//echoN("|$word|");
		//echoN($lang);
		//echoN($isConceptSearch);
		
		/*if ($lang=="EN" && $isConceptSearch )
		{

			
		}*/
		
		//preprint_r($MODEL_SEARCH['INVERTED_INDEX'][$word]);
		$invertedIndexEntry = getModelEntryFromMemory($lang,"MODEL_SEARCH","INVERTED_INDEX",$word);
		
		foreach ($invertedIndexEntry as $documentArrInIndex)
		{
	
			//echoN("$word");
			//preprint_r($documentArrInIndex);;
			$SURA = $documentArrInIndex['SURA'];
			$AYA = $documentArrInIndex['AYA'];
			$INDEX_IN_AYA_EMLA2Y = $documentArrInIndex['INDEX_IN_AYA_EMLA2Y'];
			$INDEX_IN_AYA_UTHMANI= $documentArrInIndex['INDEX_IN_AYA_UTHMANI'];
			$WORD_TYPE = $documentArrInIndex['WORD_TYPE'];
			$EXTRA_INFO = $documentArrInIndex['EXTRA_INFO'];
	
	
	
	
			//echo getQACLocationStr($SURA,$AYA,$INDEX_IN_AYA_EMLA2Y);
			$qacLocation = getQACLocationStr($SURA+1,$AYA+1,$INDEX_IN_AYA_UTHMANI);
	
			$verseText = getVerseByQACLocation($QURAN_TEXT, $qacLocation);
	
			
			/*
			 *
			* NOTE: A DECISION SHOULD BE TAKEN TO SERACH AROUND AND REMOVE PAUSE MARKS OR NOT
			*/
			if ($lang=="AR")
			{
				$verseTextWithoutPauseMarks = removePauseMarkFromVerse($verseText);
			}
			else
			{
				$verseTextWithoutPauseMarks = removeSpecialCharactersFromMidQuery($verseText);
			}
			
			//echoN("|$query|$verseTextWithoutPauseMarks");
			$fullQueryIsFoundInVerseCount = preg_match_all("/(^|[ ])$query([ ]|\$)/umi", $verseTextWithoutPauseMarks);

			//echoN("$query | $word");
		
			
			if ( $isPhraseSearch && $WORD_TYPE!="PRONOUN_ANTECEDENT")
			{
					
				$numberOfOccurencesForWord = $fullQueryIsFoundInVerseCount;
				
				
				if ( $numberOfOccurencesForWord ==0)
				{
					continue;
				}
					
					
					
			}
			else
			{
				$numberOfOccurencesForWord = preg_match_all("/$word/um", $verseText);
				
				/*if ( $numberOfOccurencesForWord> 100)
				{
					echoN($word);
					echoN($verseText);
					preprint_r($extendedQueryWordsArr);
					exit;
				}*/
			}
	
			//echoN($numberOfOccurencesForWord);
			//echoN("$qacLocation|$targetQACLocation|$word|$EXTRA_INFO|$WORD_TYPE");
	
	
			// incase of non normal word ( QAC/QURANA) .. translate WordIndex from Uthmani script to Imla2y script
			/*if ( $WORD_TYPE!="NORMAL_WORD"   )
			{
			//echoN("OLD:$INDEX_IN_AYA_EMLA2Y");
			$INDEX_IN_AYA_EMLA2Y = getImla2yWordIndexByUthmaniLocation($qacLocation,$UTHMANI_TO_SIMPLE_LOCATION_MAP);
			//echoN("NEW:$INDEX_IN_AYA_EMLA2Y");
			}*/
	
			//echoN($word);
			//preprint_r($documentArrInIndex);
			//preprint_r($MODEL_QAC['QAC_MASTERTABLE'][$qacLocation]);
	
			if (!isset($scoringTable[$SURA.":".$AYA]))
			{
				$scoringTable[$SURA.":".$AYA]=array();
					
				$scoringTable[$SURA.":".$AYA]['SCORE']=0;
				$scoringTable[$SURA.":".$AYA]['FREQ']=0;
				$scoringTable[$SURA.":".$AYA]['DISTANCE']=0;
				$scoringTable[$SURA.":".$AYA]['WORD_OCCURENCES_COUNT']=0;
				$scoringTable[$SURA.":".$AYA]['QUERY_WORDS_IN_VERSE']=0;
				$scoringTable[$SURA.":".$AYA]['IS_FULL_QUERY_IN_VERSE']=0;
				$scoringTable[$SURA.":".$AYA]['SURA']=$SURA;
				$scoringTable[$SURA.":".$AYA]['AYA']=$AYA;
				$scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS']=array();
				$scoringTable[$SURA.":".$AYA]['WORD_TYPE']=$WORD_TYPE;
				$scoringTable[$SURA.":".$AYA]['EXTRA_INFO']=$EXTRA_INFO;
				$scoringTable[$SURA.":".$AYA]['INDEX_IN_AYA_EMLA2Y']=$INDEX_IN_AYA_EMLA2Y;
				$scoringTable[$SURA.":".$AYA]['INDEX_IN_AYA_UTHMANI']=$INDEX_IN_AYA_UTHMANI;
				$scoringTable[$SURA.":".$AYA]['PRONOUNS']=array();
					
			}
	
	
	
	
			$scoringTable[$SURA.":".$AYA]['WORD_OCCURENCES_COUNT'] = $numberOfOccurencesForWord;
	
			//echoN($numberOfOccurencesForWord);
	
			if ( !isset($scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS'][$word]) &&
			$numberOfOccurencesForWord > 0 &&
			$scoringTable[$SURA.":".$AYA]['FREQ']>0 )
			{
				//TODO: seems duplicate of WORD_OCCURENCES_COUNT
				// Raise the frequency (score) of ayas containing more than one of the query items
				$scoringTable[$SURA.":".$AYA]['FREQ']++;//=$numberOfOccurencesForWord;
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
				$wordInAya = getItemFromUthmaniToSimpleMappingTable($EXTRA_INFO);
		
					
				if ( empty($wordInAya ) ) { $wordInAya = getItemFromUthmaniToSimpleMappingTable(removeTashkeel($EXTRA_INFO)); }
					
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
				
				if ( $isTransliterationSearch )
				{
					
					$scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS'][$word]=$WORD_TYPE;
				}
				else
				{
					// word was in original user query, not in our extended one
					///if ( in_array($word,$queryWordsArr))
					//{
		
						
					$scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS'][$word]=$WORD_TYPE;
				}
				//}
			}
	
			
			
			$scoringTable[$SURA.":".$AYA]['IS_FULL_QUERY_IN_VERSE'] = $fullQueryIsFoundInVerseCount;
			
	
			$scoringTable[$SURA.":".$AYA]['QUERY_WORDS_IN_VERSE']=count($scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS']);
	
			$scoringTable[$SURA.":".$AYA]['SCORE'] = ($scoringTable[$SURA.":".$AYA]['FREQ']/2)+
													 ($scoringTable[$SURA.":".$AYA]['DISTANCE']*1)+
													 ($scoringTable[$SURA.":".$AYA]['QUERY_WORDS_IN_VERSE']*10)+
													 (count($scoringTable[$SURA.":".$AYA]['PRONOUNS'])*1)+
													 ($scoringTable[$SURA.":".$AYA]['WORD_OCCURENCES_COUNT']*1)+
													 ($scoringTable[$SURA.":".$AYA]['IS_FULL_QUERY_IN_VERSE']*20);
	
		}
	}
	
	

	
	rsortBy($scoringTable, 'SCORE');
	
	
	//preprint_r($scoringTable);exit;
	
	return $scoringTable;
}

function getStatsByScoringTable($scoringTable)
{
	//// GENERATING DATA FOR CHART AND STATISTICS TABLE
	$uniqueResultSuras = array();
	$uniqueResultVerses = array();
	$uniqueResultRepetitionCount = 0;
		
	$frequencyPerSuraArr = array();
		
	//preprint_r($scoringTable);exit;
		
	foreach ($scoringTable as $verseID => $scoringArr)
	{
		/*
		 * SURA and AYA IDS are 0 based instrad of 1
		*/
		$suraID = $scoringArr['SURA']+1;
		$ayaID = $scoringArr['AYA'];
		$freq = $scoringArr['FREQ'];
		$wordOccurences = $scoringArr['WORD_OCCURENCES_COUNT'];
	
		$uniqueResultSuras[$suraID]=1;
	
			
		$uniqueResultVerses[$verseID]=1;
		$uniqueResultRepetitionCount += $wordOccurences;
	
		if ( !isset($frequencyPerSuraArr[$suraID]) ) $frequencyPerSuraArr[$suraID]=0;
		$frequencyPerSuraArr[$suraID]+=1;
			
	
	}

		
	//echoN($wordDistributionChartJSON);
		
	$searchResultsChaptersCount = count($uniqueResultSuras);
	$searchResultsVersesCount = count($uniqueResultVerses);
		
	
	$statsArr = array();
	$statsArr['UNIQUE_REP']=$uniqueResultRepetitionCount;
	$statsArr['CHAPTERS_COUNT']=$searchResultsChaptersCount;
	$statsArr['VERSES_COUNT']=$searchResultsVersesCount;
	
	return $statsArr;
}

function getDistributionChartData($scoringTable)
{
	//// GENERATING DATA FOR CHART AND STATISTICS TABLE


	$frequencyPerSuraArr = array();



	foreach ($scoringTable as $verseID => $scoringArr)
	{
		/*
		 * SURA and AYA IDS are 0 based instrad of 1
		*/
		$suraID = $scoringArr['SURA']+1;
		$ayaID = $scoringArr['AYA'];

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
	return $wordDistributionChartJSON;
}

function printResultVerses($scoringTable,$lang,$direction,$query,$isPhraseSearch,$isQuestion,$script,$significantCollocationWords=null,$isTransliterationSearch=false)
{
	global $script,$TRANSLITERATION_VERSES_MAP;
	
	
	
	$QURAN_TEXT = getModelEntryFromMemory($lang, "MODEL_CORE", "QURAN_TEXT", "");
	$QURAN_TEXT_UTH = getModelEntryFromMemory("AR_UTH", "MODEL_CORE", "QURAN_TEXT", "");

	$META_DATA = getModelEntryFromMemory($lang, "MODEL_CORE", "META_DATA", "");
	$TOTALS = getModelEntryFromMemory($lang, "MODEL_CORE", "TOTALS", "");
	
	if ( $lang=="EN")
	{
	
		
		if ($script=="simple")
		{
			$QURAN_TEXT_OTHER_LANG = getModelEntryFromMemory("AR", "MODEL_CORE", "QURAN_TEXT", "");
		}
		else
		{
			$QURAN_TEXT_OTHER_LANG = $QURAN_TEXT_UTH;
		}
	
	}
	else
	{
		
		$QURAN_TEXT_OTHER_LANG = getModelEntryFromMemory("EN", "MODEL_CORE", "QURAN_TEXT", "");
	
	}
	

	
	
	//preprint_r($scoringTable);exit;
	
	$searchResultsTextArr = array();
	
	//preprint_r($scoringTable);exit;
	
	$relevanceReverseOrderIndex = count($documentScoreArr);
	foreach($scoringTable as $documentID => $documentScoreArr)
	{
		//preprint_r($documentScoreArr);
		$relevanceReverseOrderIndex--;
	
		$SURA = $documentScoreArr['SURA'];
		$AYA = $documentScoreArr['AYA'];
		$TEXT = $QURAN_TEXT[$SURA][$AYA];
		$TEXT_UTH = $QURAN_TEXT_UTH[$SURA][$AYA];
		$TEXT_TRANSLITERATED = cleanTransliteratedText($TRANSLITERATION_VERSES_MAP[($SURA+1).":".($AYA+1)]);
		
		
		$WORD_TYPE = $documentScoreArr['WORD_TYPE'];
		$EXTRA_INFO = ($documentScoreArr['EXTRA_INFO']);
		$INDEX_IN_AYA_EMLA2Y = $documentScoreArr['INDEX_IN_AYA_EMLA2Y'];
		$WORDS_IN_AYA = $documentScoreArr['POSSIBLE_HIGHLIGHTABLE_WORDS'];
		$PRONOUNS = $documentScoreArr['PRONOUNS'];
	
		$score = $documentScoreArr['SCORE'];
	
		$searchResultsTextArr[]=$TEXT;
	
		$TEXT_TRANSLATED = $QURAN_TEXT_OTHER_LANG[$SURA][$AYA];
	
		$SURA_NAME = $META_DATA['SURAS'][$SURA]['name_'.strtolower($lang)];
	
		$SURA_NAME_LATIN = $META_DATA['SURAS'][$SURA]['name_trans'];
	
	
		// وكذلك جلناكم امة وسطا 143/256
		$TOTAL_VERSES_OF_SURA = $TOTALS['TOTAL_PER_SURA'][$SURA]['VERSES'];
	
	
		//preprint_r($MODEL['QURAN_TEXT']);
	
		$MATCH_TYPE="";
	
		if ( $WORD_TYPE=="PRONOUN_ANTECEDENT")
		{
	
			$MATCH_TYPE = "ضمير";
			
			if ( $lang=="EN")
			{
				$MATCH_TYPE = "pronoun";
			}
	
		}
		else if ( $WORD_TYPE=="ROOT" || $WORD_TYPE=="LEM")
		{
	
			$MATCH_TYPE = "تصريف / إشتقاق";
	
		}
	
	
		// empty in case of only pronouns
		if ( !empty($WORDS_IN_AYA))
		{

			if ( $isPhraseSearch )
			{
				// mark all POSSIBLE_HIGHLIGHTABLE_WORDS
				$TEXT = preg_replace("/(".$query.")/mui", "<marked>\\1</marked>", $TEXT);
	
	
			}
			else
			{
				// mark all POSSIBLE_HIGHLIGHTABLE_WORDS
				$TEXT = preg_replace("/(".join("|",array_keys($WORDS_IN_AYA)).")/mui", "<marked>\\1</marked>", $TEXT);
	
				
				if ( $isTransliterationSearch)
				{
					$TEXT_TRANSLITERATED = preg_replace("/(".join("|",array_keys($WORDS_IN_AYA)).")/mui", "<marked>\\1</marked>", $TEXT_TRANSLITERATED);
				}
			}
		}
	
	

		//preprint_r($PRONOUNS);
		
		// mark PRONOUNS
		//if ( $WORD_TYPE=="PRONOUN_ANTECEDENT") {} // COMMENTED SINCE WORD MAY HAVE BOTH PRON AND NORMAKL WORDS
		
			foreach( $PRONOUNS as $pronounText => $PRONOUN_INDEX_IN_AYA_EMLA2Y)
			{
				$pronounText = removeTashkeel($pronounText);
	

				$TEXT = markSpecificWordInText($TEXT,($PRONOUN_INDEX_IN_AYA_EMLA2Y-1),$pronounText,"marked");
	
				//$TEXT = preg_replace("/(".$EXTRA_INFO.")/mui", "<marked>\\1</marked>", $TEXT);
				//echoN("|".$TEXT);
			
			}
		
		
			if ( $isQuestion )
			{
				//preprint_r($significantCollocationWords);
				foreach( $significantCollocationWords as $word => $freq )
				{
						
						
			
					$TEXT = markWordWithoutWordIndex($TEXT,$word,"marked_prospect_answer");
			
					//$TEXT = preg_replace("/(".$EXTRA_INFO.")/mui", "<marked>\\1</marked>", $TEXT);
					//echoN("|".$TEXT);
			
				}
			
			
			}
	
	
	
	
		$documentID = preg_replace("/\:/", "-", $documentID);
	
		//preprint_r($documentScoreArr);
	
		?>
	
		<div class='result-aya-container'  order='<?=($SURA+1)?>' relevance='<?=($relevanceReverseOrderIndex)?>' >
		
			<div class='result-aya'  style="direction:<?=$direction?>" id="<?=$documentID?>" >
				
				 
				 
				<?php
			
			
					if ($script=="uthmani" && $lang=="AR")
					{
						echo $TEXT_UTH;
					}
					else 
					{
						echo $TEXT;
						
						if ($isTransliterationSearch)
						{
							echo("<hr class='transliteration-separator'/>");
							echo("<div class='transliteration-verse-text-area'>$TEXT_TRANSLITERATED</div>");
						}
						
					}
						
					
				?>
				
		
				
				<div id="<?=$documentID?>-translation" class='result-translated-text' style="direction:<?php echo ($lang=="AR")? "ltr":"rtl";?>" >
					
					<?=$TEXT_TRANSLATED?>
				</div>
		
			</div>
			<div class='result-aya-info'  >
			
				<span class='result-sura-info' style="direction:<?=$direction?>">
						<?=$SURA_NAME ?>
						<?php if ( $lang=="EN") { echo " ($SURA_NAME_LATIN)"; } ?> 
					[<?=($SURA+1).":".($AYA+1)?>]
						 <?php/*$TOTAL_VERSES_OF_SURA*/?> 
				</span>
		
				<span class='result-aya-showtranslation' >
				<?php 
					$showTransText = "Show Translation";
					
					if ( $lang=="EN")
					{
						$showTransText = "Show Arabic";
					}
				?>
					<a href="javascript:showTranslationFor('<?=$documentID?>')"><?=$showTransText?></a>
				</span>
			
			<span class='result-more-about-aya'>
				<a target='_new' href='http://quran.com/<?=($SURA+1)."/".($AYA+1)?>'>
				More
				</a>
			</span>
				
				<span class='result-match-type'>
					<?php echo $MATCH_TYPE?>
				</span>
			</div>
		</div>
	
	<?php 
	
	}
	
	return $searchResultsTextArr;
}

function postResultSuggestions($lang,$queryWordsWithoutDerivation)
{

	
	$wordsNotInTheQuran = array();
	

	foreach($queryWordsWithoutDerivation as $word => $dummy)
	{
		if ( mb_strlen($word) <=2) continue;
		
		if (!modelEntryExistsInMemory($lang,"MODEL_SEARCH","INVERTED_INDEX",$word) && !modelEntryExistsInMemory("ALL", "MODEL_QA_ONTOLOGY", "CONCEPTS", convertWordToConceptID($word)) )
		{
			$wordsNotInTheQuran[$word]=1;
		}
	}
	


		// GET SIMILAR WORDS BY MIN-EDIT-DISTANCE
		return getSimilarWords($lang,array_keys($wordsNotInTheQuran));

		
	
}

function showSuggestions($suggestionsArr)
{
	?>

	<?php
		
	if (!empty($suggestionsArr))
	{
		?>
				<div class='search-word-suggestion'>
					Do you mean ...
					<br>
					<?php 
					
						$index =0;
						 foreach($suggestionsArr  as $suggestedWord => $dummyFlag)
						 {
						 	if ( $index++>10) break;
						 	
						 	?>
						 	<a href='?q=<?=urlencode($suggestedWord)?>'><?=$suggestedWord?></a>&nbsp;
						 	<?php
						 	
						 }
						 
					?>
				 
				</div>	
			<?php 
	}
}
function handleEmptyResults($scoringTable,$extendedQueryWordsArr,$query,$originalQuery,$isColumnSearch,$searchType,$lang)
{
	// NOT RESULTS FOUND
	if ( empty($scoringTable))
	{
	
		// GET SIMILAR WORDS BY MIN-EDIT-DISTANCE
		$suggestionsArr = getSimilarWords($lang,array_keys($extendedQueryWordsArr));
	
	
		$searchedForText = $query;
		
		if ( $isColumnSearch)
		{
			$searchedForText = " this verse \"$originalQuery\" ";
		}
		else 
		{
			$searchedForText = "\"$searchedForText\"";
		}
		
		?>
		
			<div class='search-error-message'>
				No results found for <?php echo $searchedForText;?>
				
				<script>
				trackEvent('SEARCH',$searchType,'FAILED','');
				</script>
			</div>
			
		<?php 
		showSuggestions($suggestionsArr);
		
		exit;
		
		
	
	}
}





function searchResultsToWordcloud($searchResultTextArr,$lang,$maxItems)
{

	global $MODEL_CORE;

	$wordCloudArr = array();




	foreach($searchResultTextArr as $index => $text)
	{


		if ( $lang=="AR")
		{
			$text = removePauseMarkFromVerse($text);
		}
		$textWordsArr = preg_split("/ /",$text);

		foreach($textWordsArr as $word)
		{
				
				
			if ( $lang == "EN")
			{
				$word = cleanAndTrim($word);
				$word = strtolower($word);

			}
			
			if ( empty($word) ) continue;
			if ( isset($MODEL_CORE['STOP_WORDS'][$word]) ) continue;
			
			$wordCloudArr[$word]++;
		}
	}
	
	
	arsort($wordCloudArr);
	
	$wordCloudArr = array_slice($wordCloudArr,0, $maxItems);
	
	return $wordCloudArr;
}

function getStatisticallySginificantWords($extendedQueryWordsArr,$scoringTable)
{
	global $MODEL_CORE, $MODEL_CORE_UTH,$script;
	global $saktaLatifaMark, $sajdahMark;
	
	
	
	
	//preprint_r($extendedQueryWordsArr);exit;
	
	$queryTermsCollocation = array();
	

	
	$relevanceReverseOrderIndex = count($documentScoreArr);
	foreach($scoringTable as $documentID => $documentScoreArr)
	{

		$SURA = $documentScoreArr['SURA'];
		$AYA = $documentScoreArr['AYA'];
		$TEXT = $MODEL_CORE['QURAN_TEXT'][$SURA][$AYA];
		$TEXT_UTH = $MODEL_CORE_UTH['QURAN_TEXT'][$SURA][$AYA];

		
		$wordsArr = explode(" ",$TEXT);
		$lastWord = null;
		
		
		
		foreach($wordsArr as $word)
		{
			$word = cleanAndTrim($word);
			
			if ( empty($word)  ) continue;
			
			$word = strtolower($word);
			
			if ( isset($MODEL_CORE['STOP_WORDS'][$word])) continue;
			
			
			// ignore pause marks
			if ( isPauseMark($word, $MODEL_CORE['TOTALS']['PAUSEMARKS'], $saktaLatifaMark, $sajdahMark) )
			{
				continue;
			}
			
			if (!empty($lastWord) &&  isset($extendedQueryWordsArr[$word]) && !isset($extendedQueryWordsArr[$lastWord]) )
			{

				$queryTermsCollocation[$lastWord]++;
			}
			
	
			if (!empty($lastWord) && isset($extendedQueryWordsArr[$lastWord]) && !isset($extendedQueryWordsArr[$word]) )
			{
		
				
				$queryTermsCollocation[$word]++;
			}
			
			$lastWord = $word;
			
		}
		
		
	}
	
	arsort($queryTermsCollocation);

	//preprint_r($queryTermsCollocation);exit;
	
	$queryTermsCollocation  = array_slice($queryTermsCollocation,0,10);
	
	return $queryTermsCollocation;
}

function convertUthamniQueryToSimple($query)
{
	

	
	
	$queryWords = explode(" ",$query);
	$newQueryArr = array();
	
	
	foreach($queryWords as $index => $word)
	{
	
		if ( empty($word)) continue;
		
		$simpleWord = getItemFromUthmaniToSimpleMappingTable($word);
		
		if ( empty($simpleWord))
		{
			$simpleWord = shallowUthmaniToSimpleConversion($word);
		}
		
		$newQueryArr[] = $simpleWord;
		
	
	}
	
	return implode(" ",$newQueryArr);
}

function wordOrPhraseIsInIndex($lang,$wordOrPhrase)
{
	global $MODEL_SEARCH;
	
	$subwordsArr = explode(" ", $wordOrPhrase);
	
	foreach($subwordsArr as $index => $word)
	{
		if (modelEntryExistsInMemory($lang,"MODEL_SEARCH","INVERTED_INDEX",$word) )
		{
			return true;
		}
	
	}
	
	return false;
	
}

function cleanTransliteratedText($transliteratedText)
{
	return strtolower(strip_tags($transliteratedText));
}

function getSearchType($isPhraseSearch,$isQuestion,$isColumnSearch,$isConceptSearch,$isTransliterationSearch)
{
	if ( $isPhraseSearch)
	{
		return "PHRASE";
	}
	else if ($isQuestion)
	{
		return "QUESTION";
	}
	else if ($isColumnSearch)
	{
		return "VERSE_CHAPTER";
	}
	else if ($isConceptSearch )
	{
		return "CONCEPT";
	}
	else if ($isTransliterationSearch)
	{
		return "TRANSLITERATION";
	}
	else
	{
		return "NORMAL";
	}
}

?>