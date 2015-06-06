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
    <title>Quran Analytics | Words Clouds </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Full Analytics System for the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<script src="/libs/js/jquery.tagcloud.js" type="text/javascript" ></script> 
	 
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
					?>
				
			  	<div >
			  	
			
				<!--  
		  		 <fieldset class="word-cloud-fs" >
		  		 
  				    <legend>Full Quran Cloud - TFIDF</legend>
			  		
						
			  			<div id='fqc-cloud' class='cloud-div'>
							<?php 
						
								if ( false)
								{
									$i=0;
									foreach ($MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'] as $wordLabel => $wordFreqArr )
									{
										
										$freq = $wordFreqArr["TFIDF"];
										$i++;
										
										
									?><a class='wordfreq-item'   href="javascript:;"  rel="<?=($freq)?>" title="<?=$freq?> "><?=$wordLabel?></a><?php 
									}
								}
			  				?>
		 
			  		</div>
			  		
			  </fieldset>
			  -->
			  
			  <fieldset class="word-cloud-fs">
		  		 
  				    <legend><?php echo $MODEL_CORE['RESOURCES']['VERSE_BEGENNINGS']?></legend>
			  		
						
			  			<div id='verse-beginning-cloud' class='cloud-div'>
							<?php 
								
							shuffle_assoc($MODEL_CORE['WORDS_FREQUENCY']['VERSE_BEGINNINGS']);
							
								$i=0;
								foreach ($MODEL_CORE['WORDS_FREQUENCY']['VERSE_BEGINNINGS'] as $wordLabel => $wordFreq )
								{
									
									$freq = $wordFreq;
									$i++;
									
									
								?><a class='wordfreq-item'   href="javascript:;"  rel="<?=($freq)?>" title="<?=$freq?> "><?=$wordLabel?></a><?php 
								}
			  				?>
		 
			  		</div>
			  		
			  </fieldset>
			  
			  
			 <fieldset class="word-cloud-fs">
		  		 
  				    <legend><legend><?php echo $MODEL_CORE['RESOURCES']['VERSE_ENDINGS']?></legend></legend>
			  		
						
			  			<div id='verse-endings-cloud' class='cloud-div'>
							<?php 
						
								shuffle_assoc($MODEL_CORE['WORDS_FREQUENCY']['VERSE_ENDINGS']);
							
								$i=0;
								foreach ($MODEL_CORE['WORDS_FREQUENCY']['VERSE_ENDINGS'] as $wordLabel => $wordFreq )
								{
									
									$freq = $wordFreq;
									$i++;
									
									
								?><a class='wordfreq-item'   href="javascript:;"  rel="<?=($freq)?>" title="<?=$freq?> "><?=$wordLabel?></a><?php 
								}
			  				?>
		 
			  		</div>
			  		
			  </fieldset>
			  
			  
			  <br>
			  <hr>
			  <br>
			  
			  
			  <?php 
			  
				  $i=0;
				  /* SURA'S LOOP **/
				  for ($s=0;$s<$numberOfSuras;$s++)
				  {	
				  	
				  	$cloudId = "qc-s-$s";
				  	$suraName = $MODEL_CORE['META_DATA']['SURAS'][$s]['name_'.strtolower($lang)];
			  ?>
			  
			 	 <fieldset class="word-cloud-fs" style="min-height:auto">
		  		 
  				    <legend><?=$suraName?></legend>
			  		
						
			  			<div id='<?=$cloudId?>' class='cloud-div'>
							<?php 
						
							
								$i=0;
								foreach ($MODEL_CORE['WORDS_FREQUENCY']['WORDS_PER_SURA'][$s] as $wordLabel => $wordFreq )
								{
									
									$freq = $wordFreq;
									$i++;
									
									
								?><a class='wordfreq-item-for-sura'   href="javascript:;"  rel="<?=($freq)?>" title="<?=$freq?> "><?=$wordLabel?></a><?php 
								}
			  				?>
		 
			  		</div>
			  		
			  </fieldset>
			  
			  <script>
			  	     $("#<?=$cloudId?> a").tagcloud({ 
					     size: { 
					       start: 14, 
					       end: 62, 
					       unit: 'px',
					       
					     },
					     color: {start: '#000', end: '#C0DE22'}
					  }); 
			</script>
			  
			  <?php 
				  }
			  ?>	
			  	</div>	
   </div>
   

	<script type="text/javascript">

				
		$(document).ready(function()
		{


		
		});
		

		     $("#fqc-cloud a").tagcloud({ 
			     size: { 
			       start: 6, 
			       end: 82, 
			       unit: 'px',
			       
			     },
			     color: {start: '#000', end: '#C0DE22'}
			  }); 

		     $("#verse-beginning-cloud a").tagcloud({ 
			     size: { 
			       start: 6, 
			       end: 82, 
			       unit: 'px',
			       
			     },
			     color: {start: '#000', end: '#C0DE22'}
			  }); 


		     $("#verse-endings-cloud a").tagcloud({ 
			     size: { 
			       start: 6, 
			       end: 82, 
			       unit: 'px',
			       
			     },
			     color: {start: '#000', end: '#C0DE22'}
			  }); 


		     
		</script>
		

	<?php 
		require("../footer.php");
	?>
	

  </body>
</html>







