<?php 
require("../global.settings.php");


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analysis | Credits</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quran Semantic-based Search, Analysis & Expert System">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/qe/favicon.png">
      	 
	<script type="text/javascript">
	</script>


  </head>
  <body>

  <div id='main-container'>
			  	<div id='header'>
			  	
			  		<?php 
						require("../header.php");
					?>
			  		
			  	</div>


			  	<div id='options-area'>
			  		<div id='main-sections'>
			 			<div id='section-item-search' class='section-item' >
			  				<a href='/'>Search</a>
			  			</div>
			  			<div id='section-item-analysis' class='section-item'>
			  				<a href='/analysis/'>Analysis</a>
			  			</div>
			  		</div>
			   </div>
			   
			   	<div id="main-credits-area">
			   			This project would not have been possible if the following projects/research did not exist
			   			
			   			<ul>
				   			<li>Tanzil Project</li>
				   			<li>Quranic Arabic Corpus</li>
				   			<li>Text Mining The Quran</li>
			   			</ul>
			   			
			   			<hr>
			   			
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







