<?php 

	
	function preprint_r($arr)
	{
		echo "<pre>".print_r($arr,true)."</pre>";
	}
	
	function echoN($str)
	{
		echo($str."\n<br>");
	}
	
	function printHTMLPageHeader()
	{
		echo("<html lang='en-US'><head><title>Print Page</title><meta charset='utf-8'></head><body>");
	}
	
	function multibyteStringOrdinal($str)
	{
	
		$ordinals = array();
		$convertedStr = mb_convert_encoding($str,"UCS-4BE","UTF-8");
	
	
		for($i=0;$i<mb_strlen($convertedStr,"UCS-4BE"); $i++)
		{
		
			$char = mb_substr($convertedStr,$i,1,"UCS-4BE");
			$charDecValArr = unpack("N",$char);
			 
			//preprint_r($charDecValArr);
			 
			$ordinals[] = $charDecValArr[1];
		}
		 
		return $ordinals;
	}
		
	function multibyteCharOrdinal($str)
	{       

		$ordinals = array();
	    $convertedStr = mb_convert_encoding($str,"UCS-4BE","UTF-8");
        $char = mb_substr($convertedStr,0,1,"UCS-4BE");  
        
        $charDecValArr = unpack("N",$char);    
   
        //preprint_r($charDecValArr);
	    
	    return $charDecValArr[1];
	}
	
	function showHiddenChars($text,$lang="AR")
	{
		 
		if ( $lang=="AR")
		{
				for($index=0;$index<mb_strlen($text);$index++)
				{
					$chr =mb_substr($text,$index,1);
					echoN($chr."|".urlencode($chr));
					}
				}
		else
		{
				for($index=0;$index<strlen($text);$index++)
				{
					$chr =substr($text,$index,1);
					echoN($chr."|".urlencode($chr));
				}
		}
			exit;
	
				
	}
	
	function addCommasToNumber($numStr)
	{
		$negativeFlag=false;
		if ( strpos($numStr,"-")!==false)
		{
			$negativeFlag=true;
			$numStr = substr($numStr,1);
		}
		
		$len = strlen($numStr);
		
		$strArr = str_split($numStr);
		//print_r($strArr);
		
		$newNumStr = "";
		$couner =0;
		for($i=$len-1;$i>=0;$i--)
		{
			$newNumStr .=  $strArr[$i];
			$couner++;
			
			if ( $couner%3==0 && $i!=0)
			{
				$newNumStr .=",";
			}
		}
		
		$newNumStr = strrev($newNumStr);
		
		if ( $negativeFlag==true)
		{
			$newNumStr = "-".$newNumStr;
		}
		
		return $newNumStr;	
	}
	

	function rsortBy(&$arrayToSort, $field)
	{
		
		
		 uasort($arrayToSort, function($a, $b) use ($field) {
			
			//**** 1 and -1 are switched to make the reverse functionality
			if ($a[$field]==$b[$field] )
			{
				return -0;
			}
			else if ($a[$field]>$b[$field]  )
			{
				return -1;
			}
			else
			{
				return 1;
			}
			
		});
	
		
	
	}
	
	


	function shuffle_assoc(&$arr)
	{
		$keys = array_keys($arr);
		
		shuffle($keys);
		
		foreach ($keys as $key)
		{
		    $shuffled_array[$key] = $arr[$key];
		}

		$arr = $shuffled_array;
	}
	
	function removeTashkeel($str)
	{
		return preg_replace("/[\x{0618}-\x{061A}\x{064B}-\x{0654}\x{0670}\x{06DC}\x{06DF}\x{06E0}\x{06E2}\x{06E3}\x{06E5}\x{06E6}\x{06E8}\x{06EA}-\x{06ED}]/um","",$str);
		
	
		/* UNICODE REFERENCE
		 * http://www.fileformat.info/info/unicode/char/0618/index.htm
		 * 0618 = ARABIC SMALL FATHA
		 * 0619 = ARABIC SMALL DAMMA
		 * 061a = ARABIC SMALL KASRA
		 * 
		 * 064B = ARABIC FATHATAN
		 * 064C = ARABIC DAMMATAN
		 * 064D = ARABIC KASRATAN
		 * 064E = ARABIC FATHA
		 * 064F = ARABIC DAMMA
		 * 0650 = ARABIC KASRA
		 * 0651 = ARABIC SHADDA
		 * 0652 = ARABIC SUKUN
		 * 0653 = ARABIC MADDAH ABOVE
		 * 0654 = ARABIC HAMZA ABOVE
		 * 
		 * 0670 = ARABIC LETTER SUPERSCRIPT ALEF - ٰ
		 * 0671 = ARABIC LETTER ALEF WASLA - ٱ
		 * 
		 * 06DC = ARABIC SMALL HIGH SEEN
		 * 06DF = ARABIC SMALL HIGH ROUNDED ZERO
		 * 06E0 = ARABIC SMALL HIGH UPRIGHT RECTANGULAR ZERO
		 * 06E2 = ARABIC SMALL HIGH MEEM ISOLATED FORM
		 * 06E3 = ARABIC SMALL LOW SEEN
		 * 06E5 = ARABIC SMALL WAW
		 * 06E6 = ARABIC SMALL YEH
		 * 06E8 = ARABIC SMALL HIGH NOON
		 * 
		 * 06EA = ARABIC EMPTY CENTRE LOW STOP
		 * 06EB = ARABIC EMPTY CENTRE HIGH STOP
		 * 06EC = ARABIC ROUNDED HIGH STOP WITH FILLED CENTRE
		 * 06ED = ARABIC SMALL LOW MEEM
		 * 
		 */
	}
	
	function stripBOM($str)
	{
		$BOM =  chr(239) . chr(187) . chr(191);
		return trim(($str),$BOM);
	}
	
	function cleanAndTrim($str)
	{
		//« spoils arabic words = 0xab
		$tobeReplacedStr = "\t\n\r\0\x0B~!$%&;^*()+=-<>?\"',”“.][»";
		return trim(trim($str),$tobeReplacedStr);
	}

	
	function isArabicString($str)
	{
			
			
		$mbResult = false;
		$arabicChars = "ء|آ|أ|ؤ|إ|ئ|ا|ب|ة|ت|ث|ج|ح|خ|د|ذ|ر|ز|س|ش|ص|ض|ط|ظ|ع|غ|ف|ق|ك|ل|م|ن|ه|و|ى|ي|٫|ٮ|ٯ";
		$mbResult = mb_ereg("[$arabicChars]+", $str);
		
		
		if ($mbResult===FALSE)
		{
			$arabicCharsPresentationForms = "ﺇ|ﺆ|ﺅ|ﺄ|ﺃ|ﺂ|ﺁ|ﺀ|ﺟ|ﺞ|ﺝ|ﺜ|ﺛ|ﺚ|ﺙ|ﺘ|ﺗ|ﺖ|ﺕ|ﺔ|ﺓ|ﺒ|ﺑ|ﺐ|ﺏ|ﺎ|ﺍ|ﺌ|ﺋ|ﺊ|ﺉ|ﺈ|ﺷ|ﺶ|ﺵ|ﺴ|ﺳ|ﺲ|ﺱ|ﺰ|ﺯ|ﺮ|ﺭ|ﺬ|ﺫ|ﺪ|ﺩ|ﺨ|ﺧ|ﺦ|ﺥ|ﺤ|ﺣ|ﺢ|ﺡ|ﺠ|ﻏ|ﻎ|ﻍ|ﻌ|ﻋ|ﻊ|ﻉ|ﻈ|ﻇ|ﻆ|ﻅ|ﻄ|ﻃ|ﻂ|ﻁ|ﺿ|ﺾ|ﺽ|ﺼ|ﺻ|ﺺ|ﺹ|ﺸ|ﻧ|ﻦ|ﻥ|ﻤ|ﻣ|ﻢ|ﻡ|ﻠ|ﻟ|ﻞ|ﻝ|ﻜ|ﻛ|ﻚ|ﻙ|ﻘ|ﻗ|ﻖ|ﻕ|ﻔ|ﻓ|ﻒ|ﻑ|ﻐ|ﻼ|ﻻ|ﻺ|ﻹ|ﻷ|ﻸ|ﻶ|ﻵ|ﻴ|ﻳ|ﻲ|ﻱ|ﻰ|ﻯ|ﻮ|ﻭ|ﻬ|ﻫ|ﻪ|ﻩ|ﻨ";
			$mbResult = mb_ereg("[$arabicCharsPresentationForms]+", $str);
		}
			
		return ($mbResult!==FALSE);
	
	
	
	}
	

	
	/** My own implementation of Levenstein algorithm since the official one is not multilingual**/
	function myLevensteinEditDistance($word1,$word2)
	{
		$word1Length  = mb_strlen($word1);
		$word2Length  = mb_strlen($word2);
		
		$matrix = array();
		
		for($i=0;$i<$word1Length+1; $i++)
		{
			for($v=0;$v<$word2Length+1; $v++)
			{
				if ($i==0 ) $matrix[$i][$v] =$v;
				else if ($v ==0 )$matrix[$i][$v] =$i;
				else $matrix[$i][$v] =0;
			}
			
		}
		
		for($i=1;$i<$word1Length+1; $i++)
		{
			for($v=1;$v<$word2Length+1; $v++)
			{
				$charIWord1 = mb_substr($word1,$i-1,1);
				$charIWord2 = mb_substr($word2,$v-1,1);
				
				
				
				$substitutionCost = ($charIWord1!=$charIWord2) ? 1 : 0;
				
				//echoN("$charIWord1 $charIWord2 $substitutionCost");
				
				$matrix[$i][$v] = min(array($matrix[$i-1][$v]+1,$matrix[$i][$v-1]+1,$matrix[$i-1][$v-1]+$substitutionCost));
			}
				
		}
		
		//print2dArray($matrix);
		

		
		return $matrix[$i-1][$v-1];
		
	}
	
	function getHammingDistance($word1,$word2)
	{
		$distance=0;
		
		for($i=0;$i<mb_strlen($word1); $i++)
		{
			
			$charIWord1 = mb_substr($word1,$i,1);
			$charIWord2 = mb_substr($word2,$i,1);
		
			
			if ( $charIWord1!=$charIWord2)
			{
				$distance++;
			}
		}
		
		return $distance;
			
	}
	
	function getDistanceBetweenWords($word1,$word2)
	{
		$distance=0;
		
		$word1Length  = mb_strlen($word1);
		$word2Length  = mb_strlen($word2);
		
		if ( $word1Length==$word2Length)
		{
			//echoN("EQUAL");
			return getHammingDistance($word1,$word2);
		}
		else
		{

			return myLevensteinEditDistance($word1,$word2);

		}
	}
	
	function print2dArray($match2dArray)
	{
		
		if (empty($match2dArray)){ return null;}
		
		for ($i=0;$i<count($match2dArray);$i++)
		{
			
			
			for ($v=0;$v<count($match2dArray[$i]);$v++)
			{
				echo $match2dArray[$i][$v]."|";
				
			}
			
			echo "<br>\n";
		}
			echo "<br>\n";
	}
	
	/* Return the other supported language which should be different from the current one
	*  Mainly EN/AR 
	*/
	function toggleLanguage($currentLang)
	{
		
		$otherLanguage = "AR";
		
		if ($currentLang=="AR" )
		{
			return "EN";
		}
		
		return $otherLanguage;
		
	}
	

	function getStopWordsArrByFile($stopWordsFile)
	{
		$stopWordsArrTemp = file($stopWordsFile,FILE_SKIP_EMPTY_LINES  | FILE_IGNORE_NEW_LINES);
		

		$stopWordsArr = array();
		foreach($stopWordsArrTemp as $stopWord)
		{
			/* dont use cleanAndTrim here .. it will remove "'"   in you'ar 
			 * also it will spoil JSON ENCODING for some reason i don't know 
			 */
			$stopWord = (stripBOM($stopWord));
			$stopWordsArr[$stopWord] = 1;
		}
		
		return $stopWordsArr;
	}
	
	function getPauseMarksArrByFile($pauseMarksFile)
	{

		$pauseMarksArrTemp = file($pauseMarksFile,FILE_SKIP_EMPTY_LINES  | FILE_IGNORE_NEW_LINES);
		
		$pauseMarksArr  = array();
		
		
		foreach($pauseMarksArrTemp as $pauseMark)
		{
		
			$pauseMark = trim(($pauseMark));
		
			//echoN(multibyteCharOrdinal($pauseMark));
		
			$pauseMarksArr[$pauseMark]=1;
		}
		
		return $pauseMarksArr;
	}
	
	
	/**
	 * Arabic To Buckwalter translation based on table on QAC website
	 * http://corpus.quran.com/java/buckwalter.jsp
	 *
	 *
	 * @param String $transliteratedStr
	 * @return string
	 */
	function arabicToBuckwalter($arabicStr)
	{
	
		$buckwalterStr = "";
	
		$BUCKWATER_EXTENDED_MAP = array();
			

		$BUCKWATER_EXTENDED_MAP[1569]="'";
		$BUCKWATER_EXTENDED_MAP[1571]=">";
		$BUCKWATER_EXTENDED_MAP[1572]="&";
		$BUCKWATER_EXTENDED_MAP[1573]="<";
		$BUCKWATER_EXTENDED_MAP[1574]="}";
		$BUCKWATER_EXTENDED_MAP[1575]="A";
		$BUCKWATER_EXTENDED_MAP[1576]="b";
		$BUCKWATER_EXTENDED_MAP[1577]="p";
		$BUCKWATER_EXTENDED_MAP[1578]="t";
		$BUCKWATER_EXTENDED_MAP[1579]="v";
		$BUCKWATER_EXTENDED_MAP[1580]="j";
		$BUCKWATER_EXTENDED_MAP[1581]="H";
		$BUCKWATER_EXTENDED_MAP[1582]="x";
		$BUCKWATER_EXTENDED_MAP[1583]="d";
		$BUCKWATER_EXTENDED_MAP[1584]="*";
		$BUCKWATER_EXTENDED_MAP[1585]="r";
		$BUCKWATER_EXTENDED_MAP[1586]="z";
		$BUCKWATER_EXTENDED_MAP[1587]="s";
		$BUCKWATER_EXTENDED_MAP[1588]="$";
		$BUCKWATER_EXTENDED_MAP[1589]="S";
		$BUCKWATER_EXTENDED_MAP[1590]="D";
		$BUCKWATER_EXTENDED_MAP[1591]="T";
		$BUCKWATER_EXTENDED_MAP[1592]="Z";
		$BUCKWATER_EXTENDED_MAP[1593]="E";
		$BUCKWATER_EXTENDED_MAP[1594]="g";
		$BUCKWATER_EXTENDED_MAP[1600]="_";
		$BUCKWATER_EXTENDED_MAP[1601]="f";
		$BUCKWATER_EXTENDED_MAP[1602]="q";
		$BUCKWATER_EXTENDED_MAP[1603]="k";
		$BUCKWATER_EXTENDED_MAP[1604]="l";
		$BUCKWATER_EXTENDED_MAP[1605]="m";
		$BUCKWATER_EXTENDED_MAP[1606]="n";
		$BUCKWATER_EXTENDED_MAP[1607]="h";
		$BUCKWATER_EXTENDED_MAP[1608]="w";
		$BUCKWATER_EXTENDED_MAP[1609]="Y";
		$BUCKWATER_EXTENDED_MAP[1610]="y";
		$BUCKWATER_EXTENDED_MAP[1611]="F";
		$BUCKWATER_EXTENDED_MAP[1612]="N";
		$BUCKWATER_EXTENDED_MAP[1613]="K";
		$BUCKWATER_EXTENDED_MAP[1614]="a";
		$BUCKWATER_EXTENDED_MAP[1615]="u";
		$BUCKWATER_EXTENDED_MAP[1616]="i";
		$BUCKWATER_EXTENDED_MAP[1617]="~";
		$BUCKWATER_EXTENDED_MAP[1618]="o";
		$BUCKWATER_EXTENDED_MAP[1619]="^";
		$BUCKWATER_EXTENDED_MAP[1620]="#";
		$BUCKWATER_EXTENDED_MAP[1648]="`";
		$BUCKWATER_EXTENDED_MAP[1649]="{";
		$BUCKWATER_EXTENDED_MAP[1756]=":";
		$BUCKWATER_EXTENDED_MAP[1759]="@";
		$BUCKWATER_EXTENDED_MAP[1760]="\"";
		$BUCKWATER_EXTENDED_MAP[1762]="[";
		$BUCKWATER_EXTENDED_MAP[1763]=";";
		$BUCKWATER_EXTENDED_MAP[1765]=",";
		$BUCKWATER_EXTENDED_MAP[1766]=".";
		$BUCKWATER_EXTENDED_MAP[1768]="!";
		$BUCKWATER_EXTENDED_MAP[1770]="-";
		$BUCKWATER_EXTENDED_MAP[1771]="+";
		$BUCKWATER_EXTENDED_MAP[1772]="%";
		$BUCKWATER_EXTENDED_MAP[1773]="]";
	
			

	
		
	
		for($index=0;$index<mb_strlen($arabicStr);$index++)
		{
			$char =mb_substr($arabicStr,$index,2);
				
		
			$ordinal = multibyteCharOrdinal($char);
			
			// stop if char not a buckwalter char or space
			if ( !isset($BUCKWATER_EXTENDED_MAP[$ordinal]) && $char!=" " && $char!="2" )
			{
				throw new Exception("Strign contains invalid chars [$char]");
			}
			
			$buckwalterStr = $buckwalterStr . $BUCKWATER_EXTENDED_MAP[$ordinal];
		}
			
			
			
		return $buckwalterStr;
	
	
	}
		
	/**
	 * Buckwalter reverse translation based on table on QAC website
	 * http://corpus.quran.com/java/buckwalter.jsp
	 * 
	 * 
	 * @param String $transliteratedStr 
	 * @return string
	 */
	function buckwalterReverseTransliteration($transliteratedStr)
	{
	
			$reverseTransliteratedStr = "";
		
			$BUCKWATER_EXTENDED_MAP = array();
			
			$BUCKWATER_EXTENDED_MAP["'"]=1569;
			$BUCKWATER_EXTENDED_MAP[">"]=1571;
			$BUCKWATER_EXTENDED_MAP["&"]=1572;
			$BUCKWATER_EXTENDED_MAP["<"]=1573;
			$BUCKWATER_EXTENDED_MAP["}"]=1574;
			$BUCKWATER_EXTENDED_MAP["A"]=1575;
			$BUCKWATER_EXTENDED_MAP["b"]=1576;
			$BUCKWATER_EXTENDED_MAP["p"]=1577;
			$BUCKWATER_EXTENDED_MAP["t"]=1578;
			$BUCKWATER_EXTENDED_MAP["v"]=1579;
			$BUCKWATER_EXTENDED_MAP["j"]=1580;
			$BUCKWATER_EXTENDED_MAP["H"]=1581;
			$BUCKWATER_EXTENDED_MAP["x"]=1582;
			$BUCKWATER_EXTENDED_MAP["d"]=1583;
			$BUCKWATER_EXTENDED_MAP["*"]=1584;
			$BUCKWATER_EXTENDED_MAP["r"]=1585;
			$BUCKWATER_EXTENDED_MAP["z"]=1586;
			$BUCKWATER_EXTENDED_MAP["s"]=1587;
			$BUCKWATER_EXTENDED_MAP["$"]=1588;
			$BUCKWATER_EXTENDED_MAP["S"]=1589;
			$BUCKWATER_EXTENDED_MAP["D"]=1590;
			$BUCKWATER_EXTENDED_MAP["T"]=1591;
			$BUCKWATER_EXTENDED_MAP["Z"]=1592;
			$BUCKWATER_EXTENDED_MAP["E"]=1593;
			$BUCKWATER_EXTENDED_MAP["g"]=1594;
			$BUCKWATER_EXTENDED_MAP["_"]=1600;
			$BUCKWATER_EXTENDED_MAP["f"]=1601;
			$BUCKWATER_EXTENDED_MAP["q"]=1602;
			$BUCKWATER_EXTENDED_MAP["k"]=1603;
			$BUCKWATER_EXTENDED_MAP["l"]=1604;
			$BUCKWATER_EXTENDED_MAP["m"]=1605;
			$BUCKWATER_EXTENDED_MAP["n"]=1606;
			$BUCKWATER_EXTENDED_MAP["h"]=1607;
			$BUCKWATER_EXTENDED_MAP["w"]=1608;
			$BUCKWATER_EXTENDED_MAP["Y"]=1609;
			$BUCKWATER_EXTENDED_MAP["y"]=1610;
			$BUCKWATER_EXTENDED_MAP["F"]=1611;
			$BUCKWATER_EXTENDED_MAP["N"]=1612;
			$BUCKWATER_EXTENDED_MAP["K"]=1613;
			$BUCKWATER_EXTENDED_MAP["a"]=1614;
			$BUCKWATER_EXTENDED_MAP["u"]=1615;
			$BUCKWATER_EXTENDED_MAP["i"]=1616;
			$BUCKWATER_EXTENDED_MAP["~"]=1617;
			$BUCKWATER_EXTENDED_MAP["o"]=1618;
			$BUCKWATER_EXTENDED_MAP["^"]=1619;
			$BUCKWATER_EXTENDED_MAP["#"]=1620;
			$BUCKWATER_EXTENDED_MAP["`"]=1648;
			$BUCKWATER_EXTENDED_MAP["{"]=1649;
			$BUCKWATER_EXTENDED_MAP[":"]=1756;
			$BUCKWATER_EXTENDED_MAP["@"]=1759;
			$BUCKWATER_EXTENDED_MAP["\""]=1760;
			$BUCKWATER_EXTENDED_MAP["["]=1762;
			$BUCKWATER_EXTENDED_MAP[";"]=1763;
			$BUCKWATER_EXTENDED_MAP[","]=1765;
			$BUCKWATER_EXTENDED_MAP["."]=1766;
			$BUCKWATER_EXTENDED_MAP["!"]=1768;
			$BUCKWATER_EXTENDED_MAP["-"]=1770;
			$BUCKWATER_EXTENDED_MAP["+"]=1771;
			$BUCKWATER_EXTENDED_MAP["%"]=1772;
			$BUCKWATER_EXTENDED_MAP["]"]=1773;
				
			
	
			

	
			//echoN(strlen($transliteratedStr));

			for($index=0;$index<strlen($transliteratedStr);$index++)
			{
				$char =substr($transliteratedStr,$index,1);
					
				// stop if char not a buckwalter char or space
				if ( !isset($BUCKWATER_EXTENDED_MAP[$char]) && $char!=" " && $char!="2" )
				{
					throw new Exception("Input string is not pure Buckwalter transliteration! [$char]");
				}
				/*
				 * Comments from Stackoverflow about the conversion below
				* UCS-4BE is a Unicode encoding which stores each character as a 32-bit (4 byte) integer. This accounts for the "UCS-4"; the "BE" prefix indicates that the integers are stored in big-endian order. The reason for this encoding is that, unlike smaller encodings (like UTF-8 or UTF-16), it requires no surrogate pairs -- each character is a fixed size.
				* http://stackoverflow.com/questions/11304582/searching-for-a-good-unicode-compatible-alternative-to-the-php-ord-function/11304763#11304763
				*/
				
					
				// convert from decimal to binary string in 'UCS-4BE' encoding
				// N  = unsigned long (always 32 bit, big endian byte order)
				// http://php.net/manual/en/function.pack.php
				$char = (pack('N',$BUCKWATER_EXTENDED_MAP[$char]));
					
				// convert from 32 bit encoding to Arabic "UTF-8"
				$char = mb_convert_encoding($char, "UTF-8", 'UCS-4BE');
					
				//echoN("|".$char."|");
				
				$reverseTransliteratedStr = $reverseTransliteratedStr . $char;
			}
			
			
			
			return $reverseTransliteratedStr;
		
		
	}
	
	function stripHTMLComments($xmlContent)
	{
		return (preg_replace('/<!--.*-->/s', "", $xmlContent));

	}
	
	function getQACSegmentByQuranaSeqment($qacMasterSegmentTable,$suraID,$verseID,$verseLocalSegmentIndex,$quranaSegmentForm)
	{
		$masterIDPrefix = "$suraID:$verseID:";
		
		$currentSegment = 0;
		$currentWordIndex = 1;
		
		$masterID = $masterIDPrefix.$currentWordIndex;
		
		//preprint_r($qacMasterSegmentTable);
		
		$matchingSegmentLocation = -1;
		
		while( isset($qacMasterSegmentTable[$masterID]))
		{
			$segmentsInWord = $qacMasterSegmentTable[$masterID];
			
			
			
			foreach($segmentsInWord as $segmentArr)
			{
				
				
				$currentSegment++;
				
				//echoN("MASTER ID:$masterID SEG:$currentSegment PASSED SEG:$verseLocalSegmentIndex");
				
				//echoN("$quranaSegmentForm - ".$segmentArr['FORM_AR']);
				
				/*if ( $quranaSegmentForm== $segmentArr['FORM_AR'])
				{
					return $segmentArr['SEGMENT_INDEX'];
				}*/
				
				if ( $currentSegment== $verseLocalSegmentIndex)
				{
					$matchingSegmentLocation = $segmentArr['SEGMENT_INDEX'];;
				}
				
			}
			
			$currentWordIndex++;
			$masterID = $masterIDPrefix.$currentWordIndex;
			
		}
		
		return $matchingSegmentLocation;
		
	}
	function getWordIndexByQACSegment($qacMasterSegmentTable,$suraID,$verseID,$segmentID)
	{
		$masterIDPrefix = "$suraID:$verseID:";
		
		$currentSegment = 0;
		$currentWordIndex = 1;
		
		$masterID = $masterIDPrefix.$currentWordIndex;
		
		
		while( isset($qacMasterSegmentTable[$masterID]))
		{
			$segmentsInWord = $qacMasterSegmentTable[$masterID];
			
			foreach($segmentsInWord as $segmentArr)
			{
				
				
				$currentSegment = $segmentArr['SEGMENT_INDEX'];;
				
				echoN("MASTER ID:$masterID SEG:$currentSegment PASSED SEG:$segmentID");
				
				if ( $segmentID== $currentSegment)
				{
					return $currentWordIndex;
				}
				
			}
			
			$currentWordIndex++;
			$masterID = $masterIDPrefix.$currentWordIndex;
			
		}
		
		return null;
		
		
		
	}
	
	function addToInvertedIndex(&$INVERTED_INDEX,$word,$suraID,$verseID,$wordIndex,$wordType,$extraInfo=null)
	{
		if (!isset($INVERTED_INDEX[$word]) ) $INVERTED_INDEX[$word] = array();
		
		$indexInAyaName = "INDEX_IN_AYA_EMLA2Y";
		
		if ($wordType!="NORMAL_WORD")
		{
			$indexInAyaName = "INDEX_IN_AYA_UTHMANI";
		}
		
		$INVERTED_INDEX[$word][] = array("SURA"=>$suraID,"AYA"=>$verseID,"$indexInAyaName"=>$wordIndex,"WORD_TYPE"=>"$wordType","EXTRA_INFO"=>$extraInfo);
		
	
	}
	
	function getVerseByQACLocation($MODEL_CORE,$qac3PartLocationStr)
	{
		
		
		if ( strpos($qac3PartLocationStr,":")===false)
		{
			throw new Exception("Invalid QAC location");
		}
		
		//echoN($qac3PartLocationStr);
		$locationArr = preg_split("/\:/", $qac3PartLocationStr);
			
		//preprint_r($locationArr);
			
		$suraID =  $locationArr[0];
		
		$verseID =  $locationArr[1];
			
		$wordIndex =  $locationArr[2];
		
		return $MODEL_CORE['QURAN_TEXT'][($suraID-1)][($verseID-1)];
			
		
		
	}
	
	function  getWordIndexFromQACLocation($qac3PartLocationStr)
	{
		$locationArr = preg_split("/\:/", $qac3PartLocationStr);

			
		return  $locationArr[2];
	}
	
	function markSpecificWordInText($TEXT,$wordIndex,$charsToBeMarked,$markingTagName)
	{
		global $MODEL_CORE;
		
		$wordsArr = preg_split("/ /", $TEXT);
		
		//preprint_r($wordsArr);
		
		$wordsArr = removePauseMarksFromArr($MODEL_CORE['TOTALS']['PAUSEMARKS'],$wordsArr);
		
		
		
		$wordsArr[$wordIndex] = preg_replace("/(".$charsToBeMarked.")/mui", "<$markingTagName>\\1</$markingTagName>", $wordsArr[$wordIndex],1);
		
		//preprint_r($wordsArr);
		
		 $TEXT = implode(" ", $wordsArr);
		 
		 return $TEXT;
		 
		 //echoN($TEXT);
		 
		 //exit;
	}
	
	function getQACLocationStr($sura,$aya,$wordIndex)
	{
		return "$sura:$aya:$wordIndex";
	}
	
	function isDevEnviroment()
	{
		return ($_SERVER['REMOTE_ADDR']=="127.0.0.1" );
	}
	
	function isPauseMark($value,$pauseMarksArr,$saktaLatifaMark,$sajdahMark)
	{
		
		
		return (isset($pauseMarksArr[$value]) || $value == $saktaLatifaMark || $value == $sajdahMark);
	}
	function removePauseMarksFromArr($pauseMarksArr,$targetArr)
	{
		global $saktaLatifaMark,$sajdahMark;

		$newArr = array();

	
	
			foreach($targetArr as $index => $value)
			{
				if ( !isPauseMark($value,$pauseMarksArr,$saktaLatifaMark,$sajdahMark) )
				{
					$newArr[]=$value;
				
				}
			
			}
	
		return $newArr;
	}
	
	
	function getImla2yWordIndexByUthmaniLocation($uthmaniQACLocation,$UTHMANI_TO_SIMPLE_LOCATION_MAP)
	{
		
		$locationArr = preg_split("/\:/", $uthmaniQACLocation);
		
		$suraAyaBaseLocation = $locationArr[0].":".$locationArr[1];
		
		$emla2tWordIndex = $UTHMANI_TO_SIMPLE_LOCATION_MAP[$suraAyaBaseLocation][($locationArr[2])];
			
		return  $emla2tWordIndex;
	}
	
	function getWordFromVerseByIndex($MODEL_CORE,$verseText, $oneBasedWordIndex)
	{
		$wordsArr = preg_split("/ /", $verseText);

	
		$wordsArr = removePauseMarksFromArr($MODEL_CORE['TOTALS']['PAUSEMARKS'],$wordsArr);
	
		
		return $wordsArr[$oneBasedWordIndex-1];
	}
	
	function handleDBError($sqliteDBObj)
	{
		$lastError = $sqliteDBObj->lastErrorCode();
		
		if ( $lastError!=0)
		{
			//UNIQUE constraint failed
			if ( $lastError=="19")
			{
				return "Error, data already in DB ! ";
			}
			else
			{
				return  "Error occurred [$lastError] ";
			}
			
		}
	}
	
	function findSmallestWordInArray($arr)
	{
		$smallestLength = 999;
		$smallestIndex = -1;
		
		foreach ($arr as $index => $word)
		{
			if ( empty($word)) continue;
			
			//echoN($word.$index);
			
			$lengthOfCurrentWord = mb_strlen($word);
			
			if ( $lengthOfCurrentWord <  $smallestLength)
			{
				$smallestLength = $lengthOfCurrentWord;
				$smallestIndex = $index;
				
			}
		}
		
		//preprint_r($arr);
		//echoN($smallestIndex."-".$smallestLength);
		
		return $arr[$smallestIndex];
		
		
	}
	
	
	function loadUthmaniDataModel()
	{
		return  apc_fetch("MODEL_CORE[AR_UTH]");
	}
	
	

	
	
	function loadUthmaniToSimpleMappingTable()
	{
		return apc_fetch("UTHMANI_TO_SIMPLE_WORD_MAP");
	}
	
	function loadLemmaToSimpleMappingTable()
	{
		return apc_fetch("LEMMA_TO_SIMPLE_WORD_MAP");
	}	

	
	// doen not contain tashkeel
	function isSimpleQuranWord($str)
	{
		if ( removeTashkeel($str)==$str) return true;
		
		return false;
	}
	
	function initArrayWithZero(&$arrItem)
	{
		if ( empty($arrItem))
		{
			$arrItem=0;
		}
	}
	
	function plotHistogram($data)
	{
		session_start();
		$_SESSION['PLOTTING_DATA'] = $data ;
		
		echo "<IFRAME id='plotting-frame' SRC='/tools/plotting/histogram.php'>";
		echo "</IFRAME>";
	}
	
	function arrayToCSV($data)
	{
		foreach($data as $key=>$val)
		{
			echoN($key.",".$val);
		}
	}
	
	function histogramFromArray($data)
	{
		$histoBins = array();
		
		foreach( $data as $key => $value)
		{
		
			initArrayWithZero($histoBins[$value]);
			
			$histoBins[$value]++;
		}
		
		return $histoBins;
		
	}
	
	function advanceArrayCounter(&$arr,$numberOfMoves)
	{
		for($i=0;$i<$numberOfMoves;$i++)
		{
			next($arr);
		}
	}
	
	/*
	 * $threshold: frequency of verse repetition, returned verses will have freq more than the specified threshold
	 */
	function getRepeatedVerses($threshold=1)
	{
		global $MODEL_CORE,$numberOfSuras;
		
	
		
		$repeatedVerses = array();
		
			$i=0;
			/* SURA'S LOOP **/
			for ($s=0;$s<$numberOfSuras;$s++)
			{
				
				
				$suraSize = count($MODEL_CORE['QURAN_TEXT'][$s]);
						
						/* VERSES LOOP **/
				for ($a=0;$a<$suraSize;$a++)
				{
						
					$i++;
					$verseText = $MODEL_CORE['QURAN_TEXT'][$s][$a];
						
						
					initArrayWithZero($repeatedVerses[$verseText]);
						
					$repeatedVerses[$verseText]++;
				
				}
			
			}
			
			arsort($repeatedVerses);
				
			
				
			$repeatedVerses = array_filter($repeatedVerses, 
					function($v) use ($threshold) 
					{
						return	$v > $threshold; 
					} );
			
			return $repeatedVerses;
	}

	/*
	 * $threshold: frequency of Ngrams repetition, returned ngrams with frequency more than the specified threshold
	*/
	function getNGrams($n,$threshold=0)
	{
		global $MODEL_CORE,$numberOfSuras,$mandatoryStop,$saktaLatifaMark,$sajdahMark;
		
		
		$grams = $n;
		
		$nGramesArr = array();
		
		$i=0;
		/* SURA'S LOOP **/
		for ($s=0;$s<$numberOfSuras;$s++)
		{
			
			
			$suraSize = count($MODEL_CORE['QURAN_TEXT'][$s]);
				
			/* VERSES LOOP **/
			for ($a=0;$a<$suraSize;$a++)
			{
				
				$i++;
				$verseText = $MODEL_CORE['QURAN_TEXT'][$s][$a];
				
				
						
					$wordsArr = preg_split("/ /", $verseText);
				
					//$wordsArr = removePauseMarksFromArr($MODEL_CORE['TOTALS']['PAUSEMARKS'], $wordsArr);
				
				
							$verseLength = count($wordsArr);
				
							// 3 grames in 2 words verse
							//REVISIT PAUSE MARKS COUNT HERE
							if ( $grams > $verseLength)
							{
								continue;
							}
							
				
				// echoN("$verseLength-$grams");
				// groups loop
				for ($group=0;$group<=($verseLength-$grams);$group++)
				{
					
					// reset ngrame to start accumulating by start of each verse
					$nGrameString = "";
					reset($wordsArr);
						
					//move array cursor according to the start of the new group
					advanceArrayCounter($wordsArr,$group);
						
						$subsentenceStopFlag =0 ;
						$nGramsWordCount =0;
						
						//echoN("-- NEW GROUP --");
						
					// words loop
						while( ($nGramsWordCount < $grams)  )
						{
							$word = current($wordsArr);
							//echoN("|$word|");
							
					
							if ( isPauseMark($word,$MODEL_CORE['TOTALS']['PAUSEMARKS'],$saktaLatifaMark,$sajdahMark) )
							{
							
								// in case of mandatory pause, just jump and start a new group after the pause
								if ($word == $mandatoryStop)
								{
									//echoN($verseText);
									$subsentenceStopFlag=1;
										
									//echoN("####".$nGrameString);
									// group will be incremented again by  the loop after break, so it will be +2 for bigrams
									$group+=($grams-1);
									break;
										
								}
							
								// in case of all pause-words except mandatory pause, just ignore pause and get next word
								next($wordsArr);
								$group+=($grams-1);
								continue;
							}
							
							$nGrameString = $nGrameString." ".$word;
							
							$nGramsWordCount++;
					
							next($wordsArr);
						}
					
						
						
					
							
						
						
						if ( $subsentenceStopFlag==1)
						{
							
							//to prevent  "يسمعون و الموتى" mandatory stop case
							continue;
						}
					
							
						$nGrameString = trim($nGrameString);
						
						initArrayWithZero($nGramesArr[$nGrameString]);
						
						$nGramesArr[$nGrameString]++;
						
						
				
				}
			
			
			
				
			
				
			}
		}
		
		arsort($nGramesArr);
		
			
		
		$nGramesArr = array_filter($nGramesArr,
										function($v) use ($threshold)
										{
											return	$v > $threshold;
										} );
		
		return $nGramesArr;
	}
	
	
	/*
	 * $threshold: frequency of PoS-Ngrams repetition, returned ngrams with frequency more than the specified threshold
	*/
	function getPoSNGrams($posPatternString,$threshold=0)
	{
		global $MODEL_CORE,$numberOfSuras,$quranCorpusMorphologyFile;
	
	
		$grams = $n;
	
		$nGramesArr = array();
		
		
		/////////////////////////////////
		$patternArr = preg_split("/ /",$posPatternString);
		
		$posTagsCountInPattern = count($patternArr);
		
		
		$qacFileLinesArr = file($quranCorpusMorphologyFile,FILE_IGNORE_NEW_LINES);
		
		
		
		$headerIndex=0;
		$segmentIndex=1;
		while( current($qacFileLinesArr) !== false)
		{
		
		
		
			$headerIndex++;
		
			//ignore header sections
			if ( $headerIndex <= 57)
			{
				next($qacFileLinesArr);
				continue;
			}
		
		
		
			$line = current($qacFileLinesArr);
		
			//echoN( htmlentities($line));
		
			// convert columns to array
			$lineArr = preg_split("/\t/",$line);
		
		
		
		
			$location = $lineArr[0];
			$formOrSegment = $lineArr[1];
			$formOrSegmentAR = buckwalterReverseTransliteration($formOrSegment);
			$posTAG = $lineArr[2];
		
			// remove brackets from location and keep it only SURA/AYA/WORDINDEX/SEGMENTINDEX
			$masterID = preg_replace("/\(|\)|/", "", $location);
		
			$locationArr = preg_split("/\:/", $masterID);
		
		
		
		
			$posTaggedNGramsStr = "";
			if ( $posTAG == $patternArr[0] || $patternArr[0]=="*")
			{
					
				//echoN("### $posTAG-".buckwalterReverseTransliteration($formOrSegment)."-$location");
					
				$posTaggedNGramsStr = $formOrSegmentAR;
				$numberOfGramsFound = 1;
					
				//echoN($posTaggedNGramsStr);
					
				$prevVerseNumber = $locationArr[1];
					
				for($i=0;$i<$posTagsCountInPattern-1;$i++)
				{
			
			
					$line = next($qacFileLinesArr);
			
					//echoN($line);
			
					// convert columns to array
					$lineArr = preg_split("/\t/",$line);
			
			
			
					$location = $lineArr[0];
					$formOrSegment = $lineArr[1];
					$formOrSegmentAR = buckwalterReverseTransliteration($formOrSegment);
					$posTAG = $lineArr[2];
			
					// remove brackets from location and keep it only SURA/AYA/WORDINDEX/SEGMENTINDEX
					$masterID = preg_replace("/\(|\)|/", "", $location);
			
					$locationArr = preg_split("/\:/", $masterID);
			
					$currentVerseNumber = $locationArr[1];
			
							//preprint_r($locationArr);
							//echoN("$prevVerseNumber!=$currentVerseNumber");
			
					if ( $prevVerseNumber!=$currentVerseNumber)
					{
						break;
					}
			
					$prevVerseNumber = $currentVerseNumber;
			
					if ( $posTAG == $patternArr[$numberOfGramsFound] || $patternArr[$numberOfGramsFound]=="*")
					{
						$posTaggedNGramsStr = $posTaggedNGramsStr." ".$formOrSegmentAR;
							
						$numberOfGramsFound++;
							
						//echoN($posTaggedNGramsStr);
							
						//echoN("2- $posTAG-$formOrSegment");
			
					}
					else
					{
						// revert back to the postition after the first checked POS in the pattern
						for($v=1;$v<$numberOfGramsFound;$v++)
						{
							rewind($qacFileLinesArr);
						}
						
						break;
					}
	


				}
					
				//echoN("$posTagsCountInPattern == $numberOfGramsFound"."-$location");
					
				if ( $posTagsCountInPattern == $numberOfGramsFound)
				{
	
					$posTaggedNGramsStr = trim($posTaggedNGramsStr);
	
					initArrayWithZero($nGramesArr[$posTaggedNGramsStr]);
	
					$nGramesArr[$posTaggedNGramsStr]++;
				}
				//preprint_r($nGramesArr);

			}
		
		
			next($qacFileLinesArr);
		
		}
		
		/////////////////////////////////
		
		
		arsort($nGramesArr);
		
			
		
		$nGramesArr = array_filter($nGramesArr,
				function($v) use ($threshold)
				{
					return	$v > $threshold;
				} );
		
		return $nGramesArr;
		
	}
	
	function getWordInfo($word,$MODEL_CORE,$MODEL_SEARCH,$MODEL_QAC,$fast=FALSE,$exactWord=FALSE)
	{

		$wordInfoArr = array();
		
		$word = trim($word);
		$wordUthmani = "";;
		$wordSimple = "";;
		
		$UTHMANI_TO_SIMPLE_WORD_MAP_VS = loadUthmaniToSimpleMappingTable();
		
		
		if ( isSimpleQuranWord($word))
		{
		
		
			$wordUthmani = $UTHMANI_TO_SIMPLE_WORD_MAP_VS[$word];
			$wordSimple = $word;
		}
		else
		{
			$wordUthmani = $word;
			
			
			//preprint_r($UTHMANI_TO_SIMPLE_WORD_MAP_VS);
			
		
			// tashkeel of last char is significant, ex: lemmas will probably not be in the MAP because of that
			$wordSimple =  $UTHMANI_TO_SIMPLE_WORD_MAP_VS[$wordUthmani];;
		
			
		}
		

		
		$freqArr = $MODEL_CORE['WORDS_FREQUENCY']['WORDS_TFIDF'][$wordSimple];
		
		//preprint_r($freqArr);
		
		$wordInfoArr['WORD_SIMPLE']=$wordSimple;
		$wordInfoArr['WORD_UTHMANI']=$wordUthmani;
				
		/*echoN("Simple:".$wordSimple);
		 echoN("Uthmani:".$wordUthmani);
		
		
		
		echoN("Repetition:".$freqArr['TF']);
		echoN("TF-IDF Weight:".round($freqArr['TFIDF'],2));
		
		
		
		*/
		
		$wordInfoArr['TF']=$freqArr['TF'];
		$wordInfoArr['TFIDF']=round($freqArr['TFIDF'],2);

		
		//preprint_r($MODEL_QAC['QAC_MASTERTABLE']);
		//preprint_r(array_keys($MODEL_QAC['QAC_FEATURES']));
		
		$buckwalterTransliteration = "";
		$posTagsArr = array();
		$lemmasArr = array();
		$wordRoot ="";
		
		$versesArr = array();
		$versesTagsArr = array();
		
		$buckwalterTransliteration = "";
		$wordRoot="";
		
	
		if ( empty($wordSimple) || count($MODEL_SEARCH['INVERTED_INDEX'][$wordSimple])==0)
		{
			return null;
		}
		
		
		foreach ($MODEL_SEARCH['INVERTED_INDEX'][$wordSimple] as $documentArrInIndex)
		{

		
			$SURA = $documentArrInIndex['SURA'];
			$AYA = $documentArrInIndex['AYA'];
			$INDEX_IN_AYA_EMLA2Y = $documentArrInIndex['INDEX_IN_AYA_EMLA2Y'];
			$INDEX_IN_AYA_UTHMANI= $documentArrInIndex['INDEX_IN_AYA_UTHMANI'];
			$WORD_TYPE = $documentArrInIndex['WORD_TYPE'];
			$EXTRA_WORD_TYPE_INFO = $documentArrInIndex['EXTRA_INFO'];
			
			
			// INGORE ROOT SOURCES AND PRONOUNS, WE ONLY NEED THE NROMAL CORRESPONDING WORD
			if ( $WORD_TYPE=="PRONOUN_ANTECEDENT" || $WORD_TYPE=="ROOT" ) continue;
		
		
			$qacLocation = getQACLocationStr($SURA+1,$AYA+1,$INDEX_IN_AYA_UTHMANI);
				
			
		
			//preprint_r($MODEL_QAC['QAC_MASTERTABLE'][$qacLocation]);
			//exit;
		
			// search QAC for roots and LEMMAS for this word
			foreach ( $MODEL_QAC['QAC_MASTERTABLE'][$qacLocation] as $segmentIndex => $segmentDataArr)
			{
				$tag = $segmentDataArr['TAG'];
				$segmentWord = $segmentDataArr['FORM_AR'];
				
				$segmentWordSimple="";
				if ( isset($UTHMANI_TO_SIMPLE_WORD_MAP_VS[$segmentWord] ))
				{
					$segmentWordSimple = $UTHMANI_TO_SIMPLE_WORD_MAP_VS[$segmentWord];
				}
				
				$buckwalterTransliteration = $segmentDataArr['FORM_EN'];
				
				if ( isset($segmentDataArr['FEATURES']['LEM']) )
				{
					$lemma  = $segmentDataArr['FEATURES']['LEM'];
				}
				
				
				$verseText = getVerseByQACLocation($MODEL_CORE,$qacLocation);
		
				
		
				$wordId = (getWordIndexFromQACLocation($qacLocation));
				
				if ( $exactWord==TRUE)
				{
					$wordFromVerseAtLocation = getWordFromVerseByIndex($MODEL_CORE, $verseText, $wordId);
					
					if ( $wordSimple!==$wordFromVerseAtLocation) continue;
				}
				
				//echoN("$segmentWord|$tag");
				
				//for segments like ال no corresponding simple words to compare, not our target segment, so continue
				//if ( empty($segmentWordSimple)) continue;
		
				if ( isset($segmentDataArr['FEATURES']['ROOT']) && $segmentDataArr['FEATURES']['ROOT']!=-1)
				{
					$wordRoot =  $segmentDataArr['FEATURES']['ROOT'];
				}
		
				$posTagsArr[$tag]=1;
				$lemmasArr[$lemma]=1;
		
				
		
				//echoN("|$segmentWordSimple|$wordSimple|$segmentWord");
			
		
					
				
		
				//$verseText = markSpecificWordInText($verseText,$wordId,$segmentWordSimple,"marked_fg");
		
				$verseHash = md5($verseText);
		
				if ( !isset($versesArr[$verseHash]))
				{
					$versesArr[$verseHash] = $verseText;
					
				
				}
				
				if ( !isset($versesTagsArr[$verseHash]) )
				{
					$versesTagsArr[$verseHash]="";
				}
				
				$versesTagsArr[$verseHash] = $versesTagsArr[$verseHash]." ".$tag;
					
					
		
		
		
		
					
			}
			
			// we don't need all inverted index list except for verses, only break if we found at least one word
			if ( $fast==true && !empty($versesArr))
			{
				break;
			}
		}
		
		$wordInfoArr['BUCKWALTER']=$buckwalterTransliteration;
		$wordInfoArr['ROOT']=$wordRoot;
		$wordInfoArr['LEM']=$lemmasArr;
		$wordInfoArr['POS']=$posTagsArr;
		$wordInfoArr['VERSES']=$versesArr;
		$wordInfoArr['VERSES_POS_TAGS']=$versesTagsArr;
		
		
		return $wordInfoArr;
	}
	
	function showTechnicalError($error)
	{
		echoN("<div id='technical-error'>$error</div>");
	}
	
	function getKeyIndexFromArray($arr, $sentKey)
	{
		$counter =1;
		foreach ($arr as $key=>$val)
		{
			if ( $sentKey==$key)
			{
				return $counter;
			}
			
			$counter++;
		}
	}
	
?>