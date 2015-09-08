<?php 
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

								
							$verseText = getVerseTextBySuraAndAya($firstAnswerVerseArr['SURA']+1, $firstAnswerVerseArr['AYA']+1);
						
							
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







