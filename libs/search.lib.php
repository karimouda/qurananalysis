<?php 


function getDistanceByCommonChars($word1,$word2)
{
	$word1Arr =  preg_split('//u', $word1, -1, PREG_SPLIT_NO_EMPTY);
    $word2Arr =  preg_split('//u', $word2, -1, PREG_SPLIT_NO_EMPTY);

    $commonChars = implode((array_intersect($word1Arr, $word2Arr)));
    
   

    //echoN($commonChars);
    
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
			
			
			// only one char len diff between words for not compoaring all 14k words
			if ( abs(mb_strlen($wordFromQuran)-mb_strlen($wordFromQuery)) <=3 )
			{
				
				//echoN($wordFromQuran);
				
				$distance = getDistanceBetweenWords($wordFromQuran,$wordFromQuery);
				
				//echoN("$wordFromQuran $wordFromQuery | $distance");
				
				if ( $distance <=3 )
				{
					$simmilarWords[$wordFromQuran] = getDistanceByCommonChars($wordFromQuran,$wordFromQuery);
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