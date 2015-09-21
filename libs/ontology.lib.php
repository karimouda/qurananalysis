<?php
require_once(dirname(__FILE__)."/../libs/core.lib.php");
require_once(dirname(__FILE__)."/custom.translation.table.lib.php");

function mapQACPoSToWordnetPoS($qacPOS)
{
	
	$trans = array("PN" => "noun", "N" => "noun", "V" => "verb", "ADJ" => "adj", "LOC" => "adv", "T" => "adv");
	
	// since concept extracted from relations may have "SUBJECT" or "OBJECT" POS
	if ( !isset($trans[$qacPOS]) )
	{
		return "noun";
	}
	return  strtr($qacPOS,$trans);
}
function trimVerb($verb)
{
	//very bad idea, spoils everything
	return preg_replace("/^(وَ|فَ)/um", "", $verb);
}

function isNounPhrase($posPattern)
{
	return ( $posPattern=="N" || $posPattern=="PN" || $posPattern=="DET N"
	);
	//REMOVED || $posPattern=="N PRON"  نصيبك
}



function generateEmptyConceptMetadata()
{
	return array("LEM"=>"","FREQ"=>0,
			"POS"=>"","SEG"=>array(),"SIMPLE_WORD"=>"",
			"ROOT"=>"","WEIGHT"=>"","AKA"=>array(),
			"TRANSLATION_EN"=>"","TRANSLITERATION_EN"=>"",
			"MEANING_AR"=>array(),"MEANING_EN"=>array(),
			"DBPEDIA_LINK"=>"","WIKIPEDIA_LINK"=>"", "IMAGES"=>"", "DESC_EN"=>array(), "DESC_AR"=>array());
}

function getTermArrBySimpleWord($finalTerms, $sentSimpleWord)
{


	foreach ($finalTerms as $lemaUthmani=>$termArr)
	{
			
		$mySimpleWord = $termArr['SIMPLE_WORD'];
			
		//echoN("$sentSimpleWord==$mySimpleWord");
			
		if ( $sentSimpleWord==$mySimpleWord)
		{
			return $termArr;
		}
			
	}

	return false;
}

function addNewConcept(&$finalConceptsArr,$newConceptName,$coneptType,$exPhase,$freq,$engTranslation)
{





	if ( !isset($finalConceptsArr[$newConceptName]))
	{
		$conceptMetaDataArr = generateEmptyConceptMetadata();
		
		if ( !empty($engTranslation))
		{
			$conceptMetaDataArr['TRANSLATION_EN']=$engTranslation;
		}
		
		$newConceptName = trim($newConceptName);
		$engTranslation = trim($engTranslation);
		
		$finalConceptsArr[$newConceptName]=array("CONCEPT_TYPE"=>$coneptType,"EXTRACTION_PHASE"=>$exPhase,"FREQ"=>$freq,"EXTRA"=>$conceptMetaDataArr);
		
		return true;
	
	}
	else 
	{
		//
		// IT WAS MEANT TO BE T-BOX IF IT WAS NOT FOUND, SO IF IT IS FOUNJD SWITCH IT TO T-BOX SINCE IT IS A PARENT
		if ( $coneptType=="T-BOX")
		{
			// SHOULD SWITCH TO T-BOX SINCE IT IS A PARENT CLASS NOW - FOR OWL SERIALIZATION BUGS
			$finalConceptsArr[$newConceptName]['CONCEPT_TYPE']='T-BOX';
		}
		
		return false;
	}
		
	
	
}

function printRelation($relationArrEntry)
{
	 
	echoN("---SUBJ:<b>".$relationArrEntry['SUBJECT']."</b> VERB:".$relationArrEntry['VERB']." OBJ:<b>".$relationArrEntry['OBJECT']."</b>");
}



function addNewRelation(&$relationArr,$type,$subject,$verbSimple,$object,$posPattern,$verbEngTranslation,$verbUthmani)
{
	$newRelation= array("TYPE"=>$type,"SUBJECT"=>trim($subject),
			"VERB"=>trim($verbSimple),
			"OBJECT"=>trim($object),
			"POS_PATTERN"=>$posPattern,
			"FREQ"=>1,
			"VERB_ENG_TRANSLATION"=>trim($verbEngTranslation),
			"VERB_UTHMANI"=>trim($verbUthmani));
	
	printRelation($newRelation);
	
		
	$relationHash = md5($newRelation['SUBJECT'].$newRelation['VERB'].$newRelation['OBJECT']);
		
	if ( !isset($relationArr[$relationHash]))
	{
	
		$relationArr[$relationHash]=$newRelation;
		return true;
	}
	else
	{
		$relationArr[$relationHash]['FREQ']++;
		return false;
	}
}

function addRelation(&$relationsArr,$type, $subject,$verb,$object,$joinedPattern,$verbEngTranslation="",$fullVerbQuranWord="")
{
	global  $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS;
	global $WORDS_TRANSLATIONS_AR_EN;
	global $is_a_relation_name_en;
	
		
	
	if ( empty($subject) || empty($object) )
	{
		return false;
	}
	
	
	// make shallow last resort, since it spoils words and lead to duplicate oncepts
	if ( !isSimpleQuranWord($subject) )
	{
		//CONVERT UTHMANI TO SIMPLE
		$subjectSimple = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$subject];
			
		// IF NOT CORRESPONDING SIMPLE WORD, CONVERT USING SHALLOW CONVERSION ALGORITHM
		if ( empty($subjectSimple))
		{
			$subjectSimple = shallowUthmaniToSimpleConversion($subject);
		}
	}
	else 
	{
		$subjectSimple = $subject;
	}

	// SAME AS ABOVE BUT FOR OBJECT
	if ( !isSimpleQuranWord($object) )
	{
		$objectSimple = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$object];

		//object simple to avoid null in case when not in the mapping table
		if ( empty($objectSimple))
		{
			$objectSimple = shallowUthmaniToSimpleConversion($object);
		}
	}
	else
	{
		$objectSimple = $object;
	}
		
	
	$verbUthmani = $verb;
	$verbSimple = "";
	
	///////// VERB TRANSLATION
	if ( empty($verbEngTranslation))
	{
		$verbEngTranslation ="";
	
		// SINGLE WORD VERB
		if ( !isMultiWordStr($verb))
		{
			$verb = trim($verb);
			
			$translatableVerb = $fullVerbQuranWord;
			
			// VERB IS SIMPLE
			if ( isSimpleQuranWord($verb) )
			{
				$translatableVerb = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$fullVerbQuranWord];

			}
			else
			{

				$verbSimple = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$verb];;
			}
			
			$verbEngTranslation = cleanEnglishTranslation($WORDS_TRANSLATIONS_AR_EN[$translatableVerb]);
			
			//IF NOT IN TRANSLATION TABLE - EX: ONE OF THE SEGMENTS TRIMMED
			if ( empty($verbEngTranslation))
			{
				// CHECK IF IS ALSO NOTO IN TRANSLATION ENTRY
				if (!isFoundInTranslationTable($translatableVerb,"VERB"))
				{
					

					// TRANSLATE USING MICROSOFT API
					$verbEngTranslation = translateText($translatableVerb,"ar","en");
					
					// ADD TO QA CUSTOM TRANSLATION TABLE
					addTranslationEntry($verbEngTranslation, "VERB", $translatableVerb,"AR");
					
					//no need
					//persistTranslationTable();
				}
				else
				{
					$customTranslationEntryArr =getTranlationEntryByEntryKeyword($translatableVerb);
					
					$verbEngTranslation = $customTranslationEntryArr['EN_TEXT'];
				}
			}
		}
		// MUTIWORD VERB (PHRASE) such as negated verbs
		else
		{
			
			//SPLIT PHRASE
			$verbPhraseArr = preg_split("/ /", $verb);
				
			foreach($verbPhraseArr as $verbPart)
			{
				
				$translatableVerb = $verbPart;
				
				// IF SIMPLE
				if ( isSimpleQuranWord($verbPart) )
				{
					//GET UTHMANI WORD TO BE ABEL TO TRANSLATE
					$translatableVerb = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$verbPart];
				}
				else
				{
					// GET SIMPLE WORD TO BE ADDED IN RELATION META
					$simplePart = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$verbPart];
					
					//if not in translation table, use shalow conversion
					if ( empty($simplePart))
					{
						$simplePart = shallowUthmaniToSimpleConversion($verbPart);
					}
					
					$verbSimple = $verbSimple." ".$simplePart;
					
					// THIS VARIABLE NEEDED FOR TRANSLATION
					$translatableVerb = $simplePart;
				}
				
				// TRANSLATE
				$verbPartTranslated = cleanEnglishTranslation($WORDS_TRANSLATIONS_AR_EN[$translatableVerb]);
				
				//IF NOT IN TRANSLATION TABLE - EX: ONE OF THE SEGMENTS TRIMMED
				if ( empty($verbPartTranslated))
				{
					// CHECK IF IS ALSO NOTO IN TRANSLATION ENTRY
					if (!isFoundInTranslationTable($verbPart,"VERB"))
					{
							

						
						// TRANSLATE USING MICROSOFT API
						$verbPartTranslated = translateText($verbPart,"ar","en");
							
						// ADD TO QA CUSTOM TRANSLATION TABLE
						addTranslationEntry($verbPartTranslated, "VERB", $verbPart,"AR");
							
						
						//persistTranslationTable();
					}
					else
					{
						$customTranslationEntryArr =getTranlationEntryByEntryKeyword($verbPart);
							
						$verbPartTranslated = $customTranslationEntryArr['EN_TEXT'];
					}
				}
				
				// TRANSLATION ACCUMILATION
				$verbEngTranslation = $verbEngTranslation . " " .$verbPartTranslated;
			}
		}
	}
	
	if ( $verbEngTranslation!="is kind of" && $verbEngTranslation!="part of" && $verbEngTranslation!=$is_a_relation_name_en)
	{
		//$verbEngTranslation = removeBasicEnglishStopwordsNoNegation($verbEngTranslation);
	}
		
	$verbSimple = trim($verbSimple);
	
	if ( empty($verbSimple))
	{
		$verbSimple = removeTashkeel(shallowUthmaniToSimpleConversion($verbUthmani));
	}

		
	return addNewRelation($relationsArr,$type,$subjectSimple,$verbSimple,$objectSimple,$joinedPattern,$verbEngTranslation,$verbUthmani);
}

function resolvePronouns($qacLocation)
{
	global $MODEL_QURANA;
	$pronArr = array();
	$index=0;
	//echoN($qacLocation);
	//if ( $qacLocation=="3:146:11")
	//preprint_r($MODEL_QURANA['QURANA_PRONOUNS']);
	foreach($MODEL_QURANA['QURANA_PRONOUNS'][$qacLocation] as $coneptArr)
	{

		$coneptId = $coneptArr['CONCEPT_ID'];
		$conceptName = $MODEL_QURANA['QURANA_CONCEPTS'][$coneptId]['AR'];

		echoN($conceptName);

		// qurana null concept
		//if ( $conceptName=="null") continue;

		$pronArr[$index++]=$conceptName;
	}

	return $pronArr;
}

function flushProperRelations(&$relationsArr,&$conceptsArr,&$verb,&$lastSubject,$ssPoSPattern,&$filledConcepts)
{


	if ( count($conceptsArr)>=2   )
	{

		if (empty($verb))
		{
			$verb = "n/a";
		}
			
			

		if ( $conceptsArr[0]!=$conceptsArr[1])
		{
			$type = "NON-TAXONOMIC";
			addRelation($relationsArr,$type, $conceptsArr[0],$verb,$conceptsArr[1],$ssPoSPattern);

			if ( count($conceptsArr)>2 )
			{
				addRelation($relationsArr,$type, $conceptsArr[1],"n/a",$conceptsArr[2],$ssPoSPattern);
				addRelation($relationsArr,$type, $conceptsArr[0],"n/a",$conceptsArr[2],$ssPoSPattern);
			}
		}
			
		$conceptsArr=array();
		$verb = null;
		$filledConcepts=0;
	}
		
		
	if ( count($conceptsArr)==1 && !empty($verb) && !empty($lastSubject) && $conceptsArr[0]!=$lastSubject)
	{

		//echoN("||||".$conceptsArr[0]."|".$lastSubject);




		$temp = $conceptsArr[0];
		$conceptsArr[0] = $lastSubject;
		$conceptsArr[1] = $temp;


		// many problems
		if ( $conceptsArr[0]!=$conceptsArr[1])
		{
			$type = "NON-TAXONOMIC";
			addRelation($relationsArr,$type, $conceptsArr[0],$verb,$conceptsArr[1],$ssPoSPattern);
		}
			
			

			
		$conceptsArr=array();
		$verb = null;

		$filledConcepts=0;
	}
}
	
	


function getConceptBySegment($conceptsArr, $segment)
{
	foreach ($conceptsArr as $conceptName=>$conceptArr)
	{
		$extraArr = $conceptArr['EXTRA'];
		$simpleWord = $extraArr['SIMPLE_WORD'];
			
		foreach ($extraArr['SEG'] as $uthmaniSegment=>$simpleName)
		{
			//echoN("$uthmaniSegment==$segment");

			if ( $uthmaniSegment==$segment)
			{
					
				return $simpleWord;
			}
		}

			
	}

	return false;
}

function getConceptByLemma($conceptsArr, $lemma)
{
	foreach ($conceptsArr as $conceptName=>$conceptArr)
	{
		$extraArr = $conceptArr['EXTRA'];
		$simpleWord = $extraArr['SIMPLE_WORD'];


		//echoN("$uthmaniSegment==$segment");

		if ( $extraArr['LEM']==$lemma)
		{

			return $simpleWord;
		}
			


	}

	return false;
}

function getConceptTypeFromDescriptionText($abstract)
{
	$matches = array();
		

	$taggesSentenceArr = posTagText($abstract);

	//printTag($taggesSentenceArr);

	$counter =0;
	reset($taggesSentenceArr);
	while(current($taggesSentenceArr))
	{
		$currentTagArr = current($taggesSentenceArr);
		$nextTagArr = next($taggesSentenceArr);
			
		if ( ($currentTagArr['tag']=="VBZ" || $currentTagArr['tag']=="VBD" )
		&& $nextTagArr['tag']=="DT")
		{
			$thirdTagArr = next($taggesSentenceArr);


			if ( ($thirdTagArr['tag']=="NN" || $thirdTagArr['tag']=="VBG" )&& strtolower($thirdTagArr['token'])!="name")
			{
				$forthTagArr = next($taggesSentenceArr);
				if ( !empty($forthTagArr) && $forthTagArr['tag']=="IN"  )
				{
					//echoN("########".$nextTagArr['token']);
					return $thirdTagArr['token'];
				}
			}

		}
			
		if ( $counter++ > 20 ) return false;
			
			
			
	}


	return false;
		
	/*
	 if ( empty($matches))
	 {
	preg_match("/word for (.*?)[ \.,]/", $abstract,$matches);
	}

	if ( empty($matches))
	{
	preg_match("/kind of (.*?)[ \.,]/", $abstract,$matches);
	}





		

	if ( empty($matches))
	{
	preg_match("/name of an (.*?) (?:in|that)/", $abstract,$matches);
	}

	if ( empty($matches))
	{
	preg_match("/is (?:the|a) (.*?) of/", $abstract,$matches);
	}

	if ( empty($matches))
	{
	preg_match("/(?:as|is|was) a[n]? (.*?)[ \.,]/", $abstract,$matches);
	}

	if ( empty($matches))
	{
	preg_match("/ a[n]? (.*?) (?:for|of|in|or|on|to|from|that|\.,)/", $abstract,$matches);
	}
	//echoN($matches[1]);

	if ( preg_match_all("/ /",$matches[1])>4)
	{
	$matches[1] = substr($matches[1], 0,strpos($matches[1]," "));
	}
	else
	if ( preg_match_all("/ /",$matches[1])>4)
	{
	$matches[1] = substr($matches[1], 0,strpos($matches[1]," "));
	}


	return $matches[1];
	*/
}


/** Returns words from QAC by PoS tags - grouped by lemma **/
function getWordsByPos(&$finalTerms,$POS)
{
	global $MODEL_QAC,$MODEL_CORE,$UTHMANI_TO_SIMPLE_LOCATION_MAP,$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS;
	global $LEMMA_TO_SIMPLE_WORD_MAP;
	 
	 
	// Get all segment in QAC for that PoS
	foreach($MODEL_QAC['QAC_POS'][$POS] as $location => $segmentId)
	{

		// get Word, Lema and root
		$segmentWord = $MODEL_QAC['QAC_MASTERTABLE'][$location][$segmentId-1]['FORM_AR'];
		$segmentWordLema = $MODEL_QAC['QAC_MASTERTABLE'][$location][$segmentId-1]['FEATURES']['LEM'];
		$segmentWordRoot = $MODEL_QAC['QAC_MASTERTABLE'][$location][$segmentId-1]['FEATURES']['ROOT'];
		$verseLocation = substr($location,0,strlen($location)-2);
		//$segmentWord = removeTashkeel($segmentWord);


		// get word index in verse
		$wordIndex = (getWordIndexFromQACLocation($location));


		//$segmentFormARimla2y = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$segmentWord];

		// get simple version of the word index
		$imla2yWordIndex = getImla2yWordIndexByUthmaniLocation($location,$UTHMANI_TO_SIMPLE_LOCATION_MAP);


		// get verse text
		$verseText = getVerseByQACLocation($MODEL_CORE,$location);
		 
		//$imla2yWord = getWordFromVerseByIndex($MODEL_CORE,$verseText,$imla2yWordIndex);
		 
		 
		//echoN("|$segmentWord|$imla2yWord");
		$segmentWordNoTashkeel = removeTashkeel($segmentWordLema);
		 
		$superscriptAlef = json_decode('"\u0670"');
		$alefWasla = "ٱ"; //U+0671
		 
		//$imla2yWord = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];
		 
		 
		// this block is important since $LEMMA_TO_SIMPLE_WORD_MAP is not good for  non $superscriptAlef words
		// ex زيت lemma is converted to زيتها which spoiled the ontology concept list results
		if(mb_strpos($segmentWordLema, $superscriptAlef) !==false
		|| mb_strpos($segmentWordLema, $alefWasla) !==false )
		{

			$imla2yWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$segmentWordLema];

			if (empty($imla2yWord))
			{
				$imla2yWord = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];
			}



		}
		else
		{
			$imla2yWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$segmentWordLema];

			if ( empty($imla2yWord))
			{
				$imla2yWord = shallowUthmaniToSimpleConversion($segmentWordLema);//$segmentWordNoTashkeel;
					
			}
		}
		 
		 
		 
		/// in case the word was not found after removing tashkeel, try the lema mappign table
		$termWeightArr = $MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'][$imla2yWord];


		 
		// NOT WORKIGN BECAUSE LEMMAS WILL NOT BE IN SIMPLE WORDS LIST و الصابيئن =>صَّٰبِـِٔين
		// if the word after removing tashkeel is not found in quran simple words list, then try lemma table
		/*if (!isset($MODEL_CORE['WORDS_FREQUENCY']['WORDS'][$imla2yWord]) )
		 {
		 $imla2yWord = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];

		 if ( empty($imla2yWord) )
		 {
		 echoN($segmentWordLema);
		 echoN($imla2yWord);
		 preprint_r($LEMMA_TO_SIMPLE_WORD_MAP);
		 preprint_r($MODEL_CORE['WORDS_FREQUENCY']['WORDS']);
		 exit;
		 }
		 }*/

		 
		if ( empty($termWeightArr))
		{
			//only for weight since the lema table decrease qurana matching
			$imla2yWordForWeight = $LEMMA_TO_SIMPLE_WORD_MAP[$segmentWordLema];
			$termWeightArr = $MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'][$imla2yWordForWeight];


		}
		 
		$termWeight = $termWeightArr['TFIDF'];
		////////////////////////////////////////////

		$termWord = $segmentWordLema;//$imla2yWord;//"|$segmentWord| ".$imla2yWord ." - $location:$segmentId - $wordIndex=$imla2yWordIndex";
		 
		if ( !isset($finalTerms[$termWord]))
		{
			$finalTerms[$termWord] = generateEmptyConceptMetadata();

			$finalTerms[$termWord]['LEM'] = $segmentWordLema;
			$finalTerms[$termWord]['POS'] = $POS;
			$finalTerms[$termWord]['SIMPLE_WORD'] = $imla2yWord;
			$finalTerms[$termWord]['ROOT'] = $segmentWordRoot;
			$finalTerms[$termWord]['WEIGHT'] = $termWeight;


		}
		 
		$finalTerms[$termWord]["FREQ"]=$finalTerms[$termWord]["FREQ"]+1;
			
		if ( !isset($finalTerms[$termWord]["SEG"][$segmentWord]) )
		{
			$finalTerms[$termWord]["SEG"][$segmentWord]=$imla2yWord;
				
		}
			
		if ( !isset($finalTerms[$termWord]["POSES"][$POS]))
		{
			$finalTerms[$termWord]["POSES"][$POS]=1;
		}
			
			
		 
		 





	}
	 
	return $finalTerms;
}

function loadExcludesByType($type)
{
	$fileArr = file("../data/ontology/extraction/cleaner/excluded.$type",FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
	
	$excludedItemsArr = array();
	
	foreach($fileArr as  $itemName)
	{

		$itemName = trim($itemName);
		$excludedItemsArr[$itemName]=1;
		
	}
	
	return $excludedItemsArr;
	
}

function loadExcludedSynonymssArr()
{
	$fileArr = file("../data/ontology/extraction/excluded.synonyms",FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);

	$EXCLUDED_SYN = array();

	foreach($fileArr as  $synonym)
	{
		$synonym = trim($synonym);
		$EXCLUDED_SYN[$synonym]=1;

	}

	return $EXCLUDED_SYN;

}

function getXMLFriendlyString($className)
{
	return strtr($className, " ", "_");
}

function stripOntologyNamespace($className)
{
	global $qaOntologyNamespace;
	
	$hashLocation = strpos($className,"#");
	if ($hashLocation!==false)
	{
		$className = substr($className,$hashLocation+1);
	}
	else
	{
		$className = str_replace(substr($qaOntologyNamespace,0,-1), "", $className);
	}
	
	return $className;
}

function conceptHasSubclasses($relationsArr,$concept)
{
	global $is_a_relation_name_ar;
	
	foreach($relationsArr as $hash => $relationArr)
	{
		$relationsType = $relationArr['TYPE'];
	
		$subject = 	$relationArr['SUBJECT'];
		$object = $relationArr['OBJECT'];
		$verbAR = $relationArr['VERB'];
		
		
	
			
		// IF IT IS AN IS-A RELATION
		if ( $verbAR==$is_a_relation_name_ar && $concept==$object)
		{
			return true;
		}
	}
	
	return false;
}
function conceptHasParentClasses($relationsArr,$concept)
{
	global $is_a_relation_name_ar;

	foreach($relationsArr as $hash => $relationArr)
	{
		$relationsType = $relationArr['TYPE'];

		$subject = 	$relationArr['SUBJECT'];
		$object = $relationArr['OBJECT'];
		$verbAR = $relationArr['VERB'];
			
		// IF IT IS AN IS-A RELATION
		if ( $verbAR==$is_a_relation_name_ar && $concept==$subject)
		{
			return true;
		}
	}

	return false;
}


function buildRelationHashID($subject,$verb,$object)
{
	return md5("$subject,$verb,$object");
}

function isWordPartOfAVerbInVerbIndex($word,$lang)
{
	global $MODEL_QA_ONTOLOGY;
	
	
	
	foreach( $MODEL_QA_ONTOLOGY['VERB_INDEX'] as $verbWord => $verbArr)
	{
		if ( $lang=="EN")
		{
			$verbWord = strtolower($verbWord);
			
		}
		
		if ( mb_strpos($verbWord, $word)!==false) 
		{
			//echoN("|$verbWord| |$word|".( mb_strpos($verbWord, $word)!==false));
			return $verbArr;
		}
	}
	
	return false;
}

function handleNewConceptFromRelation(&$finalConcepts,$subjectOrObject,$conceptLocationInRelation,&$notInCounceptsCounter,&$statsUniqueSubjects)
{
	global $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS, $WORDS_TRANSLATIONS_AR_EN;
	
	$subjectOrObjectFlag =  null;
		
	// SUBJECT NOT IN MASTER CONCEPTS LIST
	if ( !isset($finalConcepts[$subject]) )
	{
		
		if ( $conceptLocationInRelation=="SUBJECT")
		{
			echoN("NOT IN CONCEPTS:S:$subjectOrObject");
		}
		else
		{
			echoN("NOT IN CONCEPTS:O:$subjectOrObject");
		}
		$notInCounceptsCounter++;
			
		$statsUniqueSubjects[$subjectOrObject]=1;

	
	}
	

	
	$termsArr = getTermArrBySimpleWord($finalTerms,$subjectOrObject);
		
	$freq = $termsArr['FREQ'];
		
	
		
	$isQuranaPhraseConcept = false;
	
	//echoN("^&&*:".(strpos($subjectOrObject," ")!==false));
	
	if( isMultiWordStr($subjectOrObject))
	{
		$quranaConceptArr = getQuranaConceptEntryByARWord($subjectOrObject);
	
	
		$engTranslation = ucfirst($quranaConceptArr['EN']);
			
		echoN("^^$subjectOrObject");
		$isQuranaPhraseConcept = true;
	}
	else
	{
		$uthmaniWord = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$subjectOrObject];
		$engTranslation = ucfirst(cleanEnglishTranslation($WORDS_TRANSLATIONS_AR_EN[$uthmaniWord]));
	}
		
		
		
	addNewConcept($finalConcepts, $subjectOrObject, "A-BOX", "POPULATION_FROM_RELATIONS", $freq, $engTranslation);
	
	$finalConcepts[$subjectOrObject]['EXTRA']['POS']=$subjectOrObjectFlag;
	$finalConcepts[$subjectOrObject]['EXTRA']['WEIGHT']=$termsArr['WEIGHT'];
	
	if ( $isQuranaPhraseConcept)
	{
		echoN($isQuranaPhraseConcept."||||$subjectOrObject");
		$finalConcepts[$subjectOrObject]['EXTRA']['IS_QURANA_NGRAM_CONCEPT']=true;
	}
}


function doesQuestionIncludesVerb($extendedQueryArr)
{
	foreach($extendedQueryArr as $word => $pos)
	{
		if ( posIsVerb($pos))
		{
			if ( $word!="is" && $word!="are")
			{
				return true;
			}
		}
	}
	return false;
}

function getConceptRichnessScore($coneptArr)
{
	return strlen(print_r($coneptArr,true));
}

function updateNameInAllRelations(&$relationsArr, $nameFrom, $nameTo)
{
	$relationsArrComp = $relationsArr;
	
	foreach($relationsArr as $hash => $relationArr)
	{
		$relationsType = $relationArr['TYPE'];
	
		$subject = 	$relationArr['SUBJECT'];
		$object = $relationArr['OBJECT'];
		$verbAR = $relationArr['VERB'];
		
			
		if ( $subject=="$nameFrom")
		{
			$relationsArr[$hash]['SUBJECT']=$nameTo;
		}
		if ( $object=="$nameFrom")
		{
			$relationsArr[$hash]['OBJECT']=$nameTo;
		}
			
			
		$newHash = md5($relationsArr[$hash]['SUBJECT'].$relationsArr[$hash]['VERB'].$relationsArr[$hash]['OBJECT']);
			
		//echoN("###  $newHash $hash $subject $verbAR $object");
		
		if ( $newHash!=$hash)
		{
			$relationsArrComp[$newHash] = $relationsArr[$hash];
			unset($relationsArrComp[$hash]);
		}
	}
	
	 $relationsArr = $relationsArrComp;
}

function getConceptsFoundInText($text,$lang)
{
	

	global $thing_class_name_ar, $is_a_relation_name_ar;

	global $MODEL_QA_ONTOLOGY;
	
	$conceptsInTextArr = array();



		
		$textWordsArr = preg_split("/ /",$text);
	
		foreach($textWordsArr as $index=>$word)
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
				
			//echoN($wordConveretedToConceptID);
				
			if ( isset($MODEL_QA_ONTOLOGY['CONCEPTS'][$wordConveretedToConceptID]) )
			{
				//preprint_r($MODEL_QA_ONTOLOGY['CONCEPTS'][$wordConveretedToConceptID]);exit;
				//echoN($wordConveretedToConceptID);

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


				if (  $wordConveretedToConceptID==$thing_class_name_ar) continue;

					
	

				$conceptsInTextArr[$wordConveretedToConceptID]= createNewConceptObj($nodeSerialNumber,$lang, $finalNodeLabel, $mainConceptArr,$randomXLocation,$randomYLocation,1);
	
					

			}
				
		}
	

	return $conceptsInTextArr;

}

?>