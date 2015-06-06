<?php

?>
<div id='footer-section'>

<a href="/info/about.php" class='footer-item'>About</a> /
<a href="/info/resources.php" class='footer-item'>Resources</a> /
<a href="/info/credits.php" class='footer-item'>Credits</a> /
<a href="/info/contact.php" class='footer-item'>Contact</a>

<br>

<div id='footer-statement'>
Quran Analysis, 2015
</div>

	<?php 

	if ( isDevEnviroment())
	{
		$debug =  ((memory_get_usage(true)/1024)/1024)."/".((memory_get_peak_usage(true)/1024)/1024)."Memory <br>";
		echo $debug;
	}

?>

</div>