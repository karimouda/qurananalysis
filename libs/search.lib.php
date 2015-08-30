<?php 
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
    
 

    
    return mb_strlen($commonChars);
}

function getSimilarWords($queryWords)
{
	global $MODEL_CORE;
	

	$simmilarWords = array();
	
	
	foreach ($MODEL_CORE['WORDS_FREQUENCY']['WORDS'] as $wordFromQuran=>$one)
	{
		
		
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

function  containsQuestionWords($query,$lang)
{
	
	$query = strtolower($query);

	$arabicQuestionWords = array();
	$arabicQuestionWords['من هو']=null;
	$arabicQuestionWords['من هم']=null;
	$arabicQuestionWords['من هى']=null;
	$arabicQuestionWords['من الذى']=null;
	$arabicQuestionWords['من الذين']=null;
	$arabicQuestionWords['ما هى']=null;
	$arabicQuestionWords['ما هو']=null;
	$arabicQuestionWords['ماذا']=null;
				
	$englishQuestionWords = array();
	$englishQuestionWords['who']=null;
	$englishQuestionWords['what']=null;
	
	if ( $lang=="EN")
	{
		foreach($englishQuestionWords as  $word=>$dummy)
		{
			if ( strpos($query, "$word ")===0)
			{
				return $word;
			}
				
			
		}
	}
	else
	{
		foreach($arabicQuestionWords as  $word=>$dummy)
		{
				if ( mb_strpos($query, "$word ")===0)
			{
				return $word;
			}
		
				
		}
	}
	
	return false;
	
}

function posTagUserQuery($query, $lang)
{
	global $MODEL_CORE;
	
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
	global $MODEL_QA_ONTOLOGY;
	
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
			
			foreach ($MODEL_QA_ONTOLOGY['CONCEPTS'] as $conceptID => $mainConceptArr )
			{
		
				
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
	global $MODEL_QA_ONTOLOGY, $is_a_relation_name_ar,$thing_class_name_en,$thing_class_name_ar,$TRANSLATION_MAP_EN_TO_AR;
	
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
			$conceptIDStr = $MODEL_QA_ONTOLOGY['CONCEPTS_EN_AR_NAME_MAP'][$word];
		}
		else 
		{
			$conceptIDStr = $word;
		}
	
	
		
		//!$questionIncludesVerb since if the question includes a verb then the user is not looking ofr is-a relation
		if ( !$questionIncludesVerb && isset($MODEL_QA_ONTOLOGY['CONCEPTS'][$conceptIDStr]) )
		{
			$inboundRelationsArr = $MODEL_QA_ONTOLOGY['GRAPH_INDEX_TARGETS'][$conceptIDStr];
			$outboundRelationsArr = $MODEL_QA_ONTOLOGY['GRAPH_INDEX_SOURCES'][$conceptIDStr];
			
			
		
			// FOR INBOUND IS-A RELATIONS EX: X IS AN ANIMAL($word)
			foreach($inboundRelationsArr as $index => $relationArr)
			{
				$subject = $relationArr['source'];
				$verbAR = $relationArr['link_verb'];
				
				if ( $lang=="EN")
				{
					$subject = trim(removeBasicEnglishStopwordsNoNegation(($MODEL_QA_ONTOLOGY['CONCEPTS'][$subject]['label_en'])));
				}
				
				/// CLEAN AND REPLACE CONCEPT
				$subject = cleanWordnetCollocation($subject);
				///////////////////////////
				
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
				// FOR OUTBOUND IS-A RELATIONS EX: X($word) IS AN PERSON			
				foreach($outboundRelationsArr as $index => $relationArr)
				{
					
					$verbAR = $relationArr['link_verb'];
					$object = $relationArr['target'];
				
					if ( $lang=="EN")
					{
						$object = trim(removeBasicEnglishStopwordsNoNegation(($MODEL_QA_ONTOLOGY['CONCEPTS'][$object]['label_en'])));
					}
				
					/// CLEAN AND REPLACE CONCEPT
					$object = cleanWordnetCollocation($object);
					///////////////////////////
					
				
					if ( $verbAR==$is_a_relation_name_ar && $object!=$thing_class_name)
					{
						
						
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
		

		//$lang=="AR" check since AR wprds are not PoS tagged yet
		if ( $isQuestion && ($lang=="AR" ||posIsVerb($pos)) )
		{
			if (  (($verbArr=$MODEL_QA_ONTOLOGY['VERB_INDEX'][$word])!=null) || ($verbArr = isWordPartOfAVerbInVerbIndex($word,$lang) ) )
			{
				
		
				foreach($verbArr as $index => $verbSTArr)
				{
					$subject = $verbSTArr['SUBJECT'];
					$object = $verbSTArr['OBJECT'];
					
					if ( $lang=="EN")
					{
						$object = trim(removeBasicEnglishStopwordsNoNegation(($MODEL_QA_ONTOLOGY['CONCEPTS'][$object]['label_en'])));
						$subject = trim(removeBasicEnglishStopwordsNoNegation(($MODEL_QA_ONTOLOGY['CONCEPTS'][$subject]['label_en'])));
					}
					
					//echoN("$subject>$word>$object");
	
				
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
	
	//preprint_r($conceptsFromTaxRelations);

	
	return $conceptsFromTaxRelations;
}

function extendQueryByExtractingWordDerviations($extendedQueryWordsArr)
{
	global $MODEL_SEARCH,$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS,$UTHMANI_TO_SIMPLE_LOCATION_MAP;


	

		/** GET ROOT/STEM FOR EACH QUERY WORD **/
		foreach ($extendedQueryWordsArr as $word)
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
				//preprint_r($MODEL_QAC['QAC_MASTERTABLE'][$qacLocation]);
					
				// search QAC for roots and LEMMAS for this word
				foreach ( $MODEL_QAC['QAC_MASTERTABLE'][$qacLocation] as $segmentIndex => $segmentDataArr)
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
		}
	
	
	
		/** GET EMLA2Y (SIMPLE) WORDS CORRESPONSING TO ANY QAC SEGMENT CONTAINING THE ROOT/STEMS IN THE EXTENDED QUERY WORD FROM INVERTED INDEX
		 *  ADD TO EXTENDED QUERY WORDS
		 *  TODO: recheck to remove this whole loop
		 * **/
		foreach ($extendedQueryWordsArr as $word => $dummy)
		{
	
			// ONLY UTHMANI SHOULD BE HANDLED
			if ( isSimpleQuranWord($word)) continue;
	
			foreach ($MODEL_SEARCH['INVERTED_INDEX'][$word] as $documentArrInIndex)
			{
				$SURA = $documentArrInIndex['SURA'];
				$AYA = $documentArrInIndex['AYA'];
				$INDEX_IN_AYA_EMLA2Y = $documentArrInIndex['INDEX_IN_AYA_EMLA2Y'];
				$INDEX_IN_AYA_UTHMANI= $documentArrInIndex['INDEX_IN_AYA_UTHMANI'];
				$WORD_TYPE = $documentArrInIndex['WORD_TYPE'];
				$EXTRA_WORD_TYPE_INFO = $documentArrInIndex['EXTRA_INFO'];
	
				$qacLocation = getQACLocationStr($SURA+1,$AYA+1,$INDEX_IN_AYA_UTHMANI);
					
				//preprint_r($MODEL_QAC['QAC_MASTERTABLE'][$qacLocation]);
					
				$verseText = getVerseByQACLocation($MODEL_CORE,$qacLocation);
					
				$wordFromVerse = getWordFromVerseByIndex($MODEL_CORE,$verseText,$INDEX_IN_AYA_EMLA2Y);
					
			
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

function getScoredDocumentsFromInveretdIndex($extendedQueryWordsArr,$query,$isPhraseSearch,$isQuestion,$isColumnSearch,$columnSearchKeyValParams)
{
	global $MODEL_CORE,$MODEL_SEARCH;
	global $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS,$UTHMANI_TO_SIMPLE_LOCATION_MAP;
	

	
	if ( $isColumnSearch)
	{
		
		$SURA = $columnSearchKeyValParams['KEY']-1;
		$AYA = $columnSearchKeyValParams['VAL']-1;
		
		// VERSE VALIDITY CHECK
		$qacLocation = getQACLocationStr($SURA+1,$AYA+1,0);
		
		$verseText = getVerseByQACLocation($MODEL_CORE, $qacLocation);
		
		if ( empty($verseText)) 
		{
			return array();
		}
		//////////////////////
				
		$scoringTable[$SURA.":".$AYA]=array();
			
		$scoringTable[$SURA.":".$AYA]['SCORE']=1;
		$scoringTable[$SURA.":".$AYA]['SURA']=$SURA;
		$scoringTable[$SURA.":".$AYA]['AYA']=$AYA;
		
		return $scoringTable;
	}


	

//	$MODEL_QURANA  = apc_fetch("MODEL_QURANA");
	//preprint_r($extendedQueryWordsArr);
	
	
	/**
	 * GET ALL RESULT FORM INDEX USING EXTENDED QUERY WORD (WHICH INCLUDES ALL VARIATIONS AND PRONOUNS)
	 */
	foreach ($extendedQueryWordsArr as $word =>$targetQACLocation)
	{
		//preprint_r($MODEL_SEARCH['INVERTED_INDEX'][$word]);exit;
		foreach ($MODEL_SEARCH['INVERTED_INDEX'][$word] as $documentArrInIndex)
		{
	
			//echoN("$word");
			//preprint_r($documentArrInIndex);exit;
			$SURA = $documentArrInIndex['SURA'];
			$AYA = $documentArrInIndex['AYA'];
			$INDEX_IN_AYA_EMLA2Y = $documentArrInIndex['INDEX_IN_AYA_EMLA2Y'];
			$INDEX_IN_AYA_UTHMANI= $documentArrInIndex['INDEX_IN_AYA_UTHMANI'];
			$WORD_TYPE = $documentArrInIndex['WORD_TYPE'];
			$EXTRA_INFO = $documentArrInIndex['EXTRA_INFO'];
	
	
	
	
			//echo getQACLocationStr($SURA,$AYA,$INDEX_IN_AYA_EMLA2Y);
			$qacLocation = getQACLocationStr($SURA+1,$AYA+1,$INDEX_IN_AYA_UTHMANI);
	
			$verseText = getVerseByQACLocation($MODEL_CORE, $qacLocation);
	
			
			/*
			 *
			* NOTE: A DECISION SHOULD BE TAKEN TO SERACH AROUND AND REMOVE PAUSE MARKS OR NOT
			*/
			$verseTextWithoutPauseMarks = removePauseMarkFromVerse($verseText);
			//echoN("|$query|$verseText");
			$fullQueryIsFoundInVerseCount = preg_match_all("/(^|[ ])$query([ ]|\$)/umi", $verseTextWithoutPauseMarks);
			
		
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
	
	
			// incase of non normal word ( QAC/QURANA) .. transslate WordIndex from Uthmani script to Imla2y script
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
				$scoringTable[$SURA.":".$AYA]['IS_FILL_QUERY_IN_VERSE']=0;
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
	
			
			
			$scoringTable[$SURA.":".$AYA]['IS_FILL_QUERY_IN_VERSE'] = $fullQueryIsFoundInVerseCount;
			
	
			$scoringTable[$SURA.":".$AYA]['QUERY_WORDS_IN_VERSE']=count($scoringTable[$SURA.":".$AYA]['POSSIBLE_HIGHLIGHTABLE_WORDS']);
	
			$scoringTable[$SURA.":".$AYA]['SCORE'] = ($scoringTable[$SURA.":".$AYA]['FREQ']/2)+
													 ($scoringTable[$SURA.":".$AYA]['DISTANCE']*1)+
													 ($scoringTable[$SURA.":".$AYA]['QUERY_WORDS_IN_VERSE']*10)+
													 (count($scoringTable[$SURA.":".$AYA]['PRONOUNS'])*1)+
													 ($scoringTable[$SURA.":".$AYA]['WORD_OCCURENCES_COUNT']*1)+
													 ($scoringTable[$SURA.":".$AYA]['IS_FILL_QUERY_IN_VERSE']*20);
	
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

function printResultVerses($scoringTable,$lang,$direction,$query,$isPhraseSearch,$isQuestion,$script,$significantCollocationWords=null)
{
	global $MODEL_CORE, $MODEL_CORE_UTH,$script;
	

	if ( $lang=="EN")
	{
	
		
		if ($script=="simple")
		{
			$MODEL_CORE_OTHER_LANG =  apc_fetch("MODEL_CORE[AR]");
		}
		else
		{
			$MODEL_CORE_OTHER_LANG = loadUthmaniDataModel();
		}
	
	}
	else
	{
		
		$MODEL_CORE_OTHER_LANG = apc_fetch("MODEL_CORE[EN]");
	
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
		$TEXT = $MODEL_CORE['QURAN_TEXT'][$SURA][$AYA];
		$TEXT_UTH = $MODEL_CORE_UTH['QURAN_TEXT'][$SURA][$AYA];
		$WORD_TYPE = $documentScoreArr['WORD_TYPE'];
		$EXTRA_INFO = ($documentScoreArr['EXTRA_INFO']);
		$INDEX_IN_AYA_EMLA2Y = $documentScoreArr['INDEX_IN_AYA_EMLA2Y'];
		$WORDS_IN_AYA = $documentScoreArr['POSSIBLE_HIGHLIGHTABLE_WORDS'];
		$PRONOUNS = $documentScoreArr['PRONOUNS'];
	
		$score = $documentScoreArr['SCORE'];
	
		$searchResultsTextArr[]=$TEXT;
	
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
	
			}
		}
	
	

		
		
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
						$showTransText = "Show Origninal";
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

function postResultSuggestions($queryWordsWithoutDerivation)
{
	global $MODEL_SEARCH,$MODEL_QA_ONTOLOGY;
	
	$wordsNotInTheQuran = array();
	
	preprint_r($queryWordsWithoutDerivation);
	foreach($queryWordsWithoutDerivation as $word => $dummy)
	{
		if (!isset($MODEL_SEARCH['INVERTED_INDEX'][$word]) && !isset($MODEL_QA_ONTOLOGY['CONCEPTS'][convertWordToConceptID($word)]) )
		{
			$wordsNotInTheQuran[$word]=1;
		}
	}
	
	preprint_r($wordsNotInTheQuran);

		// GET SIMILAR WORDS BY MIN-EDIT-DISTANCE
		return getSimilarWords(array_keys($wordsNotInTheQuran));

		
	
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
function handleEmptyResults($scoringTable,$extendedQueryWordsArr,$query)
{
	// NOT RESULTS FOUND
	if ( empty($scoringTable))
	{
	
		// GET SIMILAR WORDS BY MIN-EDIT-DISTANCE
		$suggestionsArr = getSimilarWords(array_keys($extendedQueryWordsArr));
	
	
		?>
		
			<div class='search-error-message'>
				No results found for "<?php echo $query;?>"
			</div>
			
		<?php 
		showSuggestions($suggestionsArr);
		
		exit;
		
		
	
	}
}


function answerUserQuestion($queryWordsArr,$taggedSignificantWords, $lang)
{
	
	$conceptsFromTaxRelations = extendQueryWordsByConceptTaxRelations($taggedSignificantWords, $lang, true);
	
	
	return $conceptsFromTaxRelations;
	

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
	
     global $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS;
	
	
	$queryWords = explode(" ",$query);
	$newQueryArr = array();
	
	
	foreach($queryWords as $index => $word)
	{
	
		if ( empty($word)) continue;
		
		$simpleWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$word];
		
		if ( empty($simpleWord))
		{
			$simpleWord = shallowUthmaniToSimpleConversion($word);
		}
		
		$newQueryArr[] = $simpleWord;
		
	
	}
	
	return implode(" ",$newQueryArr);
}

?>