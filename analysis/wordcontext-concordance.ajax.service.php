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




$lang = $_GET['lang'];

if ($lang=="EN")
{
	showTechnicalError("Only Arabic is supported here, you chose English !");
}

$lang = "AR";

loadModels("core,search,qac",$lang);



$word = trim($_GET['word']);
$userPreferedContextLevel =  trim($_GET['level']);




	
//preprint_r($posTaggedSubsentences);

//echoN("SubSentences Count:".addCommasToNumber(count($posTaggedSubsentences)));

$topPoSAggregation = array();
$ssPoSAggregation = array();
$ssPoSAggregationCorrespondingSent = array();



			function getContextPhrases($contextArr,$maxLevel,$beforeOrAfter="BEFORE")
			{
				$phraseStr = "";
				
				$levelSign = "-";
				
				if ($beforeOrAfter!="BEFORE")
				{
					// then after
					$levelSign = "+";
				}
				
				
					
				if ( $levelSign=="+")
				{
					for($i=1;$i<=$maxLevel;$i++)
					{
						// ["+1i]
						foreach($contextArr["$levelSign"."$i"] as $word => $freq)
						{
							if ( empty($word)) continue;
							
							$phraseStr = $phraseStr. " ".$word;
						}
					}
				}
				else
				{
					for($i=$maxLevel;$i>=1;$i--)
					{
						// ["+1i]
						foreach($contextArr["$levelSign"."$i"] as $word => $freq)
						{
							if ( empty($word)) continue;
								
							$phraseStr = $phraseStr. " ".$word;
						}
					}
				}
					
				
				return trim($phraseStr);
				
				
			}
		
			//echoN("Word:$word");
			

			$targetType = "POS";
			
			if ( isArabicString($word))
			{
				$targetType = "WORD";
				
				if ( isSimpleQuranWord($word))
				{
					$posTaggedSubsentences = getPoSTaggedSubsentences("SIMPLE");
				}
				else
				{
					$posTaggedSubsentences = getPoSTaggedSubsentences();
				}
			}
			else
			{
				$posTaggedSubsentences = getPoSTaggedSubsentences();
			}
			
			$targetPOSorWord = trim($word);
			
			
			if ( $targetType=="POS" )
			{
				
				if (   !modelEntryExistsInMemory("AR","MODEL_QAC","QAC_POS",$targetPOSorWord))
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
			$concordanceMaxLevel = 3;
			
			if ( !empty($userPreferedContextLevel)) 
			{
				$concordanceMaxLevel = $userPreferedContextLevel;
				
			}
			
	
			
			for($i=$concordanceMaxLevel;$i>=1;$i--)
			{
				$contextArr["+$i"]=array();
				
			}
			for($i=1;$i<=$concordanceMaxLevel;$i++)
			{
				$contextArr["-$i"]=array();
			}	
			/////////////////////////////////////////////////
	
			//preprint_r($contextArr);exit;
			$targetWordCounter = 0;
			
			$phrasesBeforeArr = array();
			$phrasesAfterArr = array();
			$fullSentences = array();
			$concordanceIndex=0;
			

			
			
			foreach ($posTaggedSubsentences as $ssLoc => $ssArray)
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
					
					// FLATTEN POS ARRAY FROM TO GET BETTER CONTEXT - EX: GET PRON PRON AFTER V FOR "V PRON PRON" WORDS
					$posOrWordsArr = explode(" ", $wordsOrPoSStr);
						
					/*
					 * FLATTENING POS ARRAY FROM:
					* Array
					(
							[0] => REL
							[1] => V PRON
							[2] => P DET N
							[3] => CONJ V PRON
							[4] => DET N
							[5] => REM P REL
							[6] => V PRON PRON
							[7] => V PRON
					)
					* TO:
					*
					* Array
					(
							[0] => REL
							[1] => V
							[2] => PRON
							[3] => P
							[4] => DET
							[5] => N
							[6] => CONJ
							[7] => V
							[8] => PRON
							[9] => DET
							[10] => N
							[11] => REM
							[12] => P
							[13] => REL
							[14] => V
							[15] => PRON
							[16] => PRON
							[17] => V
							[18] => PRON
					)
					*/
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
						//reset context array
						$contextArr = array();
						
						//$qacWordIndex = $qacIndexesArr[$wordIndexInArray];
						//echoN(substr($ssLoc,0,strrpos($ssLoc, "-")).":".$qacWordIndex);
						$targetWordCounter++;
						
						if ( $wordIndexInArray>=1)
						{
							$level = 1;
							for($before=$wordIndexInArray-1;$before>=0;$before--)
							{
								
								if ( $level > $concordanceMaxLevel)
								{
									break;
								}
								
								$posOrWordInLevel = $posOrWordsArr[$before];
								$contextArr["-$level"][$posOrWordInLevel]++;
								
								$level++;
								
								
							}
						}
						
						$posOrWordArrLength = count($posOrWordsArr);
						
						if ( ($posOrWordArrLength-($wordIndexInArray+1))>=1)
						{
							$level = 1;
							for($after=$wordIndexInArray+1;$after<$posOrWordArrLength;$after++)
							{
								if ( $level > $concordanceMaxLevel)
								{
									break;
								}
								
								$posOrWordInLevel = $posOrWordsArr[$after];
								$contextArr["+$level"][$posOrWordInLevel]++;
								$level++;
							}
						}
					}
					
					
					$fullSentences[$concordanceIndex]="";
					$phraseBefore = getContextPhrases($contextArr,$concordanceMaxLevel,"BEFORE");
					$phraseAfter = getContextPhrases($contextArr,$concordanceMaxLevel,"AFTER");
					
					if ( !empty($phraseBefore))
					{
						$phrasesBeforeArr[$phraseBefore]++;
						
						$fullSentences[$concordanceIndex]=$phraseBefore;
					}
					
					$fullSentences[$concordanceIndex] .= " $targetPOSorWord ";
					
					if ( !empty($phraseAfter))
					{
						$phrasesAfterArr[$phraseAfter]++;
						
						$fullSentences[$concordanceIndex] .=$phraseAfter;
					}
					
					
					$concordanceIndex++;
					
					

				}
				
				
				//preprint_r($contextArr);
				
				
				//echoN($phraseBefore);
				//echoN($phraseAfter);
				
				
			
				
				//preprint_r($phrasesAfterArr);
				
				
		
				
			}
			
		
			arsort($phrasesBeforeArr);
			arsort($phrasesAfterArr);
			
			
			//preprint_r($fullSentences);
			
			//preprint_r($phrasesBeforeArr);
			//preprint_r($phrasesAfterArr);
			
			
		
	
		
		
		if ( count($fullSentences)==0)
		{
			showTechnicalError("No data found, make sure the word is found in the Quran");
			showTechnicalError("Use proper Simple or Uthmani word ");
			showTechnicalError("Use Word information tool to convert words or its validity ");
				
			exit;
		}
		
		
		$direction = "";
		$titleDirection  ="";
		 if ($targetType=="WORD")
		 {
		 	$direction= "style='direction:rtl'";
		 	$titleDirection = "style='direction:ltr'";
		 }
					
?>
					
					<table id='words-pos-context-table' <?=$direction?> >
						<thead>
						<tr>
							<th colspan='<?=($concordanceMaxLevel*2)+1?>'>
	
							</th>
						</tr>
						<tr>
							<th <?=$titleDirection?>>
								Context Before <?=$targetPOSorWord?>
							</th>
							<th colspan='1' <?=$titleDirection?> >
							Concordance for 
							<?=$targetPOSorWord?>
							</th>
							<th <?=$titleDirection?>>
									Context After <?=$targetPOSorWord?>
							</th>
					
						</tr>
						</thead>
						<tbody>
						 <tr>
						   <td>
						   		<ul class='concordance-list'>
									<?php 
									foreach ($phrasesBeforeArr as $phrase => $freq)
									{
										?>
										<li>[<?=$freq?>] <?=$phrase?></li>	
										<?php 
									}
									?>
								</ul>
						   </td>
						    <td>
						    	<table>
									<?php 
										$levelArr = $contextArr[$levelName];
										arsort($levelArr);
										
										foreach ($fullSentences as $concordanceIndex => $concSentence)
										{
											?>
											<tr>	
													<?php

														//$concSentence = markWordWithoutWordIndex($concSentence, $targetPOSorWord, "marked_for_concordance");
														//echoN($concSentence);
														
													$afterPart = mb_substr($concSentence,(mb_strpos($concSentence, $targetPOSorWord)+mb_strlen($targetPOSorWord)));
													$beforePart = mb_substr($concSentence,0,(mb_strpos($concSentence, $targetPOSorWord)));
													
													// to pre-clean for marking code
													$beforePart = trim($beforePart);
													$afterPart = trim($afterPart);
													
												
													$afterPart = markSpecificWordInText($afterPart, 0, ".*", "marked_for_concordance_right_left");
													$beforePart = markSpecificWordInText($beforePart, preg_match_all("/ /",$beforePart), ".*", "marked_for_concordance_right_left");
													
												
													?>
												<td <?php  if ($targetType=="POS") echo 'align="right"'; else echo 'align="left"';?> >
													<?=$beforePart?>
												</td>
												<td align="center">
													<span class='marked_for_concordance'><?=$targetPOSorWord ?></span>
												</td>
												<td <?php  if ($targetType=="POS") echo 'align="left"'; else echo 'align="right"';?> >
													<?=$afterPart?>
												</td>
											</tr>
											<?php 
										}
									?>
								</table>
							</td>
							<td>
								<ul class='concordance-list'>
									<?php 
									foreach ($phrasesAfterArr as $phrase => $freq)
									{
										?>
										<li>[<?=$freq?>] <?=$phrase?></li>	
										<?php 
									}
									?>
								</ul>
							
							</td>
						</tr>
						
								
					</tbody>
					</table>

<?php 
			
?>		
			
	<?php 
		require("../footer.php");
	?>
