<?php 
require_once("../global.settings.php");
require_once("../libs/core.lib.php");

loadModels("core,qac", "AR");


$LEMMA_TO_SIMPLE_WORD_MAP = loadLemmaToSimpleMappingTable();


printHTMLPageHeader();
?>