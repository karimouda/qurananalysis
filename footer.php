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
	
	
	<div id='footer-sharing-section'>
	
		<span class='twitter-button-fixer' >
		<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?="http://".$_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI']?>" data-via="qurananalysis" data-count="none">Tweet</a>
		</span>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

		<div class="fb-like" data-href="<?="http://".$_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI']?>" data-layout="button" data-action="like" data-show-faces="true" data-share="true"></div>
	
	</div>

</div>