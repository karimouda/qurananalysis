<?php 
#   PLEASE DO NOT REMOVE OR CHANGE THIS COPYRIGHT BLOCK
#   ====================================================================
#
#    Quran Analysis (www.qurananalysis.com). Full Semantic Search and Intelligence System for the Quran.
#    Copyright (C) 2015  Karim Ouda
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#    You can use Quran Analysis code, framework or corpora in your website
#	 or application (commercial/non-commercial) provided that you link
#    back to www.qurananalysis.com and sufficient credits are given.
#
#  ====================================================================
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
    <title>Quran Word Concordance | Quran Analysis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Concordance of words in the Quran">
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
			  	
 				 <!-- <h1 class='analysis-title-header'>Concordance</h1>-->
			  		    <?php include_once("help-content.php"); ?>
			  		    
			  	
			  	<div id='words-context-options'>
			  	<table>
				  	<tr>
						<td >
							Arabic Word or PoS Tag
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


						  			trackEvent('ANALYSIS','concordance',word,level);
	
						 	 	
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







