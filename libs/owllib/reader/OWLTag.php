<?php
require_once "$OWLLIB_ROOT/OWLOntology.php";


/**
 *	OWLTag class
 *
 *  @version	$Id: OWLTag.php,v 1.5 2004/04/07 06:20:42 klangner Exp $
 */
class OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * create tag
	 */
	function create(&$model, $name, $attributes, $base)
  {
  	$this->model =& $model;
  	$this->name = $name;
  	$this->attributes = $attributes;
  	$this->base = $base;
  	$this->current_tag = null;
  	$this->wants_more = true;
  	$this->label = "";;
  	
  	//preprint_r($attributes);
  	
  	//echoN($name);
	
		if(array_key_exists($this->XML_BASE, $attributes))
			$this->base = $attributes[$this->XML_BASE];
   	
		
		
  }


	//---------------------------------------------------------------------------
	/**
	 * get tag name
	 */
	function getName()
  {
  
  	
  	if ( strpos($this->name,"#:")!==false)
  	{
  		
  		return substr($this->name, strrpos($this->name, ":")+1);
  	}
  	
  	
  	return $this->name;
  }
  
  //---------------------------------------------------------------------------
  /**
   * get tag name
   */
   function getLanguage()
   {
   	return $this->attributes[$this->XML_LANG];
   }
 

	
	//---------------------------------------------------------------------------
	/**
	 * get subclass id
	 */
	function getID()
  {
  	
  	return $this->id;
  }

  /**
   * get subclass id
   * @author KARIM
   */
  function getLabel()
  {
  
  	return $this->label;
  }
  
	
	//---------------------------------------------------------------------------
	/**
	 * namespace declaration handler
	 */
	function namespaceDecl($parser, $prefix, $uri){

		if(strlen($prefix) == 0 && strlen($this->name) == 0){
			$this->model->setNamespace($uri);
		}
	}

	//---------------------------------------------------------------------------
	/**
	 * start tag
	 */
	function startTag($parser, $tag, $attributes)
  {
  	$this->parsingTagContent = false;
  	
  	
  	$conceptName = $attributes['http://www.w3.org/1999/02/22-rdf-syntax-ns#:ID'];
  	
	
  	
  	
  	if($this->current_tag == null){
  		$this->current_tag = $this->createTag($tag, $attributes, $this->base);
  	}
  	else{
  		$this->current_tag->startTag($parser, $tag, $attributes);
  	}
  }


	//---------------------------------------------------------------------------
	/**
	 * tag data
	 */
	function characters($parser, $cdata)
  {
  	

  	
  	//echoN($cdata);
  	
  	// only accumilate if we are inside tag data ( second round)
  	if ( $this->parsingTagContent)
  	{
	  	//ADDED BY KARIM
	  	$this->label .= $cdata;
  	}
  	else 
  	{
	  	//ADDED BY KARIM
	  	$this->label = $cdata;
  	}
  
  	$this->parsingTagContent = true;
  	
  	if($this->current_tag != null){
	 		$this->current_tag->characters($parser, $cdata);
  	}
  }


	//---------------------------------------------------------------------------
	/**
	 * end tag
	 */
	function endTag($parser, $tag)
  {
  	
  	$this->parsingTagContent = false;
  	
  		//echoN("OWLTAG_ENDTAG");
 	 	if($this->current_tag != null){
 	 		
 	 		//echoN("OWLTAG_ENDTAG2");
 	 		
 			$this->current_tag->endTag($parser, $tag);
 			if(!$this->current_tag->wantsMore()){
 				
 				//echoN("OWLTAG_ENDTAG3");
 				$this->processChild($this->current_tag);
 				$this->current_tag = null;
 			}
 	 	}
 	 	else{
			$this->wants_more = false; 	 	
		}
  }

	
	//---------------------------------------------------------------------------
	/**
	 * process child
	 */
	function processChild($child)
  {
  	
  }

	
	//---------------------------------------------------------------------------
	/**
	 * Check if this tag parsered all its data
	 *
	 * @return true if this tag wants more data from XML file
	 */
	function wantsMore()
  {
  	return $this->wants_more;
  }

	
	//---------------------------------------------------------------------------
	/**
	 * add base component to URI when nessesery
	 */
	function addBaseToURI($uri)
  {
		if(strpos($uri, ':') === false)
			$id = $this->base . $uri;
		else
			$id = $uri;
			
		return $id;
  }


	//---------------------------------------------------------------------------
	/**
	 * create tag class
	 */
	function createTag($tag, $attributes, $base)
  {
  	$obj = null;
  	
  	$uri = preg_replace("/#:/", "#", $tag);
  	$namespace  = substr($tag,0, strpos($tag, "#:"));
  	
  	$conceptName = $attributes['http://www.w3.org/1999/02/22-rdf-syntax-ns#:ID'];
  	
  /*	if ( mb_strpos($conceptName, "عيسى")!==false)
  	{

  		echoN("####".$conceptName);
  		
  		
  	}
  	echoN("YY:$uri");
  	echoN("TTT:$tag");
  	echoN("BASE:$base");
  	echoN("NS:$namespace");
  	//preprint_r($attributes);
  	 * 
  	 */
  	

 
  	if(array_key_exists($tag, $this->tag_classes)){
  		//echoN("111:$tag");
  		$obj = new $this->tag_classes[$tag]();
  	}
  	else if(array_key_exists($namespace, $this->tag_classes)){
  		//echoN("111-2:$namespace");
  		$obj = new $this->tag_classes[$namespace]();
  	}
  	else if($this->model->getClass($uri) != null){
  		//echoN("222");
  		$obj = new OWLInstanceTag();
		}
		else if($this->model->getProperty($uri) != null){
			//echoN("333");
  		$obj = new OWLPropInstanceTag();
		}
		else{
			//echoN("444");
  		$obj = new OWLTag();
		}
  		
		
		
		
  	$obj->create($this->model, $tag, $attributes, $base);
  	
  	
  	
  	return $obj;
  }

	
	//---------------------------------------------------------------------------
	// Private members
	var $model;	
	var $name;	
	var $id;	
	var $base;
	var $attributes;	
	var $current_tag;
	var $wants_more;
	var $label;
	
	/** Constants */
	var $XML_BASE = "http://www.w3.org/XML/1998/namespace:base";
	var $XML_LANG = "http://www.w3.org/XML/1998/namespace:lang";
	var $OWL_ONTOLOGY = "http://www.w3.org/2002/07/owl#:Ontology";
	var $OWL_CLASS = "http://www.w3.org/2002/07/owl#:Class";
	var $OWL_OBJECTPROPERTY = "http://www.w3.org/2002/07/owl#:ObjectProperty";
	var $OWL_DATATYPEPROPERTY = "http://www.w3.org/2002/07/owl#:DatatypeProperty";
	var $RDF_RDF = "http://www.w3.org/1999/02/22-rdf-syntax-ns#:RDF";
	var $RDF_ID = "http://www.w3.org/1999/02/22-rdf-syntax-ns#:ID";
	var $RDF_ABOUT = "http://www.w3.org/1999/02/22-rdf-syntax-ns#:about";
	var $RDFS_SUBCLASSOF = "http://www.w3.org/2000/01/rdf-schema#:subClassOf";
	var $RDFS_DOMAIN = "http://www.w3.org/2000/01/rdf-schema#:domain";
	var $RDFS_RANGE = "http://www.w3.org/2000/01/rdf-schema#:range";
	var $RDF_RESOURCE = "http://www.w3.org/1999/02/22-rdf-syntax-ns#:resource";
	var $RDFS_LABEL = "http://www.w3.org/1999/02/22-rdf-syntax-ns#:label";

	/** array with tag names */
	var $tag_classes = array(
		
		"http://www.w3.org/2002/07/owl#:ObjectProperty" => "OWLPropertyTag",
		"http://www.w3.org/2002/07/owl#:DatatypeProperty" => "OWLPropertyTag",
		"http://www.w3.org/2002/07/owl#:intersectionOf" => "OWLIntersectionOfTag",
		"http://www.w3.org/2002/07/owl#:inverseOf" => "OWLInverseOfTag",
		"http://www.w3.org/2000/01/rdf-schema#:subClassOf" => "OWLSubClassOfTag",
		"http://www.w3.org/2000/01/rdf-schema#:domain" => "OWLDomainTag",
		"http://www.w3.org/2000/01/rdf-schema#:range" => "OWLRangeTag",
		"http://www.w3.org/2000/01/rdf-schema#:label" => "OWLLabelTag",
		"http://www.w3.org/2002/07/owl#:Class" => "OWLClassTag",
		"http://qurananalysis.com/data/ontology/qa.ontology.v1.owl/objectproperties" => "OWLPropInstanceTag"
	);

}

?>
