<?php
/**
 * PDFbase.class.php sets up the PDF parsing environment and contains methods for
 * extracting complex data types. It is the parent of all other PDF Reader
 * classes.
 *
 * PHP version 5.1
 *
 * @category  File_Formats
 * @package   File_PDFreader
 * @author    John M. Stokes <jstokes@heartofthefyre.us>
 * @copyright 2010, 2011 John M. Stokes
 * @license   http://www.opensource.org/licenses/bsd-license.html BSD Style License
 * @link      http://heartofthefyre.us/PDFreader/index.php
 */

require_once 'PDFexception.class.php';

/**
 * I include one class per file, so the file description is the class's description.
 *
 * @category  File_Formats
 * @package   File_PDFreader
 * @author    John M. Stokes <jstokes@heartofthefyre.us>
 * @copyright 2010, 2011 John M. Stokes
 * @license   http://www.opensource.org/licenses/bsd-license.html BSD Style License
 * @version   Release: 0.1.6
 * @link      http://heartofthefyre.us/PDFreader/index.php
 */
class PDFbase
{
    /*************
    * PROPERTIES *
    **************/

    const HEX_STRING_PATTERN = '|\\<[0-9A-Fa-f]{2,}?\\>|';
    const OCTAL_PATTERN = '|\\\\[0-7]{3}|';
    const REF_PATTERN = '|[0-9]+ [0-9]+ R|';
    const STRING_PATTERN = '|\\(.+?\\)|';
    const MAX_ITERATIONS = 2000;
    const DEBUG_OFF = 0;
    const DEBUG_HIDE_DECODING = 1;
    const DEBUG_HIDE_EXTRACTION = 2;
    const DEBUG_HIDE_STRUCTURE = 3;
    const DEBUG_SHOW_ALL = 4;

    //PDF Document Structure
    protected $Xrefs;
    protected $trailers;
    protected $root;
    protected $pageTree;

    //Programmer aids
    protected $debugLevel = 0;
    protected $iterations = 0;
    protected $fh;
    protected $PDFdecoder;

    /**********
    * METHODS *
    ***********/

    /**
     * __construct sets the debug level for all children
     */
    public function __construct()
    {
        $this->debugLevel = self::DEBUG_OFF;
    }//End __construct


    /**
     * extractObject extracts a PDF object as a string. i.e. all the data
     * between the obj and endobj tags for a given object reference.
     *
     * ALL OTHER DATA TYPE EXTRACTORS RELY ON THIS FUNCTION
     *
     * @param string $reference - a PDF style reference of the form "%d %d R"
     *
     * @return string $buffer - all the data between the obj and endobj tags
     */
    protected function extractObject($reference)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo 'Entered extractObject - ';
        }
        if (!isset($this->fh)) {
            Throw new PDFexception('Error: invalid file handle');
        }

        //Determine the object reference's offset
        if (isset($this->Xrefs[$reference])) {
            $offset = $this->Xrefs[$reference];
        } else {
            /*
             * When parsing the Xref table itself, we may not have all Xref
             * object references available. Crawl through the file and look
             * for the object.
             */
            $objectName = str_replace('R', 'obj', $reference);
            $objectFound = false;
            rewind($this->fh);
            while (!feof($this->fh)) {
                $line = fgets($this->fh);
                if (strpos($line, $objectName) !== false) { //Found obj. Get offset.
                    $objectFound = true;
                    //Calculate how far to back up the pointer to get the offset
                    $backup = strlen(substr($line, strpos($line, $objectName)));
                    $offset = ftell($this->fh)-$backup;
                    break;
                }
            }
            if (!$objectFound) {
                Throw new PDFexception("Error: Object $reference not found.\n");
            }
        }
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Offset for $reference: $offset<br />\n";
        }

        /*
         * In the case of stream objects, the reference refers to the actual object,
         * not the offset, so return it immediately
         */
        if (!is_integer($offset)) {
            return $offset;
        }

        fseek($this->fh, $offset);
        $buffer = fread($this->fh, 64); //Import the object in 64 byte increments
        //Chop 'obj' and anything before it off the string
        $buffer = substr($buffer, strpos($buffer, 'obj')+3);
        while (strpos($buffer, 'endobj') === false) {
            $buffer .= fread($this->fh, 64);
        }
        //Chop 'endobj' and everything after it off the string
        $buffer = substr($buffer, 0, strpos($buffer, 'endobj'));
        $buffer = ltrim($buffer);

        return $buffer;
    }//End extractObject


    /**
     * extractDictionary converts a PDF dictionary into a PHP associative array
     *
     * @param string $dictString - a string to parse for dictionary key/value pairs.
     *     Should be delimited by << and >>
     *
     * @return array $dictionary - the (multi-dimensional?) array of key/value pairs
     */
    protected function extractDictionary($dictString)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractDictionary<br />\n";
        }
        if (++$this->iterations > self::MAX_ITERATIONS) { //Recursion failsafe
            Throw new PDFexception('Dictionary Overflow Error');
        }
        //Strip off the << and anything before it
        $dictString = substr($dictString, strpos($dictString, '<<')+2);
        //In the case of linearized PDFs, strip anything after the pseudo-EOF
        //so we don't get a false position for >>
        if (strpos($dictString, '%%EOF') !== false) {
            $dictString = substr($dictString, 0, strpos($dictString, '%%EOF'));
        }
        //In the case of stream objects, strip anything after the 'stream'
        //so we don't get a false position for >>
        if (strpos($dictString, 'stream') !== false) {
            $dictString = substr($dictString, 0, strpos($dictString, 'stream'));
        }
        //Strip off the last >> and anything after it
        $dictString = substr($dictString, 0, strrpos($dictString, '>>'));


        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo 'Dictionary String, pre-extraction: ';
            echo htmlentities($dictString)."<br />\n";
        }

        /**** Extract objects that could contain whitespace ****/
        /*
         * SUBDICTIONARY EXTRACTION
         * << and >> should have already been removed from the parent dictionary
         */

        /*
         * HANDLE NESTED SUBDICTIONARIES (replacing top-level << and >> with ``
         * This convoluted workaround is necessary because dictionaries can be
         * nested to an arbitrary depth and use a two-character delimiter
         */
        $nestLevel = 0;
        $arrayNestLevel = -1; //-1 = not in array. Ignore arrays of subdicts for now.
        $currentChar = 0;
        //Use delimiters that are unlikely to appear together
        $dictDelim = '`';
        $openSubdict = '@';
        $closeSubdict = '^';
        $inString = false;
        while ($currentChar < strlen($dictString)) {
            //Ignore curly braces in string literals
            if ($dictString[$currentChar] == '(') {
                $inString = true;
            } else if ($dictString[$currentChar] == ')') {
                $inString = false;
            }

            if (!$inString && $dictString[$currentChar] == '[') {
                ++$arrayNestLevel;
            } else if (!$inString && $dictString[$currentChar] == ']') {
                --$arrayNestLevel;
            }

            if (!$inString && $arrayNestLevel < 0
                && $dictString[$currentChar] == '<'
                && $dictString[$currentChar+1] == '<'
            ) {
                if (isset($dictString[$currentChar-1])
                    && $dictString[$currentChar-1] == '['
                ) {
                    break; //If this subdictionary is in an array, ignore it
                }
                if ($nestLevel == 0) {
                    $dictString[$currentChar] = $dictDelim;
                    $dictString[$currentChar+1] = $dictDelim;
                } else {
                    $dictString[$currentChar] = $openSubdict;
                    $dictString[$currentChar+1] = $openSubdict;
                }
                ++$nestLevel;
            } else if (!$inString && $arrayNestLevel < 0
                && $dictString[$currentChar] == '>'
                && $dictString[$currentChar+1] == '>'
            ) {
                --$nestLevel;
                if ($nestLevel == 0) {
                    $dictString[$currentChar] = $dictDelim;
                    $dictString[$currentChar+1] = $dictDelim;
                } else {
                    //Temporarily replace inner subdictionary delimiters,
                    //so subdicts of the form <<sub<<sub>>>> don't get confused
                    $dictString[$currentChar] = $closeSubdict;
                    $dictString[$currentChar+1] = $closeSubdict;
                }
            }
            $currentChar += 1;
        }
        //Now fix the subdictionary delimiters
        $dictString = str_replace($openSubdict.$openSubdict, '<<', $dictString);
        $dictString = str_replace($closeSubdict.$closeSubdict, '>>', $dictString);
        $subDictPattern = '/'.$dictDelim.$dictDelim.'.*?'.$dictDelim.$dictDelim.'/';
        $subDictArray = array();
        $matches = preg_match_all(
            $subDictPattern, $dictString, $subDictArray, PREG_PATTERN_ORDER
        );
        if ($matches > 0) {
            $dictString = preg_replace(
                $subDictPattern, 'subdictionary', $dictString
            );
        }
        /* END SUBDICTIONARY EXTRACTION */      

        /* ARRAY EXTRACTION */
        //HANDLE NESTED ARRAYS (replacing [ and ] with `
        $nestLevel = 0;
        $currentChar = 0;
        $inString = false;
        while ($currentChar < strlen($dictString)) {
            //Ignore brackets in string literals
            if ($dictString[$currentChar] == '(') {
                $inString = true;
            } else if ($dictString[$currentChar] == ')') {
                $inString = false;
            }

            if (!$inString && $dictString[$currentChar] == '[') {
                if ($nestLevel == 0) {
                    $dictString[$currentChar] = '`';
                }
                ++$nestLevel;
            } else if (!$inString && $dictString[$currentChar] == ']') {
                --$nestLevel;
                if ($nestLevel == 0) {
                    $dictString[$currentChar] = '`';
                }
            }
            ++$currentChar;
        }
        $arrayPattern = '/`.*?`/s';
        $arrayArray = array();       
        $matches = preg_match_all(
            $arrayPattern, $dictString, $arrayArray
        );
        if ($matches > 0) {
            $dictString = preg_replace($arrayPattern, 'PDFarray', $dictString);
        }         
        /* END ARRAY EXTRACTION */

        //Extract references
        $refArray = array();
        $matches = preg_match_all(
            self::REF_PATTERN, $dictString, $refArray, PREG_PATTERN_ORDER
        );
        if ($matches > 0) {
            $dictString = preg_replace(self::REF_PATTERN, 'reference', $dictString);
        }
        //Extract literal strings
        //Temporarily replace sub-parentheses
        $dictString = str_replace('\\(', 'OPENPAREN', $dictString);
        $dictString = str_replace('\\)', 'CLOSEPAREN', $dictString);
        $stringArray = array();
        $matches = preg_match_all(
            self::STRING_PATTERN, $dictString, $stringArray, PREG_PATTERN_ORDER
        );
        if ($matches > 0) {
            $dictString = preg_replace(
                self::STRING_PATTERN, 'literalString', $dictString
            );
        }

        //Extract hexadecimal strings
        $hexStringArray = array();
        $matches = preg_match_all(
            self::HEX_STRING_PATTERN, $dictString,
            $hexStringArray, PREG_PATTERN_ORDER
        );
        if ($matches > 0) {
            $dictString = preg_replace(
                self::HEX_STRING_PATTERN, 'hexString', $dictString
            );
        }

        //Be sure there are spaces before objects
        $findArray = array('true','false','/','PDFarray','subdictionary',
            'reference','literalString','hexString'
        );
        $replaceArray = array(' true',' false',' /',' PDFarray',' subdictionary',
            ' reference',' literalString',' hexString'
        );
        $dictString = str_replace($findArray, $replaceArray, $dictString);
        //Finally, replace any extra whitespace created above with a single space
        $dictString = str_replace('  ', ' ', $dictString);
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Dictionary String, post-extraction: $dictString<br />\n";
        }

        $dictString = trim($dictString);
        /*
         * Split on white space.
         * 0 and even numbers should be keys
         * odd numbers should be values
         */
        $dictArray = preg_split('|\s|', $dictString);
        $dictionary = array();
        for ($i=0; $i<count($dictArray); $i+=2) {
            $key = trim($dictArray[$i]);
            if (empty($key)) { //Ignore blanks
                --$i; //Subtract 1, add 2 so we offset by +1
                continue;
            }
            if ($key[0] == '/') {
                //Strip the leading slash from the Name object
                $key = substr($key, 1);
            } else {
                --$i; //No slash. This must be a value.
                continue;
            }
            if (isset($dictArray[$i+1])) {
                $dictionary[$key] = $dictArray[$i+1];
            }
        }


        //Repopulate dictionaries
        $refCounter = $stringCounter = $hexCounter = 0;
        $arrayCounter = $subdictCounter = 0;
        $keys = array_keys($dictionary);
        foreach ($keys as $key) {
            if ($dictionary[$key] == 'subdictionary') {
                //Put the outside << and >> back on the subdictionary
                $subdictionary = trim($subDictArray[0][$subdictCounter++]);
                $subdictionary[0] = '<';
                $subdictionary[1] = '<';
                $subdictionary[strlen($subdictionary)-2] = '>';
                $subdictionary[strlen($subdictionary)-1] = '>';
                //Recursively handle sub-dictionaries
                $dictionary[$key] = $this->extractDictionary($subdictionary);
            }
        }
        //Repopulate arrays
        foreach ($keys as $key) {
            if ($dictionary[$key] == 'PDFarray') {
                $dictionary[$key] = $this->extractArray(
                    $arrayArray[0][$arrayCounter++]
                );
            }
        }

        //Repopulate references, string literals, hex strings
        foreach ($keys as $key) {
            if ($dictionary[$key] == 'reference') {
                $dictionary[$key] = $refArray[0][$refCounter++];
            } else if ($dictionary[$key] == 'literalString') {
                //Put literal parentheses back
                $dictionary[$key] = str_replace(
                    'OPENPAREN', '(', $stringArray[0][$stringCounter++]
                );
                $dictionary[$key] = str_replace(
                    'CLOSEPAREN', ')', $dictionary[$key]
                );
            } else if ($dictionary[$key] == 'hexString') {
                if ($key == 'Contents') {
                    $dictionary[$key] = $this->PDFdecoder->decodeHexString(
                        $hexStringArray[0][$hexCounter++]
                    );
                } else {
                    $dictionary[$key] = $hexStringArray[0][$hexCounter++];
                }
            }
        }

        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo 'Finished Dictionary: ';
            var_dump($dictionary);
            echo "<br />\n";
        }

        return $dictionary;
    }//End extractDictionary


    /**
     * extractStream extracts all the data between the stream and
     * endstreamtags for a given object reference.
     *
     * @param string $reference - a PDF reference of the form %d %d R
     *
     * @return array $stream - a multi-dimensional array containing
     *     the stream dictionary and contents
     */
    protected function extractStream($reference)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractStream<br />\n";
        }
        $stream = array();

        $streamObj = $this->extractObject($reference);
        $this->iterations = 0; //Reset iterations for extractDictionary call
        $stream['Dictionary'] = $this->extractDictionary($streamObj);

        //Chop 'stream' and anything before it off the string
        $buffer = substr($streamObj, strpos($streamObj, 'stream')+6);
        $buffer = ltrim($buffer); //Remove whitespace before stream, per PDF spec
        if (isset($stream['Dictionary']['Length'])
            && preg_match(self::REF_PATTERN, $stream['Dictionary']['Length'])
        ) { //If Length is a reference, extract the actual value
            $length = $this->extractObject($stream['Dictionary']['Length']);
            $buffer = substr($buffer, 0, $length);
        } else if (isset($stream['Dictionary']['Length'])) {
            $buffer = substr($buffer, 0, $stream['Dictionary']['Length']);
        }
        $stream['Contents'] = $buffer;

        return $stream;
    }//End extractStream


    /**
     * extractObjectStream recursively decompresses and
     * assembles a collection of stream objects
     *
     * @param string $reference - a PDF reference of the form %d %d R
     *
     * @return array $objectArray - associative array of reference-to-object mappings
     */
    protected function extractObjectStream($reference)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractObjectStream<br />\n";
        }
        if (++$this->iterations > self::MAX_ITERATIONS) { //Recursion failsafe
            Throw new PDFexception('Object Stream Overflow Error');
        }
        $objectArray = array();

        $objectStream = $this->extractStream($reference);
        $data = $objectStream['Contents'];
        if (isset($objectStream['Dictionary']['Filter'])) {
            $data = $this->PDFdecoder->unfilter(
                $objectStream['Dictionary']['Filter'], $data
            );
        }

        //Get the references and byte offsets (i.e. all data before ['First'])
        if (!isset($objectStream['Dictionary']['First'])) {
            Throw new PDFexception('Object Stream Error:
                "First" dictionary entry missing'
            );
        }
        $references = substr($data, 0, $objectStream['Dictionary']['First']);
        $objects = substr($data, $objectStream['Dictionary']['First']);
        /*
         * Split $references into an array.
         * even #s should be Objects
         * odd #s should be byte offsets
         */
        $references = explode(' ', trim($references));
        for ($i=0; $i<count($references); $i+=2) {
            $reference = $references[$i].' 0 R';
            if (isset($this->Xrefs[$reference])
                && strpos($this->Xrefs[$reference], '/V') !== false
            ) {
                continue; //If this object has been decoded (with a value), skip it
            }
            $start = $references[$i+1];
            if (isset($references[$i+3])) {
                //End of the current object = start of the next object
                $length = $references[$i+3]-$references[$i+1];
                $objectArray[$reference] = substr($objects, $start, $length);
            } else { //Last object will go to end of string
                $objectArray[$reference] = substr($objects, $start);
            }
        }

        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Object Stream:<br />\n";
            foreach ($objectArray as $ref=>$obj) {
                echo "Reference: $ref Object: ".htmlentities($obj)."<br />\n";
            }
        }

        return $objectArray;
    }//End extractObjectStream


    /**
     * extractArray splits a string representing a PDFarray into a PHP array
     *
     * @param string $arrayString - a string representing a PDFarray
     *     Should be delimited by [ and ]
     *
     * @return array $arrayArray - the PHP array constructed from the PDF array
     */
    protected function extractArray($arrayString)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractArray<br />\n";
        }
        if (++$this->iterations > self::MAX_ITERATIONS) { //Recursion failsafe
            Throw new PDFexception('Array Overflow Error');
        }

        $arrayString = substr($arrayString, 1, -1); //Strip [ and ] or ` and `
        $arrayArray = $tempArray = array();
        $arrayPattern = '|\\[.*?\\]|';

        //Be sure there are spaces before objects
        $searchArray = array('true', 'false', '/');
        $replaceArray = array(' true', ' false', ' /');
        $arrayString = str_replace($searchArray, $replaceArray, $arrayString);
        //Finally, replace any extra whitespace created above with single spaces
        $arrayString = str_replace('  ', ' ', $arrayString);

        //Determine the array type
        if (preg_match(self::REF_PATTERN, $arrayString)) { //Reference array
            preg_match_all(self::REF_PATTERN, $arrayString, $tempArray);
            $arrayArray = $tempArray[0];
        } else if (preg_match(self::STRING_PATTERN, $arrayString)) {
            //Temporarily replace escaped parentheses
            $arrayString = str_replace('\\(', 'OPENPAREN', $arrayString);
            $arrayString = str_replace('\\)', 'CLOSEPAREN', $arrayString);
            preg_match_all(
                self::STRING_PATTERN, $arrayString, $tempArray
            );
            foreach ($tempArray[0] as $entry) {
                $entry = substr($entry, 1, -1); //Strip ( and );
                //Put parens back
                $entry = str_replace('OPENPAREN', '\\(', $entry);
                $entry = str_replace('CLOSEPAREN', '\\)', $entry);
                $arrayArray[] = $entry;
            }
        } else if (preg_match(self::HEX_STRING_PATTERN, $arrayString)) {
            preg_match_all(
                self::HEX_STRING_PATTERN, $arrayString, $tempArray
            );
            $arrayArray = $tempArray[0];
        } else if (preg_match($arrayPattern, $arrayString)) { //Array of arrays
            preg_match_all($arrayPattern, $arrayString, $tempArray);
            foreach ($tempArray[0] as $entry) {
                $arrayArray[] = $this->extractArray($entry);
            }
        } else {//If nothing else matches, assume space-delimited string
            $arrayArray = explode(' ', trim($arrayString));
        }

        return $arrayArray;
    }//End extractArray
}//End PDFbase class
?>