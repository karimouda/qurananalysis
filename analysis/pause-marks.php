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
    <title>Quran Analytics | Pause Marks </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pause Marks in the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>
			<?php 
				require_once("./analysis.header.php");
			?>
				
  <div id='main-container'>
	

		
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







