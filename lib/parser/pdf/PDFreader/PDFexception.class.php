<?php
/**
 * PDFexception.class.php is simply a package-specific implementation
 * of PHP 5's standard Exception class
 *
 * PHP version 5
 *
 * @category  File_Formats
 * @package   File_PDFreader
 * @author    John M. Stokes <jstokes@heartofthefyre.us>
 * @copyright 2010, 2011 John M. Stokes
 * @license   http://www.opensource.org/licenses/bsd-license.html BSD Style License
 * @link      http://heartofthefyre.us/PDFreader/index.php
 */

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
class PDFexception extends Exception
{
    /*************
    * PROPERTIES *
    **************/

    /**********
    * METHODS *
    ***********/

    /**
     * __construct invokes the default Exception constructor
     *
     * @param string $message - an error message
     * @param int    $code    - an error code
     *
     * @return N/A
     */
    public function __construct($message=null, $code=0)
    {
        parent::__construct($message, $code);
    }//End __construct


    /**
     * override default __toString method to identify this as PDFreader Exception
     *
     * @return string - PDFreader-specific exception message
     */
    public function __toString()
    {
        return "PDFreader Exception: {$this->message}\n";
    }//End __toString

}//End PDFexception class
?>