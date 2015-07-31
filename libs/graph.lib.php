<?php 
require_once(dirname(__FILE__)."/../global.settings.php");
require_once(dirname(__FILE__)."/core.lib.php");

function convertConceptIDtoGraphLabel($conceptID)
{
	return ucfirst(str_replace("_", " ", $conceptID));;
}
function convertWordToConceptID($word)
{
	return (str_replace(" ", "_", $word));;
}

function formatEnglishConcept($conceptEN)
{
	return ucfirst(removeBasicEnglishStopwordsNoNegation($conceptEN));
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

function createNewConceptObj(&$nodeSerialNumber,$lang,$finalNodeLabel,$ontologyConceptArr,$randomXLocation,$randomYLocation)
{
	

	
	$conceptLabelAR = $ontologyConceptArr['label_ar'];
	$conceptLabelEN = $ontologyConceptArr['label_en'];
	$conceptFrequency = $ontologyConceptArr['frequency'];
	$conceptWeight = $ontologyConceptArr['weight'];
	
	if ( $lang=="EN")
	{
		$conceptShortDesc = $ontologyConceptArr['meaning_wordnet_en'];
	}
	else 
	{
		$conceptShortDesc = $ontologyConceptArr['meaning_wordnet_translated_ar'];
	}
	
	$conceptImage = $ontologyConceptArr['image_url'];
	
	if ( $lang=="EN")
	{
		$conceptLongDesc = htmlentities($ontologyConceptArr['long_description_en']);
		
		//preprint_r($ontologyConceptArr);
	}
	else
	{
		$conceptLongDesc = $ontologyConceptArr['long_description_ar'];
	}
	
	$conceptWikipediaLink = $ontologyConceptArr['wikipedia_link'];
	
	return array("id"=>$nodeSerialNumber++,"word"=>ucfirst($finalNodeLabel),
			"size"=>$conceptWeight,"frequency"=>$conceptFrequency,
			"short_desc"=>$conceptShortDesc,"long_desc"=>$conceptLongDesc,
			"external_link"=>$conceptWikipediaLink,"image_url"=>$conceptImage,
			"x"=>$randomXLocation,
			"y"=>$randomYLocation);
}
function ontologyTextToD3Graph($MODEL_QA_ONTOLOGY,$searchResultTextArr,$minFreq=0,$widthHeigthArr,$lang)
{
	
	global $thing_class_name_ar, $is_a_relation_name_ar;

	$graphObj = array();


	$graphNodes = array();
	$graphLinks = array();
	
	
	////// calculate start points
	$width  = $widthHeigthArr[0];
	$height  = $widthHeigthArr[1];
	
	$startLocationXMin = ($width/2)-100;
	$startLocationXMax = ($width/2)+100;;
	$startLocationYMin = ($height/2)-100;
	$startLocationYMax = ($height/2)+100;;
	

	
	
	////////////////////////////


	
	/** SHOULD BE ZERO BASED FOR D3 TO WORK - o.target.weight = NULL**/
	$nodeSerialNumber = 0;
	
	$lastWord = null;
	
	
	
	foreach($searchResultTextArr as $index => $text)
	{
	
		$textWordsArr = preg_split("/ /",$text);
		
		
		
		foreach($textWordsArr as $word)
		{
			
			
			if ( $lang == "EN")
			{
				$word = cleanAndTrim($word);
				$word = strtolower($word);
				
				// translate English name to arabic concept name/id
				$wordConveretedToConceptID = $MODEL_QA_ONTOLOGY['CONCEPTS_EN_AR_NAME_MAP'][$word];
			}
			else 
			{
			
				$wordConveretedToConceptID = convertWordToConceptID($word);
			}
			
			
			
			if ( isset($MODEL_QA_ONTOLOGY['CONCEPTS'][$wordConveretedToConceptID]) )
			{
				//preprint_r($MODEL_QA_ONTOLOGY['CONCEPTS'][$wordConveretedToConceptID]);exit;
				
				$mainConceptArr = $MODEL_QA_ONTOLOGY['CONCEPTS'][$wordConveretedToConceptID];
				
				$conceptLabelAR = $mainConceptArr['label_ar'];
				$conceptLabelEN = $mainConceptArr['label_en'];
				$conceptFrequency = $mainConceptArr['frequency'];
				$conceptWeight = $mainConceptArr['weight'];
				
				$finalNodeLabel = $conceptLabelAR;
				
				if ( $lang == "EN")
				{
					$finalNodeLabel = $conceptLabelEN;
				}
		
					
				if ( $conceptFrequency< $minFreq) continue;
				
				if ( !isset($graphNodes[$wordConveretedToConceptID]) )
				{
				
					
					$randomXLocation = rand($startLocationXMin,$startLocationXMax);
					$randomYLocation = rand($startLocationYMin,$startLocationYMax);
				
				
					$graphNodes[$conceptLabelAR]= createNewConceptObj($nodeSerialNumber,$lang, $finalNodeLabel, $mainConceptArr,$randomXLocation,$randomYLocation);
				
				}
					
				
						

					
					
				
			}
			
		}
	}

	
	
	$linksHashLookupTable = array();

	foreach($graphNodes as $concept => $conceptArr)
	{
	
		$conceptID = convertWordToConceptID($concept);
		$relationsOfConceptAsSource = $MODEL_QA_ONTOLOGY['GRAPH_INDEX_SOURCES'][$conceptID];
		$relationsOfConceptAsTarget = $MODEL_QA_ONTOLOGY['GRAPH_INDEX_TARGETS'][$conceptID];
			
		$randomXLocation = rand($startLocationXMin,$startLocationXMax);
		$randomYLocation = rand($startLocationYMin,$startLocationYMax);
		
		foreach( $relationsOfConceptAsSource as $index => $relArr)
		{
				
			$verb  = $relArr["link_verb"];
			$object = $relArr["target"];
			
			//echoN("$verb==$is_a_relation_name_ar && $object==$thing_class_name_ar");
			// ignore is-a thing relations
			if ( $verb==$is_a_relation_name_ar && $object==$thing_class_name_ar) continue;
			
			$relHashID = buildRelationHashID($conceptID,$verb,$object);
			
			$fullRelationArr = $MODEL_QA_ONTOLOGY['RELATIONS'][$relHashID];
			
			
			$conceptArr = $MODEL_QA_ONTOLOGY['CONCEPTS'][$object];
		
			$finalNodeLabel = $conceptArr['label_ar'];
				
			if ( $lang == "EN")
			{
				$finalNodeLabel = formatEnglishConcept($conceptArr['label_en']);
				$verb = $fullRelationArr['verb_translation_en'];
			
			}
			
			if ( !isset($graphNodes[$object]))
			{

				$graphNodes[$object]= createNewConceptObj($nodeSerialNumber,$lang, $finalNodeLabel, $conceptArr,$randomXLocation,$randomYLocation);
			}
		
			$linkArr=array("source"=>$graphNodes[$concept]["id"],
					"target"=>$graphNodes[$object]["id"],"link_verb"=>$verb);
			
			//////// HANDLING MULTIPLE LINKS BETWEEN SAME NODES BEFORE ASSIGNING LINK
			$arrHash = getArrayHashForFields($linkArr,array('source','target'));
			
				
			if ( !isset($linksHashLookupTable[$arrHash]))
			{
				$graphLinks[]=$linkArr;
			}
			else
			{
				$linkIndex = $linksHashLookupTable[$arrHash];
				
				if ( strpos($graphLinks[$linkIndex]['link_verb'],"$verb")===false )
				{
					$graphLinks[$linkIndex]['link_verb'].= ",".$verb;		
				}		
			}
			
			$linksHashLookupTable[$arrHash]=(count($graphLinks)-1);
			/////////////////////////////////////////////////////////////
		
		}
			
		foreach( $relationsOfConceptAsTarget as $index => $relArr)
		{
				
			$verb  = $relArr["link_verb"];
			$subject = $relArr["source"];
		
			$relHashID = buildRelationHashID($subject,$verb,$concept);
			$fullRelationArr = $MODEL_QA_ONTOLOGY['RELATIONS'][$relHashID];
		
			
			$conceptArr = $MODEL_QA_ONTOLOGY['CONCEPTS'][$subject];
	
			$finalNodeLabel = $conceptArr['label_ar'];
			
			if ( $lang == "EN")
			{
				$finalNodeLabel = formatEnglishConcept($conceptArr['label_en']);;
				$verb = $fullRelationArr['verb_translation_en'];
			}
			
			if ( !isset($graphNodes[$subject]))
			{
			
				

				
				$graphNodes[$subject]= createNewConceptObj($nodeSerialNumber,$lang, $finalNodeLabel, $conceptArr,$randomXLocation,$randomYLocation);
				
			}
		
			$linkArr = array("source"=>$graphNodes[$subject]["id"],
					"target"=>$graphNodes[$concept]["id"],"link_verb"=>$verb);
			

			//////// HANDLING MULTIPLE LINKS BETWEEN SAME NODES BEFORE ASSIGNING LINK
			$arrHash = getArrayHashForFields($linkArr,array('source','target'));
			
				
			if ( !isset($linksHashLookupTable[$arrHash]))
			{
				$graphLinks[]=$linkArr;
			}
			else
			{
				$linkIndex = $linksHashLookupTable[$arrHash];
				
				if ( strpos($graphLinks[$linkIndex]['link_verb'],"$verb")===false )
				{
					$graphLinks[$linkIndex]['link_verb'].= ",".$verb;		
				}					
			}
			
			$linksHashLookupTable[$arrHash]=(count($graphLinks)-1);
			//////////////////////////////////////////////////////////////
				
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



function ontologyToD3Graph($MODEL_QA_ONTOLOGY,$minFreq=0)
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
				if ( $conceptFrequency < 100)
				{
					$startLocationXMin = 500;
					$startLocationXMax = 800;
					$startLocationYMin = 100;
					$startLocationYMax = 800;
				}
				else
				{
					$startLocationXMin = 100;
					$startLocationXMax = 200;
					$startLocationYMin = 100;
					$startLocationYMax = 200;
				}
				
				$graphNodes[$conceptNameID]= array("id"=>$nodeSerialNumber++,"word"=>$conceptLabelAR,
						"size"=>$conceptWeight,"x"=>rand($startLocationXMin,$startLocationXMax),
						"y"=>rand($startLocationYMin,$startLocationYMax));
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




function ontologyToD3Treemap($MODEL_QA_ONTOLOGY,$minFreq=0)
{
	global $lang;


	$treeRootObj = array();
	
	$treeRootObj["name"]="قرآن";
	$treeRootObj["children"]=array();

	
	$currentArr = &$treeRootObj["children"];

	/** SHOULD BE ZERO BASED FOR D3 TO WORK - o.target.weight = NULL**/
	$nodeSerialNumber = 0;



	foreach($MODEL_QA_ONTOLOGY['CONCEPTS'] as $conceptNameID => $conceptArr)
	{

		$conceptLabelAR = $conceptArr['label_ar'];
		$conceptLabelEN = $conceptArr['label_en'];
		$conceptFrequency = $conceptArr['frequency'];
		$conceptWeight = $conceptArr['weight'];
			
		if ( $conceptFrequency< $minFreq) continue;

	
		$conceptNameClean = convertConceptIDtoGraphLabel($conceptNameID);
			/*= array("id"=>$nodeSerialNumber++,"word"=>$conceptLabelAR,
					"size"=>$conceptWeight,"x"=>rand($startLocationXMin,$startLocationXMax),
					"y"=>rand($startLocationYMin,$startLocationYMax));*/
		$currentArr[] = array("name"=>$conceptNameClean,"size"=>$conceptWeight,"children"=>array());

		
		


	}



	foreach($MODEL_QA_ONTOLOGY['RELATIONS'] as $index => $relArr)
	{

		$subject = $relArr['subject'];
		$verbAR = $relArr['verb'];
		$verbEN = $relArr['verb_translation_en'];
		$verbUthmani = $relArr['verb_uthmani'];
		$relFreq = $relArr['frequency'];
		$object = $relArr['object'];
			
		
		//$treeRootObj[$subject]["children"][]["name"]=$object;
		
		$objectConceptArr = $MODEL_QA_ONTOLOGY['CONCEPTS'][$object];
		
			
		$index = search2DArrayForValue($currentArr,$subject);
		
		
		$isObjectIncludedBefore = search2DArrayForValue($currentArr[$index]["children"],$object);
		
		if ( $isObjectIncludedBefore===false)
		{
			//$currentArr[$index]["children"][] = array("name"=>$object,"size"=>$objectConceptArr['frequency'],"children"=>array());
		}

	}




	return $treeRootObj;
}


?>