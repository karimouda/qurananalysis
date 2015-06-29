<?php 
require_once("../global.settings.php");

$lang = "AR";



//preprint_r($MODEL_QAC);

if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core,qac",$lang);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analytics | Part Of Speech </title>
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
	<?php 
		require_once("./analysis.header.php");
	?>
				
  <div id='main-container'>
			  	
		
			

		
			  
			  	
			  	<div id='pos-options-area'>
					<?php 
					
					//preprint_r($MODEL['QAC_MASTERTABLE']);
					//preprint_r($MODEL['QAC_POS']);
					//preprint_r($MODEL['QAC_FEATURES']);
					//preprint_r($MODEL_QURANA['QURANA_CONCEPTS']);
					//preprint_r($MODEL['QURANA_PRONOUNS']);
					?>
				
				Select PoS Tag: 
				<select id='pos-selection'>
					 <option value="" selected="true">&nbsp;</option>
						<?php 
							foreach ($MODEL_QAC['QAC_POS'] as $posTypeName => $posArr )
							{
								
								
						?>
								
								 <option  value="<?=$posTypeName?>"><b><?=$posTypeName?> </b></option>
					
					  
					  <?php 
							}
					  ?>
					</select>
					<div id="loading-layer">
			  		Loading ...
					</div>
					
				</div>
	<br>
		  		
     				<div id='pos-data-area'>
							
							<span class='note'>based on QAC ( Quranic Arabic Corpus ), more about Tags here:http://corpus.quran.com/documentation/tagset.jsp</span>
					</div>
					
					
			
   </div>

			  	

	<script type="text/javascript">


				
		$(document).ready(function()
		{


		
		});


		$("#pos-selection").change(function()
		{
	        var selectedPOS = $("option:selected", this).val();


			if ( selectedPOS=="" )
			{
				return;
			}

			$("#loading-layer").show();

			doGetPOSData(selectedPOS);

				  
		});

		
		function doGetPOSData(selectedPOS)
		{
		

	

				
				$.ajaxSetup({
					url:  "/analysis/pos.ajax.service.php?lang=<?=$lang?>&pos="+encodeURIComponent(selectedPOS),
					global: false,
					type: "GET"
					
				  });


				$.ajax({
					
					timeout: 60000,
					success: function(retRes)
							{

						  			$("#loading-layer").hide();
						      	 	
						  			$("#pos-data-area").html(retRes);

						  	
	
						 	 	
						     },
					      	 error: function (xhr, ajaxOptions, thrownError)
					         {
					      		$("#content-area").html("<center>Error occured !</center>");
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







