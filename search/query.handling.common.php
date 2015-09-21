<?php

require_once("../global.settings.php");

require_once("../libs/search.lib.php");
require_once("../libs/graph.lib.php");
require_once("../libs/question.answering.lib.php");


$lang = "EN";
$direction = "ltr";



$query = $_GET['q'];

// QUERY OVERWRITE BY TEST PAGES
if ( $isInTestScript )
{
	$query = $testQuery;
}


$originalQuery = $query;


$script = $_GET['script'];

if ( empty($script))
{
	$script = "simple";
}



///DETEDCT LANGUAGE //LOCATION SIGNIFICANT
if (isArabicString($query))
{
	$lang = "AR";
	$direction = "rtl";
}





echoN(memory_get_peak_usage());

//echoN(time());
loadModels("core,search,qac,ontology",$lang);
//echoN(time());
	
echoN(memory_get_peak_usage());


	
	
//preprint_r($MODEL_CORE);

$significantWords = array();

//echoN($query);exit;

$MODEL_CORE_UTH = &loadUthmaniDataModel();
$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = &apc_fetch("UTHMANI_TO_SIMPLE_WORD_MAP");
$UTHMANI_TO_SIMPLE_LOCATION_MAP = &apc_fetch("UTHMANI_TO_SIMPLE_LOCATION_MAP");


echoN(memory_get_peak_usage());

//$TRANSLATION_MAP_EN_TO_AR = apc_fetch("WORDS_TRANSLATIONS_EN_AR");

//preprint_r($TRANSLATION_MAP_EN_TO_AR);

$isPhraseSearch = false;
$isQuestion = false;
$isColumnSearch = false;
$columnSearchType = null;
$columnSearchKeyValParams = null;
$noDerivationsConstraint = false;
$noOntologyExtentionConstraint = false;
$isConceptSearch = false;
$isTransliterationSearch = false;

$matchesCount = preg_match("/\".*?\"/", $query);

if ( $matchesCount >=1 ) $isPhraseSearch = true;



if ( !$isPhraseSearch && preg_match("/\?|؟/", $query)>0  )
{
	$isQuestion = true;
	$query = preg_replace("/\?|؟/", "", $query);
}

if ( !$isPhraseSearch && containsQuestionWords($query,$lang))
{
	$isQuestion = true;
}



$trimmedQuery = trim($query);

if ( is_numeric($trimmedQuery) && intval($trimmedQuery)>=1 && intval($trimmedQuery)<=114)
{
		//CHAPTER
		$columnSearchKeyValParams['KEY'] = $trimmedQuery;
		//VERSE
		$columnSearchKeyValParams['VAL'] = "ALL";
		
		$isColumnSearch = true;
}

if (strpos($query,"CONCEPTSEARCH")!==false )
{
	$query = str_replace("CONCEPTSEARCH:", "", $query);
	$isConceptSearch = true;
}

if ( !$isConceptSearch && !$isPhraseSearch && !$isQuestion && strpos($query,":")!==false)
{

	
	$columnFeaturesArr = explode(" ",$query);
	
	foreach($columnFeaturesArr as $index => $queryPartStr)
	{
		if (  strpos($queryPartStr,":")!==false )
		{ 
			$columnSearchArr = explode(":",$queryPartStr);
		
			
			
			if (is_numeric ($columnSearchArr[0]) && is_numeric ($columnSearchArr[1]))
			{
				$columnSearchType = "VERSE";
				
				//CHAPTER
				$columnSearchKeyValParams['KEY'] = $columnSearchArr[0];
				//VERSE
				$columnSearchKeyValParams['VAL'] = $columnSearchArr[1];
				
				$isColumnSearch = true;
			}
			
			if ($columnSearchArr[0]=="CONSTRAINT" )
			{
				$columnSearchType = "CONSTRAINT";
				
				if ( $columnSearchArr[1]=="NODERIVATION")
				{
					
					$query = str_replace("CONSTRAINT:NODERIVATION", "", $query);
					
					$noDerivationsConstraint = true;
					
				}
				
				if ( $columnSearchArr[1]=="NOEXTENTIONFROMONTOLOGY")
				{
						
					$query = str_replace("CONSTRAINT:NOEXTENTIONFROMONTOLOGY", "", $query);
						
					$noOntologyExtentionConstraint = true;
						
				}
			}
		
		}
		
	}
}

//preprint_r($columnSearchKeyValParams);exit;
//echoN("IS QUESTION:$isQuestion");
//echoN("noOntologyExtentionConstraint:$noOntologyExtentionConstraint");
//echoN("noDerivationsConstraint:$noDerivationsConstraint");

/// CLEANING
$query = cleanAndTrim($query);



//$query = removeTashkeel($query);

//  remove tashkeel - convert from uthmani to simple
// didn't use remove tashkeel since it leaves "hamzet el wasl" which is not in the simple text

if ( !isSimpleQuranWord($query))
{
$query = convertUthamniQueryToSimple($query);
}



// CASE HANDLING
if ($lang=="EN"  )
{
	$query = strtolower($query);

	$query = removeSpecialCharactersFromMidQuery($query);

}
else 
{

	$query = removeNonArabicAndSpaceChars($query);

}

$originalQueryWordsArr = preg_split("/ /",$query);

//for faster access
$originalQueryWordsArrSwaped = swapAssocArrayKeyValues($originalQueryWordsArr);


// CHECK IF TRANSLITERATION
if ($lang=="EN" &&  !$isConceptSearch && !$isPhraseSearch && !$isQuestion  )
{
	$firstWordInQuery =$originalQueryWordsArr[0];
	
	// load transliteration verse mapping
	$TRANSLITERATION_VERSES_MAP = apc_fetch("TRANSLITERATION_VERSES_MAP");
	
	$TRANSLITERATION_WORDS_INDEX = apc_fetch("TRANSLITERATION_WORDS_INDEX");
	

	echoN("==".memory_get_peak_usage());
	
	if (!empty($firstWordInQuery) && isset($TRANSLITERATION_WORDS_INDEX[$firstWordInQuery]) )
	{
		$isTransliterationSearch = true;
	}
	
	
}

//echoN("isTransliterationSearch:$isTransliterationSearch");


if ( $isquestion || (!$isPhraseSearch && !$noDerivationsConstraint && !$isColumnSearch && !$isConceptSearch && !$isTransliterationSearch ) )
{

	
	$taggedSignificantWords = posTagUserQuery($query,$lang);

	$taggedSignificantWordsAfterDerivation = extendQueryWordsByDerivations($taggedSignificantWords,$lang);
	
	echoN(memory_get_peak_usage());

	//preprint_r($taggedSignificantWords);

	$queryWordsArr = array_keys($taggedSignificantWordsAfterDerivation);
	
	$derivedWords = array();

	// keep derived words in a separate array
	foreach($queryWordsArr as $index => $word)
	{
	
		if ( !isset($taggedSignificantWords[$word]))
		{
			//$derivedWords[$word]=1;
			// neededs to be added for question answering
			$taggedSignificantWords[$word]=1;
		}
	}
	

	
}
else
{
	$queryWordsArr = $originalQueryWordsArr;

	//consider the full query as oen word, needed for Qurana pronouns
	if ($isPhraseSearch)
	{
		$queryWordsArr[]=$query;
	}
	else if ( $isConceptSearch)
	{
		//$queryWordsArr = array();
		// if the concept is a phrase concept the individual words need to be added also
		// since the phrase might not be in the index but the words will ex: most merciful
		$queryWordsArr[]=$query;
		$originalQueryWordsArr = $queryWordsArr;
	}
	
	
	
}

//!$isQuestion && 
if (  !$isColumnSearch && !$noOntologyExtentionConstraint && !$isTransliterationSearch )
{

	
	$conceptsFromTaxRelations = extendQueryWordsByConceptTaxRelations(swapAssocArrayKeyValues($queryWordsArr), $lang);
	
	
	$queryWordsArr  = array_merge($queryWordsArr,$conceptsFromTaxRelations);
}


//echoN($query);preprint_r($queryWordsArr);exit;

//////////////
$scoringTable = array();

$lastWord = null;


$extendedQueryWordsArr = array_fill_keys($queryWordsArr,1);




// IF NOT PHRASE OF QUESTION SEARCH, EXTEND QUERY BY ADDING DERVIATION OF THE QUERY WORDS
if ( $lang=="AR" && $isPhraseSearch==false && $isQuestion==false && !$isColumnSearch && !$isConceptSearch && !$isTransliterationSearch)
{

	$extendedQueryWordsArr = extendQueryByExtractingQACDerviations($extendedQueryWordsArr);
}


$extendedQueryBeforeRemovingStopWords = $extendedQueryWordsArr;
$extendedQueryWordsArr = removeBasicStopwordsFromArr($extendedQueryWordsArr,$lang);
//preprint_r($extendedQueryWordsArr);

// if the query is all stop words
if ( empty($extendedQueryWordsArr))
{
	$extendedQueryWordsArr = $extendedQueryBeforeRemovingStopWords;
}

if ( count($extendedQueryWordsArr) > 25 )
{

	$extendedQueryWordsArr = array_slice($extendedQueryWordsArr, 0,25);
}

//preprint_r($extendedQueryWordsArr);

// SEARCH INVERTED INDEX FOR DOCUMENTS
$scoringTable = getScoredDocumentsFromInveretdIndex($extendedQueryWordsArr,$query,$isPhraseSearch,$isQuestion,$isColumnSearch,$columnSearchKeyValParams,$isConceptSearch,$lang,$isTransliterationSearch);


if ($isQuestion)
{
	
	
	 $answerInformationContainerArr = answerUserQuestion($query,$queryWordsArr,$taggedSignificantWords,$scoringTable, $lang);

	 $userQuestionAnswerConceptsArr = $answerInformationContainerArr['ANSWER_CONCEPTS'];
	 
	 $userQuestionAnswerVersesArr = $answerInformationContainerArr['ANSWER_VERSES'];
	 
	
	 //preprint_r($userQuestionAnswerConceptsArr);
	 
	 $queryWordsArr  = array_merge($queryWordsArr,$userQuestionAnswerConceptsArr);
}

/// LOG QUERY //LOCATION SIGNIFICANT BEFORE  handleEmptyResults because of exit inside
if ( !isDevEnviroment() )
{
	$searchType = getSearchType($isPhraseSearch,$isQuestion,$isColumnSearch,$isConceptSearch,$isTransliterationSearch);
	
	logQuery($lang,$query,$searchType, count($scoringTable));
}

// NOT RESULTS FOUND
handleEmptyResults($scoringTable,$extendedQueryWordsArr,$query,$originalQuery,$isColumnSearch,$searchType);

if ( !$isQuestion && !$isColumnSearch)
{
	// Check is some words were written incorrectly even if results are returned 
	// اكثر الناس case should be أكثر
	$queryWordsWithoutDerivation = array_diff_assoc($extendedQueryWordsArr, $derivedWords);
	


	$postResultSuggestionArr = postResultSuggestions(($originalQueryWordsArrSwaped));
	
	
	// remove query words from suggestion
	/*foreach($queryWordsWithoutDerivation as $word => $dummy)
	{
		if ( isset($postResultSuggestionArr[$word])) unset($postResultSuggestionArr[$word]);
	}*/
	
	//preprint_r($postResultSuggestionArr);
}


if ($isQuestion)
{
	$significantCollocationWords = getStatisticallySginificantWords($extendedQueryWordsArr,$scoringTable);
}

///// GET STATS BYT SCORING TABLE

$resultStatsArr = getStatsByScoringTable($scoringTable);


//preprint_r($scoringTable);
echoN(memory_get_peak_usage());


?>