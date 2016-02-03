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
require("../global.settings.php");
require_once("../libs/core.lib.php");

require_once("../libs/question.answering.lib.php");


$query = $_GET['q'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>QA Accuracy Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quran Semantic-based Search, Analysis & Expert System">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>

	

	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/qe/favicon.png">
      	 
	<script type="text/javascript">
	</script>


  </head>
  <body>
  

  
  <div id='main-container'>
			  	
			  	<h1> Question Answering Accuracy Test</h1>
			<?php 
			
				
			
					function answerQuestion($testQuery)
					{
						$isInTestScript = true;

						require("../search/query.handling.common.php");
						
						$answered = "No";
						
						$answerArr = null;
						if ( !empty($userQuestionAnswerConceptsArr)  )
						{
							echo "\"$testQuery\"| Yes| Yes| ".join(" AND ",$userQuestionAnswerConceptsArr)."<br>";
						}
						else
						if ( !empty($userQuestionAnswerVersesArr) )
						{
							
							$firstAnswerVerseArr = current($userQuestionAnswerVersesArr);

								
							$verseText = getVerseTextBySuraAndAya("EN",$firstAnswerVerseArr['SURA']+1, $firstAnswerVerseArr['AYA']+1);
						
							
							echo "\"$testQuery\"| Yes| Yes| ".$verseText."<br>";
							
							
						}
						else
						{
							echo "\"$testQuery\", No, No, NA.<br>";
						}
						
						
						
						
					}
					
					answerQuestion("How long should I breastfeed my child for ?");
					answerQuestion("What allah loves ?");
					answerQuestion("What are the attributions of Allah ?");
					answerQuestion("When was the Quran Revealed ?");
					answerQuestion("Animals in the Quran ?");
					answerQuestion("How many signs were sent to pharaoh ?");
					answerQuestion("What did Allah said to Adam ?");
					answerQuestion("What are the colors in the Quran ?");
					answerQuestion("Who is the prophet whom Allah spoke to ?");
					answerQuestion("Fruits in Heaven ?");
					answerQuestion("Number of wives allowed in Islam ?");
					answerQuestion("Who are the people of the Book ?");
					
			?>

	
			  	<div id="loading-layer">
			  		Loading ...
			  	</div>
		
			 
   </div>
   

	<script type="text/javascript">


		$(document).ready(function()
		{


			

		
		});




 


	</script>






	
  </body>
</html>







