<?php 
require("global.settings.php");

$query = $_GET['q'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Semantic-based Search, Analysis and Expert System </title>
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
						require("header.php");
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
					
					<table>

						<tr>
							<td>
			  					<input id="search-field" type="text" value="" ></input>
	   						</td>
	   						<td>
	   							<input  type="submit" id="doSearch"  onclick='doSearch()' value="Search"/>
	   						</td>
	   					</tr>
	   					<tr>
							<td>
			  					
	   						</td>
	   						<td>
	   						
	   						</td>
	   					</tr>
	   				</table>
	   				
			  	</div>	
			  	<div id="loading-layer">
			  		Loading ...
			  	</div>
			  	<div id='content-area'>
			  	 	
			  	</div>
   </div>
   

	<script type="text/javascript">

				
		$(document).ready(function()
		{

			<?php if ( !empty($query) ):?>
				$("#search-field").val(("<?=$query?>"));
				doSearch();

			<?php endif;?>

		
		});
	
		function doSearch()
		{
		

		
				var query = $("#search-field").val();
		
				

				$("#loading-layer").show();
				
				
				$.ajaxSetup({
					url:  "/search/index.php?q="+encodeURIComponent(query),
					global: false,
					type: "GET"
					
				  });


				$.ajax({
					
					timeout: 300000,
					success: function(prepareRes)
							{

						  			$("#loading-layer").hide();
						      	 	
						  			$("#content-area").html(prepareRes);

						  	
	
						 	 	
						     },
					      	 error: function (xhr, ajaxOptions, thrownError)
					         {
					      		$("#content-area").html("<center>Error occured !</center>");
					      		$("#loading-layer").hide();
					         }
						});
							
				
				
				
				
				
		}


    	$("#search-field").keyup(function(e){ 
		    var keyCode = e.which; 
		    
		    if(keyCode==13)
		    {
		    	e.preventDefault();
		      	$("#doSearch").click();
		    } 
		});



	</script>






	<?php 
		require("footer.php");
	?>
	
  </body>
</html>







