<?php 
require("global.settings.php");

$query = $_GET['q'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Smart Semantic Search and Question Answering System - QA (BETA)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quran Semantic-based Search, Analysis & Expert System">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<script type="text/javascript" src="<?=$TINYSORT_PATH?>"></script>
	<script type="text/javascript" src="<?=$TINYSORT_JQ_PATH?>"></script>	
	<script type="text/javascript" src="<?=$JQUERY_TAGCLOUD_PATH?>" ></script> 
	

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
			  	 	<h1 id='main-page-main-message'>Search and Explore the Quran like never before ...</h1>
			  	 	<div id='main-page-examples-area'>
			  	 	
			  	 	
			  	 	<div id='main-page-try'><b>Click</b> and try the following examples</div> 
			  	 	
			  	 	<table id='main-page-examples-table'>
			  	 		<tr>
			  	 			<td>
			  	 				One Word
			  	 			</td>
			  	 			<td>
			  	 				<a href="?q=Muhammad" class='main-page-example-item'>Muhammad</a>
			  	 				/
			  	 				<a href="?q=محمد" class='main-page-example-item'>محمد</a>
			  	 			</td>
			  	 		</tr>
			  	 		<tr>
			  	 			<td>
			  	 				Multiple Words
			  	 			</td>
			  	 			<td>
			  	 				<a href="?q=Heaven Hellfire" class='main-page-example-item'>Heaven Hellfire</a>
			  	 				/
			  	 				<a href="?q=الجنة و النار" class='main-page-example-item'>الجنة و النار</a>
			  	 				<br>
			  	 				<span class='note'>Verses containing Heaven OR Hellfire</span>
			  	 			</td>
			  	 		</tr>
			  	 		<tr>
			  	 			<td>
			  	 				Phrases
			  	 			</td>
			  	 			<td>
			  	 				<a href="?q=%22Those who believe%22" class='main-page-example-item'>"Those who believe"</a>
			  	 				/
			  	 				<a href="?q=<?php echo urlencode('"الذين آمنوا"')?>" class='main-page-example-item'>"الذين آمنوا"</a>
			  	 				<br>
			  	 				 <span class='note'>Should be enclosed by quotes ""</span>
			  	 			</td>
			  	 		</tr>
			  	 		<tr>
			  	 			<td>
			  	 				Questions
			  	 			</td>
			  	 			<td>
			  	 				<a href="?q=Animals in the Quran?" class='main-page-example-item'>Animals in the Quran ?</a>
			  	 				/
			  	 				<a href="?q=الحيوانات فى القرآن ؟" class='main-page-example-item'>الحيوانات فى القرآن ؟</a>
			  	 				<br>
			  	 				<a href="?q=Who is Muhammad" class='main-page-example-item'>Who is Muhammad</a>
			  	 				/
			  	 				<a href="?q=من هو محمد" class='main-page-example-item'>من هو محمد</a>
			  	 				<br>
			  	 				 <span class='note'>Who, What and "?" are supported in Arabic and English</span>
			  	 			</td>
			  	 		</tr>
			  	 		<tr>
			  	 			<td>
			  	 				Specific Verse <br>
			  	 			   <span class='note'>(Chapter : Verse)</span>
			  	 			</td>
			  	 			<td>
			  	 				<a href="?q=50:12" class='main-page-example-item'>50:12</a>
			  	 			 	 				
		
			  	 			</td>
			  	 		</tr>
			  	 	</table>
			  	 	<?php /*
			  	 		

			  	 	
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
			  	 		
		  	 		*/
			  		?>
			  	 		
			  	 	</div>
			  	</div>
   </div>
   

	<script type="text/javascript">


		$(document).ready(function()
		{

			<?php if ( !empty($query) ):?>
				$("#search-field").val(("<?= addslashes($query)?>"));
				doSearch();
				
			<?php else:?>

			$("#options-area").css("margin-top","100px");

			<?php endif;?>

			

		
		});

		function clientSortResults(listElementsClass)
		{
		
				var selectedField = $("#qa-sort-select option:selected").val();

				var currentOrder = $("#qa-sort-select option:selected").attr("sortorder");

				
	
				$('.'+listElementsClass).tsort({attr:selectedField, order: currentOrder});


				
			
		}	

		function changeDefaultQuranScript()
		{
			var query = $("#search-field").val();
			var script = $("#qa-script-select option:selected").val();
			showResultsForQueryInSpecificDiv(query,"result-verses-area",script);
		}
	
		function doSearch()
		{
		

		
				var query = $("#search-field").val();
		
				

				$("#loading-layer").show();

				$("#content-area").html("");

				destroyGraph();
				

				$("#options-area").css("margin-top","40px");
				
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





	<div id='truth-area' >
	Caution: in addition to the beta-experimental nature of this website,<br> it is a human endeavour which can't be perfect and should NOT be considered truth or fact source 
	</div>
	<?php 
		require("footer.php");
	?>
	
  </body>
</html>







