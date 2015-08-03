<?php
require_once(dirname(__FILE__)."/../global.settings.php");
require_once(dirname(__FILE__)."/core.lib.php");



$CUSTOM_TRANSLATION_TABLE_EN_AR = array();
$TABLE_LOADED = false;

function loadTranslationTable()
{
	global $customTranslationTableFile;
	global $CUSTOM_TRANSLATION_TABLE_EN_AR,$TABLE_LOADED;
	
	
	
	$fileLinesArr = file($customTranslationTableFile,FILE_SKIP_EMPTY_LINES|FILE_IGNORE_NEW_LINES);
	


	foreach ($fileLinesArr as $index => $line)
	{
		$lineArr = preg_split("/\|/", $line);
		
		$enWord = trim(removeUnacceptedChars($lineArr[0]));
		$wordType = trim(removeUnacceptedChars($lineArr[1]));
		$arTranslation = trim(removeUnacceptedChars($lineArr[2]));

				
		$CUSTOM_TRANSLATION_TABLE_EN_AR[$enWord]=array("EN_TEXT"=>$enWord,"TYPE"=>$wordType,"AR_TEXT"=>$arTranslation);
		
	}
	
	$TABLE_LOADED = true;
	
	return $CUSTOM_TRANSLATION_TABLE_EN_AR;
}

function isFoundInTranslationTable($enStr)
{
	global $CUSTOM_TRANSLATION_TABLE_EN_AR;
	
	return  isset($CUSTOM_TRANSLATION_TABLE_EN_AR[$enStr]) ;
}

function isFoundInTranslationTableArabicKeyword($arStr)
{
	global $CUSTOM_TRANSLATION_TABLE_EN_AR;

	$translationKey = search2DArrayForValue($CUSTOM_TRANSLATION_TABLE_EN_AR, $arStr);
	

	
	if ($translationKey!==false)
	{
		return true;
	}
	
	return false;
}


function getTranlationEntryByEntryKeyword($enStr)
{
	global $CUSTOM_TRANSLATION_TABLE_EN_AR,$TABLE_LOADED;
	
	if ( !$TABLE_LOADED)
	{
		return false;
	}
	
	return $CUSTOM_TRANSLATION_TABLE_EN_AR[$enStr];
}
function getTranlationEntryByArabicEntryKeyword($arStr)
{
	global $CUSTOM_TRANSLATION_TABLE_EN_AR,$TABLE_LOADED;

	if ( !$TABLE_LOADED)
	{
		return false;
	}
	
	$translationKey = search2DArrayForValue($CUSTOM_TRANSLATION_TABLE_EN_AR, $arStr);
	

	return $CUSTOM_TRANSLATION_TABLE_EN_AR[$translationKey];
}

function addTranslationEntry($enStr, $entryType, $arStr)
{
	global $CUSTOM_TRANSLATION_TABLE_EN_AR,$TABLE_LOADED;
	
	if ( !$TABLE_LOADED)
	{
		loadTranslationTable();
	}
	
	// ALLOW DUPOLICATE ENGLISH KEYS
	//if ( !isFoundInTranslationTable($enStr) )
	//{
		if ( empty($entryType))
		{
			$entryType="NONE";
		}
		else
		{
			$entryType = strtoupper($entryType);
			
		}
		
		$CUSTOM_TRANSLATION_TABLE_EN_AR[$enStr]=array("EN_TEXT"=>$enStr,"TYPE"=>$entryType,"AR_TEXT"=>$arStr);
		return true;
	//}
	//else
	//{
	//	return false;
	//}
	
	
	
}
function removeUnacceptedChars($text)
{
	return preg_replace("/\||\\r|\\n/", "", $text);
}

function persistTranslationTable($CUSTOM_TRANSLATION_TABLE_EN_AR=null)
{
	global $customTranslationTableFile;
	global $CUSTOM_TRANSLATION_TABLE_EN_AR,$TABLE_LOADED;
	
	if (!empty($CUSTOM_TRANSLATION_TABLE_EN_AR))
	{
		 $CUSTOM_TRANSLATION_TABLE_EN_AR = $CUSTOM_TRANSLATION_TABLE_EN_AR;
	}
	
	
	if (!$TABLE_LOADED || empty($CUSTOM_TRANSLATION_TABLE_EN_AR)) return false;
	

	//preprint_r($CUSTOM_TRANSLATION_TABLE_EN_AR);
	
	//clear file
	file_put_contents($customTranslationTableFile,"");
	
	foreach ($CUSTOM_TRANSLATION_TABLE_EN_AR as $enWord => $entryArr)
	{
		if ( empty($enWord) ) continue;
	
		$enWord = trim(removeUnacceptedChars($entryArr['EN_TEXT']));
		$wordType = trim(removeUnacceptedChars($entryArr['TYPE']));
		$arTranslation = trim(removeUnacceptedChars($entryArr['AR_TEXT']));
		
		
	
		$line = "$enWord|$wordType|$arTranslation\n";
	
		
		
		file_put_contents($customTranslationTableFile, $line,FILE_APPEND);
	}
	
	
}


function printTranslationTable()
{

	global $CUSTOM_TRANSLATION_TABLE_EN_AR,$TABLE_LOADED;



	if (!$TABLE_LOADED) return false;


	echoN("TRANSLATION TABLE COUNT:".count($CUSTOM_TRANSLATION_TABLE_EN_AR));
	
	foreach ($CUSTOM_TRANSLATION_TABLE_EN_AR as $enWord => $entryArr)
	{
		$enWord = trim(removeUnacceptedChars($entryArr['EN_TEXT']));
		$wordType = trim(removeUnacceptedChars($entryArr['TYPE']));
		$arTranslation = trim(removeUnacceptedChars($entryArr['AR_TEXT']));

		$line = "$enWord|$wordType|$arTranslation\n";
		
		echoN($line);

	}


}

/*
 * TESTING 
 * $CUSTOM_TRANSLATION_TABLE_EN_AR = loadTranslationTable();

preprint_r($CUSTOM_TRANSLATION_TABLE_EN_AR);

addTranlationEntry("Person", "Concept", "شخص");

preprint_r($CUSTOM_TRANSLATION_TABLE_EN_AR);

persistTranslationTable();

$CUSTOM_TRANSLATION_TABLE_EN_AR = loadTranslationTable();

preprint_r($CUSTOM_TRANSLATION_TABLE_EN_AR);
 */

?>