<?php 
require_once("../global.settings.php");
require_once("../libs/graph.lib.php");



$lang = "AR";

if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core",$lang);

$SURA = $_GET['s'];
$AYA = $_GET['a'];
$isAllSURA = $_GET['allSURA'];

// nothing passed
if ( (($isAllSURA=="") && ($SURA=="") ) ||  (($SURA=="") && ($AYA=="") ) )
{
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analytics | Graphs IFRAME</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Full Analytics System for the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>

  <div id='main-container'>
			  	
			
			  	
			

			  		<div id='graph-maingraph-area'>
					<?php 
				
					
					
					
					if ( $isAllSURA )
					{
						
						for ($s=0;$s<$numberOfSuras;$s++)
						{

							$suraSize = count($MODEL_CORE['QURAN_TEXT'][$SURA]);
								
							for ($a=0;$a<$suraSize;$a++)
							{
								$arrOfTextToGraph[] = $MODEL_CORE['QURAN_TEXT'][$SURA][$a];
							}
							
						}
						
					}
					else
					{
					
						$verseText = $MODEL_CORE['QURAN_TEXT'][$SURA][$AYA];
						$arrOfTextToGraph = array($verseText);
					}
					
					
					
					$graphObj = textToGraph($arrOfTextToGraph,$MODEL_CORE['STOP_WORDS'],600);
					
					
					
					$graphNodesArr = array();
					
					foreach($graphObj["nodes"] as $word => $nodeArr)
					{
					
						$graphNodesArr[] = $nodeArr;
					
					}
					
					//preprint_r($graphNodesArr);
					
					$graphNodesJSON = json_encode($graphNodesArr);
					$graphLinksJSON = json_encode($graphObj["links"]);
					
					
					
				
				?>
					</div>
			
	
		  		
			
   </div>
   

	<script type="text/javascript">


				
		$(document).ready(function()
		{

			drawGraph(<?php echo "$graphNodesJSON" ?>,<?php echo "$graphLinksJSON" ?>,960,400,"#graph-maingraph-area",<?php echo $graphObj["capped"]?>);
			
		
		});


		
	</script>
		



  </body>
</html>







