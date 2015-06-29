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
    <title>Quran Analytics | N-GRAMES </title>
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
			  	

			  
			  	
			  	<div id='ngrames-options'>
					
					<div id='ngrames-radio'>
						<input id="normal-ngrams-type" type='radio' name='ngrams-radio' value='1' checked="true" />
						<label for="normal-ngrams-type">N-Grams</label>
						<input id="pos-ngrams-type"type='radio' name='ngrams-radio' value='2' />
						<label for="pos-ngrams-type">PoS Tagged N-Grams</label>
					</div>
						
							
					<span id='normal-ngrams-type-span'>
					<input type="text" id="grams-number" value="2" size='1' maxlength="2" autofocus="true"/>-Grams
					</span>
					
				
					<span id='pos-ngrams-type-span'>
					
					<span class='note'>Supported Tags: "*" and all <a target='_new' href='http://corpus.quran.com/documentation/tagset.jsp'>QAC Tags</a></span>
					<br>
					PoS Pattern <input type="text" id="pos-pattern" placeholder="PN V" />
					
					
					</span>
					
					<input type="button" id='ngrams-submit' value='Find' />
				</div>
				<div id='ngrames-area'>
				
				</div>
					<div id="loading-layer">
			  		Loading ...
					</div>
		  		
			
   </div>
 


	<script type="text/javascript">


				
		$(document).ready(function()
		{
			
			

			$("#pos-ngrams-type-span").hide();

			

		
		});

		$("input:radio[name=ngrams-radio]").change(function()
				{
					var selectedRadio = $(this).val();

					$("#ngrames-options SPAN").show();
					
					if ( selectedRadio == 1)
					{
							$("#pos-ngrams-type-span").hide();
							
					}
					else
					{
						$("#normal-ngrams-type-span").hide();
					}
				});

		$("#ngrams-submit").click(function()
		{
	       

	    	var  selectedRadio = $("input:radio[name=ngrams-radio]:checked").val();

	    	var parameter = "";
	    	
	    	if ( selectedRadio == 1)
			{
	    		parameter = $("#grams-number").val();
	    		 

					
			}
			else
			{
				parameter = $("#pos-pattern").val();
			}
			
    		if ( parameter=="" )
			{
				return;
			}



			$("#loading-layer").show();

			doGetNGrams(selectedRadio,parameter);

				  
		});

		
		function doGetNGrams(selectedRadio,parameter)
		{
		

			$("#ngrames-area").html("");

				
				$.ajaxSetup({
					url:  "/analysis/ngram.ajax.service.php?lang=<?=$lang?>&parameter="+parameter+"&ngramsType="+selectedRadio,
					global: false,
					type: "GET"
					
				  });


				$.ajax({
					
					timeout: 60000,
					success: function(retRes)
							{

						  			$("#loading-layer").hide();
						      	 	
						  			$("#ngrames-area").html(retRes);

						  	
	
						 	 	
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







