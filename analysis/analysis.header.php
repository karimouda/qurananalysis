 <?php 

 
 $langParameter = "";
 if ( isset($_GET['lang']))
 {
 	$langParameter = "?lang=".$_GET['lang'];
 }

 ?>
   <div id='header'>
			  	
     <?php 
		require("../header.php");
	 ?>
  		
  </div>
 
  	<div id='options-area' class='oa-analysis'>
		  		<div id='main-sections'>
					 <div id='section-item-search' class='section-item' >
		  				<a href='/'>Search</a>
		  			</div>
		  			<div id='section-item-analysis' class='section-item'>
		  				<a href='/analysis/'>Analysis</a>
		  			</div>
		  		</div>

		  		<?php 
		  			if ( strpos($_SERVER["PHP_SELF"],"index.php")===false):
		  		?>
		  		<div id='analysis-switch-lang-button' onclick="switchToLang('<?=toggleLanguage($lang)?>')">
		  				<small>Switch to</small>
		  				 <br>
		  				<span><?=toggleLanguage($lang)?></span>
   				</div>
   				<?php 
   				endif;
   				?>
   				
   				<a href="./basic-statistics.php<?=$langParameter?>" class='analysis-links'>Basic Statistics</a>
   				<a href="./word-frequency.php<?=$langParameter?>" class='analysis-links'>Word Frequency</a>
   				<a href="./word-clouds.php<?=$langParameter?>" class='analysis-links'>Word Clouds</a>
   				<a href="./full-quran-text.php<?=$langParameter?>" class='analysis-links'>Full Text</a>
   				<a href="./charts.php<?=$langParameter?>" class='analysis-links'>Charts</a>	
   				<br>
   				<a href="./graph.php<?=$langParameter?>" class='analysis-links'>Graphs</a>
   				<a href="./ngrams.php<?=$langParameter?>" class='analysis-links'>N-Grams</a>
   				<a href="./part-of-speech.php<?=$langParameter?>" class='analysis-links'>PoS</a>
   			
   				<a href="./chronology.php<?=$langParameter?>" class='analysis-links'>Chronology</a>
   				
   				<a href="./repetition.php<?=$langParameter?>" class='analysis-links'>Repeated Verses</a>		
   				<a href="./ontology.php<?=$langParameter?>" class='analysis-links'>Ontology</a>		
   				<a href="./uthmani-to-simple.php<?=$langParameter?>" class='analysis-links'>Uthmani-to-Simple</a>
   				<a href="./words-information.php<?=$langParameter?>" class='analysis-links'>Word Information</a>			
   				<a href="./pause-marks.php<?=$langParameter?>" class='analysis-links'>Pause Marks</a>			
   				
   				
   				
   				
   		
   					
		  	</div>