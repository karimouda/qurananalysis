<?php

/**
 *	OWL instance interface
 *  @version	$Id: OWLInstance.php,v 1.6 2004/03/30 09:02:24 klangner Exp $
 */
class OWLInstance
{
	
	//---------------------------------------------------------------------------
	/**
	 * Return instance id
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
	 * Return instance class
	 */
	function getClass()
	{
	}
   
	//---------------------------------------------------------------------------
	/**
	 * @param $property_id property id
	 * @return instance property value
	 */
	function getPropertyValues($property_id)
	{
	}
   
	//---------------------------------------------------------------------------
	/**
	 * @return instance property values
	 */
	function getAllPropertyValues()
	{
	}
   
}
?>
