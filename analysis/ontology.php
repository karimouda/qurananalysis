<?php 
require_once("../global.settings.php");

$lang = "AR";



if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core,qac",$lang);

$UTHMANI_TO_SIMPLE_WORD_MAP_AND_VS = apc_fetch("UTHMANI_TO_SIMPLE_WORD_MAP");


$UTHMANI_TO_SIMPLE_LOCATION_MAP = apc_fetch("UTHMANI_TO_SIMPLE_LOCATION_MAP");


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analytics | Ontology </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Semantic Ontology for the Quran">
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
			  	

			  
			  	<h1 id='ontology-title'>Quran Ontology</h1>
			  	<div id='ontology-main-area'>
			  	
						<table id="ontology-container-table">
							<tr>
								<td >

  				   						 
  				   						 <?php  $finalConcepts = unserialize(file_get_contents("../data/ontology/temp.final.concepts")); ?>
  				   						 
  				   						 <table id='ontology-concepts-table'>
  				   						 <tr>
  				   						 <th>
  				   						Concepts (<?=count($finalConcepts)?>)
  				   						 </th>
  				   						 </tr>
  				   						
  				   						 	
  				   						 <?php
  				   						
  				   						 
  				   						 
	  				   						  foreach($finalConcepts as $concept=> $conceptArr)
	  				   						  {
  				   						 		
  				   						 
  				   						 	?>
  				   						 	   <tr>
  				   								 <td style="background-color: #dddddd;">
  				   						 			
  				   						 					<?php echo  $concept?> 
  				   						 			
  				   						 		 </td>
  				   						 		</tr>
  				   						 					
  				   						 	<?php
  				   						 						
  				   						 	   }
  				   						 	?>
  				   						 					
  				   						 </table>
  				   						 
  				   				
								</td>
								<td>
								
									
									
										<?php 
										 $relationArr = unserialize(file_get_contents("../data/ontology/temp.final.relations"));
										 
										 ?>
										 <table id='ontology-concepts-table'>
  				   						 <tr>
  				   						 <th colspan='2'>
  				   						Relations (<?=count($relationArr)?>)
  				   						 </th>
  				   						 </tr>
  				   						
  				   						 	
  				   						 <?php
  				   						
  				   						 
  				   						 
  				   						 
	  				   						  foreach($relationArr as $index=> $relationsArr)
	  				   						  {
  				   						 		
  				   						 		$subject = $relationsArr['SUBJECT'];
  				   						 		$verb = $relationsArr['VERB'];
  				   						 		$object = $relationsArr['OBJECT'];
  				   						 		
  				   						 		$type = $relationsArr['TYPE'];
  				   						 		$posPattern = $relationsArr['POS_PATTERN'];
  				   						 		
  				   						 	?>
  				   						 	   <tr>
  				   								 <td style="background-color: #dddddd;">
  				   						 			
  				   						 				<?php echo  "$subject -> $verb -> $object"?> 
  				   						 			
  				   						 		 </td>
  				   						 		 <td style='color:#eee'>
  				   						 		 		<?php echo  $type?> 
  				   						 		 </td>
  				   						 		 <!--  <td style='color:#eee'>
  				   						 		 		<?php echo  "";//$posPattern?> 
  				   						 		 </td>
  				   						 		 --> 
  				   						 		</tr>
  				   						 					
  				   						 	<?php
  				   						 						
  				   						 	   }
  				   						 	?>
  				   						 					
  				   						 </table>
								</td>
							</tr>
						</table>
				</div>
	
		  		
			
   </div>
   

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







