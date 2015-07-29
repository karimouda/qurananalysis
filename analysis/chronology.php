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
    <title>Quran Analytics | Chronology </title>
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
			  	

			

	
			  
			  	
			  	<div id='fqt-search-area'>
					SOON
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







