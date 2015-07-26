<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdfs:label> node
 *
 *  @version	$Id: OWLLabelTag.php,v 1.1 2004/03/29 07:27:50 klangner Exp $
 */
class OWLLabelTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
  {
  	OWLTag::create($model, $name, $attributes, $base);

		$this->language = "en";
		if(array_key_exists($this->XML_LANG, $attributes))
			$this->language = $attributes[$this->XML_LANG];
			
  }


	//---------------------------------------------------------------------------
	/**
	 * get language
	 */
	function getLanguage()
  {
  	return $this->language;
  }

	
	//---------------------------------------------------------------------------
	/**
	 * get label
	 */
	function getLabel()
  {
  	return $this->label;
  }

	
	//---------------------------------------------------------------------------
	/**
	 * tag data
	 */
	function characters($parser, $cdata)
  {
  	$this->label = $cdata;
  }


	//---------------------------------------------------------------------------
	// Private members
	var	$language;
	var	$label;
	
}

?>
