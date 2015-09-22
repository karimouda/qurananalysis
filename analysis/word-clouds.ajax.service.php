<?php 
require_once("../global.settings.php");

$lang = "AR";


if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core",$lang);

$cloudToShow = $_GET['cloudToShow'];

if ( $cloudToShow!="0" && empty($cloudToShow) )
{
	showTechnicalError("Invalid Cloud Type [$cloudToShow]");
	exit;
}


$RESOURCES = getModelEntryFromMemory($lang, "MODEL_CORE", "RESOURCES", "");
	
$META_DATA = getModelEntryFromMemory($lang, "MODEL_CORE", "META_DATA", "");

$WORDS_FREQUENCY = getModelEntryFromMemory($lang, "MODEL_CORE", "WORDS_FREQUENCY", "");

?>
			
					

			  <?php if ( $cloudToShow=="VB"):?>
			  
			  <fieldset class="word-cloud-fs">
		  		 
  				    <legend><?php echo $RESOURCES['VERSE_BEGENNINGS']?></legend>
			  		
						
			  			<div id='verse-beginning-cloud' class='cloud-div'>
							<?php 
								
							shuffle_assoc($WORDS_FREQUENCY['VERSE_BEGINNINGS']);
							
								$i=0;
								foreach ($WORDS_FREQUENCY['VERSE_BEGINNINGS'] as $wordLabel => $wordFreq )
								{
									
									$freq = $wordFreq;
									$i++;
									
									
								?><a class='wordfreq-item'   href="javascript:;"  rel="<?=($freq)?>" title="<?=$freq?> "><?=$wordLabel?></a><?php 
								}
			  				?>
		 
			  		</div>
			  		
			  </fieldset>
			  
			    <?php endif;?>
			  
			    <?php if ( $cloudToShow=="VE"):?>
			  
			 <fieldset class="word-cloud-fs">
		  		 
  				    <legend><legend><?php echo $RESOURCES['VERSE_ENDINGS']?></legend></legend>
			  		
						
			  			<div id='verse-endings-cloud' class='cloud-div'>
							<?php 
						
								shuffle_assoc($WORDS_FREQUENCY['VERSE_ENDINGS']);
							
								$i=0;
								foreach ($WORDS_FREQUENCY['VERSE_ENDINGS'] as $wordLabel => $wordFreq )
								{
									
									$freq = $wordFreq;
									$i++;
									
									
								?><a class='wordfreq-item'   href="javascript:;"  rel="<?=($freq)?>" title="<?=$freq?> "><?=$wordLabel?></a><?php 
								}
			  				?>
		 
			  		</div>
			  		
			  </fieldset>
			  
			  
			  <?php endif;?>
			  
			  <?php 
			
			  if ( is_numeric($cloudToShow) && $cloudToShow >=0 && $cloudToShow<=113):?>
			  
			  
				  <?php 
				  
					  $i=0;
					
					  	
					  	$cloudId = "qc-s-$cloudToShow";
					  	$suraName = $META_DATA['SURAS'][$cloudToShow]['name_'.strtolower($lang)];
				  ?>
				  
				 	 <fieldset class="word-cloud-fs" style="min-height:auto">
			  		 
	  				    <legend><?=$suraName?></legend>
				  		
							
				  			<div id='<?=$cloudId?>' class='cloud-div'>
								<?php 
							
									$suraWordFreqArr = $WORDS_FREQUENCY['WORDS_PER_SURA'][$cloudToShow];
									shuffle_assoc($suraWordFreqArr);
								
									$i=0;
									foreach ($suraWordFreqArr as $wordLabel => $wordFreq )
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
						       end: 82, 
						       unit: 'px',
						       
						     },
						     color: {start: '#000', end: '#C0DE22'}
						  }); 
				</script>
				  
		
			 
			  <?php endif;?>


	<script type="text/javascript">

				
		$(document).ready(function()
		{


		
		});
		

	     <?php if ( $cloudToShow=="VB"):?>

		     <?php endif;?>

		     <?php if ( $cloudToShow=="VB"):?>
		     $("#verse-beginning-cloud a").tagcloud({ 
			     size: { 
			       start: 12, 
			       end: 100, 
			       unit: 'px',
			       
			     },
			     color: {start: '#000', end: '#C0DE22'}
			  }); 
			<?php endif;?>
			
		    <?php if ( $cloudToShow=="VE"):?>
		     $("#verse-endings-cloud a").tagcloud({ 
			     size: { 
			       start: 12, 
			       end: 100, 
			       unit: 'px',
			       
			     },
			     color: {start: '#000', end: '#C0DE22'}
			  }); 
		     <?php endif;?>
		     

		     
		</script>
		




