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
require_once("../global.settings.php");
require_once("../libs/core.lib.php");

loadModels("core,qac", "AR");






$LEMMA_TO_SIMPLE_WORD_MAP = loadLemmaToSimpleMappingTable();


printHTMLPageHeader();

//$qacMasterTableEntryArr2 = getModelEntryFromMemory("AR","MODEL_QAC","QAC_POS",$qacLocation);


$qacPoSTagsIterator = getAPCIterator("AR\/MODEL_QAC\/QAC_POS\/.*");

$QURAN_TEXT = getModelEntryFromMemory("AR", "MODEL_CORE", "QURAN_TEXT", "");


$TOTALS = getModelEntryFromMemory("AR", "MODEL_CORE", "TOTALS", "");

$PAUSEMARKS = $TOTALS['PAUSEMARKS'];

preprint_r($PAUSEMARKS);

foreach($qacPoSTagsIterator as $qacPoSTagsIteratorCursor)
{

	$POS_ARR = $qacPoSTagsIteratorCursor['value'];
	$key = $qacPoSTagsIteratorCursor['key'];
	$POS = getEntryKeyFromAPCKey($key);
	
	if ( $POS=="N" || $POS=="PN" || $POS=="ADJ") continue;
	//echoN("|$POS|");
	
	
	foreach($POS_ARR as $location => $segmentId)
	{
		

		
		$qacMasterTableEntry = getModelEntryFromMemory("AR","MODEL_QAC","QAC_MASTERTABLE",$location);
		
	
		// get Word, Lema and root
		$segmentWord = $qacMasterTableEntry[$segmentId-1]['FORM_AR'];
		$segmentWordLema = $qacMasterTableEntry[$segmentId-1]['FEATURES']['LEM'];
		$segmentWordRoot = $qacMasterTableEntry[$segmentId-1]['FEATURES']['ROOT'];
		$verseLocation = substr($location,0,strlen($location)-2);
		//$segmentWord = removeTashkeel($segmentWord);
		
		
		
		if ( $POS=="DET"  )
		{
			// second segment PoS
			$segmentPoStag = $qacMasterTableEntry[$segmentId]['TAG'];
			
			//number of segments
			$numberOfSegmentsInWord = count($qacMasterTableEntry);
			
			if ( ($segmentPoStag=="N" || $segmentPoStag=="ADJ") && $numberOfSegmentsInWord==2 )
			{
				continue;
			}
			
			
		}
	
	
		// get word index in verse
		$wordIndex = (getWordIndexFromQACLocation($location));
	

		
		//echoN($segmentFormARimla2y);

		// get simple version of the word index
		$imla2yWordIndex = getImla2yWordIndexByUthmaniLocation($location);
	
	
		// get verse text
		$verseText = getVerseByQACLocation($QURAN_TEXT,$location);
			
		$imla2yWord = getWordFromVerseByIndex($PAUSEMARKS,$verseText,$imla2yWordIndex);
			
		//echoN($imla2yWord);
		
		$stopWordsFromQuran[$imla2yWord]=1;
			
		echoN($imla2yWord);
	
	}
}

$ya = "يا";

//add يا
$stopWordsFromQuran[$ya]=1;


echoN(count($stopWordsFromQuran));
//preprint_r($stopWordsFromQuran);
//exit;

file_put_contents(dirname(__FILE__)."/../data/quran-stop-words.strict.l2.ar", implode("\n", array_keys($stopWordsFromQuran)));
exit;

?>