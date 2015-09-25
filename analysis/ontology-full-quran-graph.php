<?php 
require_once("../global.settings.php");

require_once("../libs/search.lib.php");
require_once("../libs/graph.lib.php");



$direction = "ltr";


$lang = $_GET['lang'];
$presentation = $_GET['presentation'];

if ( empty($lang))
{
	$lang = "AR";
}

if ( empty($presentation))
{
	$presentation = "TREE";
}

if ($lang=="AR")
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
    <title>Full Quran Ontology Graph | Quran Analysis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Full Quran Ontology Visualization Graph">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/favicon.png">	 
	<script type="text/javascript">
	</script>
     
     <style>

BODY
{
width:3000px;
height: 2000px;
}
/*
.node circle {
  fill: #fff;
  stroke: steelblue;
  stroke-width: 1.5px;
}

.node {
  font: 10px sans-serif;
}

.link {
  fill: none;
  stroke: #ccc;
  stroke-width: 1.5px;
}*/

  
	.node {
    cursor: pointer;
  }

  .overlay{
      background-color:#EEE;
  }
   
  .node circle {
    fill: #fff;
    stroke: steelblue;
    stroke-width: 1.5px;
  }
   
  .node text {
    font-size:16px; 
    font-family:tahoma;
    fill:#000;
    font-weight: bold;
  }
   
  .link {
    fill: none;
    stroke: #ccc;
    stroke-width: 1.5px;
  }

  .templink {
    fill: none;
    stroke: red;
    stroke-width: 3px;
  }

  .ghostCircle.show{
      display:block;
  }

  .ghostCircle, .activeDrag .ghostCircle{
       display: none;
  }
  
  
  
.full-graph-node 
  {
  stroke: #000;
  stroke-width: 0.5px;
}

.full-graph-link
{
  stroke: #e0e0e0;
  stroke-dasharray:1,1;
  stroke-opacity: 1px;
}

.full-graph-text
{
  fill: #000;
  font-size:14px;
  font-weight: lighter;
}
</style>
       
  </head>
  <body>

	<div id='quran-full-ontology-header-panel'>

			<h1 style='display:inline'>QA Quran Ontology</h1>
			  		
		  	<select id='language-selection' onchange="handlePresentationOptions()">
   				<option value='EN' <?php if ($lang=="EN") echo 'selected'?> >EN</option>
   				<option value='AR' <?php if ($lang=="AR") echo 'selected'?>>AR</option>
   			</select>
   			
   			<select id='presentation-selection' onchange="handlePresentationOptions()">
   				<option value='TREE' <?php if ($presentation=="TREE") echo 'selected'?> >Tree</option>
   				<option value='GRAPH' <?php if ($presentation=="GRAPH") echo 'selected'?>>Force Directed Graph</option>
   			</select>
   			
   			
   	</div>			
   	
   	<?php 
			
			
			
			




if ( $presentation=="TREE")
{
$treeRootObj = ontologyToD3TreemapHierarchical($MODEL_QA_ONTOLOGY,0,$lang);

//preprint_r($treeRootObj);

$dataListObj = json_encode($treeRootObj);



}
else if ( $presentation=="GRAPH")
{
	$graphObj = ontologyToD3Graph($MODEL_QA_ONTOLOGY,0,$lang);
	
	$graphNodesJSON = json_encode($graphObj['nodes']);
	$graphLinksJSON = json_encode($graphObj["links"]);
	
	
}
else
{
	echoN("Invalid presentation type!");
}

//echoN($treeRootNodeJSON);
//echoN($graphNodesJSON);
//echoN($graphLinksJSON);
//exit;

?>
				
		<div id='full-ontology-graph-area'>

			

		</div>
					


   

	<script type="text/javascript">

		var presentation = '<?=$presentation?>';
	
				
		$(document).ready(function()
		{

			//
			

			<?php if ( $presentation=="TREE") :?>

				drawTreeMap();

			<?php elseif ( $presentation=="GRAPH") :?>

				//drawGraph(<?php echo "$graphNodesJSON" ?>,<?php echo "$graphLinksJSON" ?>,2000,1000,"#full-ontology-graph-area","");
				drawFullOntologyGraph();
			<?php endif;?>
		
		});


		<?php if ( $presentation=="GRAPH") :?>
		function drawFullOntologyGraph()
		{
			var width = 2600,
		    height = 2000;

			var zoomListener = d3.behavior.zoom()
		    .scaleExtent([0.1, 3])
		    .on("zoom",zooming);

		
			
		    var dataNodes = <?php echo "$graphNodesJSON" ?>;
		    

		
		    var dataLinks = <?php echo "$graphLinksJSON" ?>;

			var color = d3.scale.category20c();
	
			var force = d3.layout.force()
			    .charge(-1000)
			    .gravity(1)
			    .linkDistance(300)
			    .size([width, height]);
	
			var svg = d3.select("#full-ontology-graph-area").append("svg")
			    .attr("width", width)
			    .attr("height", height);
			     // .call(zoomListener);
	
			function zooming()
			{
				
				svg.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
		    }
	
			  force
			      .nodes(dataNodes)
			      .links(dataLinks);
			     // .start();
	
	//alert(JSON.stringify(dataNodes));
			  var link = svg.selectAll(".link")
			      .data(dataLinks)
			    .enter().append("line")
			      .attr("class", "full-graph-link")
			        .attr("x1", function(d) { return d.source.x; })
				      .attr("y1", function(d) { return d.source.y; })
				      .attr("x2", function(d) { return d.target.x; })
				      .attr("y2", function(d) { return d.target.y; })
			      .style("stroke-width", 
					      	function(d) 
					      	{
				      				if ( d.link_frequency > 10 ) return 10;
			      				 	return d.link_frequency; 
			      			});

			  node = svg.selectAll(".node")
		      .data(dataNodes)
		    .enter().append("g");
		    
			  node.append("circle")
			      .attr("class", "full-graph-node")
			      .attr("r", function(d) 
					      { 
				      //alert(d.size);
				      		var size = Math.log(d.size)*2;

				      		if ( size < 5 )
				      		{
				      			size = 5;
				      		}
				      		return size

					      })
			      .attr("cx", function(d) { return d.x; })
     			  .attr("cy", function(d) { return d.y; })
			      .style("fill", function(d) { return color(d.word); })
			      .call(force.drag);
		      
			      node.append("text")
			        .attr("dx","10px")
				  .attr("dy", "0px")
				   .attr("font-size", "10px")
				   .attr("class", "full-graph-text")
				  .text(function(d) { return d.word; });
			      
	
			  node.append("title")
			      .text(function(d) { return d.word; });
	
			  force.on("tick", function() {
			    link.attr("x1", function(d) { return d.source.x; })
			        .attr("y1", function(d) { return d.source.y; })
			        .attr("x2", function(d) { return d.target.x; })
			        .attr("y2", function(d) { return d.target.y; });
	
			    node.select("circle").attr("cx", function(d) { return d.x; })
			        .attr("cy", function(d) { return d.y; });

			    node.select("text").attr("dx", function(d) { return d.x+20; })
		        .attr("dy", function(d) { return d.y; });
			  });


			 
			  force.start();
			  
			/*   var n =100;
			  for (var i = 0;i<n;i++)
			  {
				   force.tick();
				  if ( force.alpha() < 0.03 )
				  {
					  break;
				  }
			  }
			  
			  force.stop();
			  */
	
					
		}	
		<?php endif;?>

		<?php if ( $presentation=="TREE") :?>
		function drawTreeMap()
		{

			var jsonData = <?=( empty($dataListObj) ) ? [] : $dataListObj;?>;
			
					var margin = {top: 20, right: 120, bottom: 20, left: 120},
					    width = 1400 - margin.right - margin.left,
					    height = 10000 - margin.top - margin.bottom;
			
					var i = 0,
					    duration = 750,
					    root;
			
					var tree = d3.layout.tree()
					    .size([height, width]);
			
					var diagonal = d3.svg.diagonal()
					    .projection(function(d) { return [d.y, d.x]; });
			
					var svg = d3.select("#full-ontology-graph-area").append("svg")
					    .attr("width", width + margin.right + margin.left)
					    .attr("height", height + margin.top + margin.bottom)
					  .append("g")
					    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
			
				
			
					  root = jsonData;
					  root.x0 = height / 2;
					  root.y0 = 0;
			
					  function collapse(d) {
					    if (d.children) {
					      d._children = d.children;
					      d._children.forEach(collapse);
					      d.children = null;
					    }
					  }
			
					  root.children.forEach(collapse);
					  update(root);
					
					d3.select(self.frameElement).style("height", "800px");
			
					function update(source) {
			
					  // Compute the new tree layout.
					  var nodes = tree.nodes(root).reverse(),
					      links = tree.links(nodes);
			
					  // Normalize for fixed-depth.
					  nodes.forEach(function(d) { d.y = d.depth * 180; });
			
					  // Update the nodes…
					  var node = svg.selectAll("g.node")
					      .data(nodes, function(d) { return d.id || (d.id = ++i); });
			
					  // Enter any new nodes at the parent's previous position.
					  var nodeEnter = node.enter().append("g")
					      .attr("class", "node")
					      .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
					      .on("click", click);
			
					  nodeEnter.append("circle")
					      .attr("r", 1e-6)
					      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });
			
					  nodeEnter.append("text")
					      .attr("x", function(d) { return (( d.children !=undefined && d.children.length>0 )) ? 10 : 10;  })
					      .attr("dy", ".15em")
					      .text(function(d) { return d.name; })
					      .style("fill-opacity", 1e-6);
			
					  // Transition nodes to their new position.
					  var nodeUpdate = node.transition()
					      .duration(duration)
					      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });
			
					  nodeUpdate.select("circle")
					      .attr("r", 4.5)
					      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });
			
					  nodeUpdate.select("text")
					      .style("fill-opacity", 1);
			
					  // Transition exiting nodes to the parent's new position.
					  var nodeExit = node.exit().transition()
					      .duration(duration)
					      .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
					      .remove();
			
					  nodeExit.select("circle")
					      .attr("r", 1e-6);
			
					  nodeExit.select("text")
					      .style("fill-opacity", 1e-6);
			
					  // Update the links…
					  var link = svg.selectAll("path.link")
					      .data(links, function(d) { return d.target.id; });
			
					  // Enter any new links at the parent's previous position.
					  link.enter().insert("path", "g")
					      .attr("class", "link")
					      .attr("d", function(d) {
					        var o = {x: source.x0, y: source.y0};
					        return diagonal({source: o, target: o});
					      });
			
					  // Transition links to their new position.
					  link.transition()
					      .duration(duration)
					      .attr("d", diagonal);
			
					  // Transition exiting nodes to the parent's new position.
					  link.exit().transition()
					      .duration(duration)
					      .attr("d", function(d) {
					        var o = {x: source.x, y: source.y};
					        return diagonal({source: o, target: o});
					      })
					      .remove();
			
					  // Stash the old positions for transition.
					  nodes.forEach(function(d) {
					    d.x0 = d.x;
					    d.y0 = d.y;
					  });
					}
			
					// Toggle children on click.
					function click(d) {
					  if (d.children) {
					    d._children = d.children;
					    d.children = null;
					  } else {
					    d.children = d._children;
					    d._children = null;
					  }
					  update(d);
					}
			
				
					/*
					var zoomListener = d3.behavior.zoom()
				    .scaleExtent([0.1, 3])
				    .on("zoom",zoom);
			
					var diameter = 1200;
			
			
			
					var tree = d3.layout.tree()
					    .size([360, diameter / 2 - 100])
					    .separation(function(a, b) { return (a.parent == b.parent ? 1 : 2) / a.depth; });
			
			
			
					var diagonal = d3.svg.diagonal.radial()
					    .projection(function(d) { return [d.y, d.x / 180 * Math.PI]; });
			
			
					var svg = d3.select("#full-ontology-graph-area").append("svg")
					    .attr("width", diameter)
					    .attr("height", diameter - 50)
					    .call(zoomListener)
					  .append("g")
					    .attr("transform", "translate(" + diameter / 2 + "," + diameter / 2 + ")");
					    
			
					function zoom()
					{
						svg.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
				    }
			
					  var nodes = tree.nodes(jsonData),
				      links = tree.links(nodes);
			
				  var link = svg.selectAll(".link")
				      .data(links)
				    .enter().append("path")
				      .attr("class", "link")
				      .attr("d", diagonal);
			
				  var node = svg.selectAll(".node")
				      .data(nodes)
				    .enter().append("g")
				      .attr("class", "node")
				      .attr("transform", function(d) { return "rotate(" + (d.x - 90) + ")translate(" + d.y + ")"; })
			
				  node.append("circle")
				      .attr("r", 4.5);
			
				  node.append("text")
				      .attr("dy", ".31em")
				      .attr("text-anchor", function(d) { return d.x < 180 ? "start" : "end"; })
				      .attr("transform", function(d) { return d.x < 180 ? "translate(8)" : "rotate(180)translate(-8)"; })
				      .text(function(d) { return d.name; });
			
				*/
		}
		<?php endif;?>


	function handlePresentationOptions()
	{
		
		
		var selectedLang = $("SELECT[id=language-selection] option:selected").val();
		var selectedPresentation = $("SELECT[id=presentation-selection] option:selected").val();
		

		
		var newURL ="";
		if ( location.href.indexOf("?") >= 0 )
		{
			newURL=location.href.substring(0,location.href.indexOf("?"));
		

		}
		
			newURL=newURL+"?lang="+selectedLang+"&presentation="+selectedPresentation;

			location.href = newURL;
		
	}

	</script>
		

	<?php 
		require("../footer.php");
	?>
	

  </body>
</html>
