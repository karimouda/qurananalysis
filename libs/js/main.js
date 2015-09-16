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
	height = 300;
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


function drawGraph(jsonNodesData,jsonLinksData,width,height,targetGraphDiv,lang,showResultsDivID)
{

	
	// Next define the main object for the layout. We'll also
	// define a couple of objects to keep track of the D3 selections
	// for the nodes and the links. All of these objects are
	// initialized later on.
	
	var force = null,
	    nodes = null,
	    links = null;
	
	
	    var dataNodes = jsonNodesData;
	    

	    var nodesLength = dataNodes.length;

	
	    var dataLinks = jsonLinksData;
	    
	  
	
	
	    var linksLength = dataLinks.length;
	

	    // 200 average expected number of links
	    var gravity = (linksLength*20)/200;
	    
	    var friction = 0.8;
	   
	 
	    
	    if ( gravity < 0.01 )
    	{
	    	gravity = 0.01;
    	}
	    else if ( gravity > 5 )
    	{
	    	gravity = 5;
    	}	    
	    
	    if ( linksLength > 50 )
    	{
	    	friction = 0.2;
    	}
	  
	   // alert(gravity);
	
		var force = d3.layout.force()
	    .size([width, height])
	    .charge(-12000) //positive = attract /negative = repulse
	    .chargeDistance(height)
	     .linkDistance(function(l) 
		{ 
	    	 sourceSize = parseFloat(l.source.size);
	    	 targetSize = parseFloat(l.target.size);
	    	 
			linkDistance = height/((sourceSize+targetSize)/2);
			
	
			
			if ( linkDistance< 50 )
			{
				linkDistance = 50;
			}
			
			if ( linkDistance > 200 )
			{
				linkDistance = 200;
			}

		
			return linkDistance;
		})
		  .linkStrength(0.1)
	    .friction(friction) // tick velocity decay
	    .gravity(gravity)
	    .theta(0.1);
	
	
	
	//	   

	
//	/+(parseInt(l.source.size)+parseInt(l.target.size) )
		
		

	
		var zoomListener = d3.behavior.zoom()
	    .scaleExtent([0.1, 32])
	    .on("zoom", zooming);
	    
		
		
	  
		
	var svg = d3.select(targetGraphDiv).
	append("svg")
	    .attr("width", width)
	    .attr("height", height)
	    // for image rendering
	    .attr("xlink","http://www.w3.org/1999/xlink");
	/*
	 * 	     .call(zoomListener)
	     .on("dblclick.zoom", null);// disable zoom on double click
	 */
	  
		////// markers
		svg.append("defs")
		.selectAll("marker")
	    .data(["blueArrow"])
	    .enter()
	    .append("marker")
	    	// x y width height
	       .attr("viewBox", "0 -5 15 10")
	    .attr("id", "blueArrow")
	    .attr("refX", 40) // goes downwords
	    .attr("refY", 0)
	      .attr("markerWidth", 5)
	      .attr("markerHeight", 5)
	    .attr("orient", "auto")
	  .append("path")
	    .attr("d", "M0 -5 L0 5 L10 0 L0 -5")
	    .style("stroke", "#000")
	    .style("opacity", "1");
		//////////////////
	

	    var linktooltip = d3.select("body").append("div")
	    .attr("class", "graph-link-tooltip")
	    .style("opacity", 0);

	    
	

	
	var link = svg.selectAll(".graph-link"),
	    node = svg.selectAll(".graph-node");
	
	


    var tooltip = d3.select("body").append("div")
    .attr("class", "graph-tooltip")
    .style("opacity", 0);
    
    tooltip.append("div");
    
    tooltip.append("img")
    .attr("src","/images/close-icon-white.png")
    .attr("class","graph-tooltip-close")
	.on("click", function() {
		hideGraphTootip();
	});
    
  
    // svg.node().parentNode =  svg's div container
    tooltip.style("left", (svg.node().parentNode.offsetLeft-20) + "px")
    .style("top", (svg.node().parentNode.offsetTop+height-40) + "px");
	
    
  	function hideGraphTootip()
	{
		tooltip.transition().duration(400).style("opacity", 0).duration(100).style("display","none");
	}
	

//    var drag = force.drag()
//    .on("dragstart", dragTracker); 
    

    function showVerbTooltip(d3Obj)
    {
    	linktooltip.transition()
        .duration(100)
        .style("opacity", .9);
 		
 	
 		linktooltip.style("top",d3.event.pageY+(20)+"px");
 		linktooltip.style("left",d3.event.pageX+(20)+"px");
 		
 		linktooltip.html(d3Obj.select("text").text());
    }
	   
	  
	 
	   //alert(JSON.stringify(dataLinks));
	   /////// LINKS
	 
	  links = link
	    .data(dataLinks)
	    .enter()
	    .append("g")
	     .attr("class", "graph-link")
	       .on("mouseover", function(l) {
	 		
	 		
	 		//d3.select(this).select("text").attr("style","visibility:visible");
	 		
	 		//alert(d3.select(this).select("text").text());
	 		
	    	   
	 		showVerbTooltip(d3.select(this));
	 		
	 		
	  		
			 
	  	})
	  	.on("mouseout", function(l) {
	  		d3.select(this).select("text").attr("style","visibility:hidden");
	  		
	  		linktooltip.transition().duration(100).style("opacity", 0);
			 
	  	})
	  	.on("click", function(l) {
	  		
	  		//alert(l.link_verb);
	  			verbArr =  l.link_verb.split(",");
				var verbQueryStr = "";
				for(var v=0;v<verbArr.length;v++)
				{
					oneVerbItem = verbArr[v];
					
					
					if ( oneVerbItem=='') continue;
					
					//ignore them for now
					if ( oneVerbItem.indexOf("_")>0)  continue;
						
					/*if ( oneVerbItem.indexOf("_")>0) 
						{
						oneVerbItem = oneVerbItem.replace(/_/," ");
						}*/
					
					verbQueryStr = verbQueryStr +" "+oneVerbItem;
					
					
				}
				
				verbQueryStr = verbQueryStr.trim();
				
	  		showResultsForQueryInSpecificDiv(l.source.word+" "+verbQueryStr+" "+l.target.word+" CONSTRAINT:NODERIVATION CONSTRAINT:NOEXTENTIONFROMONTOLOGY",showResultsDivID);
	  	})
	    .append("line")
	    	      .style("stroke-width", 
					      	function(d) 
					      	{
	    	    	  				//alert(d.link_frequency +" "+d.link_verb);
				      				if ( d.link_frequency > 10 ) return 10;
			      				 	return d.link_frequency; 
			      			});
	   
	     
	  
	   
	  linkText = svg.selectAll(".graph-link")
	    .data(dataLinks)
	    .append("g");
	    
	  
	  //alert(JSON.stringify(dataLinks));

	    // add link text, handle multiple verbs
	  linkText.each(function(d,i)
	  			{
		  				var textObj = d3.select(this).append("text");
		  		
		  				
	  					// VERB RENDERING
	  					var linkStr = d.link_verb;
	  					y=10;
	  					if ( linkStr!=null &&  linkStr.length>0 && linkStr.indexOf(",")!=-1)
	    				{
	  						verbArr =  linkStr.split(",");
	  						
	  						var renderedVerbIndex = 1;
	  						for(var v=0;v<verbArr.length;v++)
 							{
	  							oneVerbItem = verbArr[v];
	  							
	  							if ( oneVerbItem=='') continue;
	  							
	  							textObj.text(oneVerbItem).attr("relY",y);
	  							
	  							renderedVerbIndex++;
	  							y+=19;
 							}
	    				}
	  					else
 						{
	  						textObj.text(linkStr).attr("relY",y);
 						}
	  					
	  					
	  					
	  			});
	  			
	  		linkText.attr("style","visibility:hidden");
	  	
	  	
	  	
	

	  	
	  	
		  groupElement = node.data(dataNodes).enter().append("g")
		  .attr("class", "graph-node")
		            .on("mouseover", function(d) {
		            	   tooltip.style("display","block");
	              tooltip.transition()
	                   .duration(100)
	                   .style("opacity", 0.90);
	              
	              var tooltipContent = "<b>"+d.word +"</b><br>";
	              	  if (d.short_desc!="")
	              	  {
	              		  tooltipContent += ""+d.short_desc+"<br>";
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
	              	  
	              	 tooltip.select("div").html(tooltipContent);
	              	 
	              	 
	              	//showVerbTooltip(d3.select(this));
	              	  
	        
	          })
	          .on("mouseout", function(d) {
	        	  
	        	  //tooltipRight = tooltip.node().offsetLeft+tooltip.node().offsetWidth;
	        	  
	        	  
	        	  //hide verb tooltip
	        	  //linktooltip.transition().duration(100).style("opacity", 0);
	      
	             /*tooltip.transition()
	                   .duration(500)
	                   .style("opacity", 0);
	              
	              tooltip.style("display","none");*/
	          })
	          .on("click", function(d) {
	  		
	        	  
	        	// comming from another event such as drag
	        	if (d3.event.defaultPrevented) return; 
	        	  
	        	word = "\""+d.word+"\"";
	        	
	        	  		showResultsForQueryInSpecificDiv(word,showResultsDivID);
	        
	        	  
	          })
	         .attr("transform", function(d){return "translate("+d.x+",50)"})
		     .call(force.drag);
		  
		 

		   
		  
		  groupElement.append("title").text(function(d) { return d.word});
		
		  // ORDER OF APPEND IS SIGNIFICANT - Z-INDEX LEVEL
		  groupElement.append("circle")
		   .attr("r", 
				   	function(d) 
				   	{
			   			var size = null;
			   			
			   			// main nodes
			   			if ( d.level==1)
		   				{
			   				size = (20+Math.log(d.size)*5)-(nodesLength*0.005);
			   				
			   				if ( size< 15)
		   					{
		   						size = 15;
		   					}
		   				}
			   			//
			   			else
			   			{
			   				size = 1;
			   			}
			   			
			   			
			   			return size;
			   		})
		   .style("fill", 	
				   function(d) 
				   {
			   			var color = 'red';
			   			
			   			// main nodes
			   			if ( d.level==2)
		  				{
			   				color = '#0663A8';
			   			}
		   			
		   			
			   			return color;
		   		  });
		  
		   /// NODE TEXT
		   groupElement.append("text")
		  .attr("dx",0 /*function(d) { var xDistance = (Math.log(d.size)*5); if (xDistance < 20) { xDistance = 20 } return xDistance;}*/ )
		  .attr("dy", function(d) {return 0} )
		  .attr("font-size", function(d) { var size = Math.log(d.size)*3; if (size < 9) { size = 9 } return size-(nodesLength*0.010) ;})
		  .attr("class", "graph-words")
		  .attr("text-anchor", "middle")
		  .style("fill",  function(d) 
				   {
			   			var fill = '#fff';
			   			
			   			// main nodes
			   			if ( d.level==2)
		  				{
			   				fill = '#0663A8';
			   			}
		   			
		   			
			   			return fill;
		   		  })
		  .text(function(d) { return d.word });
		  
		  
		   
		   
		   
	  	force
		  .nodes(dataNodes)
		   .links(svg.selectAll(".graph-link")
				    .data())
		   .on("tick",tick)
		   .start();
	  	
	
		  
	  	
		svg.append("image")
		.attr("width", "20px")
	    .attr("height", "20px")
	    .attr("x", width-20)
	    .attr("y", 10)
	    .attr("class", "svg-button")
	    .attr("xlink:href","/images/plus-zoomin.png")
	    .on("click", function(d) {
	    			zooming(-1);
	    		
	          });
		
		svg.append("image")
		.attr("width", "20px")
	    .attr("height", "20px")
	    .attr("x", width-20)
	    .attr("y", 30)
	    .attr("class", "svg-button")
	    .attr("xlink:href","/images/minus-zoomout.png")
	    .on("click", function(d) {
	    			zooming(1);
	          });
	    
		   
		  
		  //alert(JSON.stringify(dataNodes));
		 // alert(JSON.stringify(svg.selectAll(".graph-link").data()));
	   
	 /* groupElement
	      .each(function(d)
	    		  {
	    	  			alert(JSON.stringify(d));
	    		  });*/
	
	     var lastZoomingScale = 0;
	
		function zooming(zoomScale) 
		{
				
			
			 // alert(  d3.selectAll(".graph-node")+" "+d3.event.scale );
			 // svg.selectAll(".graph-node g").attr("transform", function(d){return "scale("+d3.event.scale+")"});
			/*  groupElement.attr("transform", 
					  function(d) 
					  { 
				  		
				  		
				  	
				  		return "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")";
				  		
			});*/
			
			
			force.stop();
			
			
			/*if (  zoomScale==undefined)
			{
				return;
				 zoomScale = d3.event.scale;
			}*/
			
			//alert(JSON.stringify(d3.event)+" "+zoomScale);
			
			//var scaleDiff = zoomScale-lastZoomingScale;
			//alert( scaleDiff  +" "+force.gravity() +" "+(force.gravity()+scaleDiff) );
			
			// negative zoomScale = decrease change = expand = zoom in , positive is the opposite
			force.gravity(  force.gravity()+(zoomScale*0.5));
			
		
			force.alpha(.05);
			
			lastZoomingScale = d3.event.scale;
			
			  
		}
	  
	  	function forceBoundingBoxWidth(x)
		{
			
			var innerPadding = 20;
			var newx = x;
			if ( x <= 0 )
			{
				newx =  innerPadding ;
			}
			else 
			if ( x > width-innerPadding )
			{
				newx =  width-(innerPadding*2) ;
			}
			
			
			return newx;
		}
		
		function forceBoundingBoxHeight(y)
		{
		
			var innerPadding = 20;
			var newy = y;
			if ( y <= 0 )
			{
				newy =  innerPadding ;
			}
			else 
			if ( y > height-innerPadding )
			{
				newy-=2// =  height-(innerPadding*2) ;
			}
			
			
			return newy;
		}

	
	  
	function tick()
	{
		

		 
		
	
	
		  groupElement.attr("transform", 
				  function(d) 
				  { 
			  		
			  		
			  		var newx = forceBoundingBoxWidth(d.x);
			  		var newy = forceBoundingBoxHeight(d.y);
			  		return "translate("+newx+","+newy+")"; 
			  		
		  		  });
		
	      
		   links.attr("x1", function(d) { return forceBoundingBoxWidth(d.source.x); })
	      .attr("y1", function(d) { return forceBoundingBoxHeight(d.source.y); })
	      .attr("x2", function(d) { return forceBoundingBoxWidth(d.target.x); })
	      .attr("y2", function(d) { return forceBoundingBoxHeight(d.target.y); });
	
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
			
		
		
		
	
	    	  				
	      
		
	
		   
		 
	}
}

	function showAjaxLoaderIn(divID)
	{
		var imgObj = $("<IMG/>");
		
		//created by http://www.ajaxload.info/
		imgObj.attr("src","/images/green-ajax-loader.gif");
		
		$("#"+divID).append(imgObj);
		
	}
	
	
	function showResultsForQueryInSpecificDiv(query,divID,script)
	{
		
		$("#"+divID).html("");
			
		showAjaxLoaderIn(divID);

			
			$.ajaxSetup({
				url:  "/search/dosearch.ajax.service.php?q="+encodeURIComponent(query)+"&script="+script,
				global: false,
				type: "GET"
				
			  });


			$.ajax({
				
				timeout: 60000,
				success: function(retRes)
						{
							
					  			$("#loading-layer").hide();
					      	 	
					  			$("#"+divID).html(retRes);

					  	
					  			var selectedField = $("#qa-sort-select option:selected").val();
								var currentOrder = $("#qa-sort-select option:selected").attr("sortorder");

								
								
								$('.result-aya-container').tsort({attr:selectedField, order: currentOrder});


					 	 	
					     },
				      	 error: function (xhr, ajaxOptions, thrownError)
				         {
				      		$(divID).html("<center>Error occured !</center>");
				      		$("#loading-layer").hide();
				         }
					});
						
			
			
			
			
			
	}
	
	
	function openPopupWindow(targetURL,windowWidth,windowHeight)
	{
		return window.open(targetURL,'_blank','scrollbars=yes,menubar=no,height='+windowHeight+',width='+windowWidth+',resizable=yes,toolbar=no,location=no,status=no');
	}
	
	
	function multiplyByRandomSign(number)
	{
		return Math.cos( Math.PI *  Math.random())*number;
	}

	function showBETAWarning(showInDivID)
	{
		var text ="BETA Caution: in addition to the beta-experimental nature of this website "+
				  "it is a human endeavour which can't be perfect and should NOT be considered truth or fact source";
		
		
		$("#"+showInDivID).html(text);
		$("#"+showInDivID).toggle();
	}

	function drawSearchWordCloud(drawInDivID)
	{
		  $("#"+drawInDivID+" a").tagcloud({ 
			     size: { 
			       start: 14, 
			       end: 62, 
			       unit: 'px',
			       
			     },
			     color: {start: '#000', end: '#C0DE22'}
			  }); 
	}
	
	function scrollToTop()
	{
		

		$('html, body').animate({scrollTop: '0px'}, 1000);
	}
	
	
	function showBrowserSupportErrorMessage(showIn)
	{
		$("#"+showIn).prepend("<div id='browser-support-issue-msg'>Sorry ! Your browser does not support the technology used in this website<br>Please use the newest version of Chrome, Firefox or Safari </div>")
		
	}
