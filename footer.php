<?php

?>
<div id='footer-section'>



	<?php 
	
		if ( isDevEnviroment())
		{
			$debug =  ((memory_get_usage(true)/1024)/1024)."/".((memory_get_peak_usage(true)/1024)/1024)."MB Memory <br>";
			//echo $debug;
		}
	
	?>

</div>