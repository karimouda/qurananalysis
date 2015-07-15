<?php 
require_once("../global.settings.php");

$lang = "AR";



if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core,search,qac",$lang);



$word = $_GET['word'];


		$wordInfoArr = getWordInfo($word,$MODEL_CORE,$MODEL_SEARCH,$MODEL_QAC);
		
		if ( empty($wordInfoArr))
		{
			showTechnicalError("Word not found");
			exit;
		}
		
		//preprint_r($wordInfoArr);
		
		/*
		echoN("Buckwalter Transliteration:".$buckwalterTransliteration);
		echoN("Translation:"."");
		echoN("English Translation:"."");
		
		echoN("Word Root:".$wordRoot);
		echoN("PoS Tags:".join(",", array_keys($posTagsArr)));
		echoN("Lemmas:".join(",", array_keys($lemmasArr)));
		*/
		
		//preprint_r($versesArr);
		
		
					
?>
					
					<table id='words-info-table'>
					<thead>
					<tr>
						<td colspan='6'>

						</td>
					</tr>
					</thead>
					<tr>	
						<th>
							Simple
						</th>
						<td>
							<?=$wordInfoArr['WORD_SIMPLE']?>
						</td>
						<th>
							Uthmani
						</th>
						<td>
							<?=$wordInfoArr['WORD_UTHMANI']?>
						</td>
					</tr>
		
					<tr>	
						<th>
							Frequency
						</th>
						<td>
							<?=$wordInfoArr['TF']?>
						</td>
						<th>
							TF-IDF Weight
						</th>
						<td>
							<?=round($wordInfoArr['TFIDF'],2)?>
						</td>
					</tr>

					<tr>	
						<th>
							Buckwalter Transliteration
						</th>
						<td>
							<?=$wordInfoArr['BUCKWALTER']?>
						</td>
						<th>
							Translation
						</th>
						<td>
							<?=""?>
						</td>
					</tr>
					<tr>	
						<th>
							English Translation
						</th>
						<td>
							<?=""?>
						</td>
						<th>
							Word Root
						</th>
						<td>
							<?=$wordInfoArr['ROOT']?>
						</td>
					</tr>	
					<tr>	
						<th>
							PoS Tags (<a target="_new" href="http://corpus.quran.com/documentation/tagset.jsp">QAC</a>)
						</th>
						<td>
							<?=join(",", array_keys($wordInfoArr['POS']))?>
						</td>
						<th>
							Lemma
						</th>
						<td>
							<?=join(" ", array_keys($wordInfoArr['LEM']))?>
						</td>
					</tr>	
					
					
					<tr>
						<th >
						Features
						</th>
						<td colspan='6'>
							<?=echoN(join(",",array_keys($wordInfoArr['FEATURES'])));?>
						</td>
	
						
					</tr>	
					<tr>
						
						<th colspan='5'>
							Verses (<?=count($wordInfoArr['VERSES'])?>)
						</th>
						<th>
						Tag
						</th>
						<th>
						Loc
						</th>
						
						
					</tr>		
					<?php 
					foreach ($wordInfoArr['VERSES'] as $location => $verseText):
					
					
					?>
					<tr>
						
						<td colspan='5'>
							<?php
							
							//$verseText = markSpecificWordInText($verseText,$wordId,$wordSimple,"");
							
							$markingTagName = "marked_fg";
							$verseText = preg_replace("/(".$wordInfoArr['WORD_SIMPLE'].")/mui", "<$markingTagName>\\1</$markingTagName>", $verseText);
							
							echo $verseText;
							
							?>
						</td>
						<td style="background-color: #cacaff">
						<?=$wordInfoArr['VERSES_POS_TAGS'][$location]?>
						</td>
						<td><?=$location?></td>
					</tr>
					<?php 
					endforeach;
					?>							
			</table>

			
			

