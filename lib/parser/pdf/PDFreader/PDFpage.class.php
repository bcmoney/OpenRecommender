<?php
/**
 * PDFpage.class.php contains high-level Page attributes and
 * instantiates an array of PDF objects that make up the page
 *
 * PHP version 5.1
 *
 * @category  File_Formats
 * @package   File_PDFreader
 * @author    John M. Stokes <jstokes@heartofthefyre.us>
 * @copyright 2010 John M. Stokes
 * @license   http://www.opensource.org/licenses/bsd-license.html BSD Style License
 * @link      http://heartofthefyre.us/PDFreader/index.php
 */

require_once 'PDFbase.class.php';
require_once 'PDFobject.class.php';

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
class PDFpage extends PDFbase
{
    /*************
    * PROPERTIES *
    **************/

    //PDF Page Attributes
    protected $Type = '/Page';
    protected $Parent;
    protected $LastModified;
    protected $Resources;
    protected $MediaBox;
    protected $CropBox;
    protected $BleedBox;
    protected $TrimBox;
    protected $ArtBox;
    protected $BoxColorInfo;
    protected $Contents;
    protected $Rotate;
    protected $Group;
    protected $Thumb;
    protected $B;
    protected $Dur;
    protected $Trans;
    protected $Annots;
    protected $AA;
    protected $Metadata;
    protected $PieceInfo;
    protected $StructParents;
    protected $ID;
    protected $PZ;
    protected $SeparationInfo;
    protected $Tabs;
    protected $TemplateInstantiated;
    protected $PresSteps;
    protected $UserUnit;
    protected $VP;

    //PHP Attributes
    public $reference;
    public $tokens;

    /**********
    * METHODS *
    ***********/

    /**
     * __construct sets up the page attributes and converts some key PDF objects
     * to PHP objects
     *
     * @param resource $fh             - the file handle from the PDFreader class
     * @param array    $Xrefs          - the XRef table from the PDFreader class
     * @param object   $PDFdecoder     - the PDFdecoder instance from the PDFreader class
     * @param array    $pageDictionary - an array of PDF page attributes
     * @param string   $ref            - an optional PDF reference to this object
     *
     * @return N/A
     */
    public function __construct($fh, $Xrefs, $PDFdecoder, $pageDictionary, $ref)
    {
        parent::__construct();
        $this->fh = $fh;
        $this->Xrefs = $Xrefs;
        $this->PDFdecoder = $PDFdecoder;

        if ($this->debugLevel > self::DEBUG_OFF) {
            echo "<strong><u>CREATING PAGE $ref</u></strong><br />\n";
        }

        //Set required PDF attributes
        if (!isset($pageDictionary['Parent'])) {
            Throw new PDFexception('Error: Page parent could not be determined.');
        }
        $this->Parent = $pageDictionary['Parent'];
        if (!isset($pageDictionary['Resources'])) {
            Throw new PDFexception('Error: Page resources could not be found.');
        }
        $this->Resources = $this->extractResources($pageDictionary['Resources']);
        if (!isset($pageDictionary['MediaBox'])) {
            Throw new PDFexception('Error: Page MediaBox could not be determined.');
        }
        $this->MediaBox = $pageDictionary['MediaBox'];
        if (!isset($pageDictionary['Contents'])) {
            Throw new PDFexception('Error: Page contents could not be found.');
        }
        $this->Contents = $pageDictionary['Contents'];

        //Set optional PDF attributes
        if (isset($pageDictionary['LastModified'])) {
            $this->LastModified = $pageDictionary['LastModified'];
        }
        if (isset($pageDictionary['CropBox'])) {
            $this->CropBox = $pageDictionary['CropBox'];
        }
        if (isset($pageDictionary['BleedBox'])) {
            $this->BleedBox = $pageDictionary['BleedBox'];
        }
        if (isset($pageDictionary['TrimBox'])) {
            $this->TrimBox = $pageDictionary['TrimBox'];
        }
        if (isset($pageDictionary['ArtBox'])) {
            $this->ArtBox = $pageDictionary['ArtBox'];
        }
        if (isset($pageDictionary['BoxColorInfo'])) {
            $this->BoxColorInfo = $pageDictionary['BoxColorInfo'];
        }
        if (isset($pageDictionary['Rotate'])) {
            $this->Rotate = $pageDictionary['Rotate'];
        }
        if (isset($pageDictionary['Group'])) {
            $this->Group = $pageDictionary['Group'];
        }
        if (isset($pageDictionary['Thumb'])) {
            $this->Thumb = $pageDictionary['Thumb'];
        }
        if (isset($pageDictionary['B'])) {
            $this->B = $pageDictionary['B'];
        }
        if (isset($pageDictionary['Dur'])) {
            $this->Dur = $pageDictionary['Dur'];
        }
        if (isset($pageDictionary['Trans'])) {
            $this->Trans = $pageDictionary['Trans'];
        }
        if (isset($pageDictionary['Annots'])) {
            $this->Annots = $pageDictionary['Annots'];
        }
        if (isset($pageDictionary['AA'])) {
            $this->AA = $pageDictionary['AA'];
        }
        if (isset($pageDictionary['Metadata'])) {
            $this->Metadata = $pageDictionary['Metadata'];
        }
        if (isset($pageDictionary['PieceInfo'])) {
            $this->PieceInfo = $pageDictionary['PieceInfo'];
        }
        if (isset($pageDictionary['StructParents'])) {
            $this->StructParents = $pageDictionary['StructParents'];
        }
        if (isset($pageDictionary['ID'])) {
            $this->ID = $pageDictionary['ID'];
        }
        if (isset($pageDictionary['PZ'])) {
            $this->PZ = $pageDictionary['PZ'];
        }
        if (isset($pageDictionary['SeparationInfo'])) {
            $this->SeparationInfo = $pageDictionary['SeparationInfo'];
        }
        if (isset($pageDictionary['Tabs'])) {
            $this->Tabs = $pageDictionary['Tabs'];
        }
        if (isset($pageDictionary['TemplateInstantiated'])) {
            $this->TemplateInstantiated = $pageDictionary['TemplateInstantiated'];
        }
        if (isset($pageDictionary['PresSteps'])) {
            $this->PresSteps = $pageDictionary['PresSteps'];
        }
        if (isset($pageDictionary['UserUnit'])) {
            $this->UserUnit = $pageDictionary['UserUnit'];
        }
        if (isset($pageDictionary['VP'])) {
            $this->VP = $pageDictionary['VP'];
        }

        //Set PHP attributes
        if (isset($ref)) {
            $this->reference = $ref;
        }
        $this->extractContents($this->Resources, $this->Contents);
    }//End __construct


    /**
     * getAnnotations returns the array of annotation references for this page
     *
     * @return array $this->Annots - the PDF Dictionary's Annots property
     */
    public function getAnnotations()
    {
        return $this->Annots;
    }//End getAnnotations


    /**
     * extractResources collects resources needed for decoding the page,
     * most notably the CMap
     *
     * @param array/string $Resources - the /Page object's Resource dictionary.
     *     if $Resources is a string, it should be an indirect reference to the
     *     actual resource dictionary
     *
     * @return array $resourceDict - the dictionary of page resources
     */
    protected function extractResources($Resources)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractResources<br />\n";
        }

        if (is_array($Resources)) {
            $resourceDict = $Resources;
        } else if (is_string($Resources)) {
            $resourceObj = $this->extractObject($Resources);
            $this->iterations = 0; //Reset iterations for extractDictionary call
            $resourceDict = $this->extractDictionary($resourceObj);
        } else {
            return;
        }

        return $resourceDict;
    }//End extractResources


    /**
     * extractContents determines the content objects making up a page,
     * extracts the objects, and passes them to the appropriate
     * data extraction method
     *
     * @param array $Resources - the dictionary of page resources
     * @param array $Contents  - an array of references to content objects
     *
     * @return N/A - operates directly on the $this->tokens property
     */
    protected function extractContents($Resources, $Contents)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractContents<br />\n";
        }

        if (is_string($Contents)) {
            $Contents = array($Contents);
        }

        //Resource dictionaries sometimes contain "Contents" entries
        if (isset($Resources['Contents'])) {
            if (is_string($Resources['Contents'])) {
                $Contents[] = $Resources['Contents'];
            } else {
                $Contents = array_merge($Contents, $Resources['Contents']);
            }
        }

        //Create PHP objects for each PDF object
        $contentObjects = array();
        foreach ($Contents as $contentRef) {
            $contentObjects[] = new PDFobject(
                $this->fh, $this->Xrefs, $this->PDFdecoder,
                $contentRef, $this->Resources
            );
        }

        //Assemble the content streams into one long string for text extraction
        $contentString = '';
        foreach ($contentObjects as $obj) {
            $contentString .= $obj->getText();
        }

        $this->extractText($contentString);

        return;
    }//End extractContents


    /**
     * extractText decodes a PDF object and extracts text from it
     *
     * @param string $contentString - a string of all decoded page content
     *
     * @return N/A - operates directly on the $this->tokens property
     */
    protected function extractText($contentString)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_EXTRACTION) {
            echo "Entered extractText<br />\n";
        }

        //Resolve font mapping
        $textObjects = array();
        $pattern = '/(.|\n|\r\n)+?(Tj|TJ)/'; //Regex to extract text objects
        $textTokens = $this->extractTextTokens($contentString);
        foreach ($textTokens as $token) {
            $currentToken = '';
            //Split each token into text objects
            preg_match_all($pattern, $token, $textObjects);
            foreach ($textObjects[0] as $textObj) {
                if (!empty($textObj)) {
                    $currentToken .= $this->PDFdecoder->mapFont(
                        $this->Resources['Font'], $textObj,
                        $this->Xrefs
                    );
                }
            }
            $currentToken = trim($currentToken);
            if (!empty($currentToken)) {
                $this->tokens[] = $currentToken;
            }
        }

        if ($this->debugLevel > self::DEBUG_OFF) {
            echo '<strong>Decoded tokens:</strong>';
            var_dump($this->tokens);
            echo "<br />\n";
        }

        return;
    }//End extractText


    /**
     * extractTextTokens accepts a decoded object string and extracts the
     * various text objects from the overall string (i.e. those items that
     * appear between BT and ET)
     *
     * @param string $decodedString - the PDF page's decoded string
     *
     * @return array $subtokens - an array of strings representing PDF text objects
     */
    protected function extractTextTokens($decodedString)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered extractTextTokens<br />\n";
        }

        //Get rid of extraneous parameters
        if (strpos($decodedString, 'BT') !== false) {
            //Chop off the first BT and everything before it
            $decodedString = substr($decodedString, strpos($decodedString, 'BT')+2);
        }
        if (strpos($decodedString, 'ET') !== false) {
            //Chop off everything after the last ET
            $decodedString = substr(
                $decodedString, 0, (strrpos($decodedString, 'ET')+2)
            );
        }
        //Replace the last ET with a more specific string
        $decodedString = preg_replace('|ET$|', 'END_TEXT', $decodedString);
        //Remove all other BTs
        $decodedString = str_replace(' BT', ' ', $decodedString);
        //Replace all other ETs with a more specific string
        $decodedString = str_replace(' ET ', ' END_TEXT', $decodedString);

        /*
         * Replace various linebreak operators with a single operator.
         * (i.e. TD, Td, and T* all become T*)
         * The distinctions refer to placement in the page grid
         * and are unnecessary for text extraction.
         * Note: Td or TD preceded by a 0 indicates no break
         * Note: Tm doesn't always mean line break - future enhancement
         */
        $decodedString = str_replace('0 Td', '', $decodedString);
        $decodedString = str_replace('0 TD', '', $decodedString);
        $decodedString = $this->processTextMatrix($decodedString);
        $decodedString = str_replace(' Td', ' T*', $decodedString);
        $decodedString = str_replace(' TD', ' T*', $decodedString);

        //Split the string into text tokens
        $supertokens = $tokens = $subtokens = array();
        $supertokens = explode(' END_TEXT', $decodedString);
        foreach ($supertokens as $supertoken) {
            $tokens = explode('T*', $supertoken); //Split on line break. PDF 9.4.2
            foreach ($tokens as $token) {
                if (!empty($token)) { //ignore blanks
                    $subtokens[] = $token;
                }
            }
        }

        return $subtokens;
    }//End extractTextTokens


    /**
     * processTextMatrix crawls through a decoded string and calculates
     * transformations specified in the text matrix necessary for text
     * extraction and ignores other transformations
     *
     * @param string $decodedString - the page content string
     *
     * @return string $decodedString - the same string with transformations
     *     applied
     */
    protected function processTextMatrix($decodedString)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered processTextMatrix<br />\n";
        }

        $fontSize = null; //Current font size
        $leading = null; //Text leading
        $Ypos = null; //Y grid position

        /*
         * Extract a leading parameter.
         * Leading is usually between -2 and -1, so
         * minor variations shouldn't affect us.
         */
        $tempString = substr($decodedString, 0, strpos($decodedString, 'TD')+2);
        $tempString = substr($tempString, strrpos($tempString, '-'));
        sscanf($tempString, '%f TD', $Ty);
        $leading = $Ty;
        if (empty($leading)) {
            $leading = -1;
        }

        //Regex for entire text matrix (six floats followed by Tm)
        $matrixPattern = '/';
        for ($i=0; $i<6; $i++) {
            //Single int/float pattern
            $matrixPattern .= '[0-9]+(\.[0-9]+)? ';
        }
        $matrixPattern .= 'Tm/';
        $operatorPattern = '/ Tm/'; //Regex for text matrix operator

        //Walk through the file looking for text matrices
        for ($i=0; $i<strlen($decodedString); $i++) {
            $Sx = $sine1 = $sine2 = $Sy = $Tx = $Ty = null;
            if ($decodedString[$i] == 'T' && $decodedString[$i-1] == ' ') {
                switch ($decodedString[$i+1]) {
                case 'm': //Text Matrix
                    //34 characters should be the max length for a text matrix
                    if ($i < 34) {
                        $start = 0;
                    } else {
                        $start = $i - 34;
                    }
                    $tempString = substr($decodedString, $start, $i+2);
                    $matrix = array();
                    preg_match($matrixPattern, $tempString, $matrix);
                    sscanf(
                        $matrix[0], '%f %f %f %f %f %f Tm',
                        $Sx, $sine1, $sine2, $Sy, $Tx, $Ty
                    );

                    //Be sure we have initial values
                    if (!isset($fontSize)) {
                        $fontSize = $Sy;
                    }
                    if (!isset($Ypos)) {
                        $Ypos = $Ty;
                    }

                    /*
                     * This is my own hack to avoid matrix
                     * mathematics. If previous Y position
                     * minus current Y position is less than
                     * font size, we might be dealing with
                     * subscript or superscript, so
                     * don't add a line break. Ex:
                     * 700 - 695 = 5. Current font size = 12;
                     * 5 < 12, so don't line break
                     */
                    $Yoffset = $Ypos - $Ty;
                    if ($Yoffset > $Sy) { //Add a line break
                        $decodedString = preg_replace(
                            $operatorPattern, ' T*', $decodedString, 1
                        );
                    } else {
                        $decodedString = preg_replace(
                            $operatorPattern, '', $decodedString, 1
                        );
                    }

                    //Reset parameters from new matrix
                    $fontSize = $Sy; //Y scale = font size
                    $Ypos = $Ty; //Y text position
                    break;
                case 'd': //Line breaks
                case 'D':
                case '*':
                    /*
                     * Update the Y position.
                     * Note: leading is negative, so subtract a negative
                     * number to get total line height. PDF Spec 9.4.2.
                     * Note: Y position starts at total page height and
                     * reduces to 0, so subtract line height.
                     */
                    $Ypos -= ($fontSize - $leading);
                    break;
                }//Close switch
            }//Close if
        }//Close for

        return $decodedString;
    }//End processTextMatrix

}//End PDFpage class
?>