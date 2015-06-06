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
    <title>Quran Analytics </title>
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

  <div id='main-container'>
			  	
			  		<?php 
						require_once("../header.php");
					?>
			  		
			

					<?php 
						require_once("./analysis.header.php");
						
						//preprint_r($MODEL_CORE);
					?>
				
			  	<div >
			  	<?php 
			  		$buttonString = "Reload with Stopwords Excluded";
			  		
			  		if ( $withStopWordsExcluded==1)
			  		{
			  			$buttonString = "Reload with NO Exclusion";
			  		}
			  	?>
				<div id='stop-words-reload-button-area'>
					<input type="button" value="<?=$buttonString?>" onclick="reloadWithStopWords('<?=$withStopWordsExcluded?>')">
				</div>
	
		  		 <fieldset id="word-frequency-fs">
		  		 
  				    <legend>Word Statistics</legend>
			  		
						<table class='analysis-table' >
			  			<thead>
			  				<tr>
				  				<th>
				  					Index
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
				  					TFIDF
				  				</th>
				  			</tr>
			  			</thead>
			  			<tbody>
							<?php 
						
								//preprint_r($MODEL_CORE['STOP_WORDS']);
							
								$i=0;
								foreach ($MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'] as $wordLabel => $wordFreqArr )
								{
									
									if ( $withStopWordsExcluded ==1 )
									{
										if ( isset($MODEL_CORE['STOP_WORDS'][$wordLabel]) ) continue;
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
			  		<div>
			  			* Documents = "Verses" for this TFIDF calculations
			  		</div>
			  		
			  </fieldset>
			  	
			  	</div>	
   </div>
   

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







