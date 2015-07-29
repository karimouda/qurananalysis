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
		  		<div id='main-sections'>
					 <div id='section-item-search' class='section-item' >
		  				<a href='/'>Search</a>
		  			</div>
		  			<div id='section-item-analysis' class='section-item'>
		  				<a href='/analysis/'>Analysis</a>
		  			</div>
		  		</div>

		  
   		
   				
   		
   					
	</div>
	
	<table id="analysis-main-table">
	<tr>
		<td id='analysis-left-menu-cell'>
			<?php 
				require("./left-menu.php");
	
			?>
		</td>
		<td id='analysis-component-cell'>
		