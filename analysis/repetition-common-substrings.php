<?php 
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
    <title>Quran Analytics | Quran Repeated Phrases (Common Substrings) </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Full Analytics System for the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>
	<?php 
			require_once("./analysis.header.php");
	?>
				
  <div id='main-container'>

			  		
			

	
			  
			  	
			  	<div id='repetition-area'>
			  	<h1 class='analysis-title-header'>Common Phrases in the Quran</h1>
					<?php 
					
					$repeatedSubStrings = array();

					
					if ( $LOAD_FROM_CACHE )
					{
						$repeatedSubStrings = unserialize(file_get_contents("../data/cache/repetition-common-substrings"));

						//preprint_r($repeatedSubStrings);
					}
					else
					{
					
						
					
					?>
					
						<?php 
							$quranVersesArr = array();
							
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
						  			$suraName = $MODEL_CORE['META_DATA']['SURAS'][$s]['name_'.strtolower($lang)];
						  			
									
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
					
					<table id='ngrams-results-table'>
					<thead>
					<tr>
						<td colspan='2'>
							
							<b><?=addCommasToNumber($repeatedVersesCount) ?></b> Phrases
							
							
						</td>
					</tr>
					</thead>
					<tr>
					<th>
						Words
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







