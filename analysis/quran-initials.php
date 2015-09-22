<?php 
require_once("../global.settings.php");

$lang = "AR";



if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}



loadModels("core,qac",$lang);



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analytics | Initials Analysis in the Quran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Initials Analysis in the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<script type="text/javascript" src="<?=$JQUERY_TAGCLOUD_PATH?>" ></script> 
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>
		<?php 
				require("./analysis.template.start.code.php");
		
		?>	
				
  <div id='main-container'>
  
  
  	    <?php include_once("help-content.php"); ?>
	

		<?php 
		$QURAN_TEXT = getModelEntryFromMemory("AR_UTH", "MODEL_CORE", "QURAN_TEXT", "");
		
		if ( $lang=="EN")
		{
			showTechnicalError("Only Arabic is supported here, you chose English !");
		
		}
		$initialsVersesWordsArr = array();
		
		
		    $qacPosEntryArr = getModelEntryFromMemory("AR","MODEL_QAC","QAC_POS","INL");
		
		
			foreach($qacPosEntryArr as $location => $segmentId)
			{
			
				$qacMasterTableEntry = getModelEntryFromMemory("AR","MODEL_QAC","QAC_MASTERTABLE",$location);
				
				
				// get Word, Lema and root
				$segmentWord = $qacMasterTableEntry[$segmentId-1]['FORM_AR'];
				$segmentWordLema = $qacMasterTableEntry[$segmentId-1]['FEATURES']['LEM'];
				$segmentWordRoot = $qacMasterTableEntry[$segmentId-1]['FEATURES']['ROOT'];
				$verseLocation = substr($location,0,strlen($location)-2);
				//$segmentWord = removeTashkeel($segmentWord);
			
			
				// get word index in verse
				$wordIndex = (getWordIndexFromQACLocation($location));
			
			
				//$segmentFormARimla2y = $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS[$segmentWord];
			
				// get simple version of the word index
				$imla2yWordIndex = getImla2yWordIndexByUthmaniLocation($location,$UTHMANI_TO_SIMPLE_LOCATION_MAP);
			
			
				// get verse text
				$verseText = getVerseByQACLocation($QURAN_TEXT,$location);
				
				//echoN($verseText);
				//echoN($segmentWord);
				
				$initialsSegmentsCountArr[$segmentWord]++;
				$initialsSegmentsArr[$location]=$segmentWord;
				$initialsVersesArr[$location] = $verseText;
				
				$verseText = removePauseMarkFromVerse($verseText);
				$wordsArr = explode(" ",$verseText);
			
				$initialsVersesWordsArr = array_merge($wordsArr,$initialsVersesWordsArr);
			}
			
			$initialsVersesWordsArr = array_count_values($initialsVersesWordsArr);
			

			//echoN(count($initialsVersesArr));
			//preprint_r($initialsVersesArr);
			
			$uniqueChapters = array();
			
			foreach($initialsSegmentsArr as $location => $segmentWord)
			{
				$locationArr = explode(":", $location);
				$sura = $locationArr[0];
				$aya = $locationArr[1];
				
				$uniqueChapters[$sura]=1;
				
				
				$suraVerseArr[] = array($sura,$aya,$segmentWord);
			}
				
			$suraVerseDistributionChartJSON  = json_encode($suraVerseArr);
				
		
		?>
		<table>
		<tr>
			<td>

				<table class='analysis-table' >
		  			<thead>
		  				<tr>
			  				<th>
			  					Initial
			  				</th>
			  				<th>
			  					Count
			  				</th>
			  			</tr>
		  			</thead>
		  			<tbody>
		  			<?php 
		  				arsort($initialsSegmentsCountArr);
		  				foreach($initialsSegmentsCountArr as $segment => $count):
		  				
		  			?>
		  				<tr>
			  				<td>
			  					<?=$segment?>
			  				</td>
			  				<td>
			  					<?=$count?>
			  				</td>
			  			</tr>
		  			<?php endforeach;?>
		  			</tbody>
		  		</table>
  			
			</td>
			<td>
			
				<div id="chart-initials-distribution" ></div>
			
			</td>
		</tr>
		</table>
		
		<div>
			<table class='analysis-table' style='width: 100%;direction:rtl;' >
			<thead>
				<tr>
	  				<th>
	  					Significant Words
	  				</th>
	  			</tr>
			 </thead>
						<tr>
				  				<td style="text-align: right;">
				  				<div id='initials-cloud-area'> 
			  			<?php 
				  		
				  				foreach($initialsVersesWordsArr as $wordLabel => $freq):
				  				
				  				if ( isset($initialsSegmentsCountArr[$wordLabel]) ) continue;
				  				
				  		?>
				  			
	  					<a class='initials-wordcloud-item'  
							 href=javascript:;"  rel="<?=($freq)?>" title="<?=$freq?> "><?=$wordLabel?></a>
				
				  			
				  			
				  		<?php endforeach;?>
				  		</div>
				  			</td>
				  	</tr>
				  	</table>
				  	
				    <table class='analysis-table' style='width: 100%;' >
				  	<thead>
		  				<tr>
			  				<th>
			  					Verses (<?=count($initialsVersesArr)?>) - Chapters (<?=count($uniqueChapters)?>) 
			  				</th>
			  			</tr>
					</thead>
						<?php 
				  		
				  				foreach($initialsVersesArr as $location => $verseText):
				  				
				  				$segment = $initialsSegmentsArr[$location];
				  		?>
				  			<tr>
				  				<td style="text-align: right;">
				  					<?php
				  					
				  						$verseText = markWordWithoutWordIndex($verseText, $segment, "marked");
				  						$verseText = markSpecificWordInText($verseText, 1, ".*", "marked_for_initials");
				  						
				  						echoN($verseText);
				  						?>
				  				</td>
				  			</tr>
				  			
				  		<?php endforeach;?>
		  	
			</table>
		</div>
			  	
			  	
   </div>
   
		<?php 
				require("./analysis.template.end.code.php");
		
		?>	
	<script type="text/javascript">

	
	$(document).ready(function()
	{

		drawChart(<?=$suraVerseDistributionChartJSON?>,700,400,1,<?=$numberOfSuras?>,'#chart-initials-distribution',"Chapter Number","Verse Number",function(d){return "Chapter Number:" + d[0]+ "<br/>Verse Number: "+d[1]+"<br/>Inital:"+ d[2]} );

		drawSearchWordCloud("initials-cloud-area");
	
	});




			  				
		
	</script>
		

	<?php 
		require("../footer.php");
	?>
	


  </body>
</html>







