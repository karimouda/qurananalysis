<?php
require_once "$OWLLIB_ROOT/OWLClass.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryProperty.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryInstance.php";

/**
 *	OWL Class interface
 *  @version	$Id: OWLMemoryClass.php,v 1.7 2004/04/07 06:20:42 klangner Exp $
 */
class OWLMemoryClass extends OWLClass
{
	
	//---------------------------------------------------------------------------
	/**
	 * constructor
	 * @param $name class name
	 * @param $super array with superclasses
	 * @param $sub array with subclasses
	 * 
	 */
	function OWLMemoryClass($id, &$model){
		
		$this->id = $id;
		$this->model =& $model; 
	}
	
	
	//---------------------------------------------------------------------------
	/**
	 * Return class name
	 */
	function getID()
	{
		return $this->id;
	}
	
	
	//---------------------------------------------------------------------------
	/**
	 * get label
	 */
	function getLabel($language)
	{
		if($this->id == "http://www.w3.org/2002/07/owl#Thing")
			return "Thing";

		$labels = $this->model->getLabels($this->id); 
		
		if(is_array($labels) && array_key_exists($language, $labels)){
			$label = $labels[$language];
		}
		else{
			$label = $this->id;
		}
			
		return $label;
	}


	//---------------------------------------------------------------------------
	/**
	 * get all labels
	 */
	function getAllLabels()
	{
		return $this->model->getLabels($this->id);
	}

	//---------------------------------------------------------------------------
	/**
	 * Get all annotations for this class
	 * @author Karim
	 */
	 function getAllAnnotations()
	 {
	  
	 return $this->model->getAnnotations($this->id);
	 }

	//---------------------------------------------------------------------------
	/**
	 * get superclasses 
	 */
	function getSuperclasses()
	{
		$output = array();
		$subclasses = $this->model->getSubclasses();
		foreach($subclasses as $rel){
			
			if($rel[1] == $this->id)
				array_push($output, new OWLMemoryClass($rel[0], $this->model));
		}		
		
		if(count($output) == 0 && $this->id != "http://www.w3.org/2002/07/owl#Thing"){
			array_push($output, new OWLMemoryClass("http://www.w3.org/2002/07/owl#Thing", $this->model));
		}
		
		return $output;
	}


	//---------------------------------------------------------------------------
	/**
	 * get subclasses 
	 */
	function getSubclasses()
	{
		$output = array();
		$subclasses = $this->model->getSubclasses();
		
		foreach($subclasses as $rel){
			
			if($rel[0] == $this->id)
				array_push($output, new OWLMemoryClass($rel[1], $this->model));
		}		

		// For owl:Thing add classes without parent
		if($this->id == "http://www.w3.org/2002/07/owl#Thing"){

			$classes =& $this->model->getClasses();
			foreach($classes as $key => $data){

				$found = false;				
				foreach($subclasses as $rel){
					if($rel[1] == $key){
						$found = true;
						break;
					}
				}		
				
				if(!$found)
					array_push($output, new OWLMemoryClass($key, $this->model));
			}		
		}
				
		return $output;
	}


	//---------------------------------------------------------------------------
	/**
	 * @param $all if true return properties from superclasses
	 * @return array with OWLProperty classes 
	 */
	function getProperties($all)
	{
		$output = array();
		$properties =& $this->model->getProperties();
		foreach($properties as $key => $property){
			
			if(in_array($this->id, $property["domain"]))
				array_push($output, new OWLMemoryProperty($key, $this->model));
		}
		
		if($all){
			$superclasses = $this->getSuperclasses();
			foreach($superclasses as $superclass){
				$p = $superclass->getProperties(true);
				$output = array_merge($output, $p);
			}
		}		
		
		return $output;
	}


	//---------------------------------------------------------------------------
	/**
	 * @return array with instances
	 */
	function getInstances()
	{
		$output = array();
		$instances = $this->model->getInstances();
		foreach($instances as $key => $instance){
			
			if($this->id == $instance["class"])
				array_push($output, new OWLMemoryInstance($key, $this->model));
		}		
		
		return $output;
	}


	//---------------------------------------------------------------------------
	/**
	 * add superclass
	 */
	function addSuperclass($super)
  {
		$this->model->addSuperclass($super, $this->id);
  }

	
	//---------------------------------------------------------------------------
	// Private members
	var $id;
	var $model;
}
?>
