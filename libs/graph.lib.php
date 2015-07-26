<?php 

function convertConceptIDtoGraphLabel($conceptID)
{
	return ucfirst(str_replace("_", " ", $conceptID));;
}
function textToGraph($searchResultTextArr,$excludes,$capping=300)
{
	global $pauseMarksFile, $lang;
	
	$MAX_CAP = $capping;
	
	$graphObj = array();
	$graphObj["capped"]=0;
	
	$graphNodes = array();
	$graphLinks = array();
	
	$pauseMarksArr = getPauseMarksArrByFile($pauseMarksFile);
	
	/** SHOULD BE ZERO BASED FOR D3 TO WORK - o.target.weight = NULL**/
	$nodeSerialNumber = 0;
	
	$lastWord = null;
	
	foreach($searchResultTextArr as $index => $text)
	{
	
		$textWordsArr = preg_split("/ /",$text);
		
		//echoN($text);
		
		foreach($textWordsArr as $word)
		{
			
			
			if ( $lang == "EN")
			{
				$word = cleanAndTrim($word);
				$word = strtolower($word);
			}
			
			//echoN($word);
			
			
			if ( $pauseMarksArr[$word]) continue;
			
			if ( $excludes[$word]==1) continue;
		

			
			if ( !isset($graphNodes[$word]) )
			{
				$graphNodes[$word]= array("id"=>$nodeSerialNumber++,"word"=>$word,"size"=>1,"x"=>rand(1,800),"y"=>rand(1,400));
			}
			else
			{
				$graphNodes[$word]["size"]=$graphNodes[$word]["size"]+1;
			}
			
			
			if ( $lastWord!=null )
			{
				$graphLinks[]=array("source"=>$graphNodes[$lastWord]["id"],"target"=>$graphNodes[$word]["id"]);
			}
			
			$lastWord = $word;
			
		}
		
		if ( count($graphNodes) > $MAX_CAP )
		{
			$graphObj["capped"]=$MAX_CAP;
			break;
		}
		
		
	}
	
	$graphObj["nodes"]=$graphNodes;
	$graphObj["links"]=$graphLinks;	
	
	//preprint_r($graphLinks);
	//preprint_r($graphNodes);
	
	return $graphObj;
}

function ontologyToD3Graph($MODEL_QA_ONTOLOGY,$minFreq)
{
	global $lang;


	$graphObj = array();
	$graphObj["capped"]=0;

	$graphNodes = array();
	$graphLinks = array();


	/** SHOULD BE ZERO BASED FOR D3 TO WORK - o.target.weight = NULL**/
	$nodeSerialNumber = 0;



	foreach($MODEL_QA_ONTOLOGY['CONCEPTS'] as $conceptNameID => $conceptArr)
	{

			$conceptLabelAR = $conceptArr['label_ar'];
			$conceptLabelEN = $conceptArr['label_en'];
			$conceptFrequency = $conceptArr['frequency'];
			$conceptWeight = $conceptArr['weight'];
			
			if ( $conceptFrequency< $minFreq) continue;
				
			if ( !isset($graphNodes[$conceptNameID]) )
			{
				$graphNodes[$conceptNameID]= array("id"=>$nodeSerialNumber++,"word"=>$conceptLabelAR,
						"size"=>$conceptWeight,"x"=>rand(200,800),"y"=>rand(200,600));
			}


	}
	
	
	
		foreach($MODEL_QA_ONTOLOGY['RELATIONS'] as $index => $relArr)
		{

			$subject = $relArr['subject'];
			$verbAR = $relArr['verb'];
			$verbEN = $relArr['verb_translation_en'];
			$verbUthmani = $relArr['verb_uthmani'];
			$relFreq = $relArr['frequency'];
			$object = $relArr['object'];
			
			if ( isset($graphNodes[$subject]) && isset($graphNodes[$object]) )
			{
				$graphLinks[]=array("source"=>$graphNodes[$subject]["id"],
								    "target"=>$graphNodes[$object]["id"]);
			}
			
		
				
		}

		
		$graphNodesArr = array();
		
		foreach($graphNodes as $word => $nodeArr)
		{
		
			$graphNodesArr[] = $nodeArr;
		
		}



	$graphObj["nodes"]=$graphNodesArr;
	$graphObj["links"]=$graphLinks;


	return $graphObj;
}

?>