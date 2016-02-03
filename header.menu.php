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
$logo = "quran-analysis-logo.png";

if ( !empty($_GET['logo']) )
{
	$logo = $_GET['logo'];
}
?>
<a href='/'><img id='main-logo' src="/images/<?=$logo ?>" title='QuranAnalysis.com Logo' alt='QuranAnalysis.com Logo'/></a>
<div id='main-sections'>
			
 			<div id='section-item-search' class='section-item' >
  				<a href='/'>Search</a>
  			</div>
  			<div id='section-item-explore' class='section-item'>
  				<a href='/explore/'>Explore</a>
  			</div>
  			<div id='section-item-analysis' class='section-item'>
  				<a href='/analysis/'>Analyze</a>
  			</div>
  		</div>