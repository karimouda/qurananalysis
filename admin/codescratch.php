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

$lang = "AR";

loadModels("", $lang);


$QURAN_TEXT = getModelEntryFromMemory($lang, "MODEL_CORE", "QURAN_TEXT", "");

preprint_r($QURAN_TEXT[1][1]);

$location = "1:1:1";

$qacMasterTableEntry = getModelEntryFromMemory("AR","MODEL_QAC","QAC_MASTERTABLE",$location);

preprint_r($qacMasterTableEntry);


$qaOntologyConceptsIterator = getAPCIterator("ALL\/MODEL_QA_ONTOLOGY\/CONCEPTS\/.*");

foreach($qaOntologyConceptsIterator as $conceptsCursor )
{
	$conceptNameID = getEntryKeyFromAPCKey($conceptsCursor['key']);

	$conceptArr = $conceptsCursor['value'];


	$conceptLabelAR = $conceptArr['label_ar'];
	$conceptLabelEN = $conceptArr['label_en'];
	$conceptFrequency = $conceptArr['frequency'];
	$conceptWeight = $conceptArr['weight'];
	
	preprint_r($conceptArr);
	
	break;//only one concept
}



// print all words in wordnet
preprint_r(array_keys($MODEL_WORDNET['INDEX']));

// get all information about "Egypt" from wordnet
$wordNetEntry = getWordnetEntryByWordString("egypt");

preprint_r($wordNetEntry);


?>