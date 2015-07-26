<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdf:RDF> node
 *  All functions are implemented in OWLTag
 *
 *  @version	$Id: OWLSubClassOfTag.php,v 1.1 2004/03/29 07:27:50 klangner Exp $
 */
class OWLSubClassOfTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
  {
  	OWLTag::create($model, $name, $attributes, $base);

		if(array_key_exists($this->RDF_RESOURCE, $attributes)){
			$this->id = $this->addBaseToURI($attributes[$this->RDF_RESOURCE]);
		}
  }


	//---------------------------------------------------------------------------
	/**
	 * process child:
	 *
	 */
	function processChild($child)
  {
 		$name = get_class($child);
  	if($name == "owlclasstag"){
	 		$this->id = $child->getID(); 
  	}
  }

	
	//---------------------------------------------------------------------------
	// Private members
	
}

?>
