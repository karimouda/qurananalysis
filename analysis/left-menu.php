<?php 

 
 $langParameter = "";
 if ( isset($_GET['lang']))
 {
 	$langParameter = "?lang=".$_GET['lang'];
 }

?>

 	
<div id='left-menu-area' >
			    <?php 
		  			if ( strpos($_SERVER["PHP_SELF"],"index.php")===false):
		  		?>
   				<select id='language-selection' onchange="switchToSelectedLang()">
   					<option value='EN' <?php if ($lang=="EN") echo 'selected'?> >EN</option>
   					<option value='AR' <?php if ($lang=="AR") echo 'selected'?>>AR</option>
   				</select>
   				<?php 
   				endif;
   				?>
   				<a href="./index.php<?=$langParameter?>"  class='analysis-links' 
   				 <?php if ( isCurrentPage("index.php")) echo "selected='1'"?>  ><u>Home</u></a>
   				<a href="./basic-statistics.php<?=$langParameter?>"  class='analysis-links'
   				 <?php if ( isCurrentPage("basic-statistics.php")) echo "selected='1'"?> >
   				 Basic Statistics</a>
   				<a href="./word-frequency.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("word-frequency.php")) echo "selected='1'"?> >
   				 Word Frequency</a>
   				<a href="./word-clouds.php<?=$langParameter?>" class='analysis-links'
   				 <?php if ( isCurrentPage("word-clouds.php")) echo "selected='1'"?> >
   				 Word Clouds</a>
   				<a href="./full-quran-text.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("full-quran-text.php")) echo "selected='1'"?> 
   				 >Full Text</a>
   				<a href="./charts.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("charts.php")) echo "selected='1'"?> 
   				>Charts</a>	
   				<a href="./ngrams.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("/ngrams.php")) echo "selected='1'"?> 
   				>N-Grams</a>
   				<a href="./pos-ngrams.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("pos-ngrams.php")) echo "selected='1'"?> 
   				>PoS Patterns <br></a>
   				<a href="./part-of-speech.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("part-of-speech.php")) echo "selected='1'"?> 
   				 >PoS Query</a>
   				<!-- <a href="./chronology.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("chronology.php")) echo "selected='1'"?> 
   				 >Chronology</a> -->
   				<a href="./repetition-verses.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("repetition-verses.php")) echo "selected='1'"?> 
   				 >Repeated Verses</a>		
   				<a href="./repetition-common-substrings.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("repetition-common-substrings.php")) echo "selected='1'"?> 
   				 >Repeated Phrases <br><span class='analysis-sub-title'>&nbsp;&nbsp;&nbsp;&nbsp;(Common Substrings)</span></a>	
   				<a href="./ontology.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("ontology.php")) echo "selected='1'"?> 
   				 >Ontology Data</a>		
   				<a href="./ontology-graph.php<?=$langParameter?>" class='analysis-links'  
   				<?php if ( isCurrentPage("ontology-graph.php")) echo "selected='1'"?> 
   				>Ontology Graphs</a>
   				<a href="./uthmani-to-simple.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("uthmani-to-simple.php")) echo "selected='1'"?> 
   				 >Uthmani-to-Simple</a>
   				<a href="./words-information.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("bwords-information.php")) echo "selected='1'"?> 
   				 >Word Information</a>			
   				<a href="./word-context-collocation.php<?=$langParameter?>" class='analysis-links' 
   				 <?php if ( isCurrentPage("word-context-collocation.php")) echo "selected='1'"?> 
   				 >Collocation</a>
   				<a href="./word-context-concordance.php<?=$langParameter?>" class='analysis-links'
   				 <?php if ( isCurrentPage("word-context-concordance.php")) echo "selected='1'"?> 
   				 >Concordance</a>
   				
     		    <a href="./pause-marks.php<?=$langParameter?>" class='analysis-links'
   				 <?php if ( isCurrentPage("pause-marks.php")) echo "selected='1'"?> 
   				 >Pause Marks</a> 		
   			    <a href="./buckwalter-transliteration.php<?=$langParameter?>" class='analysis-links'
   				 <?php if ( isCurrentPage("buckwalter-transliteration.php")) echo "selected='1'"?> 
   				 >Arabic to Buckwalter &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Transliteration </a>
   				
   				 <a href="./simmilar-words.php<?=$langParameter?>" class='analysis-links'
   				 <?php if ( isCurrentPage("simmilar-words.php")) echo "selected='1'"?> 
   				 >Word Similarity</a> 	
   				 
    				 <a href="./quran-initials.php<?=$langParameter?>" class='analysis-links'
   				 <?php if ( isCurrentPage("quran-initials.php")) echo "selected='1'"?> 
   				 >Quran Initials</a> 	  				 
   				 
   				
   				
   				
   		
   					
</div>