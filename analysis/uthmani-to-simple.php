<?php 
require_once("../global.settings.php");

$lang = "AR";



if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core",$lang);


$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = loadUthmaniToSimpleMappingTable();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analytics | Uthmani to Simple Mapping </title>
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
			  	

			  
			  	
			  	<div id='uts-mapping-area'>
			  	
			  		<div id='uts-mapping-statistics'>
			  			
			  		</div>
			  		<table id='uts-mapping-table'>
							<tr>
							<th>
								Uthmani
							</th>
							<th>
								Simple
							</th>
							</tr>
							
							<?php 
							
								$uthmaniCounter = 0;
								$simpleCounter = 0;
								foreach( $UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS as $mapTermKey => $mapTermVal)
								{
									if ( isSimpleQuranWord($mapTermKey))
									{
										$simpleCounter++;
										//echoN("##".$mapTermKey);
										continue;
									}
									
									$uthmaniCounter++;
								?>
								<tr>
									<td><?=$mapTermKey?></td>
									
									<td><?=$mapTermVal?></td>
									
									<!--  <td><?/*myLevensteinEditDistance(removeTashkeel($mapTermKey), $mapTermVal)*/?></td>-->
								</tr>
								<?php 
								}
								
								
							?>
							<thead>
							<tr>
								<td colspan="2">
								<?php 
									$uthmaniCounter = addCommasToNumber($uthmaniCounter);
									$simpleCounter = addCommasToNumber($simpleCounter);
								
									echoN("$uthmaniCounter Uthmani Words - $simpleCounter Simple words");
									
								?>
								</td>
							</tr>
							</thead>
					</table>
				</div>
	
		  		
			
   </div>
   
		<?php 
				require("./analysis.template.end.code.php");
		
		?>	
	<script type="text/javascript">


				
		$(document).ready(function()
		{


		
		});


		
	</script>
		

	<?php 
		require("../footer.php");
	?>
	


  </body>
</html>







