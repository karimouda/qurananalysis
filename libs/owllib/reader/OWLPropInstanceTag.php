<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdf:RDF> node
 *  All functions are implemented in OWLTag
 *
 *  @version	$Id: OWLPropInstanceTag.php,v 1.1 2004/03/29 07:27:50 klangner Exp $
 */
class OWLPropInstanceTag extends OWLTag
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
			//echoN("HERE:$id");
			array_push($this->resources, $id);
		}
		
		
		/// ADDED BY KARIM
		unset($attributes[$this->RDF_RESOURCE]);
		array_push($this->resources, $attributes);
		//echoN("HERE2:".print_r($attributes,true));
		
		//echoN("PROCESSING $name");
		//preprint_r($this->resources);
  }


	//---------------------------------------------------------------------------
	/**
	 * Get resources
	 */
	function getResources()
  {
  	return $this->resources;
  }
  
  function getAttributes()
  {
  	return $this->attributes;
  }

	
	//---------------------------------------------------------------------------
	/**
	 * process child:
	 *
	 */
	function processChild($child)
  {
 		$name = get_class($child);
 		//echoN("C".$name);
  	if($name == "owlinstancetag"){
  		array_push($this->resources, $child->getID());
  		//echoN("HERE3:".$child->getID());
  	}
  }

	
	//---------------------------------------------------------------------------
	// Private members
	var	$resources;	

}

?>
