<?php
require_once "$OWLLIB_ROOT/OWLProperty.php";
require_once "$OWLLIB_ROOT/memory/OWLMemoryClass.php";
 

/**
 *	Implementation of OWL Property interface
 *  @version	$Id: OWLMemoryProperty.php,v 1.5 2004/04/07 06:20:42 klangner Exp $
 */
class OWLMemoryProperty extends OWLProperty
{
	
	//---------------------------------------------------------------------------
	/**
	 * @return array with domain classes
	 */
	function OWLMemoryProperty($id, &$model)
	{
		$this->id = $id;
		$this->model =& $model;
	}
   

	//---------------------------------------------------------------------------
	/**
	 * @return array with domain classes
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
	 * @return array with domain classes
	 */
	function getDomain()
	{
		$output = array();
		$property = $this->model->getPropertyData($this->id);

		if($property != null){
			foreach($property["domain"] as $class){
				array_push($output, new OWLMemoryClass($class, $this->model));
			}
		}
			
		return  $output;
	}
   

	//---------------------------------------------------------------------------
	/**
	 * @return array with range classes
	 */
	function getRange()
	{
		$output = array();
		$property = $this->model->getPropertyData($this->id);

		if($property != null){
			foreach($property["range"] as $class){
				array_push($output, new OWLMemoryClass($class, $this->model));
			}
		}
			
		return  $output;
	}


	//---------------------------------------------------------------------------
	/**
	 * @return true if this is datatype property
	 */
	function isDatatype()
	{
		$property = $this->model->getPropertyData($this->id);
		return $property["isdatatype"];
	}
   
	//---------------------------------------------------------------------------
	// Private members
	var $id;
	var	$model;
}
?>
