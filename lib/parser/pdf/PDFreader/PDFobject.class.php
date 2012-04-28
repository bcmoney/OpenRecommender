<?php
/**
 * PDFobject.class.php dereferences and decodes PDF content stream objects.
 *
 * The class name is a bit of a misnomer, since this class doesn't
 * represent ANY PDF object, only content stream objects. Content streams
 * are the most important object type for parsing text.
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

require_once 'PDFbase.class.php';
require_once 'PDFdecoder.class.php';

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
class PDFobject extends PDFbase
{
    /*************
    * PROPERTIES *
    **************/

    //PDF Page Attributes
    protected $ExtGState;
    protected $ColorSpace;
    protected $Pattern;
    protected $Shading;
    protected $XObject;
    protected $Font;
    protected $ProcSet;
    protected $Properties;

    //PDF Content Stream Attributes
    protected $Length;
    protected $Filter;
    protected $DecodeParms;
    protected $F;
    protected $FFilter;
    protected $FDecodeParms;
    protected $DL;

    //PHP properties
    protected $reference;
    protected $stream;
    protected $decoded;

    /**********
    * METHODS *
    ***********/

    /**
     * __construct accepts a single PDF reference, dereferences it,
     * parses the dictionary and decodes the text in the content stream
     *
     * The combination of a resource dictionary and content stream constitutes
     * a self-contained entity. PDF spec 7.8.1
     *
     * @param resource $fh          - the file handle created by the PDFreader class
     * @param array    $Xrefs       - the XRef table extracted by the PDFreader class
     * @param object   $PDFdecoder  - the PDFdecoder from the PDFreader class
     * @param string   $reference   - a PDF reference to this object like %d %d R
     * @param string   $Resources   - the parent page's resource dictionary
     *
     * @return N/A
     */
    public function __construct($fh, $Xrefs, $PDFdecoder, $reference, $Resources)
    {
        parent::__construct();
        $this->fh = $fh;
        $this->Xrefs = $Xrefs;
        $this->PDFdecoder = $PDFdecoder;

        if (!isset($reference)) {
            Throw new PDFexception('Error: Invalid object reference');
        }
        if ($this->debugLevel > self::DEBUG_OFF) {
            echo "<strong>Creating content stream $reference</strong><br />\n";
        }

        $this->reference = $reference;
        $this->parseResourceDictionary($Resources);
        $stream = $this->extractStream($reference);
        $this->parseStreamDictionary($stream['Dictionary']);
        $this->stream = $stream['Contents'];
        $this->decodeText();
    }//End __construct


    /**
     * getText returns the human-readable string representing this object
     *
     * @return string $this->decoded - the decoded string
     */
    public function getText()
    {
        return $this->decoded;
    }//End getText


     /**
     * parseResourceDictionary applies the page's resource dictionary to this object
     *
     * @param string $Resources - the Page's resource dictionary
     *
     * @return array N/A - operates directly on the object's properties
     */
    protected function parseResourceDictionary($Resources)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered parseResourceDictionary<br />\n";
        }

        if (isset($Resources['ExtGState'])) {
            $this->ExtGState = $Resources['ExtGState'];
        }
        if (isset($Resources['ColorSpace'])) {
            $this->ColorSpace = $Resources['ColorSpace'];
        }
        if (isset($Resources['Pattern'])) {
            $this->Pattern = $Resources['Pattern'];
        }
        if (isset($Resources['Shading'])) {
            $this->Shading = $Resources['Shading'];
        }
        if (isset($Resources['XObject'])) {
            $this->XObject = $Resources['XObject'];
        }
        if (isset($Resources['Font'])) {
            //If Font is a string, assume indirect reference
            if (is_string($Resources['Font'])) {
                $fontObj = $this->extractObject($Resources['Font']);
                $this->Font = $this->extractDictionary($fontObj);
            } else {//If it's an array, assume actual font dictionary
                $this->Font = $Resources['Font'];
            }
        }
        if (isset($Resources['ProcSet'])) {
            $this->ProcSet = $Resources['ProcSet'];
        }
        if (isset($Resources['Properties'])) {
            $this->Properties = $Resources['Properties'];
        }
        return;
    }//End parseResourceDictionary


     /**
     * parseStreamDictionary extracts the stream dictionary for this object and
     * applies the entries to the object's properties
     *
     * @param string $streamDict - the array representing this object's dictionary
     *
     * @return array N/A operates directly on the dictionary properties property
     */
    protected function parseStreamDictionary($streamDict)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered parseStreamDictionary<br />\n";
        }

        if (isset($streamDict['Length'])) {
            //If Length is a reference, extract the actual value
            if (preg_match(self::REF_PATTERN, $streamDict['Length'])) {
                $this->Length = $this->extractObject($streamDict['Length']);
            } else {
                $this->Length = $streamDict['Length'];
            }
        }
        if (isset($streamDict['Filter'])) {
            if (is_array($streamDict['Filter'])) {
                $this->Filter = implode(' ', $streamDict['Filter']);
            } else {
                $this->Filter = $streamDict['Filter'];
            }
        }
        if (isset($streamDict['DecodeParms'])) {
            $this->DecodeParms = $streamDict['DecodeParms'];
        }
        if (isset($streamDict['F'])) {
            Throw new PDFexception('External file resources are not supported');
        }
        if (isset($streamDict['FFilter'])) {
            Throw new PDFexception('External file resources are not supported');
        }
        if (isset($streamDict['FDecodeParms'])) {
            Throw new PDFexception('External file resources are not supported');
        }
        if (isset($streamDict['DL'])) {
            $this->DL = $streamDict['DL'];
        }
        return;
    }//End parseResourceDictionary


    /**
     * decodeText decodes a PDF object's stream and makes it human-readable
     *
     * @return N/A - operates directly on the $this->decoded property
     */
    protected function decodeText()
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered decodeText<br />\n";
        }
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "<strong>Original String:</strong> $this->stream<br />\n";
        }

        $rawString = $this->stream;
        //Reverse compression filter
        if (isset($this->Filter)) {
            $rawString = $this->PDFdecoder->unfilter($this->Filter, $rawString);
            if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
                echo "<strong>Unfiltered String:</strong> $rawString<br />\n";
            }
        }
        //Reverse prediction
        if (isset($this->DecodeParms['Predictor'])) {
            $rawString = $this->PDFdecoder->unpredict(
                $rawString, $this->DecodeParms
            );
            if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
                echo "<strong>Unpredicted String:</strong> $rawString<br />\n";
            }
        }

        $this->decoded = $rawString;

        return;
    }//End decodeText

}//End PDFobject class
?>