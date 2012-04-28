<?
# A simple interface to the RAP RDF/XML parser.

# Copyright (c) 2004 Morten Frederiksen
# License: http://www.gnu.org/licenses/gpl

require_once(RDFAPI_INCLUDE_DIR . 'constants.php');
require_once(RDFAPI_INCLUDE_DIR . 'util/Object.php');
require_once(RDFAPI_INCLUDE_DIR . 'util/RdfUtil.php');
require_once(RDFAPI_INCLUDE_DIR . 'syntax/RdfParser.php');

class SimpleRdfParser extends RdfParser
{
	var $triples;

	# Constructor.
	function SimpleRdfParser() {
		$this->triples = array();
	}

	# The main parsing function, requiring a string with RDF/XML (GET it
	# yourself) and a base URI.
	function & string2triples($rdf, $base) {
		$this->triples=array();
		$this->rdf_parser_create(NULL);
		$this->rdf_set_base($base);
		if (!$this->rdf_parse($rdf, TRUE)) {
			$err_code = xml_get_error_code($this->rdf_get_xml_parser());
			$line = xml_get_current_line_number($this->rdf_get_xml_parser());
			return RDFAPI_ERROR . '(class: RdfSimpleParser; method: string2triples): XML-parser-error ' . $err_code .' in Line ' . $line .' of input document.';
		}
		$this->rdf_parser_free();
		return $this->triples;
	}

	# Turn the triples array into predictable RDF/XML, grouped by subject.
	function triples2string() {
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">';
		foreach ($this->triples as $s => $po) {
			# Start with subject.
			$xml .= "\n<rdf:Description";
			if ('_:' == substr($s, 0, 2))
				$xml .= ' rdf:nodeID="' . htmlspecialchars(substr($s,2)) . '"';
			else
				$xml .= ' rdf:about="' . htmlspecialchars($s) . '"';
			$xml .= ">";
			# Loop through predicate/object pairs.
			foreach($po as $x) {
				list($p, $o) = $x;
				# Output predicate.
				$nsuri = RDFUtil::guessNamespace($p);
				$local = RDFUtil::guessName($p);
				if ('http://www.w3.org/1999/02/22-rdf-syntax-ns#' != $nsuri)
					$xml .= "\n  <ns:" . $local . ' xmlns:ns="' . $nsuri . '"';
				else
					$xml .= "\n  <rdf:" . $local;
				# Output object.
				if (is_array($o)) {
					if ('' != $o[1])
						$xml .= ' xml:lang="' . htmlspecialchars($o[1]) . '"';
					if ('http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral' == $o[2])
						$xml .= ' rdf:parseType="Literal">' . str_replace('\"', '"', $o[0]);
					else if ('' != $o[2])
						$xml .= ' rdf:datatype="' . htmlspecialchars($o[2]) . '">' . $o[0];
					else
						$xml .= '>' . htmlspecialchars($o[0]);
					if ('http://www.w3.org/1999/02/22-rdf-syntax-ns#' != $nsuri)
						$xml .= '</ns:' . $local;
					else
						$xml .= '</rdf:' . $local;
				} else if ('_:' == substr($o, 0, 2))
					$xml .= ' rdf:nodeID="' . htmlspecialchars(substr($o, 2)) . '"/';
				else
					$xml .= ' rdf:resource="' . htmlspecialchars($o) . '"/';
				$xml.=">";
			};
			$xml.="\n</rdf:Description>";
		}
		$xml.="\n</rdf:RDF>";
		return $xml;
	}

	# Private function to interface with parser.
	function add_statement_to_model(&$user_data, $subject_type, $subject,
			$predicate, $ordinal, $object_type, $object, $xml_lang, $datatype) {
		# Create subject
		if (RDF_SUBJECT_TYPE_BNODE == $subject_type)
			$s = '_:' . $subject;
		else
			$s = $subject;
		# Create predicate
		$p = $predicate;
		# Create object
		if (RDF_OBJECT_TYPE_BNODE == $object_type)
			$o = '_:' . $object;
		else if (RDF_OBJECT_TYPE_RESOURCE == $object_type)
			$o = $object;
		else
			$o = array($object, $xml_lang, $datatype);
		# Add statement to triples.
		if(!array_key_exists($s, $this->triples))
			$this->triples[$s] = array();
		array_push($this->triples[$s], array($p, $o));
	}

}

?>