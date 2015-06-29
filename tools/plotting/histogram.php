<?php 
session_start();
$dataArr = $_SESSION['PLOTTING_DATA'];

//print_r($dataArr);


?>
<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
    	  var data = new google.visualization.DataTable();
    	  data.addColumn('string', 'Key');
    	  data.addColumn('number', 'Value');
    	  data.addRows([
    	    <?php 
    	    foreach( $dataArr as $key=>$val ):
    	    ?>
			['<?=$key?>',<?=$val?>],
    	    <?php endforeach;?>

    	  ]);

        var options = {
          title: 'Data Dsitribution',
          legend: { position: 'none' },
          histogram: { bucketSize: 1 },
 
        
        };

        var chart = new google.visualization.Histogram(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
  </body>
</html>