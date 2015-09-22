<?php 
require_once("../global.settings.php");



	



$lang = "AR";

if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}


loadModels("core",$lang);



?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Quran Analysis | Basic Statistics </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Full Analytics System for the Quran">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	 
	<script type="text/javascript">
	</script>
     
       
  </head>
  <body>
		<?php 
				require("./analysis.template.start.code.php");
		
		?>		
  		
  <div id='main-container'>
			  	
				
			

		
					
	    <?php include_once("help-content.php"); ?>
	    
	    <?php 
	    
	    $TOTALS = getModelEntryFromMemory($lang, "MODEL_CORE", "TOTALS", "");
	    ?>

	
		<fieldset> 		
		
			 <legend>General Totals</legend>
			   	
			  		<table class='analysis-table'>
			  			<thead>
			  				<tr>
				  				<th>
				  					Category
				  				</th>
				  				<th>
				  					Total
				  				</th>
				  			</tr>
			  			</thead>
			  			<tbody>
			  				<tr>
			  					<td>Chapters</td><td><?=$TOTALS['SURAS']?></td>
			  				</tr>
			  				<tr>
			  					<td>Verses</td><td><?=$TOTALS['VERSES']?></td>
			  				</tr>
			  				<tr>
			  					<td>Words</td><td><?=$TOTALS['WORDS']?></td>
			  				</tr>
			  				<tr>
			  					<td>Non-Repeated Words</td><td><?=$TOTALS['NRWORDS']?></td>
			  				</tr>
		  					<tr>
			  					<td>Letters</td><td><?=$TOTALS['CHARS']?></td>
			  				</tr>
			  				<?php if ( $lang=="AR"):?>
		  					<tr>
			  					<td>Number of Pause Marks</td><td><?=count($TOTALS['PAUSEMARKS'])?></td>
			  				</tr>
		  					
		  					 <tr>
			  					<td>Pause Marks Count</td><td><?=$TOTALS['PAUSEMARKS_AGGREGATION']?></td>
			  				</tr>	
			  				<!-- -->		  				
			  				
		  					<tr>
			  					<td>Sajdat Tilawa count ۩</td><td><?=$TOTALS['SAJDAT_TELAWA']['COUNT']?></td>
			  				</tr>
		  					<tr>
			  					<td>Sakta Latifa ۜ  count</td><td><?=$TOTALS['SAKTA_LATIFA']['COUNT']?></td>
			  				</tr>
			  				<?php endif;?>
			  				<tr>
			  					<td>Min Word Length (chars)</td><td><?=$TOTALS['MIN_WORD_LENGTH']?></td>
			  				</tr>
			  				<tr>
			  					<td>Min Word</td><td><?=$TOTALS['MIN_WORD']?></td>
			  				</tr>			  				
			  				<tr>
			  					<td>Max Word Length (chars)</td><td><?=$TOTALS['MAX_WORD_LENGTH']?></td>
			  				</tr>					  				
			  				<tr>
			  					<td>Max Word</td><td><?=$TOTALS['MAX_WORD']?></td>
			  				</tr>			  				
			  				<tr>
			  					<td>Avg Word Length (chars)</td><td><?=$TOTALS['AVG_WORD_LENGTH']?></td>
			  				</tr>			  	
			  				<tr>
			  					<td>Min Verse Length (words)</td><td><?=$TOTALS['MIN_VERSE_LENGTH']?></td>
			  				</tr>
			  				<tr>
			  					<td>Min Verse </td><td><?=$TOTALS['MIN_VERSE']?></td>
			  				</tr>			  				
			  				<tr>
			  					<td>Max Verse Length (words)</td><td><?=$TOTALS['MAX_VERSE_LENGTH']?></td>
			  				</tr>					  				
			  				<tr>
			  					<td>Max Verse</td><td><?=substr($TOTALS['MAX_VERSE'],0,104)."..."?></td>
			  				</tr>			  				
			  				<tr>
			  					<td>Avg Verse Length (words)</td><td><?=$TOTALS['AVG_VERSE_LENGTH']?></td>
			  				</tr>

				  		
	
			  			</tbody>
			  			
			  		</table>
			  		<?php if ( $lang=="AR"):?>
			  		<table class='analysis-table'>
			  			<thead>
			  				<tr>
				  				<th>
				  					Pause Marks Count
				  				</th>
				  				<th>
				  					Total
				  				</th>
				  			</tr>
			  			</thead>
			  			<tbody>
							<?php 
								foreach ($TOTALS['PAUSEMARKS'] as $pmLabel => $pmCount )
								{
							?>
			  					<tr>
				  					<td><?=$pmLabel?></td><td><?=$pmCount?></td>
				  				</tr>			  				
			  				<?php 
								}
			  				?>
		  	
			  			</tbody>
			  			
			  		</table>
			  		<?php endif;?>
			  		
				 </fieldset>		  		
		  		 <fieldset>
		  		 
  				    <legend>Chapters</legend>
			  		
						<table class='analysis-table'>
			  			<thead>
			  				<tr>
				  				<th>
				  					Chapter Index
				  				</th>
				  				<th>
				  					Chapter Name
				  				</th>
				  				<th>
				  					Verses
				  				</th>
				  				<th>
				  					Words
				  				</th>
				  				<th>
				  					Chars
				  				</th>
				  			</tr>
			  			</thead>
			  			<tbody>
							<?php 
								foreach ($TOTALS['TOTAL_PER_SURA'] as $suraIndex => $perSuraArr )
								{
							?>
			  					<tr>
			  						<td><?=$suraIndex+1?></td>
				  					<td><?=$perSuraArr['NAME']?></td>
				  					<td><?=$perSuraArr['VERSES']?></td>
				  					<td><?=$perSuraArr['WORDS']?></td>
				  					<td><?=$perSuraArr['CHARS']?></td>
				  				</tr>			  				
			  				<?php 
								}
			  				?>
		  	
			  			</tbody>
			  			
			  		</table>
			  		
			  </fieldset>
			  	
			
   </div>
   
		<?php 
				require("./analysis.template.end.code.php");
		
		?>		
		
	<script type="text/javascript">

				
		$(document).ready(function()
		{


		
		});
		
	</script>
		

	<?php 
		require("../footer.php");
	?>
	

  </body>
</html>







