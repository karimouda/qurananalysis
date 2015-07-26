<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdf:RDF> node
 *  All functions are implemented in OWLTag
 *
 *  @version	$Id: OWLInstanceTag.php,v 1.2 2004/03/30 11:35:41 klangner Exp $
 */
class OWLInstanceTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
  {
  	OWLTag::create($model, $name, $attributes, $base);

  	
  	//echoN("ZZ:".$attributes[$this->RDF_ID]);

  	
  	
  	if(array_key_exists($this->RDF_ID, $attributes)){
  		
  	
  		
			$this->id = $model->getNamespace() . $attributes[$this->RDF_ID];
  	}

 
  	$this->class_id = preg_replace("/#:/", "#", $name);
  	
  	//echoN("XX:".$this->class_id);
  	$this->properties = array();
  	
  }


	//---------------------------------------------------------------------------
	/**
	 * end tag
	 */
	function endTag($parser, $tag)
  {
  	OWLTag::endTag($parser, $tag);
  
		if(!$this->wantsMore()){ 	
			
			//echoN("$this->id, $this->class_id,");
			//preprint_r($this->properties);
			
			$this->model->addInstance($this->id, $this->class_id, $this->properties);
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
 		//echoN("PROCESS_CHILD:$name");
 		
  		if($name == "OWLPropInstanceTag"){
  			
  			//echoN("HERE");
	  	$property_id = preg_replace("/#:/", "#", $child->getName());
	  	
	  	$propArr = $child->getResources();
	  	
	  	$objectID = $propArr[0];
	  	$relationMetaData  = $propArr[1];
	  	
	  	$properties = array($property_id=>array($objectID),"RELATION_META"=>$relationMetaData);
	  	
  		$this->properties[] = $properties;

	

  		
  		
  	}
  	else if($name == "OWLLabelTag"){
  		$language = $child->getLanguage();
  		$label = $child->getLabel();
			$this->model->addLabel($this->id, $language, $label);
  	}
  	else if($name == "OWLTag"){
  		$language = $child->getLanguage();
  		$label = $child->getLabel();
  		$tagName = $child->getName();
  		$this->model->addAnnotation($this->id,$language,$tagName,$label);
  	
  	}
  	
  }


	//---------------------------------------------------------------------------
	// Private members
	var	$class_id;	
	var	$properties;
}

?>
