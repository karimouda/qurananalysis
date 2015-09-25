<?php 
require_once("../global.settings.php");

$lang = "AR";



//preprint_r($MODEL_QAC);

if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core,qac",$lang);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Part Of Speech Query | Quran Analytics </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Part of Speech Query tool for the Quran">
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
		
			  
			  	
			  	<div id='pos-options-area'>
					<?php 
					
					//preprint_r($MODEL['QAC_MASTERTABLE']);
					//preprint_r($MODEL['QAC_POS']);
					//preprint_r($MODEL['QAC_FEATURES']);
					//preprint_r($MODEL_QURANA['QURANA_CONCEPTS']);
					//preprint_r($MODEL['QURANA_PRONOUNS']);
					?>
				
				<table id='pos-options-table'>
				<tr>
					<td>
							Select PoS Tag
					</td>
					<td>
							<select id='pos-selection'>
					 <option value="" selected="true">&nbsp;</option>
						<?php 
						
							$qacPoSTagsIterator = getAPCIterator("AR\/MODEL_QAC\/QAC_POS\/.*");
						
						
							
							foreach ($qacPoSTagsIterator as $qacPoSTagsIteratorCursor )
							{
								
								$posArr = $qacPoSTagsIteratorCursor['value'];
								$key = $qacPoSTagsIteratorCursor['key'];
								$posTypeName = getEntryKeyFromAPCKey($key);
							
								
								
						?>
								
								 <option  value="<?=$posTypeName?>"><b><?=$posTypeName?> </b></option>
					
					  
					  <?php 
							}
					  ?>
					</select>
					</td>
				</tr>
				<tr>
					<td>
						Features
					</td>
					<td>
						<input  type="text" name="features" id="features" style='width:194px'></input>
					</td>
				</tr>
				<tr>
					<td >
					</td>
					<td align="left">
					
						<input  type="button" name="submit" id="submit" value="Find" ></input>
					</td>
				</tr>
				</table>

					
					<div id="loading-layer">
			  		Loading ...
					</div>
					
				</div>
			<br>
		  		
     				<div id='pos-data-area'>
							
							<span class='note'>based on QAC (Quranic Arabic Corpus). more about the Tags here:
							<a target="_new" href='http://corpus.quran.com/documentation/tagset.jsp'>http://corpus.quran.com/documentation/tagset.jsp</a>
							</span>
					</div>
					
					
			
   </div>

			<?php 
				require("./analysis.template.end.code.php");
		
		?>			  	

	<script type="text/javascript">


				
		$(document).ready(function()
		{


		
		});

		function doSubmit()
		{
			 var selectedPOS = $("#pos-selection option:selected").val();

		
		        var features = $("#features").val();
		        

				if ( selectedPOS=="" )
				{
					return;
				}

				$("#loading-layer").show();

				doGetPOSData(selectedPOS,features);
		}

		$("#submit").click(function()
		{
			doSubmit();
		});

		$("#pos-selection").change(function()
		{
	       
			doSubmit();
				  
		});

		
		function doGetPOSData(selectedPOS,features)
		{
		
	
	
				
				$.ajaxSetup({
					url:  "/analysis/pos.ajax.service.php?lang=<?=$lang?>&pos="+encodeURIComponent(selectedPOS)+"&features="+features,
					global: false,
					type: "GET"
					
				  });


				$.ajax({
					
					timeout: 60000,
					success: function(retRes)
							{

						  			$("#loading-layer").hide();
						      	 	
						  			$("#pos-data-area").html(retRes);


						  			trackEvent('ANALYSIS','pos-query',selectedPOS,features);
	
						 	 	
						     },
					      	 error: function (xhr, ajaxOptions, thrownError)
					         {
					      		$("#content-area").html("<center>Error occured !</center>");
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







