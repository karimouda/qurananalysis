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
	<link rel="icon" type="image/png" href="/favicon.png"> 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>
  
		<?php 
				require("./analysis.template.start.code.php");
		
		?>	
		
  <div id='main-container'>
			  	
		
			  	<?php include_once("help-content.php")?>
			  	
			
				
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
						
							$STOP_WORDS = getModelEntryFromMemory($lang, "MODEL_CORE", "STOP_WORDS", "");
								
							$META_DATA = getModelEntryFromMemory($lang, "MODEL_CORE", "META_DATA", "");
							
							$STOP_WORDS_STRICT_L2 = getModelEntryFromMemory($lang, "MODEL_CORE", "STOP_WORDS_STRICT_L2", "");
							
							$TOTALS = getModelEntryFromMemory($lang, "MODEL_CORE", "TOTALS", "");
							
						
							$WORDS_FREQUENCY = getModelEntryFromMemory($lang, "MODEL_CORE", "WORDS_FREQUENCY", "");
								
							
								$i=0;
								foreach ($WORDS_FREQUENCY['WORDS_TFIDF'] as $wordLabel => $wordFreqArr )
								{
									
									if ( $withStopWordsExcluded ==1 )
									{
										if ( $lang=="AR")
										{
											if ( isset($STOP_WORDS_STRICT_L2[$wordLabel]) ) continue;
										}
										else
										{
											if ( isset($STOP_WORDS[$wordLabel]) ) continue;
										}
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

			trackEvent('ANALYSIS','word-frequency','reload-stopwords-button',withStopWords);
			
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







