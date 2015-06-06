<?php 
$MAIN_ROOT_PATH = dirname(__FILE__)."/";

error_reporting(E_ALL);

$BUILD_VERSION = "0.1.0";





$MAIN_JS_PATH = "/libs/js/main.js?bv=$BUILD_VERSION";


$JQUERY_PATH = "/libs/js/jquery-2.1.1.min.js";

$D3_PATH = "/libs/js/d3.min.js";


/*
$HIGHCHARTS_PATH = "/libs/js/highcharts-4-0-1/js/highcharts.js";
$HIGHCHARTS_3D_PATH = "/libs/js/highcharts-4-0-1/js/highcharts-3d.js";
$HIGHCHARTS_EXPORTING_PATH = "/libs/js/highcharts-4-0-1/js/modules/exporting.js";
$JQUERY_UI_PATH = "/libs/jquery-ui-1-11-0-beta-1/ui/minified/jquery-ui.min.js";
$JQUERY_UI_CSS_PATH = "/libs/jquery-ui-1-11-0-beta-1/themes/base/minified/jquery-ui.min.css";
*/

mb_regex_encoding('UTF-8');
mb_internal_encoding("UTF-8");


$quranMetaDataFile = dirname(__FILE__)."/data/quran-data.xml";
$quranFileEN = dirname(__FILE__)."/data/en.sahih";
$quranFileAR = dirname(__FILE__)."/data/quran-simple-clean.txt";
$quranFileUthmaniAR = dirname(__FILE__)."/data/quran-uthmani.txt";

$quranFileAR_XML = dirname(__FILE__)."/data/quran-simple-clean.xml";
$quranCorpusMorphologyFile = dirname(__FILE__)."/data/quranic-corpus-morphology-0.4.txt";
$quranaPronounResolutionConceptsFile = dirname(__FILE__)."/data/quran-pron/concepts.xml";
$quranaPronounResolutionDataFileTemplate = dirname(__FILE__)."/data/quran-pron/pronxml-%s.xml";



$englishResourceFile = dirname(__FILE__)."/data/resources.en";
$arabicResourceFile = dirname(__FILE__)."/data/resources.ar";

$modelSources = array();
$supportedLanguages = array("EN","AR","AR_UTH");
		

$modelSources['AR']= array("type"=>"TXT","file"=>$quranFileAR);
$modelSources['AR_UTH']= array("type"=>"TXT","file"=>$quranFileUthmaniAR);
//$modelSources['AR']= array("type"=>"XML","file"=>$quranFileAR_XML);
$modelSources['EN']= array("type"=>"TXT","file"=>$quranFileEN);

$serializedModelFile = dirname(__FILE__)."/data/model.ser";

$pauseMarksFile = dirname(__FILE__)."/data/pause.marks";
$arabicStopWordsFile = dirname(__FILE__)."/data/quran-stop-words.ar";
$englishStopWordsFile = dirname(__FILE__)."/data/english-stop-words.en";

$sajdahMark = "۩";
$saktaLatifaMark = "ۜ";
$numberOfVerses = 6236;
$numberOfSuras = 114;
$basmalaText = "بسم الله الرحمن الرحيم";
$basmalaTextUthmani = "بِّسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ";
$basmalaTextUthmani2 = "بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ";



## LOCATION SIGNIFICANT ##
require_once($MAIN_ROOT_PATH."/libs/core.lib.php");
require_once($MAIN_ROOT_PATH."/model.loader.php");
?>