<?php
/**
 * PDFdecoder.class.php does the hard work of decompressing, reversing
 * prediction, and character mapping text strings.
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
class PDFdecoder extends PDFbase
{
    /*************
    * PROPERTIES *
    **************/

    //STANDARD CHARACTER SET ENCODINGS
    public $StandardLatin;
    public $PDFDocEncoding;
    public $MacRomanEncoding;
    public $WinAnsiEncoding;
    public $MacExpertEncoding;
    public $Unicode;

    protected $CMap; //The current CMap used to decode characters

    /**********
    * METHODS *
    ***********/

    /**
     * __construct populates standard encoding arrays
     *
     * @param resource $fh - file handle to the PDF file
     *
     * @return N/A
     */
    public function __construct($fh)
    {
        parent::__construct();
        $this->fh = $fh;

        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Created decoder<br />\n";
        }

        $this->populateEncoding();
    }//End __construct


    /**
     * unfilter reverses Filter encoding on a data string
     *
     * @param string $filterString - entry from Stream dictionary identifying the Filter
     * @param string $data         - the encoded string
     *
     * @return string $decodedData - the decoded string
     */
    public function unfilter($filterString, $data)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered unfilter<br />\n";
        }

        $filterArray = explode(' ', trim($filterString));

        $decodedData = $data;
        foreach ($filterArray as $filter) {
            switch($filter) {
            case '/ASCIIHexDecode':
                $decodedData = $this->ASCIIHexDecode($decodedData);
                break;
            case '/ASCII85Decode':
                $decodedData = $this->ASCII85Decode($decodedData);
                break;
            case '/LZWDecode': //LZW is similar to Flate. Try gunzip
            case '/FlateDecode':
                $decodedData = $this->inflate($decodedData);
                break;
            case '/CCITTFaxDecode':
            case '/JBIG2Decode':
            case '/DCTDecode':
            case '/JPXDecode':
                Throw new PDFexception('Filter Error:
                    Graphic filter specified.
                    This software supports text extraction only.'
                );
                break;
            case '/Crypt':
                Throw new PDFexception('Filter Error: Encryption not supported.');
                break;
            default:
                Throw new PDFexception('Filter Error: Unknown filter specified.');
                break;
            }
        }//Close foreach $filter

        return $decodedData;
    }//End unfilter


    /**
     * unpredict acceptes a deflated stream object and reverses PNG prediction
     * This is a reversal of Zend Framework's prediction implementation
     *
     * @param string $buffer - the deflated xref stream
     * @param array  $params - the PNG prediction parameters from the xref dictionary
     *
     * @return array $decoded - the decoded string
     *
     * @author John M. Stokes
     * @author Zend Framework team
     */
    public function unpredict($buffer, $params)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered unpredict<br />\n";
        }

        //Verify a Predictor is set
        if (!isset($params['Predictor'])) {
            return $buffer;
        }

        //Columns
        $columns = 1;
        if (isset($params['Columns'])) {
            $columns = $params['Columns'];
        }

        //Calculate data structure parameters
        if ($params['Predictor'] != 1) {
            $bitsPerSample = 8; //Hard coding 8 - reversing means already binary
            $bytesPerSample = (int)(($bitsPerSample+7)/8); //Emulates ceil()
            $bytesPerRow = (int)(($bitsPerSample*$columns+7)/8); //Emulates ceil()
            if ($params['Predictor'] >= 10) {
                //Increment to account for the PNG predictor type byte
                $bytesPerRow++;
            }
            $numRows = ceil(strlen($buffer)/$bytesPerRow);
        }

        $decoded = '';
        switch ($params['Predictor']) {
        case 1: //1 means no prediction algorithm used
            $decoded = $buffer;
            break;
        case 2: //TIFF prediction
            Throw new PDFexception('Unprediction error:
                TIFF predictor not implemented.'
            );
            break;
        case 10: //PNG None prediction
            $decoded = $buffer;
            break;
        case 11: //PNG Sub prediction
            Throw new PDFexception('Unprediction error:
                PNG Sub predictor not implemented.'
            );
            break;
        case 12: //PNG Up prediction
            $prevRow = array_fill(0, $bytesPerRow, 0);
            $offset = 0;
            for ($i=0; $i<$numRows; $i++) {
                for ($j=0; $j<$bytesPerRow; $j++) {
                    if ($j == 0) {//Skip the predictor byte
                        ++$offset;
                        continue;
                    }
                    if (isset($buffer[$offset])) {
                        $decodedByte = ord($buffer[$offset++])+$prevRow[$j];
                        $decoded .= chr($decodedByte);
                        $prevRow[$j] = 0; //Force $prevRow to garbage-collect
                        $prevRow[$j] = $decodedByte;
                    } else { //Pad buffer with zeroes. PDF spec 7.4.4.2
                        $decoded .= chr(0);
                        ++$offset;
                    }
                }
            }
            break;
        case 13: //PNG Average prediction
            Throw new PDFexception('Unprediction error:
                PNG Average predictor not implemented.'
            );
            break;
        case 14: //PNG Paeth prediction
            Throw new PDFexception('Unprediction error:
                PNG Paeth predictor not implemented.'
            );
            break;
        case 15: //Optimal prediction
            Throw new PDFexception('Unprediction error:
                PNG Optimal predictor not implemented.'
            );
            break;
        default:
            Throw new PDFexception('Unprediction error: Unknown prediction filter.');
            break;
        }//Close switch

        return $decoded;
    }//End unpredict


    /**
     * mapFont determines the Font type and decodes it based on that type
     *
     * @param array  $fontDict - the dictionary of font references
     * @param string $token    - the deflated, unpredicted, text token
     * @param array  $Xrefs    - the Xref dictionary from PDFreader
     *
     * @return string $decodedString - the string with font glyphs resolved
     */
    public function mapFont($fontDict, $token, $Xrefs)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered mapFont<br />\n";
        }
        //The Xref dictionry is required to extract fonts
        $this->Xrefs = $Xrefs;

        //Get the raw string from the token
        $token = trim($token);
        //Determine if we're dealing with a text string or a text array
        $stringType = substr($token, -2);
        if ($stringType == 'Tj') { //Single string
            //Chop off the first ( and everything before
            $workingString = substr($token, strpos($token, '(')+1);
            //Chop off the last ) and everything after
            $workingString = substr($workingString, 0, strrpos($workingString, ')'));
        } else if ($stringType == 'TJ') {//String array
            $this->iterations = 0; //Reset iterations before extractArray call
            $stringArray = $this->extractArray($token);
            $workingString = '';
            foreach ($stringArray as $stringToken) {
                $workingString .= $stringToken;
            }
        } else { //Unknown string type
            Throw new PDFexception('Error: Unknown string token type.');
        }

        //Check for and replace embedded hex strings
        $hexStrings = array();
        preg_match_all(self::HEX_STRING_PATTERN, $workingString, $hexStrings);
        foreach ($hexStrings[0] as $hexStr) {
            $unhexedStr = $this->decodeHexString($hexStr);
            $workingString = preg_replace(
                "|$hexStr|", $unhexedStr, $workingString, 1
            );
        }

        //Decode escaped characters
        $workingString = $this->unescapeString($workingString);

        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "<strong>Working token ($stringType): </strong>";
            echo "$workingString<br />\n";
        }

        //Determine which font this token uses
        if (strpos($token, 'Tf') === false) {
            //Token doesn't specify a font. We're done.
            return $workingString;
        }
        //Chop off Tf and everything after it
        $fontString = substr($token, 0, strpos($token, 'Tf'));
        $fontString = substr($fontString, strrpos($fontString, '/')+1);
        $fontArray = explode(' ', $fontString);
        $fontShortcut = trim($fontArray[0]);

        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Font shortcut: $fontShortcut - {$fontDict[$fontShortcut]}<br />\n";
        }

        //Dereference font dictionary to get font parameters
        if (isset($fontDict[$fontShortcut])) {
            $fontObj = $this->extractObject($fontDict[$fontShortcut]);
            $this->iterations = 0; //Reset iterations for extractDictionary call
            $fontObj = $this->extractDictionary($fontObj);

            /*
             * Get or Build the CMap
             * Per PDF Spec 1.7, Annex D.1. There should not be
             * an encoding called "StandardEncoding", though
             * it can be represented as a base to diff fonts
             * from. I have named the standard encoding base
             * "StandardLatin"
             */
            if (isset($fontObj['Encoding'])) {
                switch ($fontObj['Encoding']) {
                case '/StandardEncoding':
                    $this->CMap = $this->StandardLatin;
                    break;
                case '/PDFDocEncoding':
                    $this->CMap = $this->PDFDocEncoding;
                    break;
                case '/Identity-H':
                case '/Identity-V':
                case '/WinAnsiEncoding':
                    $this->CMap = $this->WinAnsiEncoding;
                    break;
                case '/MacRomanEncoding':
                    $this->CMap = $this->MacRomanEncoding;
                    break;
                case '/MacExpertEncoding':
                    $this->CMap = $this->MacExpertEncoding;
                    break;
                default:
                    $this->CMap = $this->extractCMap($fontObj);
                    break;
                }//Close switch
            } else {
                $this->CMap = $this->extractCMap($fontObj);
            }
        } else if (empty($this->CMap)) {
            //If an Encoding hasn't been specified, default to Windows
            $this->CMap = $this->WinAnsiEncoding;
        }
        //Otherwise, use the last-used CMap, which should already be set

        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo '<strong>Cmap: </strong>';
            var_dump($this->CMap);
            echo "<br />\n";
        }

        //Process each character through the CMap
        $decodedString = '';
        for ($i=0; $i<strlen($workingString); $i++) {
            $char = $workingString[$i];
            $charCode = ord($char);
            //Ignore out-of-range characters
            if (isset($this->CMap[$charCode])) {
                $decodedString .= $this->CMap[$charCode];
            }
        }

        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "<strong>Fontmapped token: </strong> $decodedString<p></p>\n";
        }

        return $decodedString;
    }//End mapFont


    /**
     * unescapeString standardizes line endings and translates escape characters
     * PDF Spec 7.3.4
     *
     * @param string $escapedString - a string with escape sequences
     *
     * @return string $unescapedString - same string with escape sequences decoded
     */
    public function unescapeString($escapedString)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered unescapeString<br />\n";
        }

        //Standardize line endings
        $unescapedString = str_replace("\r\n", "\n", $escapedString);
        $unescapedString = str_replace("\r", "\n", $unescapedString);
        //Convert octal characters
        $octalChars = array();
        preg_match_all(self::OCTAL_PATTERN, $unescapedString, $octalChars);
        foreach ($octalChars[0] as $octal) {
            //Strip leading slash and convert to decimal
            $charCode = octdec(substr($octal, 1));
            if ($charCode > 31 && $charCode < 256) { //Check for valid character code
                $char = chr(octdec($charCode));
                $unescapedString = str_replace($octal, $char, $unescapedString);
            } else { //Remove invalid characterss
                $unescapedString = str_replace($octal, '', $unescapedString);
            }
        }
        //Clean escape characters
        $unescapedString = str_replace('\\r\\n', "\n", $unescapedString); //CRLF
        $unescapedString = str_replace('\\n', "\n", $unescapedString); //newline
        $unescapedString = str_replace('\\r', "\n", $unescapedString); //carriage rtn
        $unescapedString = str_replace('\\t', "\t", $unescapedString); //tab
        $unescapedString = str_replace('\\b', '', $unescapedString); //backspace
        $unescapedString = str_replace('\\f', '', $unescapedString); //form feed
        $unescapedString = str_replace('\\(', '(', $unescapedString); //left paren
        $unescapedString = str_replace('\\)', ')', $unescapedString); //right paren
        //Convert literal backslashes to something more manageable
        $unescapedString = str_replace('\\', 'BACKSLASH', $unescapedString);
        //Replace double backslashes with a single
        $unescapedString = str_replace('BACKSLASHBACKSLASH', '\\', $unescapedString);
        //Remove all other backslashes
        $unescapedString = str_replace('BACKSLASH', '', $unescapedString);

        return $unescapedString;
    }//End unescapeString

    /*********************************
     * COMPRESSION FILTER PROCESSORS *
     *********************************/

    /**
     * ASCIIHexDecode decodes an ASCII Hex encoded string.
     * This borrows from Zend Framework's ASCII Hex decoder
     *
     * @param string $data - ASCII Hex encoded data
     *
     * @return string $decodedData - the decoded byte stream
     */
    protected function ASCIIHexDecode($data) {
        $decodedData  = '';
        $oddCode = true;
        $commentMode = false;

        //Check for End Of Data marker
        if ($data[strlen($data)-1] != '>') {
            Throw new PDFexception('Unable to decode. ASCII Hex EOD marker not found.');
        }

        //Chop off EOD marker
        $data = substr($data, 0, -1);

        /*
         * Remove whitespace
         * Note we're intentionally leaving \n (0x0A) and
         * \r (0x0D) in as comment delimiters
         */
        $whiteSpace = array("\x00", "\x09", "\x0C", "\x20");
        $data = str_replace($whiteSpace, '', $data);

        for ($i=0; $i<strlen($data); $i++) {
            $charCode = ord($data[$i]);

            if ($commentMode) {//Skip comments
                if ($charCode == 0x0A  || $charCode == 0x0D ) {
                    $commentMode = false;
                }
                continue;
            } else if ($charCode == 0x25) { //'%' indicates comment
                $commentMode = true;
                continue;
            }

            switch ($charCode) {
                case 0x0A: // Line feed
                case 0x0D: // Carriage return
                    continue;
                    break;
            }

            //Shift character code based on range
            if ($charCode >= 0x30 /*'0'*/ && $charCode <= 0x39 /*'9'*/) {
                $code = $charCode - 0x30;
            } else if ($charCode >= 0x41 /*'A'*/ && $charCode <= 0x46 /*'F'*/) {
                $code = $charCode - 0x37/*0x41 - 0x0A*/;
            } else if ($charCode >= 0x61 /*'a'*/ && $charCode <= 0x66 /*'f'*/) {
                $code = $charCode - 0x57/*0x61 - 0x0A*/;
            } else {
                Throw new PDFexception('Invalid character in ASCII Hex stream');
            }

            if ($oddCode) {//$i is odd. Store hex digit for next pass
                $hexCodeHiByte = $code;
            } else {//$i is even. Add decoded character to output
                $decodedData .= chr($hexCodeHiByte*16 + $code);
            }

            $oddCode = !$oddCode;
        }//End for strlen($data)

        /*
         * Handle cases in which the last '0'
         * padding character is missing
         */
        if (!$oddCode) {
            $decodedData .= chr($hexCodeHiByte*16);
        }

        return $decodedData;
    }//End ASCIIHexDecode


    /**
     * ASCII85Decode decodes an ASCII 85 encoded string.
     * This borrows from Zend Framework's ASCII 85 decoder
     *
     * @param string $data - ASCII 85 encoded data
     *
     * @return string $decodedData - the decoded byte stream
     */
    protected function ASCII85Decode($data) {
        $decodedData = '';

        //Remove whitespace
        $whiteSpace = array("\x00", "\x09", "\x0A", "\x0C", "\x0D", "\x20");
        $data = str_replace($whiteSpace, '', $data);

        //Check for End of Data (EOD) marker
        if (substr($data, -2) != '~>') {
            Throw new PDFexception('Unable to decode. ASCII 85 EOD marker not found.');
        }

        $data = substr($data, 0, -2); //Chop off EOD marker

        for ($i=0; $i<strlen($data); $i+=5) {
            /*
             * All zero values are stored as a single 'z'
             * rather than '!!!!!'. Handle those.
             */
            if (substr($data, $i, 1) == "z") {
                $decodedData .= pack("N", 0);
                $i -= 4;
                continue;
            }

            //Get a 5-byte chunk with which to work
            $chunk = substr($data, $i, 5);

            /*
             * If the chunk is less than 5 characters,
             * we've run off the end. Break out and
             * handle the final, partial chunk
             */
            if (strlen($chunk) < 5) {
                break;
            }

            $chunk = unpack('C5', $chunk);
            $value = 0;

            /*
             * Subtract 33 to shift the byte toward zero
             * and multiply by the base (85) raised to the
             * appropriate exponent (taken from Zend)
             */
            for ($j=1; $j<=5; $j++) {
                $value += (($chunk[$j] - 33) * pow(85, (5 - $j)));
            }

            $decodedData .= pack("N", $value);
        }

        //Handle the final, partial chunk
        if ($i < strlen($data)) {
            $value = 0;
            $chunk = substr($data, $i);
            $partialLength = strlen($chunk);

            /*
             * Pad the chunk with u's until we have 5 characters
             */
            for ($j=0; $j<(5-$partialLength); $j++) {
                $chunk .= 'u';
            }

            $chunk = unpack('C5', $chunk);

            /*
             * Subtract 33 to shift the byte toward zero
             * and multiply by the base (85) raised to the
             * appropriate exponent (taken from Zend)
             */
            for ($j=1; $j<=5; $j++) {
                $value += (($chunk[$j] - 33) * pow(85, (5 - $j)));
            }

            $finalData = pack("N", $value);
            //Chop off the extra characters now that it's decoded
            $finalData = substr($finalData, 0, ($partialLength-1));

            $decodedData .= $finalData;
        }

        return $decodedData;
    }//End ASCII85Decode


    /**
     * inflate decodes a FlateDecoded string.
     *
     * @param string $data - the raw binary data
     *
     * @return string
     *
     * @author Chung Leong (chernyshevsky@hotmail.com)
     * @author adapted by John M. Stokes
     */
    protected function inflate($data)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered inflate<br />\n";
        }

        if (function_exists('gzinflate')) {
            $data = substr($data, 2);
            // dunno why this works; got it to through trial and error
            $data[0] = chr(ord($data[0]) | 0x01);
            $deflated = gzinflate($data);
            return $deflated;
        } else if (is_executable('gzip')) {
            // don't know what the CRC is; gzip will spit out an error
            $header = "\x1F\x8B\x08\x00\x00\x00\x00\x00\x00\x00";
            $file = fopen('.tmp.gz', 'wb');
            if (!$file) {
                Throw new PDFexception('Error writing temporary zip file.');
            }
            fwrite($file, $header);
            fwrite($file, substr($data, 2));
            fclose($file);
            return `gzip -cdq .tmp.gz`;
        } else {
            Throw new PDFexception('Extraction error: Can\'t unzip your file.');
        }
    }//End inflate

    /***************************
     * CHARACTER MAP FUNCTIONS *
     ***************************/

    /**
     * extractCMap accepts font object references, dereferences them, and
     * extracts their CMaps
     *
     * @param array $fontDict - the font dictionary for the string we're decoding
     *
     * @return array $CMap - the generated CMap for this font
     */
    protected function extractCMap($fontDict)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered extractCMap<br />\n";
        }
        $CMap = array();

        if (isset($fontDict['ToUnicode'])) {
            $CMapObj = $this->extractStream($fontDict['ToUnicode']);
        } else {
            return $this->WinAnsiEncoding;
        }

        if ($CMapObj['Dictionary']['Filter'] == '/FlateDecode') {
            $rawCMap = $this->inflate($CMapObj['Contents']);
            $CMap = $this->parseCMap($fontDict, $rawCMap);
        } else {
            $CMap = $this->parseCMap($fontDict, $CMapObj['Contents']);
        }

        return $CMap;
    }//End extractCMap


    /**
     * parseCMap extracts individual font-to-Unicode mappings from an
     * embedded character map (CMap)
     *
     * @param array  $fontDict  - the PDF dictionary describing this font CMap
     * @param string $rawCMap   - the inflated string data from an extracted CMap
     *
     * @return array $CMap - the array representing the completed CMap
     */
    protected function parseCMap($fontDict, $rawCMap)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered parseCMap<br />\n";
        }
        $CMap = array();

        /*
         * Get all bfchar strings. bfchar strings should
         * always appear before any bfrange strings
         */
        if (strpos($rawCMap, 'bfchar') !== false) {
            $bfcharString = substr($rawCMap, strpos($rawCMap, 'beginbfchar')+11);
            $bfcharString = substr(
                $bfcharString, 0, strrpos($bfcharString, 'endbfchar')
            );
            $bfchars = explode("\n", $bfcharString); //Hopefully no lone \r's exist
        }
        //Get all bfrange strings
        if (strpos($rawCMap, 'bfrange') !== false) {
            $bfrangeString = substr($rawCMap, strpos($rawCMap, 'beginbfrange')+12);
            $bfrangeString = substr(
                $bfrangeString, 0, strrpos($bfrangeString, 'endbfrange')
            );
            $bfranges = explode("\n", $bfrangeString);
        }

        //Record the font-to-unicode mappings
        //For bfchars (1-to-1 mapping)
        if (isset($bfchars)) {
            foreach ($bfchars as $bfchar) {
                if (empty($bfchar)) {
                    continue;
                }
                //Skip beginbfchar and endbfchar
                if (strpos($bfchar, 'bfchar') !== false) {
                    continue;
                }
                //Chop off the leading < and anything before it
                $bfchar = substr($bfchar, strpos($bfchar, '<')+1);
                //Chop off the trailing > and anything after it
                $bfchar = substr($bfchar, 0, strrpos($bfchar, '>'));
                //Create an array like [0]=>'0A', [1]=>'00FA'
                $charArray = explode('><', $bfchar);
                //Map the int char code to the corresponding ASCII code
                $CMap[hexdec($charArray[0])] = ord(hexdec($charArray[1]));
            }
        }
        //For bfranges (1-to-1 mapping with incrementing)
        if (isset($bfranges)) {
            foreach ($bfranges as $bfrange) {
                if (empty($bfrange)) {
                    continue;
                }
                //Skip beginbfrange and endbfrange
                if (strpos($bfrange, 'bfrange') !== false) {
                    continue;
                }
                //Chop off the leading < and anything before it
                $bfrange = substr($bfrange, strpos($bfrange, '<')+1);
                //Chop off the trailing > and anything after it
                $bfrange = substr($bfrange, 0, strrpos($bfrange, '>'));
                //Create an array like [0]=>'0A', [1]=>'0B', [2]=>'00FA'
                $bfrangeArray = explode('><', $bfrange);
                //Convert to decimal so we can do math
                $startFontCode = hexdec($bfrangeArray[0]);
                $endFontCode = hexdec($bfrangeArray[1]);
                $Unicode = hexdec($bfrangeArray[2]);
                for ($fontCode=$startFontCode; $fontCode<=$endFontCode; $fontCode++) {
                    //No support for control characters (below 32)
                    if ($Unicode < 32 || $Unicode > 255) {
                        $CMap[$fontCode] = ' '; //Hack for missing spaces
                        ++$Unicode;
                        continue;
                    }
                    //If $Unicode is below 255, it should only produce a 2-digit hex
                    $CodePoint = 'U+00'.dechex($Unicode++);
                    $CodePoint = strtoupper($CodePoint);
                    //Be sure the code point exists before mapping
                    if (isset($this->Unicode[$CodePoint])) {
                        $CMap[$fontCode] = $this->Unicode[$CodePoint];
                    }
                }
            }//Close foreach $bfrange
        }//Close if $bfranges

        return $CMap;
    }//End parseCMap


    /**
     * decodeHexString converts a hex string to ASCII per PDF spec 7.3.4.3
     *
     * @param string $hexString - a string of hexadecimal digits
     *
     * @return string $decodedString - the ASCII representation of the string
     */
    public function decodeHexString($hexString)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered decodeHexString<br />\n";
        }

        $hexString = substr($hexString, 1, -1); //Strip off the < and >
        $decodedString = '';
        for ($i=0; $i<strlen($hexString); $i+=2) {
            $hexChar = $ASCIIvalue = 0; //Reset variables to force garbage collection
            $hexChar = $hexString[$i].$hexString[$i+1];
            $ASCIIvalue = hexdec($hexChar);
            //Ignore characters outside the visible ASCII range
            if ($ASCIIvalue > 31 && $ASCIIvalue < 128) {
                $decodedString .= chr((int)$ASCIIvalue);
            }
        }

        return $decodedString;
    }//End decodeHexString


    /**
     * populateEncoding creates the Adobe-standard encoding arrays
     *
     * @return N/A - populates the class's Encoding arrays
     */
    protected function populateEncoding()
    {
        if ($this->debugLevel > self::DEBUG_HIDE_DECODING) {
            echo "Entered populateEncoding<br />\n";
        }
        $this->StandardLatin = array(
            32=>' ', 33=>'!', 34=>'"',
            35=>'#', 36=>'$', 37=>'%', 38=>'&', 39=>'’',
            40=>'(', 41=>')', 42=>'*', 43=>'+', 44=>',',
            45=>'-', 46=>'.', 47=>'/', 48=>'0', 49=>'1',
            50=>'2', 51=>'3', 52=>'4', 53=>'5', 54=>'6',
            55=>'7', 56=>'8', 57=>'9', 58=>':', 59=>';',
            60=>'<', 61=>'=', 62=>'>', 63=>'?', 64=>'@',
            65=>'A', 66=>'B', 67=>'C', 68=>'D', 69=>'E',
            70=>'F', 71=>'G', 72=>'H', 73=>'I', 74=>'J',
            75=>'K', 76=>'L', 77=>'M', 78=>'N', 79=>'O',
            80=>'P', 81=>'Q', 82=>'R', 83=>'S', 84=>'T',
            85=>'U', 86=>'V', 87=>'W', 88=>'X', 89=>'Y',
            90=>'Z', 91=>'[', 92=>'\\', 93=>']', 94=>'^',
            95=>'_', 96=>'`', 97=>'a', 98=>'b', 99=>'c',
            100=>'d', 101=>'e', 102=>'f', 103=>'g', 104=>'h',
            105=>'i', 106=>'j', 107=>'k', 108=>'l', 109=>'m',
            110=>'n', 111=>'o', 112=>'p', 113=>'q', 114=>'r',
            115=>'s', 116=>'t', 117=>'u', 118=>'v', 119=>'w',
            120=>'x', 121=>'y', 122=>'z', 123=>'{', 124=>'|',
            125=>'}', 126=>'', 127=>'', 128=>'', 129=>'',
            130=>'', 131=>'', 132=>'', 133=>'', 134=>'',
            135=>'', 136=>'', 137=>'', 138=>'', 139=>'',
            140=>'', 141=>'', 142=>'', 143=>'', 144=>'',
            145=>'', 146=>'', 147=>'', 148=>'', 149=>'',
            150=>'', 151=>'', 152=>'', 153=>'', 154=>'',
            155=>'', 156=>'', 157=>'', 158=>'', 159=>'',
            160=>'', 161=>'¡', 162=>'¢', 163=>'£', 164=>'⁄',
            165=>'¥', 166=>'ƒ', 167=>'§', 168=>'¤', 169=>"'",
            170=>'“', 171=>'«', 172=>'‹', 173=>'›', 174=>'ﬁ',
            175=>'ﬂ', 176=>'', 177=>'–', 178=>'†', 179=>'‡',
            180=>'·', 181=>'μ', 182=>'¶', 183=>'•', 184=>'‚',
            185=>'„', 186=>'”', 187=>'»', 188=>'…', 189=>'‰',
            190=>'', 191=>'¿', 192=>'', 193=>'`', 194=>'´',
            195=>'ˆ', 196=>'~', 197=>'¯', 198=>'˘', 199=>'˙',
            200=>'¨', 201=>'', 202=>'˚', 203=>'¸', 204=>'',
            205=>'˝', 206=>'˛', 207=>'ˇ', 208=>'—', 209=>'',
            210=>'', 211=>'', 212=>'', 213=>'', 214=>'',
            215=>'', 216=>'', 217=>'', 218=>'', 219=>'',
            220=>'', 221=>'', 222=>'', 223=>'', 224=>'',
            225=>'Æ', 226=>'', 227=>'ª', 228=>'', 229=>'',
            230=>'', 231=>'', 232=>'Ł', 233=>'Ø', 234=>'Œ',
            235=>'º', 236=>'', 237=>'', 238=>'', 239=>'',
            240=>'', 241=>'', 242=>'æ', 243=>'', 244=>'ø',
            245=>'ı', 246=>'', 247=>'', 248=>'ł', 249=>'',
            250=>'œ', 251=>'ß', 252=>'', 253=>'', 254=>'',
            255=>''
        );//End StandardLatin
        $this->PDFDocEncoding = array(
            32=>' ', 33=>'!', 34=>'"',
            35=>'#', 36=>'$', 37=>'%', 38=>'&', 39=>"'",
            40=>'(', 41=>')', 42=>'*', 43=>'+', 44=>',',
            45=>'-', 46=>'.', 47=>'/', 48=>'0', 49=>'1',
            50=>'2', 51=>'3', 52=>'4', 53=>'5', 54=>'6',
            55=>'7', 56=>'8', 57=>'9', 58=>':', 59=>';',
            60=>'<', 61=>'=', 62=>'>', 63=>'?', 64=>'@',
            65=>'A', 66=>'B', 67=>'C', 68=>'D', 69=>'E',
            70=>'F', 71=>'G', 72=>'H', 73=>'I', 74=>'J',
            75=>'K', 76=>'L', 77=>'M', 78=>'N', 79=>'O',
            80=>'P', 81=>'Q', 82=>'R', 83=>'S', 84=>'T',
            85=>'U', 86=>'V', 87=>'W', 88=>'X', 89=>'Y',
            90=>'Z', 91=>'[', 92=>'\\', 93=>']', 94=>'^',
            95=>'_', 96=>'`', 97=>'a', 98=>'b', 99=>'c',
            100=>'d', 101=>'e', 102=>'f', 103=>'g', 104=>'h',
            105=>'i', 106=>'j', 107=>'k', 108=>'l', 109=>'m',
            110=>'n', 111=>'o', 112=>'p', 113=>'q', 114=>'r',
            115=>'s', 116=>'t', 117=>'u', 118=>'v', 119=>'w',
            120=>'x', 121=>'y', 122=>'z', 123=>'{', 124=>'|',
            125=>'}', 126=>'~', 127=>'', 128=>'•', 129=>'†',
            130=>'‡', 131=>'…', 132=>'—', 133=>'–', 134=>'ƒ',
            135=>'⁄', 136=>'‹', 137=>'›', 138=>'Š', 139=>'‰',
            140=>'„', 141=>'“', 142=>'”', 143=>'‘', 144=>'’',
            145=>'‚', 146=>'™', 147=>'ﬁ', 148=>'ﬂ', 149=>'Ł',
            150=>'Œ', 151=>'Š', 152=>'Ÿ', 153=>'Ž', 154=>'i',
            155=>'ł', 156=>'œ', 157=>'š', 158=>'ž', 159=>'',
            160=>'€', 161=>'¡', 162=>'¢', 163=>'£', 164=>'¤',
            165=>'¥', 166=>'¦', 167=>'§', 168=>'¨', 169=>'©',
            170=>'ª', 171=>'«', 172=>'¬', 173=>'', 174=>'®',
            175=>'¯', 176=>'°', 177=>'±', 178=>'²', 179=>'³',
            180=>'´', 181=>'μ', 182=>'¶', 183=>'·', 184=>'¸',
            185=>'¹', 186=>'º', 187=>'»', 188=>'¼', 189=>'½',
            190=>'¾', 191=>'¿', 192=>'À', 193=>'Á', 194=>'Â',
            195=>'Ã', 196=>'Ä', 197=>'Å', 198=>'Æ', 199=>'Ç',
            200=>'È', 201=>'É', 202=>'Ê', 203=>'Ë', 204=>'Ì',
            205=>'Í', 206=>'Î', 207=>'Ï', 208=>'Ð', 209=>'Ñ',
            210=>'Ò', 211=>'Ó', 212=>'Ô', 213=>'Õ', 214=>'Ö',
            215=>'×', 216=>'Ø', 217=>'Ù', 218=>'Ú', 219=>'Û',
            220=>'Ü', 221=>'Ý', 222=>'Þ', 223=>'ß', 224=>'à',
            225=>'á', 226=>'â', 227=>'ã', 228=>'ä', 229=>'å',
            230=>'æ', 231=>'ç', 232=>'è', 233=>'é', 234=>'ê',
            235=>'ë', 236=>'ì', 237=>'í', 238=>'î', 239=>'ï',
            240=>'ð', 241=>'ñ', 242=>'ò', 243=>'ó', 244=>'ô',
            245=>'õ', 246=>'ö', 247=>'÷', 248=>'ø', 249=>'ù',
            250=>'ú', 251=>'û', 252=>'ü', 253=>'ý', 254=>'þ',
            255=>'ÿ'
        );//End PDFDocEncoding
        $this->MacRomanEncoding = array(
            32=>' ', 33=>'!', 34=>'"',
            35=>'#', 36=>'$', 37=>'%', 38=>'&', 39=>"'",
            40=>'(', 41=>')', 42=>'*', 43=>'+', 44=>',',
            45=>'-', 46=>'.', 47=>'/', 48=>'0', 49=>'1',
            50=>'2', 51=>'3', 52=>'4', 53=>'5', 54=>'6',
            55=>'7', 56=>'8', 57=>'9', 58=>':', 59=>';',
            60=>'<', 61=>'=', 62=>'>', 63=>'?', 64=>'@',
            65=>'A', 66=>'B', 67=>'C', 68=>'D', 69=>'E',
            70=>'F', 71=>'G', 72=>'H', 73=>'I', 74=>'J',
            75=>'K', 76=>'L', 77=>'M', 78=>'N', 79=>'O',
            80=>'P', 81=>'Q', 82=>'R', 83=>'S', 84=>'T',
            85=>'U', 86=>'V', 87=>'W', 88=>'X', 89=>'Y',
            90=>'Z', 91=>'[', 92=>'\\', 93=>']', 94=>'^',
            95=>'_', 96=>'`', 97=>'a', 98=>'b', 99=>'c',
            100=>'d', 101=>'e', 102=>'f', 103=>'g', 104=>'h',
            105=>'i', 106=>'j', 107=>'k', 108=>'l', 109=>'m',
            110=>'n', 111=>'o', 112=>'p', 113=>'q', 114=>'r',
            115=>'s', 116=>'t', 117=>'u', 118=>'v', 119=>'w',
            120=>'x', 121=>'y', 122=>'z', 123=>'{', 124=>'|',
            125=>'}', 126=>'~', 127=>'•', 128=>'Ä', 129=>'Å',
            130=>'Ç', 131=>'É', 132=>'Ñ', 133=>'Ö', 134=>'Ü',
            135=>'á', 136=>'à', 137=>'â', 138=>'ä', 139=>'ã',
            140=>'å', 141=>'ç', 142=>'é', 143=>'è', 144=>'ê',
            145=>'ë', 146=>'í', 147=>'ì', 148=>'î', 149=>'ï',
            150=>'ñ', 151=>'ó', 152=>'ò', 153=>'ô', 154=>'ö',
            155=>'õ', 156=>'ú', 157=>'ù', 158=>'û', 159=>'ü',
            160=>'†', 161=>'°', 162=>'¢', 163=>'£', 164=>'§',
            165=>'•', 166=>'¶', 167=>'ß', 168=>'®', 169=>'©',
            170=>'™', 171=>'´', 172=>'¨', 173=>'≠', 174=>'Æ',
            175=>'Ø', 176=>'∞', 177=>'±', 178=>'≤', 179=>'≥',
            180=>'¥', 181=>'µ', 182=>'∂', 183=>'∑', 184=>'∏',
            185=>'π', 186=>'∫', 187=>'ª', 188=>'º', 189=>'Ω',
            190=>'æ', 191=>'ø', 192=>'¿', 193=>'¡', 194=>'¬',
            195=>'√', 196=>'ƒ', 197=>'≈', 198=>'∆', 199=>'«',
            200=>'»', 201=>'…', 202=>'&nbsp;', 203=>'À', 204=>'Ã',
            205=>'Õ', 206=>'Œ', 207=>'œ', 208=>'–', 209=>'—',
            210=>'“', 211=>'”', 212=>'‘', 213=>'’', 214=>'÷',
            215=>'◊', 216=>'ÿ', 217=>'Ÿ', 218=>'⁄', 219=>'€',
            220=>'‹', 221=>'›', 222=>'ﬁ', 223=>'ﬂ', 224=>'‡',
            225=>'·', 226=>'‚', 227=>'„', 228=>'‰', 229=>'Â',
            230=>'Ê', 231=>'Á', 232=>'Ë', 233=>'È', 234=>'Í',
            235=>'Î', 236=>'Ï', 237=>'Ì', 238=>'Ó', 239=>'Ô',
            240=>'', 241=>'Ò', 242=>'Ú', 243=>'Û', 244=>'Ù',
            245=>'ı', 246=>'ˆ', 247=>'˜', 248=>'¯', 249=>'˘',
            250=>'˙', 251=>'˚', 252=>'¸', 253=>'˝', 254=>'˛',
            255=>'ˇ'
        );//End MacRomanEncoding
        $this->WinAnsiEncoding = array(
            32=>' ', 33=>'!', 34=>'"',
            35=>'#', 36=>'$', 37=>'%', 38=>'&', 39=>"'",
            40=>'(', 41=>')', 42=>'*', 43=>'+', 44=>',',
            45=>'-', 46=>'.', 47=>'/', 48=>'0', 49=>'1',
            50=>'2', 51=>'3', 52=>'4', 53=>'5', 54=>'6',
            55=>'7', 56=>'8', 57=>'9', 58=>':', 59=>';',
            60=>'<', 61=>'=', 62=>'>', 63=>'?', 64=>'@',
            65=>'A', 66=>'B', 67=>'C', 68=>'D', 69=>'E',
            70=>'F', 71=>'G', 72=>'H', 73=>'I', 74=>'J',
            75=>'K', 76=>'L', 77=>'M', 78=>'N', 79=>'O',
            80=>'P', 81=>'Q', 82=>'R', 83=>'S', 84=>'T',
            85=>'U', 86=>'V', 87=>'W', 88=>'X', 89=>'Y',
            90=>'Z', 91=>'[', 92=>'\\', 93=>']', 94=>'^',
            95=>'_', 96=>'`', 97=>'a', 98=>'b', 99=>'c',
            100=>'d', 101=>'e', 102=>'f', 103=>'g', 104=>'h',
            105=>'i', 106=>'j', 107=>'k', 108=>'l', 109=>'m',
            110=>'n', 111=>'o', 112=>'p', 113=>'q', 114=>'r',
            115=>'s', 116=>'t', 117=>'u', 118=>'v', 119=>'w',
            120=>'x', 121=>'y', 122=>'z', 123=>'{', 124=>'|',
            125=>'}', 126=>'~', 127=>'•', 128=>'€', 129=>'•',
            130=>'‚', 131=>'ƒ', 132=>'„', 133=>'…', 134=>'†',
            135=>'‡', 136=>'ˆ', 137=>'‰', 138=>'Š', 139=>'‹',
            140=>'Œ', 141=>'•', 142=>'Ž', 143=>'•', 144=>'•',
            145=>'‘', 146=>'’', 147=>'“', 148=>'”', 149=>'•',
            150=>'–', 151=>'—', 152=>'˜', 153=>'™', 154=>'š',
            155=>'›', 156=>'oe', 157=>'•', 158=>'ž', 159=>'Ÿ',
            160=>'', 161=>'¡', 162=>'¢', 163=>'£', 164=>'¤',
            165=>'¥', 166=>'¦', 167=>'§', 168=>'¨', 169=>'©',
            170=>'ª', 171=>'«', 172=>'¬', 173=>'¬', 174=>'®',
            175=>'¯', 176=>'°', 177=>'±', 178=>'²', 179=>'³',
            180=>'´', 181=>'μ', 182=>'¶', 183=>'·', 184=>'¸',
            185=>'¹', 186=>'º', 187=>'»', 188=>'¼', 189=>'½',
            190=>'¾', 191=>'¿', 192=>'À', 193=>'Á', 194=>'Â',
            195=>'Ã', 196=>'Ä', 197=>'Å', 198=>'Æ', 199=>'Ç',
            200=>'È', 201=>'É', 202=>'Ê', 203=>'Ë', 204=>'Ì',
            205=>'Í', 206=>'Î', 207=>'Ï', 208=>'Ð', 209=>'Ñ',
            210=>'Ò', 211=>'Ó', 212=>'Ô', 213=>'Õ', 214=>'Ö',
            215=>'×', 216=>'Ø', 217=>'Ù', 218=>'Ú', 219=>'Û',
            220=>'Ü', 221=>'Ý', 222=>'Þ', 223=>'ß', 224=>'à',
            225=>'á', 226=>'â', 227=>'ã', 228=>'ä', 229=>'å',
            230=>'æ', 231=>'ç', 232=>'è', 233=>'é', 234=>'ê',
            235=>'ë', 236=>'ì', 237=>'í', 238=>'î', 239=>'ï',
            240=>'ð', 241=>'ñ', 242=>'ò', 243=>'ó', 244=>'ô',
            245=>'õ', 246=>'ö', 247=>'÷', 248=>'ø', 249=>'ù',
            250=>'ú', 251=>'û', 252=>'ü', 253=>'ý', 254=>'þ',
            255=>'ÿ'
        );//End WinAnsiEncoding
        $this->MacExpertEncoding = array(
            32=>' ', 33=>'!', 34=>'˝',
            35=>'¢', 36=>'$', 37=>'$', 38=>'&', 39=>"’",
            40=>'(', 41=>')', 42=>'‥', 43=>'․', 44=>',',
            45=>'-', 46=>'.', 47=>'/', 48=>'0', 49=>'1',
            50=>'2', 51=>'3', 52=>'4', 53=>'5', 54=>'6',
            55=>'7', 56=>'8', 57=>'9', 58=>':', 59=>';',
            60=>'<', 61=>'—', 62=>'>', 63=>'?', 64=>'@',
            65=>'A', 66=>'B', 67=>'C', 68=>'ð', 69=>'E',
            70=>'F', 71=>'¼', 72=>'½', 73=>'¾', 74=>'⅛',
            75=>'⅜', 76=>'⅝', 77=>'⅞', 78=>'⅓', 79=>'⅔',
            80=>'P', 81=>'Q', 82=>'R', 83=>'S', 84=>'T',
            85=>'U', 86=>'ff', 87=>'ﬁ', 88=>'ﬂ', 89=>'ffi',
            90=>'ffl', 91=>'₍', 92=>'\\', 93=>'₎', 94=>'^',
            95=>'-', 96=>'`', 97=>'a', 98=>'b', 99=>'c',
            100=>'d', 101=>'e', 102=>'f', 103=>'g', 104=>'h',
            105=>'i', 106=>'j', 107=>'k', 108=>'l', 109=>'m',
            110=>'n', 111=>'o', 112=>'p', 113=>'q', 114=>'r',
            115=>'s', 116=>'t', 117=>'u', 118=>'v', 119=>'w',
            120=>'x', 121=>'y', 122=>'z', 123=>'₡', 124=>'1',
            125=>'Rp', 126=>'~', 127=>'•', 128=>'Ä', 129=>'a',
            130=>'¢', 131=>'É', 132=>'Ñ', 133=>'Ö', 134=>'Ü',
            135=>'á', 136=>'à', 137=>'â', 138=>'ä', 139=>'ã',
            140=>'å', 141=>'ç', 142=>'é', 143=>'è', 144=>'ê',
            145=>'ë', 146=>'í', 147=>'ì', 148=>'î', 149=>'ï',
            150=>'ñ', 151=>'ó', 152=>'ò', 153=>'ô', 154=>'ö',
            155=>'õ', 156=>'ú', 157=>'ù', 158=>'û', 159=>'ü',
            160=>'†', 161=>'⁸', 162=>'₄', 163=>'₃', 164=>'₆',
            165=>'₈', 166=>'₇', 167=>'š', 168=>'®', 169=>'¢',
            170=>'₂', 171=>'´', 172=>'¨', 173=>'≠', 174=>'ˇ',
            175=>'o', 176=>'₅', 177=>'±', 178=>',', 179=>'.',
            180=>'ÿ', 181=>'µ', 182=>'$', 183=>'∑', 184=>'∏',
            185=>'þ', 186=>'∫', 187=>'₉', 188=>'₀', 189=>'ž',
            190=>'æ', 191=>'ø', 192=>'¿', 193=>'₁', 194=>'ł',
            195=>'√', 196=>'ƒ', 197=>'≈', 198=>'∆', 199=>'«',
            200=>'»', 201=>'¸', 202=>'&nbsp;', 203=>'À', 204=>'Ã',
            205=>'Õ', 206=>'Œ', 207=>'œ', 208=>'–', 209=>'-',
            210=>'“', 211=>'”', 212=>'‘', 213=>'’', 214=>'¡',
            215=>'◊', 216=>'ÿ', 217=>'Ÿ', 218=>'¹', 219=>'²',
            220=>'³', 221=>'⁴', 222=>'⁵', 223=>'⁶', 224=>'⁷',
            225=>'⁹', 226=>'⁰', 227=>'„', 228=>'e', 229=>'r',
            230=>'t', 231=>'Á', 232=>'Ë', 233=>'i', 234=>'s',
            235=>'d', 236=>'Ï', 237=>'Ì', 238=>'Ó', 239=>'Ô',
            240=>'', 241=>'l', 242=>'˛', 243=>'˘', 244=>'¯',
            245=>'b', 246=>'ⁿ', 247=>'m', 248=>',', 249=>'.',
            250=>'˙', 251=>'˚', 252=>'¸', 253=>'˝', 254=>'',
            255=>'ˇ'
        );//End MacExpertEncoding
        $this->Unicode = array(
            'U+0020'=>' ', 'U+0021'=>'!', 'U+0022'=>'"', 'U+0023'=>'#',
            'U+0024'=>'$', 'U+0025'=>'%', 'U+0026'=>'&', 'U+0027'=>"'",
            'U+0028'=>'(', 'U+0029'=>')', 'U+002A'=>'*', 'U+002B'=>'+',
            'U+002C'=>',', 'U+002D'=>'-', 'U+002E'=>'.', 'U+002F'=>'/',
            'U+0030'=>'0', 'U+0031'=>'1', 'U+0032'=>'2', 'U+0033'=>'3',
            'U+0034'=>'4', 'U+0035'=>'5', 'U+0036'=>'6', 'U+0037'=>'7',
            'U+0038'=>'8', 'U+0039'=>'9', 'U+003A'=>':', 'U+003B'=>';',
            'U+003C'=>'<', 'U+003D'=>'=', 'U+003E'=>'>', 'U+003F'=>'?',
            'U+0040'=>'@', 'U+0041'=>'A', 'U+0042'=>'B', 'U+0043'=>'C',
            'U+0044'=>'D', 'U+0045'=>'E', 'U+0046'=>'F', 'U+0047'=>'G',
            'U+0048'=>'H', 'U+0049'=>'I', 'U+004A'=>'J', 'U+004B'=>'K',
            'U+004C'=>'L', 'U+004D'=>'M', 'U+004E'=>'N', 'U+004F'=>'O',
            'U+0050'=>'P', 'U+0051'=>'Q', 'U+0052'=>'R', 'U+0053'=>'S',
            'U+0054'=>'T', 'U+0055'=>'U', 'U+0056'=>'V', 'U+0057'=>'W',
            'U+0058'=>'X', 'U+0059'=>'Y', 'U+005A'=>'Z', 'U+005B'=>'[',
            'U+005C'=>'\\','U+005D'=>']', 'U+005E'=>'^', 'U+005F'=>'_',
            'U+0060'=>'`', 'U+0061'=>'a', 'U+0062'=>'b', 'U+0063'=>'c',
            'U+0064'=>'d', 'U+0065'=>'e', 'U+0066'=>'f', 'U+0067'=>'g',
            'U+0068'=>'h', 'U+0069'=>'i', 'U+006A'=>'j', 'U+006B'=>'k',
            'U+006C'=>'l', 'U+006D'=>'m', 'U+006E'=>'n', 'U+006F'=>'o',
            'U+0070'=>'p', 'U+0071'=>'q', 'U+0072'=>'r', 'U+0073'=>'s',
            'U+0074'=>'t', 'U+0075'=>'u', 'U+0076'=>'v', 'U+0077'=>'w',
            'U+0078'=>'x', 'U+0079'=>'y', 'U+007A'=>'z', 'U+007B'=>'{',
            'U+007C'=>'|', 'U+007D'=>'}', 'U+007E'=>'~', 'U+007F'=>'•',
            'U+00A0'=>' ', 'U+00A1'=>'¡', 'U+00A2'=>'¢', 'U+00A3'=>'£',
            'U+00A4'=>'¤', 'U+00A5'=>'¥', 'U+00A6'=>'¦', 'U+00A7'=>'§',
            'U+00A8'=>'¨', 'U+00A9'=>'©', 'U+00AA'=>'ª', 'U+00AB'=>'«',
            'U+00AC'=>'¬', 'U+00AD'=>'¬', 'U+00AE'=>'®', 'U+00AF'=>'¯',
            'U+00B0'=>'°', 'U+00B1'=>'±', 'U+00B2'=>'²', 'U+00B3'=>'³',
            'U+00B4'=>'´', 'U+00B5'=>'μ', 'U+00B6'=>'¶', 'U+00B7'=>'·',
            'U+00B8'=>'¸', 'U+00B9'=>'¹', 'U+00BA'=>'º', 'U+00BB'=>'»',
            'U+00BC'=>'¼', 'U+00BD'=>'½', 'U+00BE'=>'¾', 'U+00BF'=>'¿',
            'U+00C0'=>'À', 'U+00C1'=>'Á', 'U+00C2'=>'Â', 'U+00C3'=>'Ã',
            'U+00C4'=>'Ä', 'U+00C5'=>'Å', 'U+00C6'=>'Æ', 'U+00C7'=>'Ç',
            'U+00C8'=>'È', 'U+00C9'=>'É', 'U+00CA'=>'Ê', 'U+00CB'=>'Ë',
            'U+00CC'=>'Ì', 'U+00CD'=>'Í', 'U+00CE'=>'Î', 'U+00CF'=>'Ï',
            'U+00D0'=>'Ð', 'U+00D1'=>'Ñ', 'U+00D2'=>'Ò', 'U+00D3'=>'Ó',
            'U+00D4'=>'Ô', 'U+00D5'=>'Õ', 'U+00D6'=>'Ö', 'U+00D7'=>'×',
            'U+00D8'=>'Ø', 'U+00D9'=>'Ù', 'U+00DA'=>'Ú', 'U+00DB'=>'Û',
            'U+00DC'=>'Ü', 'U+00DD'=>'Ý', 'U+00DE'=>'Þ', 'U+00DF'=>'ß',
            'U+00E0'=>'à', 'U+00E1'=>'á', 'U+00E2'=>'â', 'U+00E3'=>'ã',
            'U+00E4'=>'ä', 'U+00E5'=>'å', 'U+00E6'=>'æ', 'U+00E7'=>'ç',
            'U+00E8'=>'è', 'U+00E9'=>'é', 'U+00EA'=>'ê', 'U+00EB'=>'ë',
            'U+00EC'=>'ì', 'U+00ED'=>'í', 'U+00EE'=>'î', 'U+00EF'=>'ï',
            'U+00F0'=>'ð', 'U+00F1'=>'ñ', 'U+00F2'=>'ò', 'U+00F3'=>'ó',
            'U+00F4'=>'ô', 'U+00F5'=>'õ', 'U+00F6'=>'ö', 'U+00F7'=>'÷',
            'U+00F8'=>'ø', 'U+00F9'=>'ù', 'U+00FA'=>'ú', 'U+00FB'=>'û',
            'U+00FC'=>'ü', 'U+00FD'=>'ý', 'U+00FE'=>'þ', 'U+00FF'=>'ÿ'
        );
    }//End populateEncoding

}//End PDFdecoder class
?>