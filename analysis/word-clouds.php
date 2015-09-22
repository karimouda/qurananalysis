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
    <title>Quran Analysis | Word Clouds </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Word clouds for the Quran Chapters">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<script src="<?=$JQUERY_TAGCLOUD_PATH?>" type="text/javascript" ></script> 
	 
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
  	    $RESOURCES = getModelEntryFromMemory($lang, "MODEL_CORE", "RESOURCES", "");
  	    
  	    $META_DATA = getModelEntryFromMemory($lang, "MODEL_CORE", "META_DATA", "");
  	    ?>
			  	
			<div id='words-clouds-options'>
			  	<table>
				  	<tr>
						<td >
							Choose Chapter
						</td>
						<td align="left">
						
							<select id='wordcloud-type-selection'>
							 <option value="" selected="true">&nbsp;</option>
							 <option value="VB" ><?php echo $RESOURCES['VERSE_BEGENNINGS']?></option>
							 <option value="VE" ><?php echo $RESOURCES['VERSE_ENDINGS']?></option>
						    <option value="" >--------</option>
								<?php 
								
								$i=0;
								/* SURA'S LOOP **/
								for ($s=0;$s<$numberOfSuras;$s++)
								{
									
									$cloudId = "qc-s-$s";
									$suraName = $META_DATA['SURAS'][$s]['name_'.strtolower($lang)];
									
								
										
								?>
										
										 <option  value="<?=$s?>"><b><?=$suraName?> </b></option>
							
							  
							  <?php 
									}
							  ?>
							</select>
						</td>
					</tr>

				</table>
				

					
				</div>
				<div id='words-clouds-area'>
				
				</div>
			

				
		
   </div>
   
		<?php 
				require("./analysis.template.end.code.php");
		
		?>	
	<script type="text/javascript">

				
		$(document).ready(function()
		{


		
		});
		
		$("#wordcloud-type-selection").change(function()
		{
	        var word = $("#wordcloud-type-selection option:selected").val();

		
			if ( word=="" )
			{
				return;
			}



			$("#loading-layer").show();

			showCloud(word);

				  
		});

		
		function showCloud(cloudToShow)
		{
		

	
				$("#words-clouds-area").html("");
				
				$.ajaxSetup({
					url:  "/analysis/word-clouds.ajax.service.php?lang=<?=$lang?>&cloudToShow="+encodeURIComponent(cloudToShow),
					global: false,
					type: "GET"
					
				  });


				$.ajax({
					
					timeout: 60000,
					success: function(retRes)
							{

						  			$("#loading-layer").hide();
						      	 	
						  			$("#words-clouds-area").html(retRes);


						  			trackEvent('ANALYSIS','word-clouds',cloudToShow,'');
	
						 	 	
						     },
					      	 error: function (xhr, ajaxOptions, thrownError)
					         {
					      		$("#words-clouds-area").html("<center>Error occured !</center>");
					      		$("#loading-layer").hide();
					         }
						});
							
				
				
				
				
				
		}



		     
		</script>
		

	<?php 
		require("../footer.php");
	?>
	

  </body>
</html>







