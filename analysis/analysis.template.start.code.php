 <?php 

 
 $langParameter = "";
 if ( isset($_GET['lang']))
 {
 	$langParameter = "?lang=".$_GET['lang'];
 }

 ?>
   <div id='header'>
			  	
     <?php 
		require("../header.php");
	 ?>
  		
  </div>
 
  	<div id='options-area' class='oa-analysis'>
			  	<?php 
			  		include_once("../header.menu.php");
			  	?>

		  
   		
   				
   		
   					
	</div>
	
	<table id="analysis-main-table">
	<tr>
		<td id='analysis-left-menu-cell'>
			<?php 
				require("./left-menu.php");
	
			?>
		</td>
		<td id='analysis-component-cell'>
		