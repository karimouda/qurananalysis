<?php 
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
					//preprint_r($MODEL_QAC['QAC_POS'][$POS]);

				$i=0;
				/* SURA'S LOOP **/
		  		for ($s=0;$s<$numberOfSuras;$s++)
		  		{
		  		
		  				
		  			$suraSize = count($MODEL_CORE['QURAN_TEXT'][$s]);
		  		
		  			/* VERSES LOOP **/
			  		for ($a=0;$a<$suraSize;$a++)
			  		{
			  			$i++;
			  			$verseText = $MODEL_CORE['QURAN_TEXT'][$s][$a];
			  			
			  			if ( mb_strpos($verseText,$pauseMark)!==false)
			  			{
				  			$suraName = $MODEL_CORE['META_DATA']['SURAS'][$s]['name_'.strtolower($lang)];
				  			
						
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
