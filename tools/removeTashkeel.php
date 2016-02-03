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
include_once("../global.settings.php");

//$words ="الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ";
$words ="ءَآلْـَٰٔنَ";



$words = preg_replace("/[\x{0618}-\x{061A}\x{064B}-\x{0654}\x{0670}-\x{0671}\x{06DC}\x{06DF}\x{06E0}\x{06E2}\x{06E3}\x{06E5}\x{06E6}\x{06E8}\x{06EA}-\x{06ED}]/um","",$words);

echo $words;


$stopWordsArr = file(dirname(__FILE__)."/../data/quran.datamining.st.2.txt");
/*

loadModel();
$stopWordsFromQuran = array();

foreach ($stopWordsArr as $key => $word)
{
	$word = trim($word);
	$word = preg_replace("/[\x{0618}-\x{061A}\x{064B}-\x{0654}\x{0670}-\x{0671}\x{06DC}\x{06DF}\x{06E0}\x{06E2}\x{06E3}\x{06E5}\x{06E6}\x{06E8}\x{06EA}-\x{06ED}]/um","",$word);
	//echoN($word);
		
	if ( isset($MODEL['WORDS_FREQUENCY']['WORDS'][$word]) )
	{
		$stopWordsFromQuran[$word]=1;
	}
}

echoN(count($stopWordsFromQuran));
preprint_r($stopWordsFromQuran);

file_put_contents(dirname(__FILE__)."/../data/quran.datamining.st.3.txt", implode("\n", array_keys($stopWordsFromQuran)));
exit;
*/
?>