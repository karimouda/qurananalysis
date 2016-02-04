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
<div id='header'>

<?php
if (  $_SERVER['REMOTE_ADDR']!="127.0.0.1" )
{
?>
	 <script>

 	 (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-61137992-1', 'auto');
	  ga('send', 'pageview');

	</script>

<?php
}
?>


<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=1519982634960013";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>



<div id="header-panel">
	
	
	<table>
		<tr>
			<td>
					<a href="javascript:showBETAWarning('homepage-important-messages-area')" id="home-beta-button"  >BETA!</a>
			</td>
			<td>
					<a href="/" ><b>Home</b></a>
			</td>
			<td>
					<a href="/info/index.php?page=about" >About</a>
			</td>	
			<td>
					<a href="/info/index.php?page=contribute" style="color:red" >Contribute</a>
			</td>
			<td>
					<a href="/info/index.php?page=feedback" style="color:red" >Feedback</a>
			</td>
			<td>
					<a href="/info/index.php?page=faq" >Faq</a>
			</td>
			<td>
					<a href="/info/index.php?page=resources" >Resources</a>
			</td>
			<td>
					<a href="/info/index.php?page=credits" >Credits</a>
			</td>	
			<td>
					<a href="/info/index.php?page=contact" >Contact</a>
			</td>	
			<!-- <td>
					<a href="javascript:;" >
						عربي
					</a>
			</td>
			 -->
			<td>
					<div id='copyr-statement'>
						© 2015
					</div>
			</td>
		</tr>
	</table>

	
	
	


</div>

<div  id='homepage-important-messages-area'>

</div>

</div>
