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

$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = loadUthmaniToSimpleMappingTable();

$POS = $_GET['pos'];

$MODEL_CORE_UTH = loadUthmaniDataModel();


// nothing passed
if ( ($POS=="") )
{
	exit;
}
					
					$markedVerses = array();
					$unrepeatedWords = array();
					//preprint_r($MODEL_QAC['QAC_POS'][$POS]);

					foreach($MODEL_QAC['QAC_POS'][$POS] as $location => $segmentId)
					{
						//echoN($location);
						//preprint_r($MODEL_QAC['QAC_MASTERTABLE'][$location]);
						//echoN($segmentId);
						$segmentWord = $MODEL_QAC['QAC_MASTERTABLE'][$location][$segmentId-1]['FORM_AR'];
						$verseLocation = substr($location,0,strlen($location)-2);
						//$segmentWordSimple = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$segmentWord];//removeTashkeel($segmentWord);
						
						// not an Uthmani word, maybe just a DET seg "ال"
					/*	if ( empty($segmentWordSimple))
						{
							$segmentWordSimple = removeTashkeel($segmentWord);
							
							// convert ARABIC LETTER ALEF WASLA to normal Alef
							$segmentWordSimple = preg_replace("/\x{0671}/um","ا",$segmentWordSimple);
						}
						*/
						
						if ( !isset($unrepeatedWords[$segmentWord]))
						{
							$unrepeatedWords[$segmentWord] = 1;
						}
			
					
					
						if ( !isset($markedVerses[$verseLocation]))
						{
							$verseText = getVerseByQACLocation($MODEL_CORE_UTH,$location);
						}
						else
						{
							$verseText = $markedVerses[$verseLocation];
						}
						
						$wordId = (getWordIndexFromQACLocation($location)-1);
						
					
					
						$verseText = markSpecificWordInText($verseText,$wordId,$segmentWord,"marked_fg");
						
						
				
						
						$markedVerses[$verseLocation] = $verseText;
						
					}
					
					?>
					<div id='pos-words-verses-statistics'> <b><?=addCommasToNumber(count($unrepeatedWords))?></b> Words - <b><?=addCommasToNumber(count($markedVerses))?></b> Verses  </div>
					
					<table id='pos-words-verses-table'>
					<tr>
					<th>
						Distinct Words
					</th>
					<th>
						Verses
					</th>
					</tr>
					<tr>
					<td style="background-color: #dddddd;">
					
						<?php 
						foreach($unrepeatedWords as $word=> $dummy)
						{
							
								
							?>
							<div class='pos-value-div' >
							<?php echo  $word?> 
							</div>
							<?php
						
						}
						?>
					</td>
					<td>
					<?php 
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
						</td>
					</tr>
					</table>
