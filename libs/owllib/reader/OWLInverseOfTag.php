<?php
require_once "$OWLLIB_ROOT/reader/OWLTag.php";


/**
 *  Load information from <owl:inverseOf> node
 *
 *  @version	$Id: OWLInverseOfTag.php,v 1.1 2004/04/07 06:20:42 klangner Exp $
 */
class OWLInverseOfTag extends OWLTag
{
	
	//---------------------------------------------------------------------------
	/**
	 * process child:
	 *
	 */
	function processChild($child)
  {
 		$name = get_class($child);
  	if($name == "owlpropertytag"){
	 		$this->id = $child->getID();
  	}
  }

}

?>
