<?php 
require("global.settings.php");

$query = $_GET['q'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Semantic Search and Intelligence System (BETA) </title>
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
  
  <div id='header'>
			  	
     <?php 
		require("header.php");
	 ?>
  		
  </div>
  
  <div id='main-container'>
			  	


			  	<div id='options-area'>
			  	
			  	<?php 
			  		include_once("header.menu.php");
			  	?>
					
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
			  	 	<h1 id='main-page-main-message'>Explore the Quran like never before ...</h1>
			  	 	<div id='main-page-examples-area'>
			  	 		<span id='main-page-try'>EXAMPLES:</span> 

			  	 	
			  	 		<a href="?q=Allah" class='main-page-example-item'>Allah</a>,
			  	 		<a href="?q=Muhammad" class='main-page-example-item'>Muhammad</a>,
			  	 		<a href="?q=Islam" class='main-page-example-item'>Islam</a>,
			  	 		<a href="?q=Quran" class='main-page-example-item'>Quran</a>,
			  	 		<a href="?q=Jesus" class='main-page-example-item'>Jesus</a>,
			  	 		<a href="?q=Moses" class='main-page-example-item'>Moses</a>,
			  	 		<a href="?q=Angels" class='main-page-example-item'>Angels</a>,
			  	 		<a href="?q=Adam" class='main-page-example-item'>Adam</a>,
			  	 		<br>
			  	 		
			  	 		<a href="?q=Life" class='main-page-example-item'>Life</a>,
			  	 		<a href="?q=Death" class='main-page-example-item'>Death</a>,
			  	 		<a href="?q=Win" class='main-page-example-item'>Win</a>,
			  	 		<a href="?q=Loss" class='main-page-example-item'>Loss</a>,
			  	 		<a href="?q=Signs" class='main-page-example-item'>Signs</a>,
			  	 		<a href="?q=Heaven" class='main-page-example-item'>Heaven</a>,
			  	 		<a href="?q=Hell" class='main-page-example-item'>Hell</a>,
			  	 		<a href="?q=Love" class='main-page-example-item'>Love</a>,
			  	 		<a href="?q=Manners" class='main-page-example-item'>Manners</a>,
			  	 		<a href="?q=Children" class='main-page-example-item'>Children</a>,
			  	 		<a href="?q=Repentance" class='main-page-example-item'>Repentance</a>,
			  	 		<a href="?q=Money" class='main-page-example-item'>Money</a>,
			  	 		
	
			  	 		
			  	 		<a href="?q=Sky" class='main-page-example-item'>Sky</a>,
			  	 		<a href="?q=Stories" class='main-page-example-item'>Stories</a>,
			  	 		<a href="?q=Science" class='main-page-example-item'>Science</a>,
			  	 		<a href="?q=Thought" class='main-page-example-item'>Thought</a>,
			  	 		<a href="?q=Creation" class='main-page-example-item'>Creation</a>,
			  	 		<a href="?q=Animals" class='main-page-example-item'>Animals</a>,		  	 		

			  	 		
			  	 		<a href="?q=Woman" class='main-page-example-item'>Woman</a>,
			  	 		<a href="?q=Marriage" class='main-page-example-item'>Marriage</a>,
			  	 		<a href="?q=Jihad" class='main-page-example-item'>Jihad</a>,
			  	 		<a href="?q=War" class='main-page-example-item'>War</a>,
			  	 		<a href="?q=Kill" class='main-page-example-item'>Kill</a>,
			  	 		<a href="?q=Freedom" class='main-page-example-item'>Freedom</a>,
			  	 		<a href="?q=Martyrs" class='main-page-example-item'>Martyrs</a>
			  	 		
		  	 		
			  	 		
			  	 	</div>
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

				$("#content-area").html("");

				destroyGraph();
				
				
				
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







