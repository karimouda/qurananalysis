<?php

/**
 *	OWL Ontology abstract class
 *  @version	$Id: OWLOntology.php,v 1.6 2004/04/07 06:20:42 klangner Exp $
 */
class OWLOntology
{

	//----------------------------------------------------------------------------
	/**
	 * get ontology namespace
	 */
	function getNamespace(){
	}

	//----------------------------------------------------------------------------
	/**
	 * get class
	 */
	function getClass($id){
	}

	//----------------------------------------------------------------------------
	/**
	 * get all classes
	 */
	function getAllClasses(){
	}

	//---------------------------------------------------------------------------
	/**
	 * Get property with give $id
	 * @param $id property id
	 * @return OWLProperty class
	 */ 
	function getProperty($id){
	}

	//----------------------------------------------------------------------------
	/**
	 * get all properties
	 */
	function getAllProperties(){
	}

	//---------------------------------------------------------------------------
	/**
	 * Get instance with give $id
	 * @param $id instance id
	 * @return OWLInstance class
	 */ 
	function getInstance($id){
	}

	//----------------------------------------------------------------------------
	/**
	 * get all instances
	 */
	function getAllInstances(){
	}

	//----------------------------------------------------------------------------
	/**
	 * set ontology namespace
	 */
	function setNamespace($namespace){
	}

	//---------------------------------------------------------------------------
	/**
	 * create new class
	 */
	function createClass($id){
  }

	//---------------------------------------------------------------------------
	/**
	 * create new property
	 */
	function createProperty($id){
  }

	//---------------------------------------------------------------------------
	/**
	 * add new instance
	 */
	function addInstance($id, $class, $properties){
  }


	//---------------------------------------------------------------------------
	/**
	 * add label
	 */
	function addLabel($id, $lang, $label){
  }
	
}
?>
