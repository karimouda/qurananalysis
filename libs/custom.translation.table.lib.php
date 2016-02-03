<?php
#   PLEASE DO NOT REMOVE OR CHANGE THIS COPYRIGHT BLOCK
#   ====================================================================
#
#    Quran Analysis (www.qurananalysis.com). Full Semantic Search and Intelligence System for the Quran.
#    Copyright (C) 2015  Karim Ouda
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#    You can use Quran Analysis code, framework or corpora in your website
#	 or application (commercial/non-commercial) provided that you link
#    back to www.qurananalysis.com and sufficient credits are given.
#
#  ====================================================================
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
		
		$keyLang = trim(removeUnacceptedChars($lineArr[0]));
		$mainWord = trim(removeUnacceptedChars($lineArr[1]));
		$wordType = trim(removeUnacceptedChars($lineArr[2]));
		$translatedWord= trim(removeUnacceptedChars($lineArr[3]));

		if ( $keyLang=="EN")
		{
			$CUSTOM_TRANSLATION_TABLE_EN_AR[$mainWord]=array("EN_TEXT"=>$mainWord,"TYPE"=>$wordType,"AR_TEXT"=>$translatedWord,"KEY_LANG"=>$keyLang);
		}
		else 
		{
			$CUSTOM_TRANSLATION_TABLE_EN_AR[$mainWord]=array("EN_TEXT"=>$translatedWord,"TYPE"=>$wordType,"AR_TEXT"=>$mainWord,"KEY_LANG"=>$keyLang);
		}
	}
	
	$TABLE_LOADED = true;
	
	return $CUSTOM_TRANSLATION_TABLE_EN_AR;
}

function isFoundInTranslationTable($enArStr,$wordType="CONCEPT")
{
	global $CUSTOM_TRANSLATION_TABLE_EN_AR;
	
	$enArStr = tranlstationCleanAndTrim(removeUnacceptedChars(($enArStr)));
	//preprint_r($CUSTOM_TRANSLATION_TABLE_EN_AR);exit;
		 	
	return  (isset($CUSTOM_TRANSLATION_TABLE_EN_AR[$enArStr]) 
			&& ($CUSTOM_TRANSLATION_TABLE_EN_AR[$enArStr]['TYPE']==$wordType) &&
		 	 !empty($CUSTOM_TRANSLATION_TABLE_EN_AR[$enArStr]['AR_TEXT']) && !empty($CUSTOM_TRANSLATION_TABLE_EN_AR[$enArStr]['EN_TEXT']) );
}

function tranlstationCleanAndTrim($str)
{
	//« spoils arabic words = 0xab
	$tobeReplacedStr = "\t\n\r\0\x0B ";
	return trim(trim(trim($str),$tobeReplacedStr));
}


function isFoundInTranslationTableArabicKeyword($arStr,$wordType="CONCEPT")
{
	global $CUSTOM_TRANSLATION_TABLE_EN_AR;

	$translationKey = search2DArrayForValue($CUSTOM_TRANSLATION_TABLE_EN_AR, $arStr, array("KEY"=>"TYPE","VAL"=>$wordType) );
	
	/*
	echoN(count($CUSTOM_TRANSLATION_TABLE_EN_AR));
	echoN($arStr);
	preprint_r($translationKey);
	echoN($wordType);
	preprint_r($CUSTOM_TRANSLATION_TABLE_EN_AR[$translationKey]);
	*/
	
	if ($translationKey!==false)
	{
		return true;
	}
	
	return false;
}


function getTranlationEntryByEntryKeyword($enStr)
{
	global $CUSTOM_TRANSLATION_TABLE_EN_AR,$TABLE_LOADED;
	
	$enStr = trim($enStr);
	
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



function addTranslationEntry($enStr, $entryType, $arStr,$keyLang="EN")
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
	
	if ( $keyLang=="EN")
	{

		$CUSTOM_TRANSLATION_TABLE_EN_AR[$enStr]=array("EN_TEXT"=>$enStr,"TYPE"=>$entryType,"AR_TEXT"=>$arStr,"KEY_LANG"=>$keyLang);
	}
	else
	{
		$CUSTOM_TRANSLATION_TABLE_EN_AR[$arStr]=array("EN_TEXT"=>$enStr,"TYPE"=>$entryType,"AR_TEXT"=>$arStr,"KEY_LANG"=>$keyLang);
	}
	
	return true;
	//}
	//else
	//{
	//	return false;
	//}



}

function removeUnacceptedChars($text)
{
	$text = strtr($text, "(", "[");
	$text = strtr($text, ")", "]");
	
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
	

	//preprint_r($CUSTOM_TRANSLATION_TABLE_EN_AR);exit;
	
	//clear file
	file_put_contents($customTranslationTableFile,"");
	
	foreach ($CUSTOM_TRANSLATION_TABLE_EN_AR as $enWord => $entryArr)
	{
		if ( empty($enWord) ) continue;
	
		$keyLang = tranlstationCleanAndTrim(removeUnacceptedChars($entryArr['KEY_LANG']));
		$enWord = tranlstationCleanAndTrim(removeUnacceptedChars($entryArr['EN_TEXT']));
		$wordType = tranlstationCleanAndTrim(removeUnacceptedChars($entryArr['TYPE']));
		$arTranslation = tranlstationCleanAndTrim(removeUnacceptedChars($entryArr['AR_TEXT']));
		
		if ( $keyLang=="EN")
		{
			$line = "$keyLang|$enWord|$wordType|$arTranslation\n";
		}
		else
		{
			$line = "$keyLang|$arTranslation|$wordType|$enWord\n";
		}
		
		
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
		$keyLang = trim(removeUnacceptedChars($entryArr['KEY_LANG']));
		$enWord = trim(removeUnacceptedChars($entryArr['EN_TEXT']));
		$wordType = trim(removeUnacceptedChars($entryArr['TYPE']));
		$arTranslation = trim(removeUnacceptedChars($entryArr['AR_TEXT']));

		$line = "$keyLang|$enWord|$wordType|$arTranslation\n";
		
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