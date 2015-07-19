<?php 

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

?>