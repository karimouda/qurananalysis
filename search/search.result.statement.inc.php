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