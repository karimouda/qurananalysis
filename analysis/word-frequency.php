<?php 
require_once("../global.settings.php");



	



$withStopWordsExcluded = $_GET['wstwe'];


$lang = "AR";

if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core",$lang);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analysis | Word Frequency </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Word frequencies of the quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>
  
		<?php 
				require("./analysis.template.start.code.php");
		
		?>	
		
  <div id='main-container'>
			  	
		
			  		
			

			
				
			  	<div >
			  	<?php 
			  		$buttonString = "↺ Exclude Stopwords";
			  		
			  		if ( $withStopWordsExcluded==1)
			  		{
			  			$buttonString = "↺ Show All Words ";
			  		}
			  	?>
	
	
		  		 <fieldset id="word-frequency-fs">
		  		 
  				    <legend>Word Frequencies and Weights</legend>
  				    
			  			<div id='stop-words-reload-button-area'>
								<input type="button" value="<?=$buttonString?>" onclick="reloadWithStopWords('<?=$withStopWordsExcluded?>')">
						</div>
					<div class='note' style='margin-left:20px;'>
					<br>
			  			* Document = "Chapter" for this TFIDF calculations
			  		</div>
						<table class='analysis-table' >
			  			<thead>
			  				<tr>
				  				<th>
				  					Rank
				  				</th>
				  				<th>
				  					Word
				  				</th>
				  				<th>
				  					Term Frequency
				  				</th>
				  				<th>
				  					TF Percentage
				  				</th>
				  				<th>
				  					Document Frequency*
				  				</th>
				  				<th>
				  					Inverse Document Frequency
				  				</th>
				  				<th>
				  					TFIDF Weight
				  				</th>
				  			</tr>
			  			</thead>
			  			<tbody>
							<?php 
						
								//preprint_r($MODEL_CORE['STOP_WORDS_STRICT_L2']);
							
								$i=0;
								foreach ($MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'] as $wordLabel => $wordFreqArr )
								{
									
									if ( $withStopWordsExcluded ==1 )
									{
										//if ( isset($MODEL_CORE['STOP_WORDS'][$wordLabel]) ) continue;
										if ( isset($MODEL_CORE['STOP_WORDS_STRICT_L2'][$wordLabel]) ) continue;
									}
									
									$i++;
									
									
							?>
			  					<tr>
			  						<td><?=$i?></td>
				  					<td><?=$wordLabel?></td>
				  					<td><?=$wordFreqArr['TF']?></td>
				  					<td><?=round($wordFreqArr['TPC'],4)?>%</td>
				  					<td><?=$wordFreqArr['DF']?></td>
				  					<td><?=round($wordFreqArr['IDF'],2)?></td>
				  					<td><?=round($wordFreqArr['TFIDF'],2)?></td>
				  				</tr>			  				
			  				<?php 
								}
			  				?>
		  	
			  			</tbody>
			  			
			  		</table>
			  		
			  		
			  </fieldset>
			  	
			  	</div>	
   </div>
   
		<?php 
				require("./analysis.template.end.code.php");
		
		?>	
		
	<script type="text/javascript">

				
		$(document).ready(function()
		{


		
		});

		function reloadWithStopWords(withStopWords)
		{
			//alert(withStopWords);
			if ( withStopWords==1)
			{
				if ( location.href.indexOf("?wstwe=1") >= 0 )
				{
					location.href = location.href.replace("?wstwe=1","");

				}
				else
				{
					location.href = location.href.replace("&wstwe=1","");
				}
			}
			else
			{
				if ( location.href.indexOf("?") >= 0 )
				{
					location.href= location.href+"&wstwe=1";
				}
				else
				{
					location.href= location.href+"?wstwe=1";
				}
				
			}
		}
		
	</script>
		

	<?php 
		require("../footer.php");
	?>
	

  </body>
</html>







