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
require_once("../global.settings.php");
require_once("../libs/core.lib.php");

loadModels("core,ontology", "AR");


$LEMMA_TO_SIMPLE_WORD_MAP = loadLemmaToSimpleMappingTable();


function showExcludeFor($type, $value,$secIndex=1,$savedValue=null)
{
	

?>
	<div id='<?=$type."_".$secIndex?>'>
	<h2><?=$value?></h2>
	<?php 
	if ( $type=="images")
	{
		?>
			<img src='<?=$value?>'/>	
			<br>
			
		<?php 
		}
	?>
	
	<?php 
	
	
	if ( !empty($savedValue))
	{
		$value = $savedValue;
	}
	

	?>
	<input type='button' value='Exclude' onclick="exclude('<?=$type?>','<?=trim(htmlentities(addslashes($value),ENT_QUOTES,"UTF-8"))?>','<?=$secIndex?>')" />
	<hr/>
	</div>
<?php 
}

printHTMLPageHeader();

$baseDir = dirname(__FILE__)."/../data/ontology/extraction/cleaner/";

$currentConceptIndex = $_GET['currConceptIndex'];

if ( empty($currentConceptIndex)  )
{

	//// get current index from file
	$currentConceptIndex = file_get_contents("$baseDir/concept.index");
	
	if ( empty($currentConceptIndex))
	{
		$currentConceptIndex=0;
	}
	else
	{
		$currentConceptIndex = (intval($currentConceptIndex));
	}

	/////////////////////////////////

}

/////// save concept index
$baseDir = dirname(__FILE__)."/../data/ontology/extraction/cleaner/";

$resBytes = file_put_contents("$baseDir/concept.index", ($currentConceptIndex)."\n");

if (empty($resBytes)) 
{
	echoN("CONCEPT INDEX NOT SAVED !");
	exit;
}

/////////////////////////////////





///////// LOAD EXCLUDED RELATIONS //////////////

$excludedRelationsArr = file("$baseDir/excluded.relations",FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
///preprint_r($excludedRelationsArr);
///////////////////////////////////////////////////

///////// LOAD EXCLUDED SHORTDESC //////////////

$excludedShortDescArr = file("$baseDir/excluded.shortdesc",FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
//preprint_r($excludedShortDescArr);
//() can't be found by in array
///////////////////////////////////////////////////

//$conceptsArr = $MODEL_QA_ONTOLOGY['CONCEPTS'];

$qaOntologyConceptsIterator = getAPCIterator("ALL\/MODEL_QA_ONTOLOGY\/CONCEPTS\/.*");

$conceptCount = $qaOntologyConceptsIterator->getTotalCount();


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>QA Ontology Cleaner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quran Semantic-based Search, Analysis & Expert System">
    <meta name="author" content="">

	<script type="text/javascript" src="<?=$JQUERY_PATH?>" ></script>
	<script type="text/javascript" src="<?=$MAIN_JS_PATH?>"></script>
	<script type="text/javascript" src="<?=$D3_PATH?>"></script>
	<script type="text/javascript" src="<?=$TINYSORT_PATH?>"></script>
	<script type="text/javascript" src="<?=$TINYSORT_JQ_PATH?>"></script>	
	<script type="text/javascript" src="<?=$JQUERY_TAGCLOUD_PATH?>" ></script> 
	

	<link rel="stylesheet" href="/qe.style.css?bv=<?=$BUILD_VERSION?>" />
	<link rel="icon" type="image/png" href="/qe/favicon.png">
      	 
	<script type="text/javascript">
	</script>


  </head>
  <body>

<div style='width:100%;padding:10px;background-color:#eee;'>
<div id='loading-layer'>Excluding ...</div>
<span><b>CONCEPT #<?=$currentConceptIndex?>/<?=$conceptCount?></b></span>
</div>
<input type='button' value='Next Concept >> ' onclick="showNextConcept()" style='width:100%;padding:10px;'/>
<br>
<?php

if ( $currentConceptIndex==$conceptCount)
{
	echoN("END");
	exit;
}
//preprint_r($conceptsArr);


// move to the current index
for($i=0;$i<$currentConceptIndex;$i++)
{
	if ( $qaOntologyConceptsIterator->valid() )
	{
		$qaOntologyConceptsIterator->next();
	}
	
}



$conceptToBeCleaned = $qaOntologyConceptsIterator->current();

$conceptToBeCleaned = $conceptToBeCleaned['value'];



$labelAr = $conceptToBeCleaned['label_ar'];
$labelEn = $conceptToBeCleaned['label_en'];

echoN("CONCEPT - ".$conceptToBeCleaned['pos']." ".(($conceptToBeCleaned['is_qurana_ngram_concept']==1)?"Qurana":""));

echoN("<b>$labelEn</b>");
showExcludeFor("concepts",$labelAr);





$shortDescEN = trim($conceptToBeCleaned['meaning_wordnet_en']);

if (!empty($shortDescEN) && !(in_array($shortDescEN,$excludedShortDescArr)))
{
	echoN("SHORT DESC EN");
	showExcludeFor("shortdesc",$shortDescEN);
}

$shortDescAR = trim($conceptToBeCleaned['meaning_wordnet_translated_ar']);

if (!empty($shortDescAR) && !(in_array($shortDescAR,$excludedShortDescArr)))
{
	echoN("SHORT DESC AR");
	showExcludeFor("shortdesc",$shortDescAR,2);

}


$wikipediaAR = $conceptToBeCleaned['wikipedia_link'];

if (!empty($wikipediaAR))
{
	echoN("LINK");

	showExcludeFor("links",$wikipediaAR);
}


$imageURL = $conceptToBeCleaned['image_url'];

if (!empty($imageURL))
{
	echoN("IMAGE");

	showExcludeFor("images",$imageURL);
}


$longDescEN = $conceptToBeCleaned['long_description_en'];

if (!empty($longDescEN))
{
	echoN("LONG DESC EN");
	
	// will be removed by label
	showExcludeFor("longdesc",$longDescEN,1,$labelAr);
}

$longDescAR = $conceptToBeCleaned['long_description_ar'];

if (!empty($longDescAR))
{
	echoN("LONG DESC AR");
	// will be removed by label
	showExcludeFor("longdesc",$longDescAR,2,$labelAr);
}




$sIndex = 1;
if ( isset($conceptToBeCleaned['synonym_'.($sIndex)]) )
{
	echoN("SYNONYM");
	
	while(isset($conceptToBeCleaned['synonym_'.($sIndex)]))
	{
		$synonym = $conceptToBeCleaned['synonym_'.($sIndex)];
		
		if (!empty($synonym))
		showExcludeFor("synonyms",$synonym,$sIndex);
		
		$sIndex++;
		
	}

}


echoN("RELATIONS");

$qaRelationsArr = getModelEntryFromMemory("ALL", "MODEL_QA_ONTOLOGY", "RELATIONS", "");



$relIndex =0;
foreach($qaRelationsArr as $hash => $relArr)
{
	$subject = $relArr['SUBJECT'];
	$object = $relArr['OBJECT'];
	$verb = $relArr['VERB'];
	
	$relationStr = "$subject,$verb,$object";
	

	//excluded before
	if ( in_array($relationStr,$excludedRelationsArr))
	{
		continue;
	}
	
	if ( $subject!=$labelAr &&  $object!=$labelAr)
	{
		continue;
	}	
	
	$relIndex++;
	
	$text = "$subject,$verb,$object";
	
	showExcludeFor("relations", $text,$relIndex);
}

?>
<br>
<br>

<input type='button' value='Next Concept >> ' onclick="showNextConcept()" style='width:100%;padding:10px;'/>
<br>
<br>
<script>
function showNextConcept()
{
	location.href='<?=$_SERVER['PHP_SELF']."?currConceptIndex=".($currentConceptIndex+1)?>';
	
}

function exclude(type,text,secIndex)
{
	

		
		$("#loading-layer").show();

		
		$.ajaxSetup({
			url:  "/admin/ontology-cleaner-exclude.service.ajax.php?type="+encodeURIComponent(type)+"&value="+encodeURIComponent(text),
			global: false,
			type: "GET"
			
		  });


		$.ajax({
			
			timeout: 6000,
			success: function(retRes)
					{
						
				  			$("#loading-layer").hide();

				  		
				  			$("#"+type+"_"+secIndex).hide();

				  		
				  		if ( retRes!=undefined && retRes.trim().length>0)
				  		{
					  		
				  			alert("ERROR:|"+retRes.trim()+"|");
				  		}

				 	 	
				     },
			      	 error: function (xhr, ajaxOptions, thrownError)
			         {
			      		alert("Error occured ! \n"+thrownError);
			      		$("#loading-layer").hide();
			         }
				});
					
		
		
		
		
		
}

</script>

</body>
</html>