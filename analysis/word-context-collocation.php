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
    <title>Quran Word Collocation | Quran Analysis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Collocation of words in the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/favicon.png">	 
	  
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>

		<?php 
				require("./analysis.template.start.code.php");
		
		?>	
				
  <div id='main-container'>
			  	
			  <!--  <h1 class='analysis-title-header'>Collocation</h1>-->	
			  
			  	    <?php include_once("help-content.php"); ?>
			  	    
			  	
			  	<div id='words-context-options'>
			  	<table>
			  	<tr>
			  		<td>
			  			Arabic Word or PoS Tag:
			  		</td>
			  		<td>
			  		<input type="text" id="word" autofocus="true" />
			  		</td>
			  	</tr>
			  	<tr>
			  		<td>
			  			
			  		</td>
			  		<td>
			  		<input type="button" id='words-context-submit' value='Context'  />
			  		</td>
			  	</tr>
			  	</table>
					
					
					
				</div>
				
				<div id='words-context-area'>
				
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

    	$("#word").keyup(function(e){ 
		    var keyCode = e.which; 
		    
		    if(keyCode==13)
		    {
		    	e.preventDefault();
		      	$("#words-context-submit").click();
		    } 
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


						  			trackEvent('ANALYSIS','collocation',word,'');
	
						 	 	
						     },
					      	 error: function (xhr, ajaxOptions, thrownError)
					         {
					      		$("#words-context-area").html("<center>Error occured !</center>");
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







