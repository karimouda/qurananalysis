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

$wordsInfoArr = unserialize(file_get_contents("../data/cache/words.info.all"));



$stopWordsArr = file(dirname(__FILE__)."/../data/merged-stoplist-files.2.sorted.unique.txt");

$verbsInfoArr = array();
getWordsByPos($verbsInfoArr, "V");



//preprint_r($verbsInfoArr);exit;

$verbsArr = array();

foreach($verbsInfoArr as $word => $infoArr)
{
	$simpleWord = $infoArr['SIMPLE_WORD'];
	$verbsArr[]=$simpleWord;
	
}

//preprint_r($verbsArr);exit;

//$stopWordsArr = array_merge($stopWordsArr,$verbsArr);


$stopWordsFromQuran = array();

$WORDS_FREQUENCY = getModelEntryFromMemory("AR", "MODEL_CORE", "WORDS_FREQUENCY", "");



foreach ($stopWordsArr as $key => $word)
{
	$word = trim($word);
	

	if ( isset($WORDS_FREQUENCY['WORDS'][$word]) )
	{
		
		
		$countOfPosTagsForThisWord = count($wordsInfoArr[$word]['POS']);
		
	
		
	
			
			
			// the only PoS tag for this word is ADJ, N or Pn which can't be stop word
			if ( isset($wordsInfoArr[$word]['POS']['ADJ'] ) 
			||   isset($wordsInfoArr[$word]['POS']['N'] ) 
			||   isset($wordsInfoArr[$word]['POS']['PN'] )  )
			{
				
				if ( ($countOfPosTagsForThisWord>1 && isset($wordsInfoArr[$word]['POS']['DET'] ) ) || 
					  $countOfPosTagsForThisWord==1   )
				{
					//preprint_r($wordsInfoArr[$word]['POS']);
					//echoN($word);
					
					continue;
				}
				
			}
		
		
		echoN($word);
		$stopWordsFromQuran[$word]=1;
	}
}

echoN(count($stopWordsFromQuran));
//preprint_r($stopWordsFromQuran);

file_put_contents(dirname(__FILE__)."/../data/quran-stop-words.strict.l1.ar", implode("\n", array_keys($stopWordsFromQuran)));
exit;

?>