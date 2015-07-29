<?php
require_once "$OWLLIB_ROOT/reader/OWLPropertyTag.php";
require_once "$OWLLIB_ROOT/reader/OWLClassTag.php";
require_once "$OWLLIB_ROOT/reader/OWLSubClassOfTag.php";
require_once "$OWLLIB_ROOT/reader/OWLDomainTag.php";
require_once "$OWLLIB_ROOT/reader/OWLRangeTag.php";
require_once "$OWLLIB_ROOT/reader/OWLInstanceTag.php";
require_once "$OWLLIB_ROOT/reader/OWLPropInstanceTag.php";
require_once "$OWLLIB_ROOT/reader/OWLLabelTag.php";
require_once "$OWLLIB_ROOT/reader/OWLIntersectionOfTag.php";
require_once "$OWLLIB_ROOT/reader/OWLInverseOfTag.php";





/**
 *	Parse owl file and represents file in memory as collection of tables
 *
 *  From RDF specification:
 *  - The base URI (xml:base) applies to all RDF/XML attributes that deal 
 *    with RDF URI references which are 
 *    rdf:about, rdf:resource, rdf:ID and rdf:datatype
 *
 *  - If xml:base is not provided than the baseURI of the document is used. 
 *
 *  @version	$Id: OWLReader.php,v 1.6 2004/04/07 06:20:42 klangner Exp $
 */
class OWLReader
{
	
	//---------------------------------------------------------------------------
	/**
	 * Constructor
	 */
	function OWLReader()
  {
		$this->root_tag = new OWLTag();
   	$this->parser = xml_parser_create_ns();

		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		//KARIM: removed & before "$this->root_tag" since it is not allowed in new versions of PHP
   	xml_set_object($this->parser, $this->root_tag);
   	xml_set_element_handler($this->parser, "startTag", "endTag");
		xml_set_character_data_handler($this->parser, "characters");
		xml_set_start_namespace_decl_handler ( $this->parser, "namespaceDecl");
		
  }


	//---------------------------------------------------------------------------
	/**
	 * Constructor
	 */
	function readFromFile($owl_file, &$ontology)
  {
		$filehandler = @fopen($owl_file, 'r');
		if($filehandler == false)
			return '';

		$this->root_tag->create($ontology, "", array(), $owl_file);
		$ontology->setNamespace($owl_file);
		while ($data = fread($filehandler, 4096)) {
			
    	xml_parse($this->parser, $data, feof($filehandler));
		}

		//xml_error_string(xml_get_error_code($this->parser));
		// close file
		fclose($filehandler);

  }


	//---------------------------------------------------------------------------
	// Private members
	var $parser;
	var $root_tag;

}

?>
