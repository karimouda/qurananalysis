<?php 
require_once("../global.settings.php");



	



$lang = "AR";

if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}






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
			require("./analysis.template.start.code.php");
	
		?>
			  		
  <div id='main-container'>
			  	
		<div id="analysis-main-message" >

			<div style='color:#841'>
			<h1 id='analyze-title'>Are you a Scholar ?</h1>
			  		<p>
			  			If you are a Scholar or normal user who is interested in going deep into the Quran 
			  			to find patterns, relations and new discoveries, you are in the right place ...
			  			
			  			
			  		</p>
			  		<p>
			  			This section was created to be the swiss-knife of Quran Research, it is meant to
			  			facilitate all kinds of Analysis work around Quran for both Arabic and English languages
			  		</p>
			  		
			  		 <p style="color:#0357AA">
				  			Click on one of the links on the left to start
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
			  			<li><b>PoS Patterns</b>: Get verses from the Quran which matches a specific PoS Pattern</li>
			  			<li><b>PoS Query</b>: List verses containing any specific PoS Tag from the Quran using Quranic Arabic Corpus</li>
			  			<li><b>Repeated Verses</b>: Listing of "full repeated verses" in the Quran  </li>
			  			<li><b>Repeated Phrases</b>: Listing of all repeated “phrases” ( sub-verses or substring of verses ) from the Quran using LCS algorithm </li>
			  			
			  			<li><b>Ontology Data</b>: Listing of all concepts and relations found in QA ontology </li>
			  			<li><b>Ontology Graph</b>: Visualization of the Full QA Ontology in addition to the subset ontology of any selected chapter </li>
			  			<li><b>Uthmani to Simple</b>: One-to-one mapping between words in uthmani and simple scripts</li>
			  			<li><b>Word Infromation</b>: Provides information about any Arabic word in the Quran, provided information includes root, transliteration, frequency and PoS tags </li>
			  			
			  			<li><b>Collocation</b>: Show the "collocation" context of any word or PoS tag in the Quran </li>
			  			<li><b>Concordance</b>: Show the "concordance" context of any word or PoS tag in the Quran </li>
			  			<li><b>Pause Marks</b>: Show all the verses for any selected Pause Mark</li>
			  			<li><b>Buckwalter-Arabic Translitertation</b>: Two-way conversion between Arabic and Buckwalter transliteration </li>
			  			<li><b>Word similarity</b>: Lists the top 20 similar words for any selected word from the Quran</li>
			  			<li><b>Quran Initials</b>: Analytics and Visualization to help deciphering the meaning of the disjoined letter in the Quran </li>
			  		</ul>
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







