<?php 
$MAIN_ROOT_PATH = dirname(__FILE__)."/";

error_reporting(E_ALL);

$BUILD_VERSION = "0.1.0";





$MAIN_JS_PATH = "/libs/js/main.js?bv=$BUILD_VERSION";


$JQUERY_PATH = "/libs/js/jquery/jquery-2.1.1.min.js";

$D3_PATH = "/libs/js/d3/d3.js";

$JQUERY_TAGCLOUD_PATH ="/libs/js/jquery.tagcloud.js";


$TINYSORT_PATH = "/libs/js/tinysort/tinysort.min.js";
$TINYSORT_JQ_PATH = "/libs/js/tinysort/jquery.tinysort.min.js";

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
$quranFileEN = dirname(__FILE__)."/data/translations/en.sahih";
$quranFileAR = dirname(__FILE__)."/data/quran-simple-clean.txt";
$quranFileUthmaniAR = dirname(__FILE__)."/data/quran-uthmani.txt";

$wordByWordTranslationFile = dirname(__FILE__)."/data/translations/word-by-word-english.txt";
$transliterationFile = dirname(__FILE__)."/data/transliteration/en.transliteration.txt";
$wordnetDir = dirname(__FILE__)."/data/wordnet/dict/";

$customTranslationTableFile = dirname(__FILE__)."/data/generated/translation.table";


$quranFileAR_XML = dirname(__FILE__)."/data/quran-simple-clean.xml";
$quranCorpusMorphologyFile = dirname(__FILE__)."/data/quranic-corpus-morphology-0.4.txt";
$quranaPronounResolutionConceptsFile = dirname(__FILE__)."/data/quran-pron/concepts.xml";
$quranaPronounResolutionDataFileTemplate = dirname(__FILE__)."/data/quran-pron/pronxml-%s.xml";

$qaOntologyFile =  dirname(__FILE__)."/data/ontology/qa.ontology.v1.owl";
$qaOntologyNamespace = "http://qurananalysis.com/data/ontology/qa.ontology.v1.owl#";
$is_a_relation_name_ar = "هو";
$is_a_relation_name_en = "is a";
$thing_class_name_ar = "شيء";
$thing_class_name_en = "Thing";

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
$arabicStopWordsFile = dirname(__FILE__)."/data/quran-stop-words.strict.l1.ar";
$arabicStopWordsFileL2 = dirname(__FILE__)."/data/quran-stop-words.strict.l2.ar";
$englishStopWordsFile = dirname(__FILE__)."/data/english-stop-words.en";

$sajdahMark = "۩";
$saktaLatifaMark = "ۜ";
$numberOfVerses = 6236;
$numberOfSuras = 114;
$basmalaText = "بسم الله الرحمن الرحيم";
$basmalaTextUthmani = "بِّسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ";
$basmalaTextUthmani2 = "بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ";
$mandatoryStop = "ۘ";


## LOCATION SIGNIFICANT ##
require_once($MAIN_ROOT_PATH."/libs/core.lib.php");
require_once($MAIN_ROOT_PATH."/model.loader.php");



if ( isDevEnviroment() )
{
	error_reporting(E_ERROR);
	ini_set('display_errors', true);
	function shutdown()
	{
	
		$isSevereError = false;
		$errorArr = error_get_last();
		
		if (!empty($errorArr) )
		{
				
			switch($errorArr['type'])
			{
				case E_ERROR:
					//case E_USER_ERROR:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
					$isSevereError = true;
					break;
				default:
					
	
			}
		}
	
		if ($isSevereError)
		{
			echo "SEVERE ERROR: ".$errorArr['message'];
			preprint_r($errorArr);
		}
	}
	
	register_shutdown_function('shutdown');
}
?>