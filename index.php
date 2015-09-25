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
    <meta name="description" content="Quran Analysis is a Smart Search and Question Answering System for the Quran">


	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<script type="text/javascript" src="<?=$TINYSORT_PATH?>"></script>
	<script type="text/javascript" src="<?=$TINYSORT_JQ_PATH?>"></script>	
	<script type="text/javascript" src="<?=$JQUERY_TAGCLOUD_PATH?>" ></script> 


	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/favicon.png">
      	 
	<script type="text/javascript">
	</script>


     <?php 
		require("in-head.php");
	 ?>


  </head>
  <body>
  

			  	
     <?php 
		require("header.php");
	 ?>
  		
  
  <div id='mainpage-maincontainer'>
			  	


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
							<td colspan='2'>
			  						
	   						
	   						</td>
	   					</tr>
	   				</table>
	   				
			  	 
			  	</div>	
			  	<div id="loading-layer">
			  		Loading ...
			  	</div>
			  	<div id='content-area'>
			  	 	<div id='main-page-examples-area'>
			  	 	<h1 id='main-page-main-message'>Search and Explore the Quran like never before ...</h1>
			  	 	
			  	 	<div id='main-page-try'>
			  	 	 <b>Click</b>
			  	 	 to try the following examples
			  	 	 
			  	 	 </div> 
			  	 	
			  	 	<table id='main-page-examples-table'>
			  	 		<tr>
			  	 			<td>
			  	 				One Word
			  	 					
			  	 			</td>
			  	 			<td style="position:relative">
			  	 				<a href="?q=Muhammad" class='main-page-example-item'>Muhammad</a>
			  	 				/
			  	 				<a href="?q=محمد" class='main-page-example-item'>محمد</a>
			  	 				 <br>
			  	 				<img src='/images/hand-click-icon.png' id='main-page-click-icon'/> 
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
			  	 				 <span class='note'>Should be enclosed in quotes ""</span>
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
			  	 				Phonetic Search
			  	 			</td>
			  	 			<td>
			  	 			    <a href="?q=allahu" class='main-page-example-item'>Allahu</a>
			  	 				/
			  	 				<a href="?q=taAAmaloona" class='main-page-example-item'>Taaamaloona</a>
			  	 				<br>
			  	 			 	<span class='note'>Search using transliterated Arabic words. <a target="_new" href='/info/transliteration-words-list.php'>Full List</a></span>
		
			  	 			</td>
			  	 		</tr>
			  	 		<tr>
			  	 			<td>
			  	 				Specific Chapter/Verse <br>
			  	 			  
			  	 			</td>
			  	 			<td>
			  	 			    <a href="?q=50" class='main-page-example-item'>50</a>
			  	 				/
			  	 				<a href="?q=50:12" class='main-page-example-item'>50:12</a>
			  	 				<br>
			  	 			 	 <span class='note'>(<a target="_NEW" href='/analysis/full-quran-text.php'>Chapter</a>) (<a target="_NEW" href='/analysis/full-quran-text.php'>Chapter</a>:Verse)</span>
			  	 			 	 <br>
			  	 			 	
		
			  	 			</td>
			  	 		</tr>
			  	 	</table>
			  
			  		
			  	 		
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

			var intervalID = setInterval(function(){ $("#main-page-click-icon").toggle(); },"100");

			setTimeout(function()
			{ 
				clearInterval(intervalID);
				$("#main-page-click-icon").toggle();
				
			 },500);
			 
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
		
				if (query=='' || query.trim().length ==0 )
				{
					 $("#search-field").focus();
					 return;
				}

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







