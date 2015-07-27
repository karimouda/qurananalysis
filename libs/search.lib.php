<?php 
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
	$arabicQuestionWords['من الذى']=null;
	$arabicQuestionWords['من الذين']=null;
	$arabicQuestionWords['من هم']=null;
	$arabicQuestionWords['من هى']=null;
				
	$englishQuestionWords = array();
	$englishQuestionWords['who']=null;
	
	if ( $lang=="EN")
	{
		foreach($englishQuestionWords as  $word=>$dummy)
		{
			if ( strpos($query, "$word ")!==false)
			{
				return $word;
			}
				
			
		}
	}
	else
	{
		foreach($arabicQuestionWords as  $word=>$dummy)
		{
				if ( strpos($query, "$word ")!==false)
			{
				return $word;
			}
		
				
		}
	}
	
	return false;
	
}

function posTagUserQuery($query, $lang)
{
	$taggedSignificantWords = array();
	
	if ( $lang=="EN")
	{
		$taggedWordsArr = posTagText($query);
	
		//printTag($taggesSentenceArr);exit;
		
		foreach($taggedWordsArr as $posArr)
		{
			$word = trim($posArr['token']);
			$tag  = trim($posArr['tag']);
			
			if ( strpos($tag,"NN")!==false || strpos($tag,"NP")!==false )
			{
				
				$taggedSignificantWords[$word] = $tag;
			}
			
		}
	
		
	}
	else 
	{
		$query = removeStopwordsAndTrim($query,$lang);
		$tempArr =  explode(" ", $query);
		
		foreach($tempArr as $index=>$word)
		{
			$taggedSignificantWords[$word] = "N";
		}
		
	}
	
		return $taggedSignificantWords;
}

function extendQueryWordsByDerivations($taggedSignificantWords,$lang)
{
	
	foreach($taggedSignificantWords as $word => $posTag)
	{
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
			
	}
	
	return $taggedSignificantWords;
}

function extendQueryWordsByConceptTaxRelations($extendedQueryArr,$lang)
{
	global $MODEL_QA_ONTOLOGY, $is_a_relation_name_ar,$thing_class_name_en;
	
	
	
	foreach($extendedQueryArr as $index => $word)
	{
	
		
		if ( $lang=="EN")
		{
			//corresponding arabic Concept - only if it is a concept
			$word = $MODEL_QA_ONTOLOGY['CONCEPTS_EN_AR_NAME_MAP'][$word];
		}
	
	
		if ( isset($MODEL_QA_ONTOLOGY['CONCEPTS'][$word]) )
		{
			$inboundRelationsArr = $MODEL_QA_ONTOLOGY['GRAPH_INDEX_TARGETS'][$word];
			
			foreach($inboundRelationsArr as $index => $relationArr)
			{
				$subject = $relationArr['source'];
				$verbAR = $relationArr['link_verb'];
				
				if ( $lang=="EN")
				{
					$subject = trim(removeBasicEnglishStopwordsNoNegation(($MODEL_QA_ONTOLOGY['CONCEPTS'][$subject]['label_en'])));
				}
				
			
				
				if ( $verbAR==$is_a_relation_name_ar && $subject!=$thing_class_name_en)
				{
					$extendedQueryArr[]=$subject;
				}
				
			}
		}
	
		
			
	}
	
	return $extendedQueryArr;
}

?>