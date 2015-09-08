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
    <title>Quran Analysis | Words Context ( Concordance ) </title>
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
				require("./analysis.template.start.code.php");
		
		?>			
  <div id='main-container'>
			  	
 				 <!-- <h1 class='analysis-title-header'>Concordance</h1>-->
			  	
			  	
			  	<div id='words-context-options'>
			  	<table>
				  	<tr>
						<td >
							Word or PoS Tag
						</td>
						<td align="left">
						
								<input type="text" id="word" autofocus="true" />
						</td>
					</tr>
				  	<tr>
						<td >
							Context Level
						</td>
						<td align="left">
							<input type="text" id="level" style="width:20px" maxlength="1" placeholder="3" />
						</td>
					</tr>
				  	<tr>
						<td >
							
						</td>
						<td align="left">
							<input type="button" id='words-context-submit' value='Concordance'  />
						</td>
					</tr>
				</table>
				

					
				</div>
				
				<div id='words-context-area'>
				<?php 
				
				/*
				$str = "Leeds is a university";
				$strArr = preg_split("/ /", $str);
				$strWordsCount = count($strArr);
				
				$ssLength  = $strWordsCount;
				
				while($ssLength>1)
				{
					$groupLoopsNeeded= (($strWordsCount-$ssLength)+1);
					
					$ssStr="";
					$v=0;
					while($v<$groupLoopsNeeded)
					{
						$ssStr= join(" ",array_slice($strArr, $v,$ssLength));
						echoN($ssStr);
						$v++;
					}
					
					$ssLength--;
				}
				exit;
				*/
				
				?>
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


		$("#words-context-submit").click(function()
		{
	        var word = $("#word").val();


			if ( word=="" )
			{
				return;
			}

			var level = $("#level").val();



			$("#loading-layer").show();

			getWordContext(word,level);

				  
		});

		
		function getWordContext(word,level)
		{
		

	
				$("#words-context-area").html("");
				
				$.ajaxSetup({
					url:  "/analysis/wordcontext-concordance.ajax.service.php?lang=<?=$lang?>&word="+encodeURIComponent(word)+"&level="+level,
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
					      		$("#words-context-area").html("<center>Error occured !</center>");
					      		$("#loading-layer").hide();
					         }
						});
							
				
				
				
				
				
		}






	</script>
		


	


  </body>
</html>







