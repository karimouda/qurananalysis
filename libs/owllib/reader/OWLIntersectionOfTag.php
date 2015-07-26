<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <owl:OintersectionOf> node
 *
 *  @version	$Id: OWLIntersectionOfTag.php,v 1.1 2004/03/30 13:57:36 klangner Exp $
 */
class OWLIntersectionOfTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * process child:
	 *
	 */
	function processChild($child)
  {
 		$name = get_class($child);
  	if($name == "owlclasstag"){
	 		$this->id = $child->getID(); 
  	}
  }

	
	//---------------------------------------------------------------------------
	// Private members
	
}

?>
