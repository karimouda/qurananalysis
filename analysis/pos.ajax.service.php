<?php 
require_once("../global.settings.php");




$lang = "AR";

if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

if ( $lang=="EN")
{
	echoN("POS Tags is supported only for Arabic.");
	exit;
}

loadModels("core,qac",$lang);

$POS = $_GET['pos'];


// nothing passed
if ( ($POS=="") )
{
	exit;
}
					
					$markedVerses = array();
					//preprint_r($MODEL_QAC['QAC_POS'][$POS]);

					foreach($MODEL_QAC['QAC_POS'][$POS] as $location => $segmentId)
					{
						//echoN($location);
						//preprint_r($MODEL_QAC['QAC_MATERTABLE'][$location]);
						//echoN($segmentId);
						$segmentWord = $MODEL_QAC['QAC_MATERTABLE'][$location][$segmentId-1]['FORM_AR'];
						$verseLocation = substr($location,0,strlen($location)-2);
						$segmentWord = removeTashkeel($segmentWord);
					
					
						if ( !isset($markedVerses[$verseLocation]))
						{
							$verseText = getVerseByQACLocation($MODEL_CORE,$location);
						}
						else
						{
							$verseText = $markedVerses[$verseLocation];
						}
						
						$wordId = (getWordIndexFromQACLocation($location)-1);
						
					
						$verseText = markSpecificWordInText($verseText,$wordId,$segmentWord,"marked_fg");
						
						
						$markedVerses[$verseLocation] = $verseText;
						
					}
						//echoN($segmentWord);
						//echoN($verseText);
						//exit;
					foreach($markedVerses as $location=> $verseText)
					{
						$suraName = $MODEL_CORE['META_DATA']['SURAS'][($location[0]-1)]['name_'.strtolower($lang)];
							
						?>
						<div class='pos-value-div' location='<?=$location?>'>
						<span class='pos-sura-info'>[<?=$location." ".$suraName?>]</span>&nbsp;<?php echo  $verseText?> 
						</div>
						<?php
					
					}

				
?>

