<?
/*
  GoodreadsParser PHP library version 1.0
  Last updated 2011-1-1
  For questions or bug reports, contact justin@314pies.com.
  If you found this library useful, you can make a donation at 314pies.com.

  @license GNU/LGPLv3 http://www.gnu.org/licenses/lgpl.html
  

   Copyright (c) 2011, Justin Ray and 314pies.com


    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
define("GR_API_URL", "http://www.goodreads.com");
define("LAST_REQUEST_FILE", "last.txt");
define("USER_FILE_STUB", "gr-user-");
define("REVIEW_FILE_STUB", "gr-review-");
define("CACHE_PATH", "cache/");

/*
 * Caching constants
 */
//minimum time between calls to the same API in seconds
define("GR_MIN_REQUEST_S", 1);
//maximum time to cache Goodreads results
define("GR_MAX_CACHE_S", 120);

//debug functions
if (!function_exists("preprint")) {
    function preprint($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

}
if (!function_exists("predump")) {
    function predump($var) {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }

}

/*
 * The parser obtains XML data from the Goodreads API or a local cache and
 * returns a hierarchy of Container and Leaf objects that can be used to easily
 * access the data.
 *
 * This library only makes GET requests that require an API key.  It does not
 * deal with oauth requests for modifying user data.
 *
 *********************** Caching Behavior *******************************
 *
 * XML results are cached for performance and to prevent the parser from violating
 * the API terms of use.
 *
 * File timestamps on the last request file (e.g. "user-last.txt") provide a
 * channel for communication between multiple instances of the parser.  This way,
 * the API request frequency is maintained even if there are multiple instances
 * of the parser.  This mechanism may not achieve the desired result on a
 * multithreaded server, but PHP is limited in this respect.
 *
 * XML results from the API are cached in files that reflect the API call type,
 * the user ID, bookshelf and the pagination of the result (or as many of those
 * attributes as make sense).  For example, review-12345-read-1-20.xml is the
 * first 20 results from the 'read' bookshelf of user 12345.
 * 
 * Note that this code does not cache the parsed data object, merely the xml results
 * called from the API.  Depending on the application, you may want to cache the
 * actual processed output as well
 *
 ************************ Data structures ***************************
 * 
 * Container objects have a property for each tag that they contain.  If there are 
 * multiple tags  with the same name, then the property will be an array.  
 * Each container  has an xmlAttributes property that contains the tag 
 * attributes (if any).
 * 
 * Tags that do not contain other tags are represented by Leaf objects which 
 * contain an xmlAttribute array and a value property.
 *
 ************************ Example Usage ***************************
 * 
 * Example XML snippet:
<book>
  <isbn>0803730462</isbn>
  <authors>
    <author>
      <name>John Doe</name>
      <average_rating>3.38</average_rating>
    </author>
     <author>
      <name>Jane Doe</name>
      <average_rating>3.38</average_rating>
    </author>
  </authors>
  <shelves>
    <shelf name="read" />
    <shelf name="young-adult" />
  </shelves>
</book>
 * 
 * Code Examples (based on the XML above), assuming the top-level Container 
 * object is in the variable 'book':
 * //print isbn
 * echo $book->isbn->value;
 * //print author names
 * echo $book->authors[0]->name->value . " and " . $book->authors[1]->name->value;
 * //print shelf names
 * foreach (GoodreadsParser::asArray($book->shelves->shelf) {
 *   echo $shelf->xmlAttributes->name;
 * }
 * //note the use of the asArray function -- this is needed because if the book
 * //were only on one shelf, the object $book->shelves->shelf would not be an
 * //array, and would cause a PHP error.
 */

class GoodreadsParser {

    private $apikey;
    private $id;

    /**
     *
     * @param string $apikey goodreads Developer API key -- not the secret key
     * @param string $id Goodreads user id -- this is whose info gets returned.
     */
    public function __construct($apikey, $id) {
        $this->apikey = $apikey;
        $this->id = $id;
    }

    /**
     * Helper function that encapsulates single values into arrays, but leaves
     * existing arrays alone.
     * Useful when handling the returned data structures with foreach statements.
     *
     * @param <type> $value array or single value
     * @return <type> an array containing $value unless $value is already an array
     */
    public static function asArray($value) {
        if (is_array($value)) {
            return $value;
        } else {
            return array($value);
        }
    }

    /**
     * Attempt to clear cached XML results.  Note that this is "best effort" in
     * the sense that unlink errors are ignored, so you must make sure the script
     * actually has permissions to delete the cache files.
     *
     * WARNING: if there are other files in the cache (not created by the parser)
     * that begin with one of the prefix values, they will be deleted.
     *
     * @return int the number of cache files that were deleted.
     */
    public function clearCache() {
        $count = 0;
        $cache = self::makePathToFileHere(CACHE_PATH);
        $exclusions = array(
            USER_FILE_STUB . LAST_REQUEST_FILE,
            REVIEW_FILE_STUB . LAST_REQUEST_FILE
        );
        $ret .= "\n";
        $require_prefix = array(
            USER_FILE_STUB,
            REVIEW_FILE_STUB
        );
        if ($handle = opendir($cache)) {
            //delete all files
            while (false !== ($file = readdir($handle))) {
                if (FALSE === array_search($file, $exclusions)) {
                    foreach ($require_prefix as $prefix) {
                        if (substr($file, 0, strlen($prefix)) == $prefix) {
                            //found a prefix match
                            //make file into path
                            $file = self::makePathToFileHere(CACHE_PATH . $file);
                            if (is_file($file)) {
                                $count++;
                                //delete the file
                                @unlink($file);
                            } // else not a regular file
                        } //else does not match this prefix
                    } // else does not match any prefix
                } // else excluded, so don't delete
            }
        } else {
            throw new GRException("Cache path does not exist or could not open as directory.");
        }

        //attempt to delete all files in the cache related to the parser
        return $count;
    }

    /**
     * Returns one page of reviews from the given shelf.
     *
     * @param string $shelfname name of the shelf to get reviews from
     * @param integer $page page of reviews to get
     * @param integer $perpagecount number of refiews per page.  Max value is 200
     * @return Container Data structure for the reviews according to the argument criteria
     * @throws GRException if the request results in an error from the Goodreads API.
     */
    public function getReviewData($shelfname, $page, $perpagecount) {
        $cacheFile = self::makePathToFileHere(CACHE_PATH . REVIEW_FILE_STUB . "{$this->id}-$shelfname-$page-$perpagecount.xml");
        if ($perpagecount > 200) $perpagecount = 200;
        if (($xmlData = self::getCacheFile($cacheFile)) === FALSE) {
            //no cache, or it is too old, so make a new API request
            $lastRequestFile = self::makePathToFileHere(CACHE_PATH . REVIEW_FILE_STUB . LAST_REQUEST_FILE);
            //wait until we can make an API request
            if (file_exists($lastRequestFile)) {
                self::blockUntilFileOlderThan($lastRequestFile, GR_MIN_REQUEST_S);
            }
            //make the new request
            //the 'v' parameter is case sensitive -- 'V' gives different results
            $params = array(
                "v" => '2',
                "key" => $this->apikey,
                "sort" => "author",
                "shelf" => $shelfname,
                "page" => $page,
                "per_page" => $perpagecount
            );
            $xmlData = self::doUnauthenticatedRequest("/review/list/" . $this->id . ".xml", $params);
            touch($lastRequestFile);
            //write new XML data to cache file
            self::putCacheFile($cacheFile, $xmlData);
        }
        $xml = self::parseXmlToArray($xmlData);
        $reviews = new Container();
        $reviewStart = $xml['index']['reviews'][0];
        $reviews->parseXML($xml['data'], $reviewStart);
        return $reviews;
    }

    /**
     * Return $reviewcount reviews in date order, most recent first
     *
     * Note that this returns books in the order that they have been added,
     * even if they have an empty review.
     *
     * @param int $reviewcount
     * @param int $page which set of reviews to return, e.g. $page=1 is the $reviewcount most recent reviews, $page=2 is the next most recent set, etc.
     * @return Container data structure with the recent review data
     * @throws GRException if the request results in an error from the Goodreads API.
     */
    public function getRecentReviewData($reviewcount, $page='1') {
        if ($reviewcount > 200)
            $reviewcount = 200;
        $cacheFile = self::makePathToFileHere(CACHE_PATH . REVIEW_FILE_STUB . "{$this->id}--recent--$reviewcount-$page.xml");
        if (($xmlData = self::getCacheFile($cacheFile)) === FALSE) {
            //no cache, or it is too old, so make a new API request
            $lastRequestFile = self::makePathToFileHere(CACHE_PATH . REVIEW_FILE_STUB . LAST_REQUEST_FILE);
            //wait until we can make an API request
            if (file_exists($lastRequestFile)) {
                self::blockUntilFileOlderThan($lastRequestFile, GR_MIN_REQUEST_S);
            }
            //make the new request
            $params = array(
                "v" => '2',
                "key" => $this->apikey,
                "sort" => "date_updated",
                "order" => "d",
                "page" => $page,
                "per_page" => $reviewcount
            );
            $xmlData = self::doUnauthenticatedRequest("/review/list/" . $this->id . ".xml", $params);
            touch($lastRequestFile);
            //write new XML data to cache file
            self::putCacheFile($cacheFile, $xmlData);
        }
        $xml = self::parseXmlToArray($xmlData);
        $reviews = new Container();
        $reviewStart = $xml['index']['reviews'][0];
        $reviews->parseXML($xml['data'], $reviewStart);
        return $reviews;
    }

    /**
     *
     * @return Container data structure contining the user's information, shelf lists, etc.
     * @throws GRException if the request results in an error from the Goodreads API.
     */
    public function getUserData() {
        //check request file
        $cacheFile = self::makePathToFileHere(CACHE_PATH . USER_FILE_STUB . $this->id . ".xml");
        $xmlData = self::getCacheFile($cacheFile);
        if ($xmlData === FALSE) {
            //no cache, or it is too old, so make a new API request
            $lastRequestFile = self::makePathToFileHere(CACHE_PATH . USER_FILE_STUB . LAST_REQUEST_FILE);
            //wait until we can make an API request
            if (file_exists($lastRequestFile)) {
                self::blockUntilFileOlderThan($lastRequestFile, GR_MIN_REQUEST_S);
            }

            //make the new request
            $params = array(
                "key" => $this->apikey
            );
            $xmlData = self::doUnauthenticatedRequest("/user/show/" . $this->id . ".xml", $params);
            touch($lastRequestFile);
            //write new XML data to cache file
            self::putCacheFile($cacheFile, $xmlData);
        }
        $xml = self::parseXmlToArray($xmlData);
        $user = new Container();
        $userStart = $xml['index']['user'][0];
        $user->parseXML($xml['data'], $userStart);
        return $user;
    }

    /**
     * Build a request URL and get the raw XML result from the Goodreads API
     *
     * @param string $path relative path to the api
     * @param array $parameters query parameters
     * @return string XML data
     */
    private static function doUnauthenticatedRequest($path, $parameters=array()) {
        $request = GR_API_URL . $path;
        if (count($parameters) > 0) {
            $prepend = "?";
            foreach ($parameters as $key => $value) {
                $request .= $prepend . $key . "=" . $value;
                $prepend = "&";
            }
        }
        //execute request with curl
        $ch = curl_init($request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xml = curl_exec($ch);
        $result = curl_getinfo($ch);
        if ($result['http_code'] >= 400 && $result['http_code'] < 600) {
            Throw new GRException("Invalid response from goodreads.com to GET url: " . $result['url'], $result['http_code']);
        }
        curl_close($ch);
        return $xml;
    }

    /**
     * Block until the given file is older than $lockoutS
     * Using this function with a large value of $lockoutS can cause a script
     * timeout.
     *
     * @param string $filename the filename
     * @param integer $lockoutS the minimum number of seconds to wait
     */
    private static function blockUntilFileOlderThan($filename, $lockoutS) {
        if (time() > filemtime($filename) + $lockoutS)
            return;
        @time_sleep_until(filemtime($filename) + $lockoutS);
    }

    /**
     *
     * @param string $filename
     * @param integer $lockoutS
     * @return boolean true if the file $file is older than the specified number of seconds
     */
    private static function isFileOlderThan($filename, $lockoutS) {
        return (time() > filemtime($filename) + $lockoutS);
    }

    /**
     * Create an absolute path to the current directory and append the given
     * filename.
     * @param string $filename
     * @return string absolute path to the $filename
     */
    private static function makePathToFileHere($filename) {
        return dirname(__FILE__) . "/" . $filename;
    }

    /**
     * Read the cache file and return the results, or return false if the file
     * does not exist or is older than GR_MAX_CACHE.
     *
     * @param string $cacheFile the name of the cache file
     * @return string the contents of the file or false if the file does not exist.
     */
    private static function getCacheFile($cacheFile) {
        if (!file_exists(self::makePathToFileHere(CACHE_PATH))) {
            throw new GRException("Cache path does not exist.");
        }
        if (file_exists($cacheFile) && !self::isFileOlderThan($cacheFile, GR_MAX_CACHE_S)) {
            //read the cache file
            $fp = fopen($cacheFile, "r");
            if (flock($fp, LOCK_SH)) {
                $xmlData = file_get_contents($cacheFile);
                flock($fp, LOCK_UN);
                return $xmlData;
            } else {
                trigger_error("Unable to get a shared lock to read cached Goodreads XML", E_USER_WARNING);
                return false;
            }
            fclose($fp);
        } else {
            return false;
        }
    }

    /**
     * Save the $data into the file $cacheFile
     *
     * @param string $cacheFile
     * @param string $data XML data from the Goodreads API
     */
    private static function putCacheFile($cacheFile, $data) {
        $fp = fopen($cacheFile, "w");
        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $data);
            flock($fp, LOCK_UN);
        } else {
            trigger_error("Unable to get an exclusive  lock to write cached Goodreads XML", E_USER_WARNING);
        }
        fclose($fp);
    }

    /**
     * Use PHP libraries to parse an XML file.
     *
     * @param string $xmlData Raw XML data
     * @return array of data elements and indices
     */
    private static function parseXmlToArray($xmlData) {
        //parse xml result into arrays
        $parser = xml_parser_create();
        $result = array();
        $index = array();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $xmlData, $result, $index);
        xml_parser_free($parser);
        return array("data" => $result, "index" => $index);
    }

}

/*
 * Helper class for raising errors.
 */
class GRException extends Exception {

    public function __construct($message, $code) {
        parent::__construct($message, $code);
    }

}

/*
 * Helper data class that models an xml tag that contains other tags.  Can be
 * used recursively.
 * 
 * Child tags are set as member properties of the container.
 * Multiple tags with the same name are put in an array.
 * XML attributes are set in an Attributes object in the property xmlAttribute.
 * The xml tag name is saved in the property xmlTag (in case the variable name
 * does not reflect the tag).
 *
 */
class Container {

    public function __construct() {
        
    }

    /**
     * add XML data to make this container reflect the element at the $start index
     * in the $data array.  Recursively creates additional Container and Leaf
     * objects to reflect elements inside this array.
     *
     * @param array $data XML data array
     * @param string $start index of the starting tag for this element
     * @return integer index of the ending tag element that matches the start tag
     */
    public function parseXML(& $data, $start) {
        $this->xmlTag = $data[$start]['tag'];
        if ($data[$start]['attributes']) {
            $this->xmlAttributes = new Attributes($data[$start]['attributes']);
        }
        $index = $start + 1;
        while ($data[$index]['tag'] != $this->xmlTag) {
            if ($data[$index]['type'] == 'complete') {
                $member = $data[$index]['tag'];
                $this->setMember(new Leaf($data[$index]), $member);
            } elseif ($data[$index]['type'] == 'open') {
                //create a subcontainer
                $sub = new Container();
                $member = $data[$index]['tag'];
                $this->setMember($sub, $member);
                //parse the subcontainer
                $index = $sub->parseXML($data, $index);
            }
            $index++;
        }
        return $index;
    }

    /**
     * A method for setting the tag and attributes manually.  Along with calls
     * to setMember, this can be used for manually constructing or modifying a
     * data structure.
     *
     * @param string $tag tag name
     * @param Attributes $attributes XML attributes object
     */
    public function setXmlManual($tag, $attributes) {
        $this->xmlTag = $tag;
        $this->xmlAttributes = $attributes;
        $this->xmlManual = true;
    }

    /*
     *
     * $object 
     */
    /**
     * Add a member variable with the given name.  If a non-array variable with that name
     * already exists, the member is converted to an array containing the existing
     * and new objects.  If the member is already an array, then the new object
     * is appended to the end of the array.
     *
     * @param object or string $object the object to be added -- can be a single
     * value (e.g. string) or an object, but should not be an array.
     * Especially if the first call on a $name is an array, later objects will
     * be erroenously added to the array.
     * @param string $name the name of the property to add
     */
    public function setMember($object, $name) {
        if (isset($this->$name)) {
            if (!is_array($this->$name)) {
                $arr = array($this->$name);
                $this->$name = $arr;
            }
            array_push($this->$name, $object);
        } else {
            $this->$name = $object;
        }
    }

}

/*
 * Helper data class that models an xml tag that does not contain other tags.
 * Properties:
 * value - the tag value;
 * xmlAttributes (optional) - tag attributes, if any.
 * xmlTag - the tag name
 */
class Leaf {

    public function __construct($members) {
        $this->xmlTag = $members['tag'];
        $this->value = $members['value'];
        if ($members["attributes"]) {
            $this->xmlAttributes = new Attributes($members["attributes"]);
        }
    }

}

/*
 * Helper data class that models tag attributes.  Contains a member variable for
 * each attribute whose value is that attribute value.
 */
class Attributes {

    public function __construct($attributes) {
        //convert values in the associative array to object properties.
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

}

?>