<?php 
#   PLEASE DO NOT REMOVE OR CHANGE THIS COPYRIGHT BLOCK
#   ====================================================================
#
#    Quran Analysis (www.qurananalysis.com). Full Semantic Search and Intelligence System for the Quran.
#    Copyright (C) 2015  Karim Ouda
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#    You can use Quran Analysis code, framework or corpora in your website
#	 or application (commercial/non-commercial) provided that you link
#    back to www.qurananalysis.com and sufficient credits are given.
#
#  ====================================================================
require_once("../global.settings.php");
require_once("../libs/graph.lib.php");



$lang = "AR";

if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core,ontology",$lang);

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
     
     <style>
     	.chapter-graph-node 
		  {
		  stroke: #000;
		  stroke-width: 0.5px;
		}
		
		.chapter-graph-link
		{
		  stroke: #E0E0E0;
		  stroke-opacity: .1px;
		}
		
		.chapter-graph-text
		{
		  fill: #000;
		}
     </style>
       
  </head>
  <body>

  <div id='main-container'>
			  	
			
			  	
			

			  		<div id='graph-maingraph-area'>
					<?php 
				
					
					
					
				
						
					$customFreqArr = array();
					
					$QURAN_TEXT = getModelEntryFromMemory($lang, "MODEL_CORE", "QURAN_TEXT", "");

					$suraSize = count($QURAN_TEXT[$SURA]);
						
					for ($a=0;$a<$suraSize;$a++)
					{
						$verseText = $QURAN_TEXT[$SURA][$a];
						
						$verseTextArr = explode(" ", $verseText);
						
						foreach($verseTextArr as $index=> $word)
						{
							$word = cleanAndTrim($word);
							$word = strtolower($word);
							
							$customFreqArr[$word]++;
						}
						
						$arrOfTextToGraph[] = $verseText;
					}
					
					
					
						
						
			
					
					
					
					$graphObj = ontologyTextToD3Graph($MODEL_QA_ONTOLOGY,"SEARCH_RESULTS_TEXT_ARRAY",$arrOfTextToGraph,0,array(960,600),$lang,1);
					
					
					foreach( $graphObj['nodes'] as $index => $nodeArr)
					{
						$word = strtolower($nodeArr['word']);
					
	
						if ( isset($customFreqArr[$word]))
						{
							$graphObj['nodes'][$index]['size'] = $customFreqArr[$word];
						}
						else
						{
							$graphObj['nodes'][$index]['size'] = 1;
						}
					
					}
					
				
					
					//preprint_r($graphNodesArr);
					
					$graphNodesJSON = json_encode($graphObj["nodes"]);
					$graphLinksJSON = json_encode($graphObj["links"]);
					
					
					
				
				?>
					</div>
			
	
		  		
			
   </div>
   

	<script type="text/javascript">


    var dataNodes = <?php echo "$graphNodesJSON" ?>;
    


    var dataLinks = <?php echo "$graphLinksJSON" ?>;

    var maxRadius = 12;
    var padding  = 5;
    
	function collide(alpha) {
		  var quadtree = d3.geom.quadtree(dataNodes);
		  return function(d) {
		    var r = d.radius + maxRadius + padding,
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
				
		$(document).ready(function()
		{

			//drawGraph(<?php echo "$graphNodesJSON" ?>,<?php echo "$graphLinksJSON" ?>,960,400,"#graph-maingraph-area");

			var width = 760,
		    height = 600;

			var zoomListener = d3.behavior.zoom()
		    .scaleExtent([0.1, 3])
		    .on("zoom",zooming);

		
			

			var color = d3.scale.category20();
	
			var force = d3.layout.force()
			    .charge(-1800)
			    .gravity(2.1)
			    .linkDistance(150)
			    .size([width, height]);
	
			var svg = d3.select("#graph-maingraph-area").append("svg")
			    .attr("width", width)
			    .attr("height", height);
			     // .call(zoomListener);
	
			function zooming()
			{
				
				svg.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
		    }

			function getCircleRadius(size)
			{
				return (getCircleSize(size)/2);
			}
			
		    function getCircleSize(size)
		    {
		    	size = (size*2);

	      		if ( size < 2 )
	      		{
	      			size = 2;
	      		}

	      		if ( size > 40 )
	      		{
	      			size = 40;
	      		}

	      		return size;
		    }
	
			  force
			      .nodes(dataNodes)
			      .links(dataLinks);
			     // .start();
			     
			   //  alert(JSON.stringify(dataLinks));
	
			  var link = svg.selectAll(".link")
			      .data(dataLinks)
			    .enter().append("line")
			      .attr("class", "chapter-graph-link")
			        .attr("x1", function(d) { return d.source.x; })
				      .attr("y1", function(d) { return d.source.y; })
				      .attr("x2", function(d) { return d.target.x; })
				      .attr("y2", function(d) { return d.target.y; })
				      .style("stroke-width", function(d) {  return Math.sqrt(d.link_frequency); });

			  node = svg.selectAll(".node")
		      .data(dataNodes)
		    .enter().append("g");
		    
			  node.append("circle")
			      .attr("class", "chapter-graph-node")
			      .attr("r", function(d) 
					      { 
				      		
				      		return getCircleSize(d.size);

					      })
			      .attr("cx", function(d) { return d.x; })
     			  .attr("cy", function(d) { return d.y; })
			      .style("fill", function(d) { return color(d.size); })
			      .call(force.drag);
		      
			      node.append("text")
			        .attr("dx","0px")
				  .attr("dy", "0px")
				   .attr("font-size", "10px")
				   .attr("class", "chapter-graph-text")
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

			    node.select("text").attr("dx",
					 function(d) 
					 {
					 	 var circleRadius = (getCircleRadius(d.size));
					 	 if ( circleRadius < 12 )
					 	 {
					 	 	return d.x+circleRadius+15; 
					 	 }
					 	 else
					 	 {
					 		return d.x-10;
					 	 }
					 	 
					 })
		        .attr("dy", function(d) { return d.y+5; });

			    node
			      .each(collide(0.5));
			      
			  });


			 
			  force.start();
			  
		
	
					
		
		});


		
	</script>
		



  </body>
</html>







