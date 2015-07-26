<?php

/**
 *	OWL Class interface
 *  @version	$Id: OWLClass.php,v 1.9 2004/03/30 13:07:03 klangner Exp $
 */
class OWLClass
{
	
	//---------------------------------------------------------------------------
	/**
	 * get class id
	 */
	function getID()
	{
	}


	//---------------------------------------------------------------------------
	/**
	 * get label
	 */
	function getLabel($language)
	{
	}


	//---------------------------------------------------------------------------
	/**
	 * get all labels
	 */
	function getAllLabels()
	{
	}

	//---------------------------------------------------------------------------
	/**
	 * @return array with superclasses 
	 */
	function getSuperclasses()
	{
	}


	//---------------------------------------------------------------------------
	/**
	 * @return array with subclasses 
	 */
	function getSubclasses()
	{
	}


	//---------------------------------------------------------------------------
	/**
	 * @oaram $all if true return all properties: 
	 *						 from this class and from superclasses
	 * @return array with properties
	 */
	function getProperties($all)
	{
	}


	//---------------------------------------------------------------------------
	/**
	 * @return array with instances
	 */
	function getInstances()
	{
	}


}
?>
