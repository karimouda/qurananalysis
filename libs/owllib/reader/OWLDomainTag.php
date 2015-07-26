<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdfs:domain> node
 *  All functions are implemented in OWLTag
 *
 *  @version	$Id: OWLDomainTag.php,v 1.2 2004/04/07 06:20:42 klangner Exp $
 */
class OWLDomainTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
  {
  	OWLTag::create($model, $name, $attributes, $base);
  	
  	$this->resources = array();

		if(array_key_exists($this->RDF_RESOURCE, $attributes)){
			$id = $this->addBaseToURI($attributes[$this->RDF_RESOURCE]);
			array_push($this->resources, $id);
		}
  }


	//---------------------------------------------------------------------------
	/**
	 * Get resources
	 */
	function getResources()
  {
  	return $this->resources;
  }

	
	//---------------------------------------------------------------------------
	// Private members
	var	$resources;	
}

?>
