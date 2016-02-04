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
	echoN("Pause Marks are supported only for Arabic, you chose English !");
	exit;
}

loadModels("core",$lang);



$pauseMark = $_GET['mark'];




// nothing passed
if ( ($pauseMark=="") )
{
	exit;
}


					$markedVerses = array();
					$unrepeatedWords = array();
					
					$QURAN_TEXT = getModelEntryFromMemory($lang, "MODEL_CORE", "QURAN_TEXT", "");
						
					$META_DATA = getModelEntryFromMemory($lang, "MODEL_CORE", "META_DATA", "");
					
					$TOTALS= getModelEntryFromMemory($lang, "MODEL_CORE", "TOTALS", "");
						
				
			

				$i=0;
				/* SURA'S LOOP **/
		  		for ($s=0;$s<$numberOfSuras;$s++)
		  		{
		  		
		  				
		  			$suraSize = count($QURAN_TEXT[$s]);
		  		
		  			/* VERSES LOOP **/
			  		for ($a=0;$a<$suraSize;$a++)
			  		{
			  			$i++;
			  			$verseText = $QURAN_TEXT[$s][$a];
			  			
			  			if ( mb_strpos($verseText,$pauseMark)!==false)
			  			{
				  			$suraName = $META_DATA['SURAS'][$s]['name_'.strtolower($lang)];
				  			
						
							$verseLocation = ($s+1).":".($a+1);
						
						
							// done in last preg replace
							if ( isset($markedVerses[$verseLocation]))
							{
								continue;
							}
							
							
							
							$verseText = preg_replace("/($pauseMark)/umi", "<marked_fg class='pausemarks-style'>\\1</marked_fg>", $verseText);

							$markedVerses[$verseLocation] = $verseText;
							
						}
						
					
					
						
			  		}
						
				}
					
					?>
				
					
					<table id='pausemarks-verses-table'>
					<tr>
					<th>
						Verses (<?=addCommasToNumber(count($markedVerses))?>)
					</th>
					</tr>
					<tr>
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
