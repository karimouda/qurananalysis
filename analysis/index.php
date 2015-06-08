<?php 
require_once("../global.settings.php");



	



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
    <title>Quran Analysis | Scholars Section </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Research and Analysis section in QA">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>
		<?php 
			require("./analysis.header.php");
			//preprint_r($MODEL_CORE['TOTALS']);
			//preprint_r($MODEL_CORE);exit;
		?>
			  		
  <div id='main-container'>
			  	
			
			

			
					
				
			  	<div id="analysis-main-message" >
			  	
		
				
			   		<div style='color:#841'>
				  		<p>
				  			Are you a Scholar or normal user who is interested in going deep into the Quran 
				  			to find patterns, relations and new discoveries, if so, you are in the right place ...
				  			
				  			
				  		</p>
				  		<p>
				  			This section was created to be the swiss-knife of Quran Research, it is meant to
				  			facilitate all kinds of Analysis work around Quran for both Arabic and English languages
				  		</p>
				  		
				  		 <p style="color:#0357AA">
				  			Click on one of the links above to start
				  		</p>
			  		</div>
			  		
			  		<ul>
			  			<li><b>Basic Statistics</b>: Statistics about the total number of Chapters, Verses, Words, Characters and more </li>
			  			<li><b>Word Frequencies</b>: List of all words in the Quran with their frequencies and weights calculated using the TFIDF algorithm  </li>
						<li><b>Word clouds</b>: Word clouds for each Chapter in the Quran in addition to 2 other clouds for verse endings and beginnings ( that is clouds for first and last word in each verse ), the bigger the word size the more it is mentioned   </li>
			  			<li><b>Full Text</b>: Listing of all verses in the Quran  </li>
			  			<li><b>Charts</b>: A collection of charts such as "Chapter/Verse distribution" </li>
			  			<li><b>Graphs</b>: Convert a verse or a Chapter to a Graph, each node is a word which can be connected to other words preceding or succeeding it (after excluding stopwords)  </li>
			  			<li><b>N-Grames</b>: Choose "N" to produce an N-grame of words from the Quran</li>
			  			<li><b>PoS</b>: PoS tagging of the Quran using Quranic Arabic Corpus, you can list verses containing any specific PoS Tag</li>
			  			<li><b>Chronology</b>: Verse analysis taking into consideration the Chronological Order of the Quran  </li>
			  			<li><b>Repeated Verses</b>: Listing of full repeated verses or common substring using LCS algorithms  </li>
			  			
			  		</ul>
			  		
			  
			  	
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







