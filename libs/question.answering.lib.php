<?php
function answerUserQuestion($query, $queryWordsArr,$taggedSignificantWords,$scoringTable, $lang)
{
	global $MODEL_QA_ONTOLOGY,$MODEL_CORE;
	global $is_a_relation_name_ar;
	
	
	
	$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = loadUthmaniToSimpleMappingTable();


	// answering by relevance and similarity
	$conceptsFromTaxRelations = extendQueryWordsByConceptTaxRelations($taggedSignificantWords, $lang, true);

	//preprint_r($conceptsFromTaxRelations);
	
	
	$COMMON_CONCEPTS_FACTOR = 10;
	$COMMON_QUESTION_TYPE_CONCEPTS_FACTOR = 10;
	$COMMON_ROOTS_FACTOR = 10;
	$COMMON_DERIVATIONS_FACTOR = 10;

	$scoredAnswerVersesArr = array();
	//preprint_r($taggedSignificantWords);
	//echoN($query);

	$questionType = containsQuestionWords($query,$lang);

	////////// COMMON CONCEPTS IN QUESTION
	
	$conceptsInQuestionTextArr = getConceptsFoundInText($query,$lang);
	
	//preprint_r($conceptsInQuestionTextArr);
	
	///////////////////////////////////////
	
	
	/////////// GET CONCEPTS FOR THE QUESTION TYPE
	/// GET INSTANCE CONCEPTS FROM QUESTION TYPE CLASS
	$questionType = cleanAndTrim(strtolower($questionType));
	
	//echoN($questionType);
	$conceptID = $MODEL_QA_ONTOLOGY['CONCEPTS_EN_AR_NAME_MAP'][$questionType];
	
	//echoN($conceptID);
	$relationsOfConceptAsTarget = $MODEL_QA_ONTOLOGY['GRAPH_INDEX_TARGETS'][$conceptID];
	
	$questionTypeConceptsArr = array();
	foreach( $relationsOfConceptAsTarget as $index => $relArr)
	{
	
		$verb    = $relArr["link_verb"];
		$subject = $relArr["source"];
	
		//echoN("CC:$is_a_relation_name_ar|$verb");
		if ($verb!=$is_a_relation_name_ar) continue;
	
		$questionTypeConceptsArr[] = $subject;
	
	}
	
	//////////////////////////////////////////////
	
	$debugArr = array();

	//// Answering by similarity and relevance
	foreach($scoringTable as $documentID => $documentScoreArr)
	{
		//preprint_r($documentScoreArr);
		$relevanceReverseOrderIndex--;

		$SURA = $documentScoreArr['SURA'];
		$AYA = $documentScoreArr['AYA'];
		$TEXT = $MODEL_CORE['QURAN_TEXT'][$SURA][$AYA];
		$score = $documentScoreArr['SCORE'];

		//echoN("SCORE BEFORE QUESTION RELEVANCE:$score");

		if ( $lang=="EN")
		{
			$TEXT = strtolower($TEXT);
		}

		//echoN($TEXT);

		
		
		
		
		
		$conceptsInTextArr = getConceptsFoundInText($TEXT,$lang);

		
		//preprint_r($conceptsInTextArr);
		
		
		/////////// COMMON CONCEPTS BWTEEEN QUESTION AND A VERSE TEXT
		$commonQuestionVerseConceptsCount = getIntersectionCountOfTwoArrays(
				array_keys($conceptsInQuestionTextArr), array_keys($conceptsInTextArr));
		
		//echoN("Common Concepts:$commonQuestionVerseConceptsCount");
		
		$debugArr[$documentID]['COMMON_CONCEPTS']=$commonQuestionVerseConceptsCount;
		$debugArr[$documentID]['COMMON_CONCEPTS_LIST']=join(" ",array_intersect(array_keys($conceptsInQuestionTextArr), array_keys($conceptsInTextArr)));
		
		
		//preprint_r($debugArr);exit;
		
		$score += ($commonQuestionVerseConceptsCount*$COMMON_CONCEPTS_FACTOR);
		///////////////////////////////////////////////////////////
		



		//preprint_r($questionTypeConceptsArr);
		//preprint_r(array_keys($conceptsInTextArr));

		$numberOfSharedConceptsForThisQuestionType = getIntersectionCountOfTwoArrays($questionTypeConceptsArr,array_keys($conceptsInTextArr));

		//echoN($numberOfSharedConceptsForThisQuestionType);

		$score += ($numberOfSharedConceptsForThisQuestionType*$COMMON_QUESTION_TYPE_CONCEPTS_FACTOR);
		
		$debugArr[$documentID]['COMMON_QUESTION_TYPE_CONCEPTS']=$numberOfSharedConceptsForThisQuestionType;
		$debugArr[$documentID]['COMMON_QUESTION_TYPE_CONCEPTS_LIST']=join(" ",array_intersect($questionTypeConceptsArr,array_keys($conceptsInTextArr)));
		

		
		//// QUESION-VERSE SIMILARITY MESUREMENT (wITH DERIVATIONS CONSIDERED)
		$wordsInVerseTextArr = explode(" ", $TEXT);
	
		$derivationHandledB4 = array();
		
		$commonDerivations= 0;
		if ( $lang=="EN")
		{
			
			foreach($taggedSignificantWords as $wordInQuestion => $pos)
			{
				//echoN("$word $pos");
				// for words like i (NOUN in the lexicon for some reson )
				if ( mb_strlen($wordInQuestion)<=2) continue;

				
					
				if (  $pos=="VBN" || $pos=="VBD" || $pos=="VBG" || $pos=="NN" || $pos=="NNS")
				{
	
					foreach($wordsInVerseTextArr as $index => $wordInArray)
					{
						$wordInArray = cleanAndTrim($wordInArray);
						
	
						if ( mb_strlen($wordInArray)<=2 ) continue;
						
						// if any word (noun/verb) in the quetion is a substring
						if ( strpos($wordInArray, $wordInQuestion) !==false ||
							 strpos($wordInQuestion, $wordInArray) !==false )
						{
								
							if ( isset($derivationHandledB4[$wordInArray]) ) continue;
							//echoN("$word is SS in VerseText");
							$commonDerivations++;
							
							$derivationHandledB4[$wordInArray]=1;
							//$debugArr[$documentID]['COMMON_DERIVATIONS_LIST']=
							//$debugArr[$documentID]['COMMON_DERIVATIONS_LIST']."|".$wordInArray;
							
							
								
						}
					}
				}
			}
			
			$score += ($commonDerivations*$COMMON_DERIVATIONS_FACTOR);
			
			$debugArr[$documentID]['COMMON_DERIVATIONS']=$commonDerivations;
		}
		else 
		{
			$questionWordsRootsArr = array();
			
			foreach($taggedSignificantWords as $wordInQuestion => $pos)
			{
				
				if ( mb_strlen($wordInQuestion)<=2) continue;
					
				if (  $pos=="NN" || $pos=="NNS")
				{
					//echoN("===$wordInQuestion");
					
					$root = getRootOfSimpleWord($UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS,$wordInQuestion,array("N","V"));
					
					if ( !empty($root))
					{
						$questionWordsRootsArr[]=$root;
					}
				}
				
				
			}
			
			//preprint_r($questionWordsRootsArr);
			//exit;
			
			$verseWordsRootsArr = array();
			
			foreach($wordsInVerseTextArr as $index => $wordInArray)
			{
				if ( mb_strlen($wordInArray)<=2) continue;
				
				$root = getRootOfSimpleWord($UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS,$wordInArray,array("N","V"));
					
				if ( !empty($root))
				{
					$verseWordsRootsArr[]=$root;
				}
				
			}
			
			//preprint_r($verseWordsRootsArr);
					
		}
		
		
		$commonRootsCount = getIntersectionCountOfTwoArrays($verseWordsRootsArr,$questionWordsRootsArr);
		
		
		$score+=($commonRootsCount*$COMMON_ROOTS_FACTOR);
		
		$debugArr[$documentID]['COMMON_ROOTS']=$commonRootsCount;
		
		//echoN($commonRootsCount);
	
		/////////////////////////////////////////////////////////

		//echoN("SCORE AFTER QUESTION RELEVANCE:$score");

		$scoringTable[$documentID]['SCORE'] = $score;


		$scoredAnswerVersesArr[$documentID] = $scoringTable[$documentID];


	}

	
	rsortBy($scoredAnswerVersesArr, "SCORE");
	
	//preprint_r($debugArr);
	//preprint_r($scoredAnswerVersesArr);exit;
	
	
	$scoredAnswerVersesArr = array_slice($scoredAnswerVersesArr,0, 3);
	
	//// REMOVE ANY VERSE FROM THE FINAL LIST WHICH HAS NO OBVIOUS SIMILARITY WITH THE QUESTION
	foreach ($scoredAnswerVersesArr as $documentID => $verseArr)
	{
		//preprint_r($debugArr[$documentID]);
		
		if ( $debugArr[$documentID]['COMMON_ROOTS']==0 && 
			 $debugArr[$documentID]['COMMON_CONCEPTS']==0 &&
			 $debugArr[$documentID]['COMMON_QUESTION_TYPE_CONCEPTS']==0 &&
			 $debugArr[$documentID]['COMMON_DERIVATIONS']==0 )
		{
			unset($scoredAnswerVersesArr[$documentID]);
		}
			 
	}
	/////////////////////////////////////
	
	//preprint_r($scoredAnswerVersesArr);

	//preprint_r($scoredAnswerVersesArr);


	return array("ANSWER_CONCEPTS"=>$conceptsFromTaxRelations,"ANSWER_VERSES"=>$scoredAnswerVersesArr);


}

?>