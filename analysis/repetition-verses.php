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
    <title>Quran Analytics | Repeated Verses </title>
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

			  		
			

	
			  
			  	
			  	<div id='repetition-area'>
					<?php 
					
					$repeatedVerses = array();
					
					$repeatedVerses = getRepeatedVerses();
					
					
					
					
					$repeatedVersesCount = count($repeatedVerses);
						
					
					?>
					
					<table id='repeated-results-table'>
					<thead>
					<tr>
						<td colspan='2'>
							
							<b><?=addCommasToNumber($repeatedVersesCount) ?></b> Verses
							
							
						</td>
					</tr>
					</thead>
					<tr>
					<th>
						Words
					</th>
					<th>
						Frequency
					</th>
					</tr>
					
					<?php
				
	
					
					foreach($repeatedVerses as $key=>$val)
					{
						
					?>
					<tr>	
						<td>
							<?=$key?>
						</td>
						<td>
							<?=$val?>
						</td>
					</tr>
					<?php 
					}
					?>
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







