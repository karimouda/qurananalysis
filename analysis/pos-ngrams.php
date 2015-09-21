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
    <title>Quran Analytics | PoS Patterns (N-GRAMES) </title>
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
				require("./analysis.template.start.code.php");
		
		?>			
  <div id='main-container'>
			  	
	    <?php include_once("help-content.php"); ?>
			  
			  	
			  	<div id='ngrames-options'>
					
		
		
					
				
					<span id='pos-ngrams-type-span'>
					
					
					PoS Pattern <input type="text" id="pos-pattern" placeholder="PN V" />
					
					
					</span>
					<input type="button" id='ngrams-submit' value='Find' />
					<br>
					<span class='note'>Supported Tags: "*" and all <a target='_new' href='http://corpus.quran.com/documentation/tagset.jsp'>QAC Tags</a></span>
					<br>
					
					
				</div>
				<div id='ngrames-area'>
				
				</div>
					<div id="loading-layer">
			  		Loading ...
					</div>
		  		
			
   </div>
 
		<?php 
				require("./analysis.template.end.code.php");
		
		?>	

	<script type="text/javascript">


				
		$(document).ready(function()
		{
			
			

			

			

		
		});



		$("#ngrams-submit").click(function()
		{
	       


	    	

				parameter = $("#pos-pattern").val();
			
			
    		if ( parameter=="" )
			{
				return;
			}



			$("#loading-layer").show();

			doGetNGrams(parameter);

				  
		});

		
		function doGetNGrams(parameter)
		{
		

			$("#ngrames-area").html("");

				
				$.ajaxSetup({
					url:  "/analysis/pos-ngrams.ajax.service.php?lang=<?=$lang?>&parameter="+parameter,
					global: false,
					type: "GET"
					
				  });


				$.ajax({
					
					timeout: 60000,
					success: function(retRes)
							{

						  			$("#loading-layer").hide();
						      	 	
						  			$("#ngrames-area").html(retRes);

						  	
						  			trackEvent('ANALYSIS','pos-patterns',parameter,'');
						 	 	
						     },
					      	 error: function (xhr, ajaxOptions, thrownError)
					         {
					      		$("#ngrames-area").html("<center>Error occured !</center>");
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







