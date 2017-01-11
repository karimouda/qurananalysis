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

function getRepeatedVerses2($lang,$threshold=1)
	{
		global $numberOfSuras;
		
		$QURAN_TEXT = getModelEntryFromMemory($lang, "MODEL_CORE", "QURAN_TEXT", "");

		$TRANSLITERATION_VERSES_MAP = apcu_fetch("TRANSLITERATION_VERSES_MAP");



		$repeatedVerses = array();
		$repeatedVersesPointer = array();
		$repeatedTransliterated = array();
		
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
						
						
					initArrayWithZero($repeatedVerses[$verseText]);
						
					$repeatedVerses[$verseText]++;
					$verseLocation = ($s+1).":".($a+1);
				        $repeatedVersesPointer[$verseText] = $verseLocation;

					$repeatedTransliterated[$verseText] = $TRANSLITERATION_VERSES_MAP[$verseLocation];
				
				}
			
			}
			
			arsort($repeatedVerses);
				
			
				
			$repeatedVerses = array_filter($repeatedVerses, 
					function($v) use ($threshold) 
					{
						return	$v > $threshold; 
					} );
			
			return array($repeatedVerses,$repeatedVersesPointer,$repeatedTransliterated);
	}

loadModels("core",$lang);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Repeated Verses | Quran Analysis </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Repeated Verses in the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/favicon.png">	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>

  		<?php 
				require("./analysis.template.start.code.php");
		
		?>	
				
  <div id='main-container'>
  
  		<h1>Quran Repeated Verses</h1>
			  		
			
	    <?php include_once("help-content.php"); ?>
	
			  
<a href="?tr=1">Show transliteration</a>
			  	
			  	<div id='repetition-area'>
					<?php 

					$showTR = $_GET['tr'];	
			

					$repeatedVerses = array();
					
					$res_list = getRepeatedVerses2($lang);

					$repeatedVerses = $res_list[0];

					$repeatedVersesPointer = $res_list[1];
					
					$repeatedVersesTransliteration = $res_list[2];
			
					
					
					$repeatedVersesCount = count($repeatedVerses);
						
					
					?>
					
					<table id='repeated-results-table'>
					<thead>
					<tr>
						<td colspan='2'>
							
							<b><?=addCommasToNumber($repeatedVersesCount) ?></b> Verses
							
							
						</td>
					</tr>
					</thead>
					<tr>
					<th>
						Location
					</th>
					<th>
						Verse
					</th>
					<th>
						Frequency
					</th>
					</tr>
					
					<?php
				
	
					
					foreach($repeatedVerses as $key=>$val)
					{
						
					?>
					<tr>	
						<td>
							<?=$repeatedVersesPointer[$key]?>
						</td>
						<td>
							<?
								if ($showTR==1)
								{
									echo $repeatedVersesTransliteration[$key];
								}
								else
								{
									echo $key;
								}
							?>
						</td>
						<td>
							<?=$val?>
						</td>
					</tr>
					<?php 
					}
					?>
					</table>
				</div>
	
		  		
			
   </div>
 		<?php 
				require("./analysis.template.end.code.php");
		
		?>	  

	<script type="text/javascript">


				
		$(document).ready(function()
		{


		
		});


		
	</script>
		

	<?php 
		require("../footer.php");
	?>
	


  </body>
</html>







