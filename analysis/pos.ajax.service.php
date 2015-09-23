<?php 
require_once("../global.settings.php");




$lang = "AR";

if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

if ( $lang=="EN")
{
	showTechnicalError("Only Arabic is supported here, you chose English !");
	
}

loadModels("core,qac",$lang);



$POS = $_GET['pos'];
$features = $_GET['features'];

$features = strtoupper($features);




// nothing passed
if ( ($POS=="") )
{
	exit;
}
					
					$markedVerses = array();
					$unrepeatedWords = array();
					//preprint_r($MODEL_QAC['QAC_POS'][$POS]);
					
					
					$qacPosEntryArr = getModelEntryFromMemory("AR","MODEL_QAC","QAC_POS",$POS);
					 
					
					$allOccurencesCount = count($qacPosEntryArr);
					
					
					$QURAN_TEXT = getModelEntryFromMemory($lang, "MODEL_CORE", "QURAN_TEXT", "");
			
					$META_DATA = getModelEntryFromMemory($lang, "MODEL_CORE", "META_DATA", "");

					foreach($qacPosEntryArr as $location => $segmentId)
					{
						//echoN($location);
						//preprint_r($MODEL_QAC['QAC_MASTERTABLE'][$location]);
						//echoN($segmentId);
						
						$qacMasterTableEntry = getModelEntryFromMemory("AR","MODEL_QAC","QAC_MASTERTABLE",$location);
						
						
						
						$segmentWord = $qacMasterTableEntry[$segmentId-1]['FORM_AR'];
						$verseLocation = substr($location,0,strlen($location)-2);
		
						
						// not an Uthmani word, maybe just a DET seg "ال"
					/*	if ( empty($segmentWordSimple))
						{
							$segmentWordSimple = removeTashkeel($segmentWord);
							
							// convert ARABIC LETTER ALEF WASLA to normal Alef
							$segmentWordSimple = preg_replace("/\x{0671}/um","ا",$segmentWordSimple);
						}
						*/
						
						if ( !empty($features) )
						{
							$featuresArr = preg_split("/,/",$features);
							$isFeatureFound = false;
							foreach($featuresArr as $oneFeature)
							{
								$isFeatureFound = isset($qacMasterTableEntry[$segmentId-1]['FEATURES'][$features]);
							}
						
							if ( $isFeatureFound==false)
							{
								continue;
							}
						}
						
						if ( !isset($unrepeatedWords[$segmentWord]))
						{
							$unrepeatedWords[$segmentWord] = 1;
						}
			
					
					
						if ( !isset($markedVerses[$verseLocation]))
						{
							$verseText = getVerseByQACLocation($QURAN_TEXT,$location);
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
					<div id='pos-words-verses-statistics'> 
					
					
					<b><?=addCommasToNumber($allOccurencesCount)?></b> All Segments - 
					<b><?=addCommasToNumber(count($unrepeatedWords))?></b> Distinct Words - 
					<b><?=addCommasToNumber(count($markedVerses))?></b> Verses  </div>
					
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
						$suraName = $META_DATA['SURAS'][($location[0]-1)]['name_'.strtolower($lang)];
							
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
