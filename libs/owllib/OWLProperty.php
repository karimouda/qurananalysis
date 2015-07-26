<?php

/**
 *	OWL Property interface
 *  @version	$Id: OWLProperty.php,v 1.6 2004/03/30 09:02:24 klangner Exp $
 */
class OWLProperty
{
	
	//---------------------------------------------------------------------------
	/**
	 * get property id
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
	 * @return array with domain classes
	 */
	function getDomain()
	{
	}
   

	//---------------------------------------------------------------------------
	/**
	 * @return array with range classes
	 */
	function getRange()
	{
	}
   
	//---------------------------------------------------------------------------
	/**
	 * @return true if this is datatype property
	 */
	function isDatatype()
	{
	}
   
}
?>
