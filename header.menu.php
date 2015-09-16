<?php 
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