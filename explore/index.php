<?php 
require_once("../global.settings.php");


require_once("../libs/search.lib.php");
require_once("../libs/graph.lib.php");

$direction = "rtl";

$lang = $_GET['lang'];


if ( empty($lang))
{
	$lang = "EN";
	$direction = "ltr";
}








//echoN(time());
loadModels("core,search,ontology",$lang);
//echoN(time());
	



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analysis | Explore the Quran </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Exploratory search for the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<script type="text/javascript" src="<?=$TINYSORT_PATH?>"></script>
	<script type="text/javascript" src="<?=$TINYSORT_JQ_PATH?>"></script>	
	
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	 
	<script type="text/javascript">
	</script>
     
     <style>



</style>
       
  </head>
  <body>
		<?php 
			require("../header.php");
	
		?>

  <div id='main-container'>
			 <div id='options-area'>
			 			  	<?php 
						  		include_once("../header.menu.php");
						  	?>
			  		</div>
			  		
			  		
			<div id='explore-lang-select-area'>
			
			  	<select id='language-selection' onchange="handlePresentationOptions()" style='float:left'>
	   				<option value='EN' <?php if ($lang=="EN") echo 'selected'?> >EN</option>
	   				<option value='AR' <?php if ($lang=="AR") echo 'selected'?>>AR</option>
	   			</select>	  		
	   			<span id='explore-guide-msg' style='float:none'>
			  	&nbsp;Click on any topic to find relevant verses. Topics of same color are related.
			    </span>
			  
		    </div>
			  		<?php 
			
			
			
			


	/*$MODEL_QA_ONTOLOGY['CONCEPTS'] = $qaOntologyConceptsArr;
	$MODEL_QA_ONTOLOGY['RELATIONS'] = $qaOntologyRelationsArr;
	$MODEL_QA_ONTOLOGY['GRAPH_INDEX_SOURCES'] = $qaOntologyGraphSourcesIndex;
	$MODEL_QA_ONTOLOGY['GRAPH_INDEX_TARGETS'] = $qaOntologyGraphTargetsIndex;
	*/
	
	//preprint_r($MODEL_QA_ONTOLOGY);



			  		

//$graphObj = ontologyToD3Graph($MODEL_QA_ONTOLOGY,0,$lang);

$treeRootNodeObj = ontologyToD3TreemapHierarchical($MODEL_QA_ONTOLOGY,0,$lang);

//echoN(count($treeRootNodeObj['children']));
//preprint_r($treeRootNodeObj);


/////// MOVE THING CLASS CLUSTER TO THE END OF ARRAY TO MAKE PRIORITY FOR NOED 
/////// TO BE CLUSTERED WITH SPECIFIC CLASSES FIRST - EX: مؤمن is شيء AND  صفة
$thingClassClusterCopy = null;
foreach($treeRootNodeObj['children'] as $index => $nodeArr)
{
	
	


	if ( $nodeArr['name']==$thing_class_name_ar || $nodeArr['name']==$thing_class_name_en)
	{
		$thingClassClusterCopy = $treeRootNodeObj['children'][$index];
		unset($treeRootNodeObj['children'][$index]);
	}
	
}

$treeRootNodeObj['children'][] =  $thingClassClusterCopy;

//////////////////////////////////////////////

//preprint_r($treeRootNodeObj);exit;

$handledBefore = array();
//preprint_r($treeRootNodeObj);exit;



function addChildrenToCluster(&$clusteredArr,$parentNodeArr,&$clusterSerialNumber,&$nodeSerialNumber,$level,&$handledBefore)
{

	
	
	foreach($parentNodeArr['children'] as $index => $nodeArr)
	{
		
		//prevent repetition of same node
		if ( isset($handledBefore[$nodeArr['name']]) ) continue;
		
		$clusteredArr[$nodeSerialNumber]['cluster']=$clusterSerialNumber;
		$clusteredArr[$nodeSerialNumber]['size'] = $nodeArr['size'];
		
		$clusteredArr[$nodeSerialNumber]['word'] = $nodeArr['name'];
		

			$newRad = (($nodeArr['size']*100)/3000)*3;
			if ( $newRad> 60)
			{
				$newRad = 60;
			}
			
			if ( $newRad < 22 )
			{
				$newRad=22;
			}
			
			$clusteredArr[$nodeSerialNumber]['radius']=$newRad;
		
		
		$handledBefore[$nodeArr['name']]=1;
		
		
		$nodeSerialNumber++;
		
		if ( count($nodeArr['children'])>0)
		{
			//preprint_r($nodeArr['children']);
			addChildrenToCluster($clusteredArr,$nodeArr,$clusterSerialNumber,$nodeSerialNumber,$level+1,$handledBefore);
		}
		
		//
	
	
		if ($level==1) $clusterSerialNumber++;
	}
	
	
}

$clusteredArr = array();

$nodeSerialNumber = 0;
$clusterSerialNumber =0;

addChildrenToCluster($clusteredArr,$treeRootNodeObj,$clusterSerialNumber,$nodeSerialNumber,1,$handledBefore);


//preprint_r($handledBefore);

//preprint_r($clusteredArr);exit;

//$graphNodesJSON = json_encode($graphObj['nodes']);
//$graphLinksJSON = json_encode($graphObj['links']);

//echoN($treeRootNodeJSON);
//echoN($graphNodesJSON);
//echoN($graphLinksJSON);
//exit;

$filteredClusteredArr = array();
$index = 0;
foreach($clusteredArr as $index => $clusterArrItem)
{
	
	$conceptName = strtolower(convertConceptIDtoGraphLabel($clusterArrItem['word']));
	
	$conceptNameAR  = $MODEL_QA_ONTOLOGY['CONCEPTS_EN_AR_NAME_MAP'][$conceptName];
	
	
	// if not in index (then not qurana or word in quran)
	// and does not have subclasses // then ignore
	if ( !wordOrPhraseIsInIndex($lang,$conceptName) &&
	!conceptHasSubclasses($MODEL_QA_ONTOLOGY['RELATIONS'], $conceptNameAR) )
	{
		//preprint_r($MODEL_QA_ONTOLOGY['RELATIONS']);exit;
		//echoN($conceptName);
		continue;
	
	}
	
	$index++;
	
	$filteredClusteredArr[] = $clusterArrItem;
	
}

//preprint_r($clusteredArr);

//echoN(count($clusteredArr));

$clusteredArrJSON = json_encode($filteredClusteredArr);

?>
		  		
		  
		  <div id='exploration-area'>

			

			</div>
					

   </div>
   

	<script type="text/javascript">

				
		$(document).ready(function()
		{

			$("#options-area").attr("class","oa-explore");

			var isForeignObjectSupported = document.implementation.hasFeature('http://www.w3.org/TR/SVG11/feature#Extensibility','1.1');

			if ( !isForeignObjectSupported )
			{
				showBrowserSupportErrorMessage('exploration-area');
			}
		
		});

		var jsonData = <?=$clusteredArrJSON?>;
		
			//alert(JSON.stringify(jsonData));



		var width = ($(document).width()-100),
		    height = 3000,
		    padding = 1.5, // separation between same-color circles
		    clusterPadding = 6, // separation between different-color circles
		    maxRadius = -1;

		var n = jsonData.length, // total number of circles
		    m = 10; // number of distinct clusters



		
	



		var clusters = new Array();
		var clustersSizes = new Array();

		//alert(jsonData.length);
		var clusterId = 0;
		jsonData.forEach(function(d)
				{
					
					clusterId = d.cluster; 

					
					if ( clustersSizes[clusterId]==null)
					{
						clustersSizes[clusterId]=0;
					}
					
					clustersSizes[clusterId]++;
					
					if ( d.radius > maxRadius)
					{
						maxRadius = d.radius;
						
					}
					
					if ( !clusters[clusterId] || d.radius > clusters[clusterId].radius )
					{
						clusters[clusterId] = d;

			
						
					}


		
				}
		);


		/*clusters.forEach(function(c)
				{
					if ( c.cluster == 335 )
					alert(c.cluster+" "+clustersSizes[c.cluster]+" "+c.word+" "+clusters[clusterId].word+" "+JSON.stringify(c));
				});*/



		//alert(JSON.stringify(clustersSizes));

		var clusterVerticalLocationFactor=100;
		// set cluster nodes to random locations and set thier children to teh same
		jsonData.forEach(function(d)
				{
					
					clusterId = d.cluster; 
					clusterNode = clusters[clusterId];


					var clusterNodesCount = parseInt(clustersSizes[clusterId]);

					clusterXLocation =(clusterNodesCount)%600;;

					if ( clusterXLocation > width )
					{
						clusterXLocation = width/2;
					}

					clusterYLocation = (parseInt(clusterNodesCount)*5);;
					


					
					if ( clusterYLocation > height )
					{

						clusterYLocation = height -400;
					}

					//alert(clusterYLocation);
					
					clusterNode.x = clusterXLocation;
					clusterNode.y =  clusterYLocation;

				
					d.x = clusterXLocation+  (multiplyByRandomSign(Math.random()) )*1;
					d.y =  clusterYLocation+ (multiplyByRandomSign(Math.random()) )*200;

					clusterVerticalLocationFactor++;
				}
		);


		var color = d3.scale.category10().domain(d3.range(clusters.length));

		//alert(JSON.stringify(jsonData));
	
		
		/*
		// The largest node for each cluster.
		var clusters = new Array(m);
		
		var nodes = d3.range(n).map(function() {
		  var i = Math.floor(Math.random() * m),
		      r = Math.sqrt((i + 1) / m * -Math.log(Math.random())) * maxRadius,
		      d = {cluster: i, radius: r};
		  if (!clusters[i] || (r > clusters[i].radius)) clusters[i] = d;
		  return d;
		});
		*/
		
		var force = d3.layout.force()
		    .nodes(jsonData)
		    .size([width, height])
		    .gravity(0.03)
		    .charge(0)
		    .friction(0.2)
		    .on("tick", tick)
		    .on("end", handleEndEvent)
		   	.on("start", handleStartEvent);
	    


		var svg = d3.select("#exploration-area").append("svg")
		    .attr("width", width)
		    .attr("height", height)
		    .attr("xlink","http://www.w3.org/1999/xlink");


		function getAdjustedCornerPointX(currentPageX)
		{

			//alert(currentPageX+"+"+layerWidth +">"+ width);
			var finalX = 0;
			
			var layerWidth = 600;
			if ( currentPageX+layerWidth > width )
			{
				
				finalX =  currentPageX-(layerWidth);;
			}

				if ( finalX < 0 )
				{
					finalX = 10;
				}

			return finalX;
		}
		
		function getAdjustedCornerPointY(currentPageY)
		{
			var layerHeight= 700;
			if ( currentPageY+layerHeight > height )
			{
				return currentPageY-(height-currentPageY)/2;
			}

			return currentPageY;
		}
		
		var circle = svg.selectAll("circle")
		    .data(jsonData)
		  .enter().append("g")
		  .attr("class","explore-node")
		  .on("click",function(d)
		  {

			   svg.selectAll("#explore-result-verses-container").remove();
			   
				var word = d.word;
				var foreignObject = svg.append("foreignObject")
				.attr("id","explore-result-verses-container")
				.attr("width","600px")
				.attr("height","700px")
				.attr("x",getAdjustedCornerPointX(d3.event.pageX-50) + "px")
				.attr("y",(d3.event.pageY-100) + "px");

				
				var body = foreignObject.append("xhtml:body");
				
			
				
				body.append("xhtml:img")
			    .attr("src","/images/close-icon-black.png")
			    .attr("class","explore-verses-close")
				.on("click", function() {
					
					$("#explore-result-verses-container").css("display","none");

					 svg.selectAll("#explore-result-verses-container").remove();
				});

				body.append("xhtml:div")
				.attr("id","explore-result-verses")
				//.attr("xmlns","http://www.w3.org/1999/xhtml")
				.html("");
				

				// make it a phrase search // needed for qurana pron
				//if ( word.indexOf(" ")>-1)
				//{
					//word = "\""+word+"\"";
				//}

		        	// one concept search
		        	word = "CONCEPTSEARCH:"+word+"";

				showResultsForQueryInSpecificDiv(word,"explore-result-verses");

				
				
		  });

		circle.append("circle")
		    .attr("r", function(d) { return d.radius; })
		    .style("fill", function(d) { return color(d.cluster); })
		    .attr("cx", function(d) { return d.x; })
		    .attr("cy", function(d) { return d.y; })
		    .call(force.drag);

		circle.append("text").text(
				function(d) 
				{
					var word = d.word;

					if ( word!=undefined && word.length > 5 )
					{
						word = word.substring(0,5)+"..";
					}
					
					return word; 

					 
				} );

		circle.append("title").text( function(d) { return (d.word); } );

		function tick(e) {

			
			circle.each(cluster(10 * e.alpha * e.alpha)).each(collide(0.5));

		   circle.each(handleOutOfBoundry(e.alpha));

	      //if ( e.alpha < 0.05 ) force.stop();
	      
		  circle.select("circle").attr("cx", function(d) { return d.x; });
		     circle.select("circle").attr("cy", function(d) { return d.y; });

		  circle.select("text").attr("dx", function(d) { return d.x; });
		     circle.select("text").attr("dy", function(d) { return d.y; });

		  
		}


	

		

		force.start();

		/*for(var i=0;i<1000;i++) 
			{
				force.tick();
				//circle.each(handleOutOfBoundry(0.5));
				//circle.each(cluster(10 * e.alpha * e.alpha)).each(collide(0.5));
			}
			*/
		//force.stop();

		//force.resume();
		
		function handleEndEvent()
		{
			//circle.each(handleOutOfBoundry(0.5));

			//force.resume();
			
		}
		function handleStartEvent()
		{
			//circle.each(handleOutOfBoundry(0.5));
		
		}
		 
	
		function handleOutOfBoundry(alpha)
		{

			 var boundryPadding = 100;
			 return function(d) 
			 {
				
				 
				if ( (d.x-d.radius) < boundryPadding || (d.x+d.radius) > width-boundryPadding || 
						 (d.y-d.radius) < boundryPadding || (d.y+d.radius) > height-boundryPadding)
				{

					//alert(JSON.stringify(d)+" "+d.x+" "+d.y+" "+d.radius);
					
					dsClusterObj = clusters[d.cluster];
					targetXPos = dsClusterObj.x;
					targetYPos = dsClusterObj.y;

					if ( targetXPos > width ||  targetXPos <0  )
					{

						
						diff = Math.abs(width-targetXPos)/2;

						if ( targetXPos < 0 )
						{
							targetXPos +=diff;
						}
						else
						{
							targetXPos -=diff;
						}
						

						dsClusterObj.x = targetXPos;
					
						
					}

					if ( targetYPos  > height  ||  targetYPos <0   )
					{
						diff = Math.abs(  height -targetYPos )/2;

						if ( targetYPos < 0 )
						{
							targetYPos +=diff;
						}
						else
						{
							targetYPos -=diff;
						}
						
						dsClusterObj.y = targetYPos;
					}
			
					d.x += (targetXPos-d.x)*(alpha);
					d.y += (targetYPos-d.y)*alpha;

					
					//d.x = (Math.random() * (width - 100) ) + 100;
					//d.y = (Math.random() * (3000 - 100) ) + 100;
					
				}
	
				
					
					

					
					
				
			 }
		}

		function preTickClustering()
		{
			circle
		      .each(cluster(10 * e.alpha * e.alpha))
		      .each(collide(0.5));
		}
		

		

		// Move d to be adjacent to the cluster node.
		function cluster(alpha) {
		  return function(d) {
		   var cluster = clusters[d.cluster];
		    if (cluster === d) return;
		    var x = d.x - cluster.x,
		        y = d.y - cluster.y,
		        l = Math.sqrt(x * x + y * y),
		        r = d.radius + cluster.radius;
		    if (l != r) {
		      l = (l - r) / l * alpha;
		      d.x -= x *= l;
		      d.y -= y *= l;
		      cluster.x += x;
		      cluster.y += y;
		    }
		  };
		}

		// Resolves collisions between d and all other circles.
		function collide(alpha) {
		  var quadtree = d3.geom.quadtree(jsonData);
		  return function(d) {
		    var r = d.radius + maxRadius + Math.max(padding, clusterPadding),
		        nx1 = d.x - r,
		        nx2 = d.x + r,
		        ny1 = d.y - r,
		        ny2 = d.y + r;
		    quadtree.visit(function(quad, x1, y1, x2, y2) {
		      if (quad.point && (quad.point !== d)) {
		        var x = d.x - quad.point.x,
		            y = d.y - quad.point.y,
		            l = Math.sqrt(x * x + y * y),
		            r = d.radius + quad.point.radius + (d.cluster === quad.point.cluster ? padding : clusterPadding);
		        if (l < r) {
		          l = (l - r) / l * alpha;
		          d.x -= x *= l;
		          d.y -= y *= l;
		          quad.point.x += x;
		          quad.point.y += y;
		        }
		      }
		      return x1 > nx2 || x2 < nx1 || y1 > ny2 || y2 < ny1;
		    });
		  };
		}
		
	
		function handlePresentationOptions()
		{
			
			
			var selectedLang = $("SELECT[id=language-selection] option:selected").val();
		
			

			
			var newURL ="";
			if ( location.href.indexOf("?") >= 0 )
			{
				newURL=location.href.substring(0,location.href.indexOf("?"));
			

			}
			
				newURL=newURL+"?lang="+selectedLang;

				location.href = newURL;
			
		}
	</script>
		

	<?php 
		require("../footer.php");
	?>
	

  </body>
</html>
