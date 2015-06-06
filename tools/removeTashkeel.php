<?php 
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