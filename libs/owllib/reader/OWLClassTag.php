<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <rdf:RDF> node
 *  All functions are implemented in OWLTag
 *
 *  @version	$Id: OWLClassTag.php,v 1.4 2004/04/07 06:20:42 klangner Exp $
 */
class OWLClassTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
  {
  	OWLTag::create($model, $name, $attributes, $base);

  	if(array_key_exists($this->RDF_ID, $attributes)){
			$this->id = $model->getNamespace() . $attributes[$this->RDF_ID];
			$this->cls = $model->createClass($this->id);
			
  	}
  	else if(array_key_exists($this->RDF_ABOUT, $attributes)){
			$this->id = $this->addBaseToURI($attributes[$this->RDF_ABOUT]);
			$this->cls = $model->createClass($this->id);
			
		}
  }

  //---------------------------------------------------------------------------
  /**
   * end tag
   */
   function endTag($parser, $tag)
   {
	   OWLTag::endTag($parser, $tag);
	  
	   //echoN("ENDTAG1");
	   
	   if(!$this->wantsMore())
	   {
		   	//echoN("ENDTAG2");
		   $this->model->addProperty($this->id,$this->properties,"CLASS");
	   }
   }

	//---------------------------------------------------------------------------
	/**
	 * process child:
	 *
	 * OWLSubclassOfTag add super class information 
	 */
	function processChild($child)
  {
  	
 		$name = get_class($child); 
 	
 		if($name == "OWLPropInstanceTag"){
 			
 			//echoN("PROCESSING OWLPropInstanceTag");
 			
 			$property_id = preg_replace("/#:/", "#", $child->getName());
 		
 			$propArr = $child->getResources();
 		
 			$objectID = $propArr[0];
 			$relationMetaData  = $propArr[1];
 		
 			$properties = array($property_id=>array($objectID),"RELATION_META"=>$relationMetaData);
 		
 			$this->properties[] = $properties;
 		
 			
 		
 		
 		
 		}
  	 else if($name == "OWLSubClassOfTag"){
  		$parent = $child->getID();
			$this->cls->addSuperclass($parent);
  	}
  	else if($name == "OWLIntersectionOfTag"){
  		$parent = $child->getID();
			$this->cls->addSuperclass($parent);
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
	var $cls;	
	
	var	$properties;
}

?>
