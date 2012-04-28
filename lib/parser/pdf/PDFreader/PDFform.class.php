<?php
/**
 * PDFform.class.php represents a PDF form (AcroForm) and instantiates
 * a field object for each PDF form field.
 *
 * A given PDF file is considered to contain a single form, regardless
 * of how large the form is or how many forms it appears to contain.
 * There should only be one instance of this class for any given form
 * extraction session.
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
require_once 'PDFformfield.class.php';

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
class PDFform extends PDFbase
{
    /*************
    * PROPERTIES *
    **************/

    //PDF Form Attributes
    protected $Fields;
    protected $NeedAppearances = false;
    protected $SigFlags;
    protected $CO;
    protected $DR;
    protected $DA;
    protected $Q = 0;
    protected $XFA;

    //PHP Attributes
    public $reference;
    public $SignaturesExist = false;
    public $AppendOnly = false;
    protected $Annots;
    protected $formfields;
    protected $keyValues;

    /**********
    * METHODS *
    ***********/

    /**
     * __construct sets up the page attributes and converts some key PDF objects
     * to PHP objects
     *
     * @param resource $fh          - the file handle created by the PDFreader class
     * @param array    $Xrefs       - the XRef table extracted by the PDFreader class
     * @param object   $PDFdecoder  - the PDFdecoder from the PDFreader class
     * @param string   $reference   - the PDF reference to this object
     * @param array    $Annots      - array of references to annotation dictionaries
     *
     * @return N/A
     */
    public function __construct($fh, $Xrefs, $PDFdecoder, $reference, $Annots)
    {
        parent::__construct();
        $this->fh = $fh;
        $this->Xrefs = $Xrefs;
        $this->PDFdecoder = $PDFdecoder;

        if (!isset($reference)) {
            Throw new PDFexception('Form creation error: Invalid form reference.');
        }
        if ($this->debugLevel > self::DEBUG_OFF) {
            echo "<strong><u>CREATING FORM $reference</u></strong><br />\n";
        }

        $formObj = $this->extractObject($reference);
        $formDictionary = $this->extractDictionary($formObj);

        //Set required PDF attributes
        if (!isset($formDictionary['Fields'])) {
            Throw new PDFexception('Form creation error: No form fields found.');
        }
        $this->Fields = $formDictionary['Fields'];

        //Set optional PDF attributes
        if (isset($formDictionary['NeedAppearances'])) {
            $this->NeedAppearances = $formDictionary['NeedAppearances'] == true;
        }
        if (isset($formDictionary['SigFlags'])) {
            $this->SigFlags = $this->extractSigFlags($formDictionary['SigFlags']);
        }
        if (isset($formDictionary['CO'])) {
            $this->CO = $formDictionary['CO'];
        }
        if (isset($formDictionary['DR'])) {
            $this->DR = $formDictionary['DR'];
        }
        if (isset($formDictionary['DA'])) {
            $this->DA = $formDictionary['DA'];
        }
        if (isset($formDictionary['Q'])) {
            $this->Q = $formDictionary['Q'];
        }
        if (isset($formDictionary['XFA'])) {
            $this->XFA = $this->extractXFA($formDictionary['XFA']);
        }

        //Set PHP attributes
        if (isset($reference)) {
            $this->reference = $reference;
        }
        if (isset($Annots)) {
            $this->Annots = $Annots;
        }
        $this->extractFields();
    }//End __construct


    /**
     * getKeyValuePairs is an accessor function that returns all a form's
     * field data in key value pairs
     *
     * @return array $keyValues - an associative array of key/value pairs
     */
    public function getKeyValuePairs()
    {
        return $this->keyValues;
    }//End getKeyValuePairs


    /**
     * extractSigFlags converts the SigFlags int into to a binary string and sets
     * boolean flags based on that string
     *
     * @param int $SigFlags - the int representing a 32-bit binary string of flags
     *
     * @return N/A - operates directly on object properties
     */
    protected function extractSigFlags($SigFlags)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractSigFlags<br />\n";
        }

        //Convert $SigFlags int to a 32-bit string. PDF spec 12.7.2
        $zeroes = '';
        for ($i=0; $i<32; $i++) {
            $zeroes .= '0';
        }
        $binarySigFlags = decbin($SigFlags);
        //Pad with leading zeroes
        $binarySigFlags = substr($zeroes, strlen($binarySigFlags));
        $binarySigFlags .= $binarySigFlags;

        //Set PHP Booleans based on SigFlags
        $this->SignaturesExist = $binarySigFlags[31] == 1;
        $this->AppendOnly = $binarySigFlags[30] == 1;

        return $binarySigFlags;
    }//End extractSigFlags


    /**
     * extractXFA is minimal - enhance based on PDF spec 12.7.8
     *
     * @param string/array $XFA - the external form object
     *
     * @return array $XFA - the processed form data
     */
    protected function extractXFA($XFA)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractXFA<br />\n";
        }
        if (is_string($XFA)) {
            $XFA = array($XFA);
        }
        return $XFA;
    }//End extractXFA


    /**
     * extractFields resolves form field references in the form's Fields array and
     * creates PHP formfield objects
     *
     * @return N/A - acts directly on the $this->formfields property
     */
    protected function extractFields()
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractFields<br />\n";
        }

        if (is_string($this->Fields)) {
            $Fields = array($this->Fields);
        } else {
            $Fields = $this->Fields;
        }

        $this->formfields = array();
        foreach ($Fields as $fieldRef) {
            $this->formfields[] = new PDFformfield(
                $this->fh, $this->Xrefs,
                $this->PDFdecoder, $fieldRef
            );
        }
        $this->populateKeyValuePairs();

        return;
    }//End extractFields


    /**
     * populateKeyValuePairs walks through the formfields array and
     * extracts the key/value pairs from each form field
     *
     * @return N/A - operates directly on the $this->keyValues property
     */
    protected function populateKeyValuePairs()
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered populateKeyValuePairs<br />\n";
        }

        //Populate the $this->keyValues property
        $this->keyValues = array();
        foreach ($this->formfields as $fieldObj) {
            $this->iterator = 0;
            $this->extractKeyValuePairs($fieldObj);
        }

        //Flatten key/value array
        $keyValues = array();
        $keyValues = $this->keyValues;
        $this->keyValues = array();
        foreach ($keyValues as $pair) {
            $key = array_keys($pair);
            $key = $key[0];
            $this->keyValues[$key] = $pair[$key];
        }

        return;
    }//End populateKeyValuePairs


    /**
     * extractKeyValuePairs recursively walks through all PDFformfield objects
     * and their children to extract key/value pairs from them
     *
     * @param object $field - the field object to get keys and values from
     *
     * @return N/A - operates directly on the $this->keyValues property
     */
    protected function extractKeyValuePairs($field)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractKeyValuePairs<br />\n";
        }
        if (++$this->iterations > self::MAX_ITERATIONS) { //Recursion failsafe
            Throw new PDFexception('Key/Value Overflow Error');
        }

        $fieldType = $field->getFieldType();
        if ($fieldType == 'Btn' || $fieldType == 'Sig') {
            return; //We don't care about buttons or signature fields
        }

        $this->keyValues[] = $field->getKeyValue();

        //If this field has kids, do it all again
        if ($field->hasChildren()) {
            $children = $field->getChildren();
            foreach ($children as $child) {
                $this->extractKeyValuePairs($child);
            }
        }

        return;
    }//End extractKeyValuePairs
}//End PDFform class
?>