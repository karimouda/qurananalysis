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
    <title>Quran Analysis | Word Context ( Collocation ) </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Information for each word in the Quran">
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
			  	
			  	<h1 class='analysis-title-header'>Collocation</h1>
			  
			  	
			  	<div id='words-context-options'>
					Word or PoS Tag:
					<input type="text" id="word" autofocus="true" />
					<input type="button" id='words-context-submit' value='Context'  />
				</div>
				
				<div id='words-context-area'>
				
				</div>
					<div id="loading-layer">
			  		Loading ...
					</div>
		  		
			
   </div>
 


	<script type="text/javascript">


				
		$(document).ready(function()
		{


		
		});


		$("#words-context-submit").click(function()
		{
	        var word = $("#word").val();


			if ( word=="" )
			{
				return;
			}



			$("#loading-layer").show();

			getWordContext(word);

				  
		});

		
		function getWordContext(word)
		{
		

	
				$("#words-context-area").html("");
				
				$.ajaxSetup({
					url:  "/analysis/wordcontext-collocation.ajax.service.php?lang=<?=$lang?>&word="+encodeURIComponent(word),
					global: false,
					type: "GET"
					
				  });


				$.ajax({
					
					timeout: 60000,
					success: function(retRes)
							{

						  			$("#loading-layer").hide();
						      	 	
						  			$("#words-context-area").html(retRes);

						  	
	
						 	 	
						     },
					      	 error: function (xhr, ajaxOptions, thrownError)
					         {
					      		$("#words-context-area-area").html("<center>Error occured !</center>");
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







