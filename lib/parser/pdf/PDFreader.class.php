<?php
/**
 * PDFreader.class.php is the control class that contains PDF reader's
 * public interface, parses a PDF document's control structures, and
 * instantiates the other classes.
 *
 * PDF reader will not run under PHP 4, as it uses PHP 5 class structure
 * and Exceptions. Some portions based on PDFhi by Chung Leong
 * (chernyshevsky@hotmail.com) and Zend Framework's PDF support, as noted
 * in the relevant method comments.
 *
 * SUPPORTED PDF VERSION: v1.7 and below
 * ACROFORMS (FDF) SUPPORT: included
 * SIGNED PDF SUPPORT: not available
 * DECODERS: Flate Decoding only
 * PREDICTORS: PNG Up unprediction only
 * CHARACTER SET: Unicode v5.2 Basic Latin and Latin-1 Supplement
 *     - i.e. character codes below \xFF (256)
 * ENCRYPTION: not supported
 *
 * PHP version 5.1
 *
 * LICENSE
 * Copyright (c) 2010, John M. Stokes
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  File_Formats
 * @package   File_PDFreader
 * @author    John M. Stokes <jstokes@heartofthefyre.us>
 * @copyright 2010, 2011 John M. Stokes
 * @license   http://www.opensource.org/licenses/bsd-license.html BSD Style License
 * @link      http://heartofthefyre.us/PDFreader/index.php
 */

require_once 'PDFreader/PDFbase.class.php';
require_once 'PDFreader/PDFdecoder.class.php';
require_once 'PDFreader/PDFpage.class.php';
require_once 'PDFreader/PDFform.class.php';

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
class PDFreader extends PDFbase
{
    /*************
    * PROPERTIES *
    **************/

    //User-defined attributes
    protected $startPage;
    protected $endPage;
    protected $filepath;

    //PDF Document Attributes
    protected $version;
    protected $linearized = false;

    //PHP Properties
    protected $pages;

    /**********
    * METHODS *
    ***********/

    /********************
     * PUBLIC INTERFACE *
     ********************/

    /**
     * __construct sets up an HTML page if debugging is enabled
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->debugLevel > self::DEBUG_OFF) {
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"';
            echo ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PDFreader Debugger</title>
</head>
<body>'."\n";
        }
        $this->trailer = array();
    }//End __construct


    /**
     * open creates a file handle to the PDF file
     * and optionally sets the start and end pages
     *
     * @param string $filepath  - the server path to the PDF file
     * @param int    $startPage - optional, 0-based page to start reading the PDF
     * @param int    $endPage   - optional, 0-based page to stop reading the PDF.
     *     If omitted, reads every page
     *
     * @return N/A
     */
    public function open($filepath, $startPage=0, $endPage=2147483647)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo "Entered open<br />\n";
        }

        if ($startPage > $endPage) {
            Throw new PDFexception('Error: Start page can\'t be after End page.
                Please select different pages.'
            );
        }
        $this->startPage = $startPage;
        $this->endPage = $endPage;

        $this->filepath = $filepath;
        $this->fh = fopen($filepath, 'rb');
        if ($this->fh === false) {
            Throw new PDFexception('Error: Can\'t open your PDF file.');
        }

        /*
         * Now that we have a valid file handle,
         * create the default PDFdecoder instance
         */
        $this->PDFdecoder = new PDFdecoder($this->fh);

        return;
    }//End open


    /**
     * readText is the controller function that populates
     * the object's properties and returns data
     *
     * @return array $text - an array of human-readable text strings
     */
    public function readText()
    {
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo "Entered readText<br />\n";
        }

        //readForm may have already extracted the structure
        if (count($this->Xrefs) < 1) {
            $this->extractStructure();
        }
        $text = $this->readTextStrings();

        return $text;
    }//End readText


    /**
     * readForm is the controller function that extracts key/value pairs
     * from a PDF form
     *
     * @return array $formFields - an associative array of form key/value pairs
     */
    public function readForm()
    {
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo "Entered readForm<br />\n";
        }

        //readPDF may have already extracted the structure
        if (count($this->Xrefs) < 1) {
            $this->extractStructure();
        }
        $formFields = $this->readFormFields();

        return $formFields;
    }//End readForm


    /**
     * readTextByPage is similar to the readText function, but
     * breaks up the reading page by page
     * 
     * @param int $pageNum - optional. The page number to extract
     * 
     * @return string $text - text extracted from the current page
     */
    public function readTextByPage($pageNum=1)
    {
        if ($this->debugLevel > self::DEBUG_OFF) {
            echo "Entered readTextByPage<br />\n";
        }
        
        //readForm may have already extracted the structure
        if (count($this->Xrefs) < 1) {
            $this->extractStructure();
        }
        
        if (count($this->pageTree['Kids']) < 1) {
            Throw new PDFexception('Error: No pages found in document.');
        }

        //Extract the single page the user requested
        $page = $this->createOnePage($pageNum);
        if (empty($page->tokens)) {
            Throw new PDFexception("No text found on page $pageNum.");
        }

        $textArray = array();
        foreach ($page->tokens as $token) {
            if (!empty($token)) { //Skip blank strings
                $textArray[] = utf8_encode($token);
            }
        }//Close foreach token

        if ($this->debugLevel > self::DEBUG_OFF) {
            echo '<h2>Extracted text:</h2>';
            var_dump($textArray);
            echo "<br />\n";
        }

        return $textArray;
    }//End readTextByPage


    /**
     * getPages returns the number of (PDF) pages in the document
     * 
     * @return int $numPages - the number of pages reported by the file
     */
    public function getPages()
    {
        //readForm may have already extracted the structure
        if (count($this->Xrefs) < 1) {
            $this->extractStructure();
        }
        
        return $this->pageTree['Count'];
    }//End getPages
    
    
    /**
     * close explicitly closes the PDF file handle
     *
     * @return N/A
     */
    public function close()
    {
        fclose($this->fh);
    }//End close


    /***********************
     * INTERNAL PROCESSING *
     ***********************/

    /**
     * extractStructure helps the controller functions generate the document
     * data structures
     *
     * @return N/A - operates directly on object variables
     */
    protected function extractStructure()
    {
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo "Entered extractStructure<br />\n";
        }

        //XREF TABLES AND TRAILERS
        $this->iterations = 0; //Set the failsafe for for readXrefs
        $this->readXrefs();
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo '<strong>Xref table: </strong>';
            var_dump($this->Xrefs);
            echo "<br />\n";
        }

        //ROOT
        $this->iterations = 0; //Set the failsafe for extractDictionary call
        //$this->trailers is multi-dimensional, so loop through it
        foreach ($this->trailers as $trailer) {
            if (isset($trailer['Root'])) {
                $rootString = $this->extractObject($trailer['Root']);
                $this->root = $this->extractDictionary($rootString);
                break; //Found the Root address. No need to keep looping
            }
        }
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo '<strong>Root: </strong>';
            var_dump($this->root);
            echo "<br />\n";
        }

        //PAGE TREE
        $this->iterations = 0; //Set the failsafe in preparation for readPageTree
        $pageString = $this->extractObject($this->root['Pages']);
        $this->pageTree = $this->readPageTree($pageString);
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo '<strong>Page tree: </strong>';
            var_dump($this->pageTree);
            echo "<br />\n";
        }

        return;
    }//End extractStructure


    /**
     * readXrefs parses out all the xref tables in the PDF
     * and adds them to the object's array
     *
     * @param int $xrefPosition - the byte offset for the current Xref table
     *
     * @return N/A - populates the object's $Xrefs array
     */
    protected function readXrefs($xrefPosition=null)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo 'Entered readXrefs - ';
        }
        if (++$this->iterations > self::MAX_ITERATIONS) { //Recursion failsafe
            Throw new PDFexception('XRef Overflow Error');
        }

        //Find out where the primary startxref table starts.
        if (!isset($xrefPosition)) {
            //Back up 32 bytes from the end of the file.
            //The startxref table should be in the last 32 bytes.
            fseek($this->fh, -32, SEEK_END);
            $buffer = fread($this->fh, 32);
            $startxref = strstr($buffer, 'startxref');
            //startxref table was not found. Notify the user.
            if ($startxref === false) {
                Throw new PDFexception('Error: Unable to read PDF file.
                    Your file may be damaged.'
                );
            }
            //Determine the xref table's starting position
            sscanf($startxref, "startxref %d", $xrefPosition);
        }

        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo "xrefPosition: $xrefPosition<br />\n";
        }
        fseek($this->fh, $xrefPosition, SEEK_SET); //Seek to the xref table
        $xrefLine = fgets($this->fh);

        /* TRADITIONAL XREF SECTION FOUND */
        if (strpos($xrefLine, 'xref') !== false) {
            if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
                echo "Found traditional xref<br />\n";
            }
            //Back up to the beginning of the xref table
            fseek($this->fh, $xrefPosition, SEEK_SET);
            $startIndex = 0;
            $xrefStart = $xrefCount = null;
            if (strlen($xrefLine) < 7) {
                $line = fgets($this->fh); //first line only has 'xref'. Drop it.
                //Get first index of the xreftable, and number of records
                fscanf($this->fh, '%d %d', $xrefStart, $xrefCount);
            } else if (strlen($xrefLine) < 17) {
                //Get first index of the xreftable, and number of records
                fscanf($this->fh, 'xref %d %d', $xrefStart, $xrefCount);
            } else {
                //Get first index of the xreftable, and number of records
                $objectAddr = $generation = $inUse = null;
                fscanf(
                    $this->fh, 'xref %d %d %s %s %s', $xrefStart,
                    $xrefCount, $objectAddr, $generation, $inUse
                );
                if ($inUse == 'n') { //Only get in-use objects
                    //Generate a PDF-style key for this object's byte offset
                    $key = (int)$xrefStart.' '.(int)$generation.' R';
                    $this->Xrefs[$key] = (int)$objectAddr;
                }
                $startIndex = 1;
            }
            if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
                echo "xref count: $xrefCount<br />\n";
            }
            //Read the rest of the xref table
            for ($i=$startIndex; $i<$xrefCount; $i++) {
                fscanf($this->fh, "%s %s %s", $objectAddr, $generation, $inUse);
                if ($inUse == 'n') { //Only get in-use objects
                    //Generate a PDF-style key for this object's byte offset
                    $key = ($xrefStart+$i).' '.(int)$generation.' R';
                    $this->Xrefs[$key] = (int)$objectAddr;
                }
            }
            //Read the trailer, which should immediately follow the xref table
            $line = fgets($this->fh);
            $trailer = array();
            if (substr($line, 0, 7) == 'trailer') {
                $trailer = $this->readTrailer($line);
                $this->trailers[] = $trailer;
            }
        } else if (strpos($xrefLine, '/XRef') !== false) { /* XREF STREAM FOUND */
            if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
                echo "Found Xref stream<br />\n";
            }
            //Read in the entire XRef stream object
            while (strpos($xrefLine, 'endstream') === false) {
                $xrefLine .= fread($this->fh, 64);
            }
            $trailer = $this->extractDictionary($xrefLine);
            //Chop off 'stream' and anything before it
            $buffer = substr($xrefLine, strpos($xrefLine, 'stream')+6);
            $buffer = ltrim($buffer); //Remove whitespace before stream, per PDF spec
            //Chop off anything after 'endstream'
            $buffer = substr($buffer, 0, $trailer['Length']);
            if (isset($trailer['Filter'])) {
                $buffer = $this->PDFdecoder->unfilter($trailer['Filter'], $buffer);
            }
            if (isset($trailer['DecodeParms'])) {
                $buffer = $this->PDFdecoder->unpredict(
                    $buffer, $trailer['DecodeParms']
                );
            }

            //Create arrays for our columns
            $types = array();
            $offsets = array();
            $generations = array();

            //Split the buffer into columns based on the W parameter,
            //which should always have 3 entries
            $widths = $trailer['W'];
            $totalWidth = array_sum($widths);
            $XrefArray = str_split($buffer, $totalWidth);
            foreach ($XrefArray as $row) {
                if ($widths[0] == 0) {
                    //If no type specified, default to 1 - PDF Spec 7.5.8.2
                    $types[] = chr(1);
                } else {
                    $types[] = substr($row, 0, $widths[0]);
                }
                $offsets[] = substr($row, $widths[0], $widths[1]);
                if ($widths[2] == 0) { //Generation defaults to 0
                    $generations[] = chr(0);
                } else {
                    $generations[] = substr($row, $widths[0]+$widths[1]);
                }
            }

            //Populate the $Xrefs array with the decoded data
            $streamRef = null;
            for ($i=0; $i<count($offsets); $i++) {
                $type = ord($types[$i]);
                switch ($type) {
                case 0: //Type 0 = free object (f). Ignore it.
                    break;
                case 1: //Type 1 = in use object (n), like traditional XRef table
                    $offset = hexdec(bin2hex($offsets[$i]));
                    //Determine the reference key at this offset
                    $gen = null;
                    fseek($this->fh, $offset, SEEK_SET);
                    fscanf($this->fh, '%d %d obj', $ref, $gen);
                    $key = "$ref $gen R";
                    //populate the $this->Xrefs array
                    $this->Xrefs[$key] = $offset;
                    break;
                case 2: //Type 2 = compressed object. Assume in use and generation 0
                    $reference = hexdec(bin2hex($offsets[$i]));
                    if (isset($streamRef) && $streamRef == "$reference 0 R") {
                        continue; //We've already decoded this one
                    }
                    $streamRef = "$reference 0 R";
                    $streamObject = $this->extractObjectStream($streamRef);
                    $refs = array_keys($streamObject);
                    //Add the extracted stream objects to the Xrefs table
                    foreach ($refs as $ref) {
                        //Pointless variable to comply w/PEAR 85-char limit
                        $Slen = strlen($streamObject[$ref]);

                        if (!isset($this->Xrefs[$ref])) {
                            //If it isn't in the Xrefs table, add it
                            $this->Xrefs[$ref] = $streamObject[$ref];
                        } else if (strlen($this->Xrefs[$ref]) < $Slen) {
                            //If it is in the Xrefs table, but has less data, add it
                            $this->Xrefs[$ref] = $streamObject[$ref];
                        }
                    }
                    break;
                }
            }

            $this->trailers[] = $trailer;
        } else { /* NO XREF FOUND */
            Throw new PDFexception('Error: Unable to find XRef table.');
        }

        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo 'Trailer: ';
            var_dump($trailer);
            echo "<br />\n";
        }
        
        //If trailer indicates Encrypted file, warn user
        if (isset($trailer['Encrypt'])) {
            Throw new PDFexception('Encrypted file detected. 
                Encryption is not supported in this version of PDFreader'
            );
        }

        //Determine if there's another xref table.
        //(Multiple xref tables can appear in a linearized PDF)
        if (isset($trailer['Prev'])) {
            $this->linearized = true;
            $this->readXrefs($trailer['Prev']); //Do it all again
        }

        return;
    }//End readXrefs


    /**
     * readTrailer reads the PDF trailer (the list of objects in the PDF file)
     * and puts the addresses in an array
     *
     * @param string $line - the line starting with the 'trailer' string.
     *     May contain trailer data.
     *
     * @return N/A - sets $this->trailer variable
     */
    protected function readTrailer($line)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo "Entered readTrailer<br />\n";
        }

        while (strpos($line, '>>') === false) {
            $line .= fgets($this->fh);
        }
        $trailer = $this->extractDictionary($line);

        return $trailer;
    }//End read_trailer


    /**
     * readPageTree parses the page tree dictionaries for the references to pages
     * and other page tree dictionaries
     *
     * @param string $pageString - the string representing this page tree object
     *
     * @return array $pageArray - the (multi-dimensional) array of page tree
     *     and page objects
     */
    protected function readPageTree($pageString)
    {
        if ($this->debugLevel > self::DEBUG_HIDE_STRUCTURE) {
            echo "Entered readPageTree<br />\n";
        }
        if (++$this->iterations > self::MAX_ITERATIONS) { //Recursion failsafe
            Throw new PDFexception('Page Tree Overflow Error');
        }

        $pageArray = $this->extractDictionary($pageString);

        $kidsArray = array();
        if ($pageArray['Type'] == '/Pages') { //Another Page Tree object
            foreach ($pageArray['Kids'] AS $kid) {
                $kidString = $this->extractObject($kid);
                //Do it again: Parse out the child dictionary or page object
                $kidsArray[$kid] = $this->readPageTree($kidString);
            }
            //Replace the references with arrays of the objects
            $pageArray['Kids'] = $kidsArray;
        }
        //Page objects are already parsed by the extractDictionary() call,
        //so no 'else' needed. Just return them

        return $pageArray;
    }//End readPageTree

     
    /**
     * createPages populates the $this->pages array with page objects
     *
     * @param array $pageTree - the array of pages
     *
     * @return N/A - operates directly on the $this->pages property
     */
    protected function createPages($pageTree)
    {
        if ($this->debugLevel > self::DEBUG_OFF) {
            echo "Entered createPages<br />\n";
        }

        if (count($this->pages) > 0) {
            return; //Already populated the pages array
        }
        
        if (++$this->iterations > self::MAX_ITERATIONS) {
            Throw new PDFexception('Create Pages Overflow Error');
        }
   
        foreach ($pageTree['Kids'] as $reference=>$pageDictionary) {
            if ($pageDictionary['Type'] == '/Pages') {
                $this->createPages($pageDictionary);
            } else if ($pageDictionary['Type'] != '/Page'
                || !isset($pageDictionary['Contents'])
            ) {
                continue;
            } else {
                $this->pages[] = new PDFpage($this->fh, $this->Xrefs,
                    $this->PDFdecoder, $pageDictionary, $reference
                );
            }
        }

        if (count($this->pages) < 1) {
            Throw new PDFexception('Error: No content found on pages.');
        }

        return;
    }//End createPages
    
    
    /**
     * createOnePage is a leaner version of createPages that
     * extracts the minimum necessary for a single page
     * 
     * @param int $pageNum - optional. The page to extract.
     *
     * @return object $page - the extracted page object
     */
    protected function createOnePage($pageNum=1)
    {
        if ($this->debugLevel > self::DEBUG_OFF) {
            echo "Entered createOnePage<br />\n";
        }
        
        $page = null;
        $i = 0; //Counter to compare to $pageNum
        foreach ($this->pageTree['Kids'] as $reference=>$pageDictionary) {
            if ($pageDictionary['Type'] == '/Pages') {
                //Added by Christoph Runkel, 2011-03-28
                foreach ($pageDictionary['Kids'] as $ref=>$pageDict) {
                    if ($pageDict['Type'] != '/Page'
                        || !isset($pageDict['Contents'])
                    ) {
                        continue;
                    } else {
                        if (++$i != $pageNum) {
                            continue;
                        }
                        $page = new PDFpage($this->fh,
                            $this->Xrefs, $this->PDFdecoder, $pageDict, $ref
                        );
                    }
                }//Close inner foreach
            } else if ($pageDictionary['Type'] != '/Page'
                || !isset($pageDictionary['Contents'])
            ) {
                continue;
            } else {
                if (++$i != $pageNum) {
                    continue;
                }
                $page = new PDFpage($this->fh, $this->Xrefs,
                    $this->PDFdecoder, $pageDictionary, $reference
                );
            }
        }
        
        return $page;
    }//End createOnePage


    /**
     * readTextStrings walks through each page object
     * and decodes the data in the page's text object.
     *
     * @return array $textArray - an array of human-readable text strings
     */
    protected function readTextStrings()
    {
        if ($this->debugLevel > self::DEBUG_OFF) {
            echo "Entered readTextStrings<br />\n";
        }
        if (count($this->pageTree['Kids']) < 1) {
            Throw new PDFexception('Error: No pages found in document.');
        }

        $this->createPages($this->pageTree);
        $textArray = array();
        foreach ($this->pages as $page) {
            if (empty($page->tokens)) {
                continue;
            }

            foreach ($page->tokens as $token) {
                if (!empty($token)) { //Skip blank strings
                    $textArray[] = utf8_encode($token);
                }
            }//Close foreach token
        }//Close foreach page

        if ($this->debugLevel > self::DEBUG_OFF) {
            echo '<h2>Extracted text:</h2>';
            var_dump($textArray);
            echo "<br />\n";
        }

        return $textArray;
    }//End readTextStrings


    /**
     * readFormFields walks through each form object and decodes the fields
     *
     * @return array $formFields - the extracted key/value pairs
     */
    protected function readFormFields()
    {
        if ($this->debugLevel > self::DEBUG_OFF) {
            echo "Entered readFormFields<br />\n";
        }
        if (empty($this->root['AcroForm'])) {
            Throw new PDFexception('Error: No forms found in document.');
        }

        //Assemble all the annotation dictionaries into one,
        //since they can contain form objects
        $this->createPages($this->pageTree);
        $Annots = array();
        foreach ($this->pages as $page) {
            $Annotations = $page->getAnnotations();
            if (is_string($Annotations)) {
                $Annots[$page->reference] = array($Annotations);
            } else {
                $Annots[$page->reference] = $Annotations;
            }
        }

        $formFields = array();
        $form = new PDFform(
            $this->fh, $this->Xrefs, $this->PDFdecoder,
            $this->root['AcroForm'], $Annots
        );
        $formFields = $form->getKeyValuePairs();

        return $formFields;
    }//End readFormFields


    /**
     * __destruct closes off the debugging HTML page and file handle
     */
    public function __destruct()
    {
        $this->close();
        if ($this->debugLevel > self::DEBUG_OFF) {
            echo "\n</body>\n</html>\n";
        }
    }//End __construct
}//End PDFreader class
?>