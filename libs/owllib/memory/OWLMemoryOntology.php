<?php
require_once "$OWLLIB_ROOT/OWLOntology.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryClass.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryProperty.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryInstance.php";


/**
 * Implementation of OWL Ontology interface.
 * This class reads information from owl file.
 *  @version	$Id: OWLMemoryOntology.php,v 1.9 2004/04/07 06:20:42 klangner Exp $
 */
class OWLMemoryOntology extends OWLOntology
{
	//---------------------------------------------------------------------------
	/**
	 * Constructor
	 */ 
	function OWLMemoryOntology(){
		
		$this->owl_data = array();
		$this->owl_data['classes'] = array();
		$this->owl_data['subclasses'] = array();
		$this->owl_data['properties'] = array();
		$this->owl_data['instances'] = array();
		$this->owl_data['labels'] = array();
		
		//ADDED BY KARIM
		$this->owl_data['annotations'] = array();
		$this->owl_data['annotation_properties'] = array();		
		
	}


	//----------------------------------------------------------------------------
	/**
	 * get ontology namespace
	 */
	function getNamespace(){
		return $this->namespace;
	}


	//---------------------------------------------------------------------------
	/**
	 * Get class with give $id
	 * @param $id class id
	 */ 
	function getClass($id){
		
		$class = null;
		
		if(array_key_exists($id, $this->owl_data['classes']) ||
			$id == "http://www.w3.org/2002/07/owl#Thing")
		{
			$class = new OWLMemoryClass($id, $this);
		}
		
		return $class;
	}


	//----------------------------------------------------------------------------
	/**
	 * get all classes
	 */
	function getAllClasses(){

		$classes = array();
		
		foreach($this->owl_data['classes'] as $id => $data)
		{
			$class = new OWLMemoryClass($id, $this);
			array_push($classes, $class);
		}
		
		return $classes;
	}


	//---------------------------------------------------------------------------
	/**
	 * Get property with give $id
	 * @param $id property id
	 * @return OWLProperty class
	 */ 
	function getProperty($id){
		
		$properties =& $this->owl_data['properties'];
		$property = null;

		if(array_key_exists($id, $properties)){
			$property = new OWLMemoryProperty($id, $this);
		}
			
		return $property;
	}


	//----------------------------------------------------------------------------
	/**
	 * get all properties
	 */
	function getAllProperties(){
		
		$properties = array();
		
		foreach($this->owl_data['properties'] as $id => $data)
		{
			$property = new OWLMemoryProperty($id, $this);
			array_push($properties, $property);
		}
		
		return $properties;
	}

		//----------------------------------------------------------------------------
		/**
		 * Get all annotation properties
		 * @author KARIM
		 */
		 function getAllAnnotationProperties()
		 {
		
		
			return $this->owl_data['annotation_properties'];
	
		}
	
	//---------------------------------------------------------------------------
	/**
	 * Get instance with give $id
	 * @param $id instance id
	 * @return OWLInstance class
	 */ 
	function getInstance($id,$index){
		
		$instances =& $this->owl_data['instances'];
		$instance = null;

		if(array_key_exists($id, $instances)){
			$instance = new OWLMemoryInstance($id,$index, $this);
		}
			
		return $instance;
	}


	//----------------------------------------------------------------------------
	/**
	 * get all instances
	 */
	function getAllInstances(){

		$instances = array();
		

		/*
		 * IMPORTANT NOTE: Instances of Things SHOULD BE CREATED FIRST BEFORE OTHER INSTANCES
		* THIS IS IMPORANT FOR ONTOLOGY PARSING ( READING) SINCE INSTANCE FAIL IF ITS PARENT
		* WAS NOT PRELOADED, ALSO THIS WILL MAKE THE FILE MORE STRUCTURED
		*/
		foreach($this->owl_data['instances'] as $id => $oneInstance)
		{
			foreach($oneInstance as $index => $data)
			{
				if ($data['class']=="Thing" )
				{
					$instance = new OWLMemoryInstance($id,$index, $this);
					array_push($instances, $instance);
				}
			}
		}
		
			foreach($this->owl_data['instances'] as $id => $oneInstance)
		{
			foreach($oneInstance as $index => $data)
			{
				if ($data['class']!="Thing" )
				{
					$instance = new OWLMemoryInstance($id,$index, $this);
					array_push($instances, $instance);
				}
			}
		}
		
		return $instances;
	}


	//----------------------------------------------------------------------------
	/**
	 * set ontology namespace
	 */
	function setNamespace($namespace){
		$this->namespace= $namespace;
	}
	

	//---------------------------------------------------------------------------
	/**
	 * create new class
	 */
	function createClass($id)
  {
		$this->owl_data['classes'][$id]  = array(); 
		
		return $this->getClass($id);
  }

	
	//---------------------------------------------------------------------------
	/**
	 * create new property
	 */
	  function createProperty($id, $domain, $range, $is_datatype)
	  {
			$property = array();
			$property['domain'] = $domain; 
			$property['range'] = $range; 
			$property['isdatatype'] = $is_datatype; 
			$this->owl_data['properties'][$id]  = $property;
			
			return $this->getProperty($id);
	  }
  
	  //---------------------------------------------------------------------------
	  /**
	   * Create new annotation property
	   * @author KARIM
  	   * @param $id instance ID
	   */
	   function createAnnotationProperty($id)
	   {
		   $property = array();
		   $this->owl_data['annotation_properties'][$id]  = null;
		   	
		   return $this->owl_data['annotation_properties'][$id];
	   }
  
  
  
  
  //---------------------------------------------------------------------------
  /**
   * Add new property
   * ADDED BY KARIM
   * @param $id instance ID
   */
   function addProperty($id, $properties,$classOrInstance="INSTANCE")
   {

	   // REMOVED SINCE CLASSES SHOULD HAVE PROPERTIES
//     if ( $this->getInstance($id)!=null)
// 	   {
// 	   }
	   if ( $classOrInstance=="CLASS")
	   {
	   	echoN("&&&".$id);
	   	$this->owl_data['classes'][$id][0]["properties"][] = $properties;
	   }
	   else
	   {
	  	 $this->owl_data['instances'][$id][0]["properties"][] = $properties;
	   }
	  
	  
	   
   }
   
   //---------------------------------------------------------------------------
   /**
    * Add new annotation
    * ADDED BY KARIM
    * @param $classOrInstanceID name of class or instance
    */
    function addAnnotation($classOrInstanceID,$lang,$key,$val)
    {
	    $property = array();
	   
	    
	   	// KARIM: IF STATEMENT HERE WILL NOT WORK WHILE PARSING XML IN READING FLOW	    	
	    $this->owl_data['annotations'][$classOrInstanceID][] = array("KEY"=>$key,"LANG"=>$lang,"VAL"=>$val);

	    
   
   }
   

	
	//---------------------------------------------------------------------------
	/**
	 * add new subclass
	 */
	function addSuperclass($super, $sub)
  {
		$rel = array($super, $sub);
		array_push($this->owl_data['subclasses'], $rel);
  }

	
	//---------------------------------------------------------------------------
	/**
	 * @param array with subclasses info
	 */ 
	function getSubclasses(){

		return $this->owl_data['subclasses'];
	}


	//---------------------------------------------------------------------------
	/**
	 * add new subclass
	 * @param $id is the new instance ID
	 * @param $class is the Abstract Class ( Parent ) of this new instance
	 */
	function addInstance($id, $class, $properties)
  {
		$instance = array();
		$instance["class"] = $class;
		$instance["properties"] = $properties;
		$this->owl_data['instances'][$id][] = $instance; 
  }


	//---------------------------------------------------------------------------
	/**
	 * add label
	 */
	function addLabel($id, $lang, $label)
    {
  		if(!array_key_exists($id, $this->owl_data['labels']))
			$this->owl_data['labels'][$id] = array();
			 
		$this->owl_data['labels'][$id][$lang] = $label;
    }


	//---------------------------------------------------------------------------
	/**
	 * @param array with subclasses info
	 */ 
	function getClasses(){

		return $this->owl_data['classes'];
	}


	//---------------------------------------------------------------------------
	/**
	 * @param array with properties
	 */ 
	function getProperties(){

		return $this->owl_data['properties'];
	}


	//---------------------------------------------------------------------------
	/**
	 * @param array with information about property
	 */ 
	function getPropertyData($id){

		return $this->owl_data['properties'][$id];
	}

	

	//---------------------------------------------------------------------------
	/**
	 * @param array with instances
	 */ 
	function getInstances(){

		return $this->owl_data['instances'];
	}


	//---------------------------------------------------------------------------
	/**
	 * @param array with information about instance
	 */ 
	function getInstanceData($id,$index){
	
		return $this->owl_data['instances'][$id][$index];
	}


	//---------------------------------------------------------------------------
	/**
	 * get label for given id
	 * @param $id object id
	 * @return label
	 */ 
	function getLabels($id){

		if(is_array($this->owl_data['labels'][$id]))
			return $this->owl_data['labels'][$id];
		else
			return array();
	}

	
	//---------------------------------------------------------------------------
	/**
	 * Get annotations for given id
	 * @param $id object id
	 * @return annotations array
	 * @author Karim
	 */
	 function getAnnotations($id)
	 {
	
		if (isset( $this->owl_data['annotations'][$id] )) 
		{
			return $this->owl_data['annotations'][$id];
		}
		else 
		{
			return array();
		}
	 }

	//---------------------------------------------------------------------------
	// Private members
	var		$owl_data;
	var		$namespace;
}
?>
