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
    <title>Quran Pause Marks | Quran Analysis </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Listing of verses containing Pause Marks in the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/favicon.png">	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>
		<?php 
				require("./analysis.template.start.code.php");
		
		?>	
				
  <div id='main-container'>
	

  	    <?php include_once("help-content.php"); ?>
		
			  	<div id="pause-marks-area" >
			  	<?php 
			  	$pauseMarksArr = getPauseMarksArrByFile($pauseMarksFile);
			  
			  	?>

				Pause Marks:
				<select id='pause-marks-selection'>
					 <option value="" >&nbsp;</option>
						<?php 
							
							foreach ($pauseMarksArr as $pauseMark => $dummy )
							{
								
								
						?>
								
								 <option  value="<?=$pauseMark?>"><?=$pauseMark?> </option>
					
					  
					  <?php 
							}
							
							
					  ?>
					</select>
					<div id="loading-layer">
			  		Loading ...
					</div>
				</div>	
				<div id="pausemarks-data-area">
			  		
				</div>

		  	
		
			  	
			  	
   </div>
   
		<?php 
				require("./analysis.template.end.code.php");
		
		?>	
	<script type="text/javascript">

	
	$(document).ready(function()
	{


	
	});


	$("#pause-marks-selection").change(function()
	{
        var selectedMark = $("option:selected", this).val();


		if ( selectedMark=="" )
		{
			return;
		}

		$("#loading-layer").show();

		doGetPauseMarksData(selectedMark);

			  
	});

	
	function doGetPauseMarksData(selectedMark)
	{
	

			$.ajaxSetup({
				url:  "/analysis/pausemarks.ajax.service.php?lang=<?=$lang?>&mark="+encodeURIComponent(selectedMark),
				global: false,
				type: "GET"
				
			});


			$.ajax({
				
				timeout: 60000,
				success: function(retRes)
						{

					  			$("#loading-layer").hide();
					  			$("#pausemarks-data-area").html(retRes);

					  			trackEvent('ANALYSIS','pause-marks',selectedMark,'');

					     },
				      	 error: function (xhr, ajaxOptions, thrownError)
				         {
				      		$("#pausemarks-data-area").html("<center>Error occured !</center>");
				      		$("#loading-layer").hide();
				         }
					});
						
			
			
			
			
			
	}



			  				
		
	</script>
		

	<?php 
		require("../footer.php");
	?>
	


  </body>
</html>







