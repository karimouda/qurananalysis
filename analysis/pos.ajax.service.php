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
