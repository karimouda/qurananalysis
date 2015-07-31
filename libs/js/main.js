var _gaq = _gaq || [];

function showTranslationFor(divID)
{
	$("#"+divID+"-translation").show();
}

function switchToSelectedLang()
{
	
	var selectedLang = $("SELECT[id=language-selection] option:selected").val();
	

	
	if ( location.href.indexOf("?") >= 0 )
	{
		location.href=location.href.replace(/lang\=[A-Z]{2}/,"lang="+selectedLang);

	}
	else
	{
		location.href=location.href+"?lang="+selectedLang;
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
                   .style("opacity", .95);
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


function destroyGraph()
{
	$(".graph-tooltip").hide();
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
	    
	
	
	    var linksLength = dataLinks.length;
	

	  
	
		var force = d3.layout.force()
	    .size([width, height])
	    .charge(function(d) { return d.size})
	    .linkDistance(function(l) 
		{ 
			linkDistance = linksLength*10;//parseInt((l.source.size+l.target.size)/2);
			
			if (linkDistance==undefined ||  linkDistance> 300)
			{
				linkDistance = 300;
			}
			
			if ( linkDistance< 100)
			{
				linkDistance = 100;
			}
			
			return linkDistance;
		})
	    .gravity(-0.005)
	    .on("tick", tick);
	
	
	
//	/+(parseInt(l.source.size)+parseInt(l.target.size) )
	
	var svg = d3.select(targetGraphDiv).append("svg")
	    .attr("width", width)
	    .attr("height", height);
	
	var link = svg.selectAll(".graph-link"),
	    node = svg.selectAll(".graph-node");
	
	

    var tooltip = d3.select("body").append("div")
    .attr("class", "graph-tooltip")
    .style("opacity", 0);
    
    
  
    tooltip.style("left", (svg.node().offsetLeft) + "px")
    .style("top", (svg.node().offsetLeft) + "px");
	
	  
	  link = link
	    .data(dataLinks)
	    .enter()
	    .append("g")
	     .attr("class", "graph-link")
	    .append("line");
	     
	  
	  linkText = svg.selectAll(".graph-link")
	    .data(dataLinks)
	    .append("g");

	    // add link text, handle multiple verbs
	  	svg.selectAll(".graph-link g").each(function(d,i)
	  			{
	  					// VERB RENDERING
	  					var linkStr = d.link_verb;
	  					y=10;
	  					if ( linkStr!='' && linkStr.indexOf(",")!=-1)
	    				{
	  						verbArr =  linkStr.split(",");
	  						
	  						var renderedVerbIndex = 1;
	  						for(var v=0;v<verbArr.length;v++)
  							{
	  							oneVerbItem = verbArr[v];
	  							
	  							if ( oneVerbItem=='') continue;
	  							
	  							d3.select(this).append("text").text((renderedVerbIndex)+" - "+oneVerbItem).attr("relY",y);
	  							
	  							renderedVerbIndex++;
	  							y+=19;
  							}
	    				}
	  					else
  						{
	  						d3.select(this).append("text").text(linkStr).attr("relY",y);
  						}
	  					
	  			});


	
	  groupElement = node.data(dataNodes).enter().append("g")
	  .attr("class", "graph-node")
	            .on("mouseover", function(d) {
	            	   tooltip.style("display","block");
              tooltip.transition()
                   .duration(100)
                   .style("opacity", .8);
              
              var tooltipContent = "<b>"+d.word +"</b><br>";
              	  if (d.short_desc!="")
              	  {
              		  tooltipContent += ""+d.short_desc+"><br>";
              	  }
              	  if (d.long_desc!="")
              	  {
              		  var descStr = d.long_desc;
              		  if ( descStr.length > 400)
              			  {
              			descStr = descStr.substr(0,400)+"...";
              			  }
              		  tooltipContent += descStr+"<br>";
              	  }
              	  if (d.image_url!="")
              	  {
              		 
              		  tooltip.style("background-image","url('"+d.image_url+"')");
              	
              	  }
              	  else
              	  {
              		 tooltip.style("background-image","none");
              	  }
              		  
              		  
              	  if (d.external_link!="")
              	  {
              		  tooltipContent += "<a href='"+d.external_link+"' target='_new'>"+d.external_link+"</a><br>";
              	  }
              	  
              	 tooltip.html(tooltipContent);
              	  
        
          })
          .on("mouseout", function(d) {
        	  
        	  //tooltipRight = tooltip.node().offsetLeft+tooltip.node().offsetWidth;
        	  
      
             /*tooltip.transition()
                   .duration(500)
                   .style("opacity", 0);
              
              tooltip.style("display","none");*/
          })
	  .attr("transform", function(d){return "translate("+d.x+",50)"})
	     .call(force.drag);
	  
	 
	
	  groupElement.append("title").text(function(d) { return d.size});
	
	   /// NODE TEXT
	   groupElement.append("text")
	  .attr("dx", function(d) { var xDistance = (Math.log(d.size)*5)+20; if (xDistance < 20) { xDistance = 20 } return xDistance;} )
	  .attr("dy", function(d) {return 0} )
	  .attr("font-size", function(d) { var size = Math.log(d.size)*3; if (size < 12) { size = 12 } return size ;})
	  .attr("class", "graph-words")
	  .text(function(d) { return d.word });
	  
	   groupElement.append("circle")
	   .attr("r", function(d) { var size = Math.log(d.size)*5; if (size < 5) { size = 5 } return size;})
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
	
		  linkText.attr("x", function(l) 
				  { 
			  			var startPoint =l.source.x;
			  			if (l.target.x < l.source.x)
		  				{
			  				startPoint = l.target.x;
		  				}
			  		
			  			return  startPoint+( Math.abs(l.target.x-l.source.x)/2 );
			  
				  })
				  .attr("y", function(l) 
	    		  {
	    	  
			 	     var startPoint =l.source.y;
			  			if (l.target.y < l.source.y)
						{
			  				startPoint = l.target.y;
						}
			  		
	  				return  startPoint+( Math.abs(l.target.y-l.source.y)/2 );
	  			
	    		  });

		  
			svg.selectAll(".graph-link g text").attr("y", function(l) 
		    { 
	  			var startPoint =l.source.y;
	  			if (l.target.y < l.source.y)
  				{
	  				startPoint = l.target.y;
  				}
	  		
	  			return  (startPoint+parseInt(d3.select(this).attr("relY")));
	  
		     });
			
			svg.selectAll(".graph-link g text").attr("x", function(l) 
		    { 

	  			return  parseInt(d3.select(this.parentNode).attr("x"));
	  
		     });
			

		
		  groupElement.attr("cx", function(d) { return d.x; })
	      .attr("cy", function(d) { return d.y; });
	      
		   groupElement.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
	}
	
	
	
	
	
	force.start();
	


}
