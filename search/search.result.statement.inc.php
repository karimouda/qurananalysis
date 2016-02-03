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
<div id='search-results-summary'>

<table id='search-results-summary-table'>
<tr>
<td>
<span>  <?php echo $resultStatsArr['VERSES_COUNT']?> verses found </span> - 
</td>
<td style='text-align: left;'>
<span> Searched for "<?php echo join(" ",array_unique(array_keys($extendedQueryWordsArr)))?>" </span>
</td>

</tr>
</table>
<div id='result-share-link-area'>
<a id='result-share-link' href="javascript:showShareLink('<?=getSharingLinkForQuery(urlencode($originalQuery))?>')">share</a>

</div>


 <div id="sharing-link-panel" >
 		<div id="sharing-link-panel-close-buttom" onclick="closeSharingPanel()">
			<img src='/images/close-icon-black.png' />
		</div>
		
		<h1 id="sharing-link-title">Share Result Link</h1>
		<p id='sharing-link-panel-linkholder'>
		</p>
 </div>
</div>