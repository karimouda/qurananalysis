<?php 
require_once("../global.settings.php");

require_once("../libs/search.lib.php");
require_once("../libs/graph.lib.php");


$lang = "EN";
$direction = "ltr";




///DETEDCT LANGUAGE //LOCATION SIGNIFICANT
if (isArabicString($query))
{
	$lang = "AR";
	$direction = "rtl";
}




//echoN(time());
loadModels("core,ontology",$lang);
//echoN(time());
	



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analysis | Explore the Quran </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Exploratory search graph for the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>
		<?php 
			require("../header.php");
	
		?>
			  		
  <div id='main-container'>
			  	
<?php 
			
			
			
			


	/*$MODEL_QA_ONTOLOGY['CONCEPTS'] = $qaOntologyConceptsArr;
	$MODEL_QA_ONTOLOGY['RELATIONS'] = $qaOntologyRelationsArr;
	$MODEL_QA_ONTOLOGY['GRAPH_INDEX_SOURCES'] = $qaOntologyGraphSourcesIndex;
	$MODEL_QA_ONTOLOGY['GRAPH_INDEX_TARGETS'] = $qaOntologyGraphTargetsIndex;
	*/
	
	//preprint_r($MODEL_QA_ONTOLOGY);





	$graphObj = ontologyToD3Graph($MODEL_QA_ONTOLOGY,10);






//$graphObj = textToGraph($searchResultText,$MODEL_CORE['STOP_WORDS']);




//preprint_r($graphNodesArr);

$graphNodesJSON = json_encode($graphObj["nodes"]);
$graphLinksJSON = json_encode($graphObj["links"]);

//echoN($graphNodesJSON);
//echoN($graphLinksJSON);
//exit;

?>
			

			
					
				
			  	<div id="ontology-graph" >
			  	
		
			
			  
			  	
			  	</div>	
   </div>
   

	<script type="text/javascript">

				
		$(document).ready(function()
		{

			drawGraph(<?php echo "$graphNodesJSON" ?>,<?php echo "$graphLinksJSON" ?>,1280,800,"#ontology-graph","");
			
				
		
		});
		
	

	</script>
		

	<?php 
		require("../footer.php");
	?>
	

  </body>
</html>
