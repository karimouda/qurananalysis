var _gaq = _gaq || [];

function showTranslationFor(divID)
{
	$("#"+divID+"-translation").show();
}

function switchToLang(toLang)
{
	
	
	
	if ( location.href.indexOf("?") >= 0 )
	{
		location.href=location.href.replace(/lang\=[A-Z]{2}/,"lang="+toLang);

	}
	else
	{
		location.href=location.href+"?lang="+toLang;
	}
}

function drawChart(jsonDATA,width,height,xAxisMin,xAxisMax,chartDivId,xAxisLabel,yAxisLabel,tooltipFormatFunction,oneColor)
{
	
	var data = jsonDATA;
	   
    var margin = {top: 40, right: 50, bottom: 50, left: 60}
      , width = width - margin.left - margin.right
      , height = height - margin.top - margin.bottom;
    
    var x = d3.scale.linear()
              .domain([xAxisMin,xAxisMax])//input
    		  .range([0,width]); //avilable space to mapp to 
    
    var y = d3.scale.linear()
    	      .domain([0, d3.max(data, function(d) { return d[1]; })])
    	      .range([ height, 0 ]);
    
    
    var color = d3.scale.category20();
    
    if ( oneColor!=undefined)
	{
    	color = function(c){ return oneColor}
	}
 
    var chart = d3.select(chartDivId)
	.append('svg:svg')
	.attr('width', width + margin.right + margin.left)
	.attr('height', height + margin.top + margin.bottom)
	.attr('class', 'chart')

    var main = chart.append('g')
	.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')')
	.attr('width', width)
	.attr('height', height)
	.attr('class', 'main')   
        
    // draw the x axis
    var xAxis = d3.svg.axis()
	.scale(x)
	.ticks(25) //convert x-axis to 25 section 
	.orient('bottom');

    main.append('g')
	.attr('transform', 'translate(0,' + height + ')')
	.attr('class', 'main chart-axi date')
	.call(xAxis)
	   .append("text")
      .attr("class", "label")
      .attr("x", width)
      .attr("y", 35)
      .style("text-anchor", "end")
      .text(xAxisLabel);

    // draw the y axis
    var yAxis = d3.svg.axis()
	.scale(y)
		.tickFormat(d3.format("d"))
	.orient('left');

    main.append('g')
	.attr('transform', 'translate(0,0)')
	.attr('class', 'main chart-axi date')
	.call(yAxis)
  .append("text")
      .attr("class", "label")
      .attr("transform", "rotate(-90)")
      .attr("y", -50)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text(yAxisLabel);

    var tooltip = d3.select("body").append("div")
    .attr("class", "chart-tooltip")
    .style("opacity", 0);
    
    var g = main.append("svg:g"); 
    
    g.selectAll("scatter-dots")
      .data(data)
      .enter().append("svg:circle")
          .attr("cx", function (d,i) { return x(d[0]); } )
          .attr("cy", function (d) { return y(d[1]); } )
          .attr("r", function (d) { return Math.log(d[1]*10); } )
          .attr("fill",function(d){return color(d[0]);})
          .on("mouseover", function(d) {
              tooltip.transition()
                   .duration(100)
                   .style("opacity", .9);
              tooltip.html(tooltipFormatFunction(d))
                   .style("left", (d3.event.pageX + 5) + "px")
                   .style("top", (d3.event.pageY - 28) + "px");
          })
          .on("mouseout", function(d) {
              tooltip.transition()
                   .duration(500)
                   .style("opacity", 0);
          });

}



function drawGraph(jsonNodesData,jsonLinksData,width,height,targetGraphDiv,capping)
{

	
	// Next define the main object for the layout. We'll also
	// define a couple of objects to keep track of the D3 selections
	// for the nodes and the links. All of these objects are
	// initialized later on.
	
	var force = null,
	    nodes = null,
	    links = null;
	
	
	    var dataNodes = jsonNodesData;
	
	    // The `links` array contains objects with a `source` and a `target`
	    // property. The values of those properties are the indices in
	    // the `nodes` array of the two endpoints of the link. Our links
	    // bind the first two nodes into one graph and the next two nodes
	    // into the second graph.
	
	    var dataLinks = jsonLinksData;
	
	
	
	 
	
		var force = d3.layout.force()
	    .size([width, height])
	    .charge(function(d) { return d.size*-10})
	    .linkDistance(100)
	    .gravity(.01)
	    .on("tick", tick);
	
	
	
	
	
	var svg = d3.select(targetGraphDiv).append("svg")
	    .attr("width", width)
	    .attr("height", height);
	
	var link = svg.selectAll(".graph-link"),
	    node = svg.selectAll(".graph-node");
	
	
	
	
	  link = link.data(dataLinks)
	    .enter().append("line")
	      .attr("class", "graph-link");
	
	
	  groupElement = node.data(dataNodes).enter().append("g")
	  .attr("class", "graph-node")
	  .attr("transform", function(d){return "translate("+d.x+",50)"})
	     .call(force.drag);
	
	  groupElement.append("title").text(function(d) { return d.size});
	
	   groupElement.append("text")
	  .attr("dx", function(d) { var xDistance = 10+(d.size*2); if (xDistance > 40) { xDistance = 40 } return xDistance;} )
	  .attr("dy", 0)
	  .attr("font-size", function(d) { var size = d.size*10; if (size > 42) { size = 42 } return size ;})
	  .attr("class", "graph-words")
	  .text(function(d) { return d.word });
	  
	   groupElement.append("circle")
	   .attr("r", function(d) { var size = d.size*3; if (size > 25) { size = 25 } return size;})
	   .style("fill", 'red');
	  
	
	  
	
	   force
	   .nodes(dataNodes)
	   .links(dataLinks)
	   .start();
	
	           
	
	
	function tick() {
	
		  link.attr("x1", function(d) { return d.source.x; })
	      .attr("y1", function(d) { return d.source.y; })
	      .attr("x2", function(d) { return d.target.x; })
	      .attr("y2", function(d) { return d.target.y; });
	
		
		  groupElement.attr("cx", function(d) { return d.x; })
	      .attr("cy", function(d) { return d.y; });
	      
		   groupElement.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
	}
	
	
	
	
	
	force.start();
	
	if (capping!=0)
	{
	$("<div style='text-align:center'>Graph is currently capped to "+capping+" words !</div>").insertAfter(targetGraphDiv);
	}

}
