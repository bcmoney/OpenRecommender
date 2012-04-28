<?php

require_once 'PDFreader.class.php';

/**
 * Extracts text strings from a PDF file and prints them out.
 * 
 * This is an example of PDF Reader's text extraction routine. It extracts
 * strings from a server side PDF file (uploading the file and providing the
 * file path to PDF reader is your responsibility), and returns them as an
 * array. It then prints the array, one row per string.
 * 
 * PHP version 5
 * 
 * @category  File_Formats
 * @package   PDF_Reader
 * @author    John M. Stokes <jstokes@heartofthefyre.us>
 * @copyright 2010 John M. Stokes
 * @license   http://www.opensource.org/licenses/bsd-license.html BSD Style License
 * @link      http://heartofthefyre.us/PDFreader/index.php
 */

$file = !empty($_REQUEST['file']) ? filter_var($_REQUEST['file'], FILTER_SANITIZE_URL) : 'file.pdf';


$PDF = new PDFreader();
$text = "";
try {
    $PDF->open($file);
    $text = $PDF->readText();
} catch(PDFexception $pdfEx) {
    echo '<p style="color:#FF0000; font-weight:bold; text-align:center;">'.$pdfEx.'</p>\n';
}

echo "<h2>Decoded text</h2><ul>\n";
foreach ($text as $row) {
    echo "<li>{$row}</li>\n";
}
echo "</ul>";

?>