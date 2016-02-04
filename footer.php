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