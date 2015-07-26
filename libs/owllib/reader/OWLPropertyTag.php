<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdf:RDF> node
 *  All functions are implemented in OWLTag
 *
 *  @version	$Id: OWLPropertyTag.php,v 1.3 2004/04/07 06:20:42 klangner Exp $
 */
class OWLPropertyTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
  {
  	OWLTag::create($model, $name, $attributes, $base);

		$this->should_add = false;
		
	
		
  	if(array_key_exists($this->RDF_ID, $attributes)){
			$this->id = $model->getNamespace() . $attributes[$this->RDF_ID];
			$this->should_add = true;
  	}
  	else if(array_key_exists($this->RDF_ABOUT, $attributes)){
			$this->id = $this->addBaseToURI($attributes[$this->RDF_ABOUT]);
  	}

		$this->domain = array();
		$this->range = array();
		if($name == $this->OWL_DATATYPEPROPERTY)
			$this->is_datatype = true;
		else	
			$this->is_datatype = false;
  }


	//---------------------------------------------------------------------------
	/**
	 * end tag
	 */
	function endTag($parser, $tag)
  {
  	OWLTag::endTag($parser, $tag);
  	
		if(!$this->wantsMore() && $this->should_add){ 	 	
			$this->model->createProperty($this->id, $this->domain,
				$this->range, $this->is_datatype);
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
  	if($name == "owldomaintag"){
  		$this->domain = $child->getResources();
  	}
  	else if($name == "owlrangetag"){
  		$this->range = $child->getResources();
  	}
  	else if($name == "owlinverseoftag"){
  		$this->inverse_of = $child->getID();
  	}
  	else if($name == "owllabeltag"){
  		$language = $child->getLanguage();
  		$label = $child->getLabel();
			$this->model->addLabel($this->id, $language, $label);
  	}
  }

	//---------------------------------------------------------------------------
	// Private members
	var $domain;
	var	$range;
	var	$is_datatype;	
	var $should_add;
	var $inverse_of;
}

?>
