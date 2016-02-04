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

loadModels("core",$lang);

$LOAD_FROM_CACHE = TRUE;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Repeated Phrases (Common Substrings) | Quran Analysis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Repeated Phrases (Substrings) in the Quran">
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

			  		
				    <?php include_once("help-content.php"); ?>

	
			  
			  	
			  	<div id='repetition-area'>
		
					<?php 
					if ( $lang=="EN")
					{
							
						showTechnicalError("Only Arabic is supported here, you chose English !");
					
					
					}
					
					$repeatedSubStrings = array();

					
					if ( $LOAD_FROM_CACHE )
					{
						$repeatedSubStrings = unserialize(file_get_contents("../data/cache/repetition-common-substrings"));

						//preprint_r($repeatedSubStrings);
					}
					else
					{
					
							$QURAN_TEXT = getModelEntryFromMemory($lang, "MODEL_CORE", "QURAN_TEXT", "");
							$META_DATA = getModelEntryFromMemory($lang, "MODEL_CORE", "META_DATA", "");
							
							$quranVersesArr = array();
							
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
						  			$suraName =  $META_DATA['SURAS'][$s]['name_'.strtolower($lang)];
						  			
									
						  			$verseText = removePauseMarkFromVerse($verseText);
						  			
						  			$quranVersesArr[]=$verseText;
				
								}
					  		}
					  	
					  		//preprint_r($quranVersesArr);
					  	
					/*
					 * 
					 * NOTE: A DECISION SHOULD BE TAKEN TO WHETHER SPLIT VERSES ON PAUSE MARKS BEFORE 
					 * CSS (COMMON SUBSTRINGS) ALGORITHM OR NOT  
					 */
					$lcsInVerse = array(); 
					$quranVersesArr2 = $quranVersesArr;
					$index1=0;
					foreach ($quranVersesArr as $verse1)
					{
						

						
						end($quranVersesArr2);
						
						$index2 = count($quranVersesArr2);
						while($verse2 = current(($quranVersesArr2)))
						{
							$index2--;
							
							//echoN($verse1."<br>".$verse2);
							
							//echoN("B:$index1>=$index2");
							if ($index1>=$index2) break;;
							
							//echoN("A:$index1>=$index2");
					
							
						
							$lcsStrArr = getLCSModifiedAlgorithm($verse1,$verse2);
							
							
							foreach( $lcsStrArr as $lcsStr)
							{
								//echoN($index1."|".$index2 ."|$lcsStr|");
								
								
								if ( !empty($lcsStr) && strpos($lcsStr," ")!==false )
								{
									$verseLCSHash = md5($index2 .":". $lcsStr);
									
									//echoN("|$lcsStr|");
										
								
									if (  !isset($lcsInVerse[$verseLCSHash]) )
									{
										if (!isset($repeatedSubStrings[$lcsStr]))
										{
											// 1 for verse #1 since it will NOT be chceked again ( $index1>=$index2 ) condition
											$repeatedSubStrings[$lcsStr]=1;
										}
										
										
										$repeatedSubStrings[$lcsStr]++;
									}
									
									/*
									 * FOR DEBUGGING
									if ($lcsStr=="الله لا يخلف")
									{
										echoN($index2 ."-". $lcsStr);
										echoN($verseLCSHash);
										echoN((!isset($lcsInVerse[$verseLCSHash])));
										echoN(!isset($repeatedSubStrings[$lcsStr]));
										echoN($index1);
										echoN($repeatedSubStrings[$lcsStr]);
										
									}*/
								}
								
								$lcsInVerse[$verseLCSHash]=1;
							}
							
							prev($quranVersesArr2);
						}
						
						$index1++;
						
						//if ( $index1==10) break;
					
					}
					
					arsort($repeatedSubStrings);
					  		
					  		
					  		
					  		
					  		//echoN($repeatedVersesCount);
					  		
					  		file_put_contents("../data/cache/repetition-common-substrings", serialize($repeatedSubStrings));
					  		
					  		
					  		//preprint_r($repeatedSubStrings);
					  		
					  	
					  		
					  		
					}
					
					$repeatedVersesCount = count($repeatedSubStrings);
					  		
					  		
			  				?>
					
					<table id='repeated-results-table'>
					<thead>
					<tr>
						<td colspan='2'>
							
							<b><?=addCommasToNumber($repeatedVersesCount) ?></b> Phrases
							
							
						</td>
					</tr>
					</thead>
					<tr>
					<th>
						Phrase
					</th>
					<th>
						Frequency
					</th>
					</tr>
					
					<?php
				
	
					
					foreach($repeatedSubStrings as $word=>$freq)
					{
						
					?>
					<tr>	
						<td>
							<?=$word?>
						</td>
						<td>
							<?=$freq?>
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







