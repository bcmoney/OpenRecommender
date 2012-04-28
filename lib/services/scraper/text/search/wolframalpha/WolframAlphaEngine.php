<?php

include_once 'include/WAResponse.php';
include_once 'include/WAPod.php';
include_once 'include/WASubpod.php';
include_once 'include/WAImage.php';
include_once 'include/WASubstitution.php';
include_once 'include/WAInfo.php';
include_once 'include/WAAssumption.php';

require_once "../../../Service.class.php";

/**
*  This is the Wolfram Alpha API PHP Wrapper.
*  This object will handle all requests and responses to the
*  Wolfram Alpha API.
*  @package WolframAlpha
*/
class WolframAlphaEngine extends Service {
  private $APIURL = 'http://api.wolframalpha.com/v1/query.jsp'; // REPLACE THIS WITH FINAL URL WHEN PUBLIC
  parent::setAppId();
  private $appID = parent::getAppId();  
  
  
  public function WolframAlphaEngine($appID) {
    $this->appID = $appID;
  }

  /**
   *  Contact the WolframAlpha API and return results in a PHP data structure.
   *
   *  @param string $input	string contains the input query to send to API.
   *
   *  @param array $otherParams	An optional hash array of key value pairs of other parameters
   *				to pass to the API.
   *
   *  @return mixed	Returns the results of the query in an OO data structure.
   * 			Returns NULL if no app id or input is specified.
   *  			Refer to the manual for detailed return values.
   */
  public function getResults( $input, $otherParams=array() ) {
    // if no input or appid has been specified, return null
    if ( !$input || !$this->appID ) 
      return null;

    // get the API URL
    $url = $this->constructURL( $input, $otherParams );

    // Get URL contents and parse XML
    $xml = simplexml_load_file( $url );

    return $this->cleanResponseTree( $xml );
  }


  // PRIVATE FUNCTIONS

  /**
   *  Constructs the API url to be used in this request.
   *
   *  @param string $input      string contains the input query to send to API.
   *
   *  @param array $otherParams An optional hash array of key value pairs of other parameters
   *                            to pass to the API.
   *
   *  @return string 	Returns the string URL to be used.
   */
  private function constructURL ( $input, $otherParams=array() ) {
    // construct the API URL
    $url = $this->APIURL ."?appid=". urlencode( $this->appID ) ."&input=". urlencode( $input );
    foreach ( $otherParams as $key => $value ) {
      $url .= "&". urlencode( $key ) ."=". urlencode( $value );
    }

    return $url;
  }

  /**
   *  This function will take as input a SimpleXMLElement tree and return
   *  a cleanly formatted OO representation of a Wolfram Alpha response.
   *
   *  @param SimpleXMLElement $xml	The XML Tree obtained from an API call
   *
   *  @return mixed			A formatted response object.
   */
  private function cleanResponseTree( $xml ) {
    $response = new WAResponse();

    // set raw xml if user needs it at all
    $response->rawXML = $xml->asXML();

    // set the global document attributes
    $response->attributes = $this->parseAttributes( $xml );

    // check if the API responded with an error
    if ( $response->isError() ) {
      $response->error = $xml->error;
      return $response;
    }

    foreach ( $xml->pod as $rawpod ) {
      $pod = new WAPod();
      $pod->attributes = $this->parseAttributes( $rawpod );
  
      // handle markup tag if present
      if ( $rawpod->markup ) {
        $pod->markup = (string) $rawpod->markup;
      }

      // handle subpod tags if present
      if ( $rawpod->subpod ) {
        $subpods = array();
        if ( is_array($rawpod->subpod) ) {
          $subpods = $rawpod->subpod;
        } else {
          $subpods[] = $rawpod->subpod;
        }

        foreach ( $subpods as $rawsubpod ) {
          $pod->addSubpod( $this->saveSubPod( $rawsubpod ) );
        }
      }

      // handle substitutions tag if present
      if ( $rawpod->substitutions ) {
        $substitutions = array();
        if ( is_array($rawpod->substitutions->substitution) ) {
          $substitutions = $rawpod->substitutions->substitution;
        } else {
          $substitutions[] = $rawpod->substitutions->substitution;
        }

        foreach ( $substitutions as $rawsub ) {
          $sub = new WASubstitution();
          $att = $this->parseAttributes( $rawsub );
          $sub->name = $att['name'];
          
          $pod->addSubstitution( $sub );
        }
      }

      if ( $rawpod->infos ) {
        $infos = array();
        if ( is_array($rawpod->infos->info) ) {
          $infos = $rawpod->infos->info;
        } else {
          $infos[] = $rawpod->infos->info;
        }

        foreach ( $infos as $rawinfo ) {
          $info = new WAInfo();
          $info->text = (string) $rawinfo->asXML();

          $pod->addInfo( $info );
        }
      }

      $response->addPod( $pod );
    }

    // if this response has a scripts section then add it
    if ( $xml->scripts ) {
      $response->script = (string) $xml->scripts;
    }
    // if this response has a css section then add it
    if ( $xml->css ) {
      $response->css = (string) $xml->css;
    }

    // if this response has any assumptions then loop throught them
    if ( $xml->assumptions ) {
      foreach ( $xml->assumptions->assumption as $rawassumption ) {
        $att = $this->parseAttributes( $rawassumption );
        foreach ( $rawassumption->value as $value ) {
          $valAtt = $this->parseAttributes( $value );
          $assumption = new WAAssumption();
          $assumption->type = $att['type'];
          $assumption->word = $att['word'];
          $assumption->name = $valAtt['name'];
          $assumption->description = $valAtt['desc'];
          $assumption->input = $valAtt['input'];

          $response->addAssumption( $assumption );
        }
      }
    }

    return $response;
  }

  /**
   *  Parses a SimpleXMLElement subpod into a WASubpod object
   *  @param SimpleXMLElement $rawsubpod	the xml tree of a subpod
   *  @return WASubpod	a WASubpod representation of the input param
   */
  private function saveSubPod( $rawsubpod ) {
    $subpod = new WASubpod();
    $subpod->attributes = $this->parseAttributes( $rawsubpod );
    $subpod->plaintext = (string) $rawsubpod->plaintext;
    if ( $rawsubpod->img ) {
      $subpod->image = new WAImage();
      $subpod->image->attributes = $this->parseAttributes( $rawsubpod->img );
    }
    $subpod->minput = (string) $rawsubpod->minput;
    $subpod->moutput = (string) $rawsubpod->moutput;
    if ( $rawsubpod->mathml ) {
      $subpod->mathml = $rawsubpod->mathml->asXML();
    }

    return $subpod;
  }

  /**
   *  Will parse out the attributes of a SImpleXMLElement into an array
   *  @param SimpleXMLElement $attributes	the attributes element
   * @return array()	key, value pairs of the attributes
   */
  private function parseAttributes( $attributes ) {
    $ret = array();
    foreach ( $attributes->attributes() as $key => $val ) {
      $ret[$key] = (string) $val;
    }
    return $ret;
  }
}

?>
