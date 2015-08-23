<?php



$lang = "EN";
$direction = "ltr";



$query = $_GET['q'];

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



if ($lang=="EN" && !isDevEnviroment() )
{
	file_put_contents( dirname(__FILE__)."/../data/query.log.en", time().",".$query."\n",FILE_APPEND);
}
else if ($lang=="AR" && !isDevEnviroment())
{
	file_put_contents( dirname(__FILE__)."/../data/query.log.ar",  time().",".$query."\n",FILE_APPEND);
}


	//echoN(time());
	loadModels("core,search,qac,ontology",$lang);
	//echoN(time());
	
	
//preprint_r($MODEL_CORE);

$significantWords = array();

//echoN($query);exit;

$MODEL_CORE_UTH = loadUthmaniDataModel();

//$TRANSLATION_MAP_EN_TO_AR = apc_fetch("WORDS_TRANSLATIONS_EN_AR");

//preprint_r($TRANSLATION_MAP_EN_TO_AR);

$isPhraseSearch = false;
$isQuestion = false;
$isColumnSearch = false;
$columnSearchType = null;
$columnSearchKeyValParams = null;
$noDerivationsConstraint = false;
$noOntologyExtentionConstraint = false;

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


if ( !$isPhraseSearch && !$isQuestion && strpos($query,":")!==false)
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
$query = shallowUthmaniToSimpleConversion($query);



// CASE HANDLING
if ($lang=="EN" )
{
	$query = strtolower($query);
}




//removed is question from here

if ( !$isPhraseSearch && !$noDerivationsConstraint)
{

	$taggedSignificantWords = posTagUserQuery($query,$lang);

	$taggedSignificantWords = extendQueryWordsByDerivations($taggedSignificantWords,$lang);
	
	//preprint_r($taggedSignificantWords);exit;
	$queryWordsArr = array_keys($taggedSignificantWords);
}
else
{
	$queryWordsArr = preg_split("/ /",$query);

	//consider the full query as oen word, needed for Qurana pronouns
	if ($isPhraseSearch)
	{
		$queryWordsArr[]=$query;
	}
	
	
}

//!$isQuestion && 
if (  !$isColumnSearch && !$noOntologyExtentionConstraint )
{
	echoN("D");
	
	$conceptsFromTaxRelations = extendQueryWordsByConceptTaxRelations(swapAssocArrayKeyValues($queryWordsArr), $lang);
	
	
	$queryWordsArr  = array_merge($queryWordsArr,$conceptsFromTaxRelations);
}

if ($isQuestion)
{
	$userQuestionAnswerConceptsArr = answerUserQuestion($queryWordsArr,$taggedSignificantWords, $lang);
	
	$queryWordsArr  = array_merge($queryWordsArr,$userQuestionAnswerConceptsArr);
}

//echoN($query);preprint_r($userQuestionAnswerConceptsArr);exit;

//////////////
$scoringTable = array();

$lastWord = null;

$extendedQueryWordsArr = array_fill_keys($queryWordsArr,1);




// IF NOT PHRASE OF QUESTION SEARCH, EXTEND QUERY BY ADDING DERVIATION OF THE QUERY WORDS
if ( $lang=="AR" && $isPhraseSearch==false && $isQuestion==false && !$isColumnSearch)
{

	$extendedQueryWordsArr = extendQueryByExtractingWordDerviations($extendedQueryWordsArr);
}


$extendedQueryBeforeRemovingStopWords = $extendedQueryWordsArr;
$extendedQueryWordsArr = removeBasicStopwordsFromArr($extendedQueryWordsArr,$lang);

// if the query is all stop words
if ( empty($extendedQueryWordsArr))
{
	$extendedQueryWordsArr = $extendedQueryBeforeRemovingStopWords;
}

if ( count($extendedQueryWordsArr) > 25 )
{

	$extendedQueryWordsArr = array_slice($extendedQueryWordsArr, 0,25);
}


preprint_r($extendedQueryWordsArr);
// SEARCH INVERTED INDEX FOR DOCUMENTS
$scoringTable = getScoredDocumentsFromInveretdIndex($extendedQueryWordsArr,$query,$isPhraseSearch,$isQuestion,$isColumnSearch,$columnSearchKeyValParams);


// NOT RESULTS FOUND
handleEmptyResults($scoringTable,$extendedQueryWordsArr,$query);


if ($isQuestion)
{
	$significantCollocationWords = getStatisticallySginificantWords($extendedQueryWordsArr,$scoringTable);
}

///// GET STATS BYT SCORING TABLE

$resultStatsArr = getStatsByScoringTable($scoringTable);




?>