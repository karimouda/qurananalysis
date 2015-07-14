<?php 
require_once("../global.settings.php");

$lang = "AR";



if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core,search,qac",$lang);



$word = trim($_GET['word']);




	
//preprint_r($poTaggedSubsentences);

//echoN("SubSentences Count:".addCommasToNumber(count($poTaggedSubsentences)));

$topPoSAggregation = array();
$ssPoSAggregation = array();
$ssPoSAggregationCorrespondingSent = array();



		
			//echoN("Word:$word");
			

			$targetType = "POS";
			
			if ( isArabicString($word))
			{
				$targetType = "WORD";
				
				if ( isSimpleQuranWord($word))
				{
					$poTaggedSubsentences = getPoSTaggedSubsentences("SIMPLE");
				}
				else
				{
					$poTaggedSubsentences = getPoSTaggedSubsentences();
				}
			}
			else
			{
				$poTaggedSubsentences = getPoSTaggedSubsentences();
			}
			
			$targetPOSorWord = trim($word);
			
			
			if ( $targetType=="POS" )
			{
				if (   !isset($MODEL_QAC['QAC_POS'][$targetPOSorWord]))
				{
					showTechnicalError("Not a valid PoS tag !");
					exit;
				}
			}
			else 
			{
				if (  empty($targetPOSorWord) )
				{
					showTechnicalError("Word not valid !");
					exit;
				}
			}
			
			
			/////////// PREPARE CONTEXT ARRAY ////////////////
			$contextMaxLevel = 3;
			$contextArr = array();
			
			$contextArr[$targetPOSorWord]=array();
			
			for($i=$contextMaxLevel;$i>=1;$i--)
			{
				$contextArr[$targetPOSorWord]["+$i"]=array();
				
			}
			for($i=1;$i<=$contextMaxLevel;$i++)
			{
				$contextArr[$targetPOSorWord]["-$i"]=array();
			}	
			/////////////////////////////////////////////////
	
			//preprint_r($contextArr);exit;
			$targetWordCounter = 0;
			
			foreach ($poTaggedSubsentences as $ssLoc => $ssArray)
			{
				
				$wordsArr = $ssArray['WORDS'];
				$posArr = $ssArray['POS_TAGS'];
				$qacIndexesArr = $ssArray['QAC_WORD_INDEXES'];
				
				$wordsOrPoSStr = "";
				$posOrWordsArr = array();
				
				if ($targetType =="POS")
				{
					$posOrWordsArr = $posArr;
					$wordsOrPoSStr = join(" ",$ssArray['POS_TAGS']);
				}
				else
				{
					$posOrWordsArr = $wordsArr;
					$wordsOrPoSStr = join(" ",$ssArray['WORDS']);
				}
				
		
				
			
				$posOrWordEntryKey = false;
				$posOrWordsIndexesArr = array();
				
					// didn't use array_search because of "multple tags phrases" 
					// example searching fro PN while the word tag is "P PN" which will not match
					foreach($posOrWordsArr as $index => $posTagOrPhrase)
					{
						
						// can't use strpos PN is found in 'IMPN' 
						if ( preg_match("/(^|[ ])+$targetPOSorWord([ ]|\$)+/", $posTagOrPhrase))
						{
							$posOrWordEntryKey = $index;
							$posOrWordsIndexesArr[]=$index;
							
						}
					}
					
			
				
				
				if ( !empty($posOrWordsIndexesArr) )
				{
		
				
					
		
					foreach($posOrWordsIndexesArr as $sequenctialIndex => $wordIndexInArray )
					{
						//$qacWordIndex = $qacIndexesArr[$wordIndexInArray];
						//echoN(substr($ssLoc,0,strrpos($ssLoc, "-")).":".$qacWordIndex);
						$targetWordCounter++;
						
						if ( $wordIndexInArray>=1)
						{
							$level = 1;
							for($before=$wordIndexInArray-1;$before>=0;$before--)
							{
								
								if ( $level > $contextMaxLevel)
								{
									break;
								}
								
								$posOrWordInLevel = $posOrWordsArr[$before];
								$contextArr[$targetPOSorWord]["-$level"][$posOrWordInLevel]++;
								
								$level++;
								
								
							}
						}
						
						$posOrWordArrLength = count($posOrWordsArr);
						
						if ( ($posOrWordArrLength-($wordIndexInArray+1))>=1)
						{
							$level = 1;
							for($after=$wordIndexInArray+1;$after<$posOrWordArrLength;$after++)
							{
								if ( $level > $contextMaxLevel)
								{
									break;
								}
								
								$posOrWordInLevel = $posOrWordsArr[$after];
								$contextArr[$targetPOSorWord]["+$level"][$posOrWordInLevel]++;
								$level++;
							}
						}
					}

				}
				
				
				
				
				
				
				/*foreach($posOrWordsArr as $pos)
				{
					$topPoSAggregation[$pos]++;
				}

				$ssPoSPattern = join(", ",$posOrWordsArr);
				
				
				
				$ssPoSAggregation[$ssPoSPattern]++;
				
				$ssPoSAggregationCorrespondingSent[$ssPoSPattern] = join(" ",$wordsArr);
				*/
				
			}
			
			//preprint_r($contextArr);
			
			/*
			arsort($ssPoSAggregation);
			arsort($topPoSAggregation);
			
			echoN("SS Tags:".count($ssPoSAggregation));
			echoN("Top Tags:".count($topPoSAggregation));
						
			//preprint_r($ssPoSAggregation);
			
			foreach($ssPoSAggregation as $pattern=>$freq)
			{
				echoN($pattern."|".$freq."|".$ssPoSAggregationCorrespondingSent[$pattern]);
			}
			preprint_r($topPoSAggregation);
			*/
			
			
			
			
	
		
		
		if ( count($contextArr[$targetPOSorWord]["+1"])==0)
		{
			showTechnicalError("No data found, make sure the word is found in the Quran");
			showTechnicalError("Use proper Simple or Uthmani word ");
			showTechnicalError("Use Word information tool to convert words or its validity ");
				
			exit;
		}
		
		
		
		
					
?>
					
					<table id='words-pos-context-table' <?php if ($targetType=="WORD"){ echo "style='direction:rtl'" ;}?>>
						<thead>
						<tr>
							<th colspan='<?=($contextMaxLevel*2)+1?>'>
	
							</th>
						</tr>
						<tr>
							<?php 
								for($i=$contextMaxLevel;$i>=1;$i--):
								$levelName = "-$i";
							?>
								<th><?=$levelName?></th>
							<?php 
								endfor;
							?>
							<th>Zero</th>
							<?php 
								for($i=1;$i<=$contextMaxLevel;$i++):
								$levelName = "+$i";
							?>
								<th><?=$levelName?></th>
							<?php 
								endfor;
							?>
					
						</tr>
						</thead>
						<tr>	
							<?php 
								for($i=$contextMaxLevel;$i>=1;$i--):
								$levelName = "-$i";
							?>
								<td>
									<?php 
										$levelArr = $contextArr[$targetPOSorWord][$levelName];
										arsort($levelArr);
										
										foreach ($levelArr as $posOrWord => $posOrWordFreq)
										{
											echoN("[$posOrWordFreq] <b>$posOrWord</b>");
										}
									?>
								
								</td>
							<?php 
								endfor;
							?>
							<td id='target-pos-level-zero-cell'>
							<?=$targetPOSorWord?>
							<br>
							[<?=$targetWordCounter?>]
							</td>
							<?php 
								for($i=1;$i<=$contextMaxLevel;$i++):
								$levelName = "+$i";
							?>
								<td>
									<?php 
										$levelArr = $contextArr[$targetPOSorWord][$levelName];
										arsort($levelArr);
										
										foreach ($levelArr as $posOrWord => $posOrWordFreq)
										{
											echoN("[$posOrWordFreq] <b>$posOrWord</b>");
										}
									?>
								
								</td>
							<?php 
								endfor;
							?>
						</tr>
					</table>

<?php 
			
?>		
			

