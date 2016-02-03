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

$lang = "AR";



if ( isset($_GET['lang']) )
{
	$lang = $_GET['lang'];
}

loadModels("core,qac",$lang);




$parameter = $_GET['parameter'];



		
					$grams = $parameter;
					
					$nGramesArr = array();
					
					
					$nGramesArr = getNGrams($lang,$grams);
					
					
					
				
					
					
					//echoN("N-Grams Avg:".($avgCollocationFreq/($nGramsCount)));
					
					//$secondArr = array_slice($nGramesArr, floor($nGramsCount/2), 5);
					
					//preprint_r( ($secondArr));
					
					
					
					
					//arrayToCSV($nGramesArr);
					//$histoArr = histogramFromArray($nGramesArr);
					//plotHistogram($nGramesArr);
					
					/*foreach($histoArr as $key=>$val)
					{
						echoN("$key,$val");
					}*/
			
					


$avgCollocationFreq = array_sum($nGramesArr);
	
$nGramsCount = count($nGramesArr);
					?>
					
					<table id='ngrams-results-table'>
					<thead>
					<tr>
						<td colspan='2'>
							
							Number of N-Grams:<b><?=addCommasToNumber($nGramsCount) ?></b>
							Total repetitions:<b><?=addCommasToNumber($avgCollocationFreq) ?></b>
							
						</td>
					</tr>
					</thead>
					<tr>
					<th>
						Words
					</th>
					<th>
						Frequency
					</th>
					</tr>
					
					<?php
				
						
				
					
					//echoN($nGramesArr[floor($nGramsCount/2)]);
					//preprint_r($nGramesArr);
					
					foreach($nGramesArr as $key=>$val)
					{
						
					?>
					<tr>	
						<td>
							<?=$key?>
						</td>
						<td>
							<?=$val?>
						</td>
					</tr>
					<?php 
					}
					?>
			</table>


