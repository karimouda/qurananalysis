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
require_once(dirname(__FILE__)."/../global.settings.php");

/**
 * 
 * Replace undrscore with space for multi-word concepts
 */
function cleanWordnetCollocation($str)
{
	return strtr($str,"_", " ");
}

function isExcludableSemanticType($type)
{
	$type = trim(strtolower($type));
	
	//as in lexnames
	if ( $type=="ppl" ||  $type=="all" || $type=="tops" || $type=="pert" )
	{
		return true;
	}
	
	return false;
}

function getSymbolDescriptionFromMappingTable($symbol)
{
	//for nouns
	$SYMBOL_MAPPING_TABLE["!"]="Antonym";
	$SYMBOL_MAPPING_TABLE["@"]="Hypernym";
	$SYMBOL_MAPPING_TABLE["@i"]="Instance Hypernym";
	$SYMBOL_MAPPING_TABLE["~"]="Hyponym";
	$SYMBOL_MAPPING_TABLE["~i"]="Instance Hyponym";
	$SYMBOL_MAPPING_TABLE["#m"]="Member holonym";
	$SYMBOL_MAPPING_TABLE["#s"]="Substance holonym";
	$SYMBOL_MAPPING_TABLE["#p"]="Part holonym";
	$SYMBOL_MAPPING_TABLE["%m"]="Member meronym";
	$SYMBOL_MAPPING_TABLE["%s"]="Substance meronym";
	$SYMBOL_MAPPING_TABLE["%p"]="Part meronym";
	$SYMBOL_MAPPING_TABLE["="]="Attribute";
	$SYMBOL_MAPPING_TABLE["+"]="Derivationally related form";
	$SYMBOL_MAPPING_TABLE[";c"]="Domain of synset - TOPIC";
	$SYMBOL_MAPPING_TABLE["-c"]="Member of this domain - TOPIC";
	$SYMBOL_MAPPING_TABLE[";r"]="Domain of synset - REGION";
	$SYMBOL_MAPPING_TABLE["-r"]="Member of this domain - REGION";
	$SYMBOL_MAPPING_TABLE[";u"]="Domain of synset - USAGE";
	$SYMBOL_MAPPING_TABLE["-u"]="Member of this domain - USAGE";

	/// for verbs
	$SYMBOL_MAPPING_TABLE["*"]="Entailment";
	$SYMBOL_MAPPING_TABLE[">"]="Cause";
	$SYMBOL_MAPPING_TABLE["^"]="Also see";
	$SYMBOL_MAPPING_TABLE["$"]="Verb Group";


	/// for adjectives
	$SYMBOL_MAPPING_TABLE["&"]="Similar to";
	$SYMBOL_MAPPING_TABLE["<"]="Participle of verb";
	$SYMBOL_MAPPING_TABLE["\\"]="Pertainym (pertains to noun)/Derived from adjective ";


	/// for adverbs
	$SYMBOL_MAPPING_TABLE["!"]="Entailment";
	$SYMBOL_MAPPING_TABLE[">"]="Cause";
	$SYMBOL_MAPPING_TABLE["^"]="Also see";
	$SYMBOL_MAPPING_TABLE["$"]="Verb Group";


	return $SYMBOL_MAPPING_TABLE[$symbol];
}


function getLexicoSemanticCategories()
{
	global $wordnetDir;

	$lexographerArr = array();

	$fileArr = file("$wordnetDir/lexnames");

	foreach($fileArr as $line)
	{
		$lineArr = explode("\t", $line);
		$lexId = $lineArr[0];
		$lexSemDesc = $lineArr[1];

		$lexographerArr[$lexId] = $lexSemDesc;

	}

	return $lexographerArr;
}

function populateIndexArrByPoS(&$wordnetIndex,$pos)
{
	global $wordnetDir;
	

	
	
	
	$fileName = "$wordnetDir/index.$pos";
	
	if (!file_exists($fileName) )
	{
		echoN("File not found! [$fileName]");
		return false;
	}
	
	$fileHandle = fopen($fileName, "r");
	
	$lineCounter = 0;
	while(!feof($fileHandle))
	{
		$line = fgets($fileHandle);
		
		if ( $lineCounter++ < 29) continue;
		
		//https://wordnet.princeton.edu/wordnet/man/wndb.5WN.html#sect3
		//lemma  pos  synset_cnt  p_cnt  [ptr_symbol...]  sense_cnt  tagsense_cnt   synset_offset  [synset_offset...] 
		$lineArr = explode(" ", $line);
		
		$lemma = cleanWordnetCollocation($lineArr[0]);
		
		//$numberOfEntriesForLemma = count($wordnetIndex[$lemma]);
		
		
		
		$wordnetIndex[$lemma][$pos] = array();
		
		$synsetCount = $lineArr[2];
		
		$wordnetIndex[$lemma][$pos]['SYNSETS']=array();
		
		$pointerCount = $lineArr[3];
		
		$wordnetIndex[$lemma][$pos]['POINTERS_TYPES']=array();
		
		$synsetPoimtersArr = array();
		
		$currentArrIndex = 4;
		for($i=0;$i< $pointerCount;$i++)
		{
			$singlePointer = $lineArr[$currentArrIndex+$i];
			
			$symbolDesc = getSymbolDescriptionFromMappingTable($singlePointer);
			
			//https://wordnet.princeton.edu/wordnet/man/wninput.5WN.html
			$wordnetIndex[$lemma][$pos]['POINTERS_TYPES'][$singlePointer]=$symbolDesc;
			
			$synsetPoimtersArr[$i]=$singlePointer;
		}
		
		$currentIndex =$currentArrIndex+$i;
		
		$notUsedSenseCount =  $lineArr[$currentIndex++];
		$notUsedTagsenset =   $lineArr[$currentIndex++];
		
		
		for($i=0;$i< $synsetCount;$i++)
		{
			$singleSynsetPointer = $lineArr[$currentIndex+$i];
			//https://wordnet.princeton.edu/wordnet/man/wninput.5WN.html
			$wordnetIndex[$lemma][$pos]['SYNSETS'][$i]=$singleSynsetPointer;
		}
		
		
		$wordnetIndex[$lemma][$pos]['POS']=$pos;
		
		
	}
	
	
	
	fclose($fileHandle);
	
	return $indexArr;
	
}



function getSynsetDataByPoS($pos)
{
	global $wordnetDir;
	
	$dataArr = array();
	
	
	
	$fileName = "$wordnetDir/data.$pos";
	
	if (!file_exists($fileName) )
	{
		echoN("File not found! [$fileName]");
		return false;
	}
	
	$fileHandle = fopen($fileName, "r");
	
	$lineCounter = 0;
	while(!feof($fileHandle))
	{
		$line = fgets($fileHandle);
	
		if ( $lineCounter++ < 29) continue;
	
		//https://wordnet.princeton.edu/wordnet/man/wndb.5WN.html#sect3
		//synset_offset  lex_filenum  ss_type  w_cnt  word  lex_id  [word  lex_id...]  p_cnt  [ptr...]  [frames...]  |   gloss

		// separate glossary 
		$lineSplit1Arr =  explode("|", $line);
		
		$gloss = $lineSplit1Arr[1];
		
		$lineArr = explode(" ", $lineSplit1Arr[0]);
	
		$offset = ($lineArr[0]);
		
		$lexicoSemanticCategoryID = $lineArr[1];
		
		$pos = $lineArr[2];
		
		$numberOfWordsInSynset = intval($lineArr[3]);

	
		$dataArr[$offset] = array();
		
		$dataArr[$offset]["GLOSSARY"]=$gloss;
		
		$dataArr[$offset]["SEMANTIC_CATEGORY_ID"]=$lexicoSemanticCategoryID;
		
		
		
		
		$dataArr[$offset]["POS"] = $pos;
		
		$dataArr[$offset]["WORDS"] = array();;
	

		
	
		$currentArrIndex = 4;
		for($i=0;$i< $numberOfWordsInSynset;$i++)
		{
			
			$word = cleanWordnetCollocation($lineArr[$currentArrIndex++]);
			
			//not sure what is this
			$wordLexId = $lineArr[$currentArrIndex++];

			$dataArr[$offset]["WORDS"][$word]=$wordLexId;
		}
	

		$pointerCount =  intval($lineArr[$currentArrIndex++]);


	
		for($i=0;$i< $pointerCount;$i++)
		{
			$pointerSymbol = $lineArr[$currentArrIndex++];
			$pointerSynsetOffset = $lineArr[$currentArrIndex++];
			$pointerPoS = $lineArr[$currentArrIndex++];
			$pointerSourceTarget = $lineArr[$currentArrIndex++];
			
			$symbolDesc = getSymbolDescriptionFromMappingTable($pointerSymbol);
			$pointerArr = array("SYMBOL"=>$pointerSymbol,"SYNSET_OFFSET"=>$pointerSynsetOffset,
			"POS"=>$pointerPoS,"SOURCE_TARGET"=>$pointerSourceTarget,"SYMBOL_DESC"=>$symbolDesc);
			
			$dataArr[$offset]['POINTERS'][$i]=$pointerArr;
		}
	
	
	
	}
	
	
	
		fclose($fileHandle);
	
		return $dataArr;
}

function getLongPoSName($smallPOS)
{
	$trans = array("n" => "noun", "v" => "verb", "a" => "adj", "s" => "adj", "r" => "adv");
	return  strtr($smallPOS,$trans);
}

function loadWordnet(&$MODEL_WORDNET)
{

	
	
	if (  apc_fetch("WORDNET_INDEX") == false)
	{
		 $wordnetDir = array();
		 $wordnetIndex = array();
		 
		 $lexicoSemanticCategories = getLexicoSemanticCategories();
		 
		 $posesArr = array("noun","verb","adj","adv");
		
		 foreach($posesArr as $pos)
		 {
			 populateIndexArrByPoS($wordnetIndex,$pos);
		 }
		
		 
		 //preprint_r($lexicoSemanticCategories);
		 //preprint_r($wordnetIndex);
		 
		 foreach($posesArr as $pos)
		 {
			 $dataArr[$pos]= getSynsetDataByPoS($pos);
		 }
		 
		//preprint_r($dataArr);
		
		 $res = apc_store("WORDNET_INDEX",$wordnetIndex);
		 
		 if ( $res===false){ throw new Exception("Can't cache WORDNET_INDEX"); }
		 
		 $MODEL_WORDNET['INDEX'] = $wordnetIndex;
		 
		 $res = apc_store("WORDNET_LEXICO_SEMANTIC_CATEGORIES",$lexicoSemanticCategories);
		
		 
		 if ( $res===false){ throw new Exception("Can't cache WORDNET_INDEX"); }
		 
		 $MODEL_WORDNET['LEXICO_SEMANTIC_CATEGORIES'] = $lexicoSemanticCategories;
	
		 $res = apc_store("WORDNET_DATA",$dataArr);
		 
		 if ( $res===false){ throw new Exception("Can't cache WORDNET_DATA"); }
		 
		 $MODEL_WORDNET['DATA'] = $dataArr;
		 
	}
	
	return true;
	
}

function getWordnetEntryByWordString($wordToSearchFor, $includeOnlyRelationsOfType="")
{
	global $MODEL_WORDNET;
	
	if ( empty($MODEL_WORDNET)) { throw  new Exception("Wordnet module is not loaded!"); }
	if (empty($wordToSearchFor)) return false;
	


	$wordToSearchFor = strtolower($wordToSearchFor);
	

	
	$wordnetInfoArr = array();
	$wordnetInfoArr['SYNONYMS']=array();
	$wordnetInfoArr['SEMANTIC_TYPES']=array();
	$wordnetInfoArr['RELATIONSHIPS']=array();
	$wordnetInfoArr['WORD']=$wordToSearchFor;
	
	// Not found in Wordnet
	if ( !isset($MODEL_WORDNET['INDEX'][$wordToSearchFor])) return false;
	


	foreach( $MODEL_WORDNET['INDEX'][$wordToSearchFor] as $pos => $currIndexArr)
	{
	
		
		
			$wordIndexEntryArr = $currIndexArr;
			
			//preprint_r($wordIndexEntryArr);
			
			
			
			//$pos =$wordIndexEntryArr['POS'];;
			
			$wordnetInfoArr['POS'][$pos]=1;
			
			// each synset in INDEX
			foreach($wordIndexEntryArr['SYNSETS'] as $index => $fileOffset)
			{
				$entryArr = $MODEL_WORDNET['DATA'][$pos][$fileOffset];
				
		
				
				
				if ( !isset($wordnetInfoArr['SYNONYMS'][$pos]))
				{
					$wordnetInfoArr['SYNONYMS'][$pos]  = array_keys($entryArr['WORDS']);
				}
				else 
				{
					$wordnetInfoArr['SYNONYMS'][$pos]  = array_merge(array_keys($entryArr['WORDS']),$wordnetInfoArr['SYNONYMS'][$pos]);
				}
				
				$lexicoSemanticCategoryID =$entryArr['SEMANTIC_CATEGORY_ID'];
				
				$semanticType = $MODEL_WORDNET['LEXICO_SEMANTIC_CATEGORIES'][$lexicoSemanticCategoryID];
			
				$semanticType = ucfirst(substr($semanticType, strpos($semanticType, ".")+1));
				
				$wordnetInfoArr['SEMANTIC_TYPES'][$pos][$index]=$semanticType;
				
				$wordnetInfoArr['GLOSSARY'][$pos] = $entryArr['GLOSSARY'];;
				
				// EACH POINTER IN THE CURRENT SYNSET
				foreach($entryArr['POINTERS'] as $index2 => $pointersArr)
				{
					$pointerOffset = $pointersArr['SYNSET_OFFSET'];
					$relationName = $pointersArr['SYMBOL_DESC'];
					$pointerPoS = getLongPoSName($pointersArr['POS']);
					
					
					$pointerEntryArr = $MODEL_WORDNET['DATA'][$pointerPoS][$pointerOffset];
					
					$pointerGLoss = $pointerEntryArr['GLOSSARY'];
					$pointerWordsArr = $pointerEntryArr['WORDS'];
					
					$pointerWordsEditedArr = array();
					foreach($pointerWordsArr as $word=>$dummy)
					{
						$word = cleanWordnetCollocation(ucfirst($word));
						$pointerWordsEditedArr[$word]=1;
					}
					
					
					$pointerSemanticCatID =  $pointerEntryArr['SEMANTIC_CATEGORY_ID'];
					
					$wordnetInfoArr['RELATIONSHIPS'][$pos][] = array("RELATION"=>$relationName,
														"WORDS"=>$pointerWordsEditedArr,
														"SEMANTIC_CATEGORY_ID"=>$pointerSemanticCatID,
														"GLOSSARY"=>$pointerGLoss
					);
					
				}
				
			}
			
			$wordnetInfoArr['SYNONYMS'][$pos] = array_unique($wordnetInfoArr['SYNONYMS'][$pos]);
			
			
			
			
			
	}
	
	
	return $wordnetInfoArr;
	
	
	
	
}

function getGlossaryFirstPart($glossary)
{

	$semiColumnLoc = strpos($glossary, ";");
	
	if ( $semiColumnLoc!==false)
	{
		return trim(substr($glossary, 0, $semiColumnLoc));
	}

		return $glossary;
	
}






?>
