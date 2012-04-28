<?php


require '../../../../parser/html/QueryPath/QueryPath.php';

//////////////////////////////////////////////////////////////
// This class parses deviantART.com user profile pages
//
// Scroll down to see example.
// 
//
// Author: http://ukj.pri.ee
//
//////

define('ENC_CLASS_DEVIANTART','iso-8859-1');

mb_internal_encoding(ENC_CLASS_DEVIANTART);
mb_regex_encoding(ENC_CLASS_DEVIANTART);
set_time_limit(8);

 /**
 * Get range from text without a recusrion
 *
 * @param string $s source
 * @param string $c begin
 * @param string $e end
 * @param int $o offset default is 0
 * @param bool $ibe include begin and end
 * @param string $enc encoding
 * @return string , bool FALSE otherwise
 * @author http://ukj.pri.ee
 */
function getfrange(&$s, $b, $e, &$o, $ibe=FALSE, $enc='iso-8859-1') {
    /*
    // $o =(int) $o;
    if( in_array(strtolower($enc), array('ascii','us-ascii','iso-8859-1','latin-1','latin1'))){
        $fnStrpos = 'strpos';
        $fnStrlen = 'strlen';
        $fnSubstr = 'substr';
    } else {
        $fnStrpos = 'mb_strpos';
        $fnStrlen = 'mb_strlen';
        $fnSubstr = 'mb_substr';
    }
    */
    if($o < 0) $o = 0;

    $pb = mb_strpos($s, $b, $o, $enc);
    if ( $pb === FALSE ) return FALSE;
    $lb = mb_strlen( $b, $enc );
    
    $pe = mb_strpos($s, $e , $pb+$lb, $enc );
    if( $pe === FALSE ) return FALSE;
    $le = mb_strlen( $e, $enc );
    $ls = mb_strlen( $s, $enc );

    if($o > $ls) return FALSE;
    if($ibe)
        $sr = $b . mb_substr($s, $pb+$lb, $pe-$pb-$lb, $enc ) . $e;
    else
        $sr = mb_substr($s, $pb+$lb, $pe-$pb-$lb, $enc );
    $o=$pe+$le;

    return $sr;
} //getfrange()

/**
 * 
 * @author http://ukj.pri.ee
 * @class deviantart
 * 
 * TODO: JSON, XML output and external proxi.php file and dA RSS parsers
 */
class deviantart {
    
    /**
     * Data about member of deviantRT.com 
     * @var array
     */
    public $deviantID = array();
    
    private $username = '';
    private $baseURL = '';
    private $galleryURL = '';
    private $journalURL = '';
    private $journalRSSURL = '';
    private $favoritesURL = '';
    private $printsURL = '';
    private $doc = NULL;
    private $img_profilepicURL = NULL;
    private $page_profilepicURL = NULL;
    private $img_avatarURL = NULL;
    private $rawHTML = '';
    private $publicStuff = array(
        'baseURL',
        'galleryURL',
        'galleryRSSURL',
        'journalURL',
        'journalRSSURL',
        'favoritesURL',
        'favoritesRSSURL',
        'printsURL',
        'img_profilepicURL',
        'page_profilepicURL',
        'img_avatarURL',
        'username',
        'deviantID',
        'dALabel'
    );
    
    /**
     * Array of label ids and texts
     * @var array
     */
    public $dALabel = array(
        'fav_visual_artists'=>'Favourite visual artist',
        'fav_musical_artists'=>'Favourite band or musician',
        'fav_movies'=>'Favourite movies',
        'fav_tvshows'=>'Favourite TV shows',
        'fav_cartoon_character'=>'Favourite cartoon character',
        'fav_writer'=>'Favourite poet or writer',
        'fav_books'=>'Favourite books',
        'fav_games'=>'Favourite game',
        'fav_gaming_platform'=>'Favourite gaming platform',
        'fav_tools_of_trade'=>'Tools of the Trade',
        'fav_other'=>'Other Interests',
        
        'member_role'=>'Member role',
        'realname'=>'Real Name',
        'member_type'=>'Member Type',
        'location' => 'Location',
        'age' => 'Age',
        'sex' => 'Sex',
        'male'=>'Male',
        'female'=>'Female',
        'birthday' => 'Birthday',
        'whyiam_here'=>'Why I Am Here'
    );  
    
    
    
        
    
    /**
     * Loads user profile page from deviantART.com server and calls parser
     * 
     * @param string $username
     * @return void
     */
    public function __construct($username='') {
        $this->init();
        
        if( $this->set_valid_username($username) ) {
            $curl = new curl_get($this->baseURL);
            // $curl = TRUE;
            if($curl) {
                $this->rawHTML = $curl->returndata;           
                //$this->rawHTML = file_get_contents('da.html');
                //$this->rawHTML = file_get_contents('da.p.html');
                
                
                // Clean up 
                for($o=0,$script='';is_string($script);) {
                $script = getfrange($this->rawHTML, '<script', '</script>', $o, TRUE, ENC_CLASS_DEVIANTART);
                $style = getfrange($this->rawHTML, '<style', '</style>', $o, TRUE, ENC_CLASS_DEVIANTART);
                $this->rawHTML = str_replace($script,'',$this->rawHTML);
                $this->rawHTML = str_replace($style,'',$this->rawHTML);
                $o=0;
                }
                for($o=0,$style='';is_string($style);) {
                $style = getfrange($this->rawHTML, '<style', '</style>', $o, TRUE, ENC_CLASS_DEVIANTART);
                $this->rawHTML = str_replace($style,'',$this->rawHTML);
                $o=0;
                }
            }
                        
            $this->parse_deviantID();
            
        } 
        
    }
    
    /**
     *  Empty the object
     */
    public function __destruct() {
        $this->init();
        return FALSE;
    }
    
    
    /**
     * Print out usage example
     * 
     * @return string
     */
    public function __tostring() {
        
        $deviantID_root = "\n\n";
        $deviantID_root_sub = "\n";
        
        $tostring = 
        " <h4>".$this->username." on deviantART.com:</h4> \n"
        . "<dl> \n"
        . "   <dt>\$da = new deviantart('".$this->username."');</dt> \n";
        
        foreach($this->publicStuff as $name) {
            if(!empty($this->$name))
            
            if(is_int(strpos($this->$name,'http://')))
            $tostring .=
          '      <dd>(string) $da-><a href="'.$this->$name.'" target="_blank" title="'.$this->$name.'">'.$name."</a></dd> \n";
            elseif(is_array($this->$name))
            $tostring .=
          "      <dd>(array) \$da->".$name."</dd> \n";
        }
        
        foreach($this->deviantID as $name => $data) {
            if(is_string($data)){
                if(is_int(strpos($data,'http://')))
                    $deviantID_root .=
          "      <dd>(string) \$da->deviantID['".$name."'] = <a href=\"".$data."\" target=\"_blank\">".$data."</a></dd>";
                else
                    $deviantID_root .=
          "      <dd>(string) \$da->deviantID['".$name."'] = ".$data."</dd>";   
            }
            elseif(is_array($data)) {
                foreach($data as $sub_name => $sub_data) {
                    $label = $this->get_dALabel($sub_name);
                    $deviantID_root_sub .=
          "      <dd>(array) \$da->deviantID['".$name."']['".$sub_name."'] = <b>".$label.':</b> '.$sub_data."</dd>";
                }
            }
        }

        $tostring .= $deviantID_root . $deviantID_root_sub .
          "</dl> \n\n";
                    
        return $tostring;
    }
    
    
    /**
     * Get out one value by name
     * 
     * @param string $name
     * @return mixed
     */
    public function get($name) {
        $name = (string) $name;
        if(in_array($name, $this->publicStuff)) return $this->$name;
        else return '';
    }

    /**
     * Returns labels as beautiful text
     * 
     * Example:
     * <code>
     * $label = 'fav_movies';
     * echo '<p><b>'.$da->get_dALabel($label) .': </b>'. $da->deviantID['interests'][$label] . '</p>';
     * // Echos out:
     * // <p><b>Favourite movies: </b>Forrest Gump, Matrix, Green Line</p>
     * </code>
     * 
     * @param string $label
     * @return string
     */
    public function get_dALabel($label) {
        if(array_key_exists($label, $this->dALabel))
            return $this->dALabel[$label];
        return '';
    }   
    
    
    
    
    /**
     * Parses deviantART.com user page.
     * 
     * In some cases this method uses QueryPath.
     * 
     * TODO: Split to smaller parts
     */
    private function parse_deviantID() {
        
        /* Structure
         <div class="authorative-avatar">
            <a target="_self" href="http://liviugherman.deviantart.com/">
                <img class="avatar" style=" float:left; 
                    margin-right: 2px; margin-bottom: 2px;"  
                    src="http://a.deviantart.net/avatars/l/i/liviugherman.jpg" 
                    alt=":iconliviugherman:" title="liviugherman"/>
                 </a>
          </div>
         */
        $off = mb_strpos($this->rawHTML, '<div class="authorative-avatar">',0, ENC_CLASS_DEVIANTART);
        if(is_int($off)) {
            $img = getfrange($this->rawHTML, 'src="', '"', $off, FALSE, ENC_CLASS_DEVIANTART);    
            if(is_string($img)) {
                $this->deviantID['img_avatarURL'] = $img;
                $this->img_avatarURL = $img;
            }
        }       
        
        
        
        /* Structure:
         <dl class="f">
            <dt class="f h">Premium Member</dt>
            <dd class="f h oobehide">I am a Portrait Photographer</dd>
            
            <dt class="f h">Basistka</dt>
            <dd class="f h">23/Female/Poland</dd>
            
            <dt class="f h">Birthday</dt>
            <dd class="f h">March 3, 1988</dd>
            
            <dt class="f h oobehide">Why I Am Here</dt>
            <dd class="f oobehide">- To make friends</dd>
            <dd class="f oobehide">- To show my artwork to the world</dd>
         </dl>
         */
        $off = mb_strpos($this->rawHTML, 'id="super-secret-why"',0, ENC_CLASS_DEVIANTART);
        if(is_int($off))
        $secrets = getfrange($this->rawHTML, '<dl class="f">', '</dl>', $off, TRUE, ENC_CLASS_DEVIANTART);
        
        $doc = qp($this->qp_html5_hack($secrets));
        $Birthday = '';
        $WhyIAmHere = -1;
        $secreto_cnt = 0;
        foreach($doc->find('dl>.f') as $secreto) {
            $secret = $secreto->text();
            
            if($secreto_cnt==0)
                $this->deviantID['secrets']['member_type'] = $secret;
            if($secreto_cnt==1) {
                if(mb_strpos($secreto->attr('class'),'oobehide',0, ENC_CLASS_DEVIANTART))
                    $this->deviantID['secrets']['member_role'] = trim($secret,"\t\n\r -");
            }
            else $secreto_cnt++;
                
            
            
            if(is_int(mb_strpos($secret,'/',0, ENC_CLASS_DEVIANTART))) {
                foreach(explode('/', $secret) as $secret_i) {
                    if(is_numeric($secret_i)) 
                        $this->deviantID['secrets']['age'] = $secret_i;
                    elseif(is_int(mb_strpos($secret_i,'Female',0, ENC_CLASS_DEVIANTART)) 
                        or is_int(mb_strpos($secret_i,'Male',0, ENC_CLASS_DEVIANTART)) )
                        $this->deviantID['secrets']['sex'] = $secret_i;
                    else
                        $this->deviantID['secrets']['location'] = $secret_i;
              }
            }
            
            if($Birthday!='') {
                $this->deviantID['secrets']['birthday'] = $secret;
                $Birthday='';
            }
            if($secret=='Birthday') { $Birthday='birthday'; } 
            
            if($WhyIAmHere > -1) {
                $this->deviantID['secrets']['whyiam_here'] .=
                ($WhyIAmHere==0) ? trim($secret,"\t\n\r -") : ' | ' . trim($secret,"\t\n\r -") ;
                $WhyIAmHere++;
            }
            if($secret=='Why I Am Here') { 
                $WhyIAmHere=0; 
                $this->deviantID['secrets']['whyiam_here'] = '';
            }
                       
        }

           
            

        
        $off = mb_strpos($this->rawHTML, 'id="aboutme-profile-pic-shadow"',0, ENC_CLASS_DEVIANTART);
        if(is_int($off))
            $off = mb_strpos($this->rawHTML, '<a',$off, ENC_CLASS_DEVIANTART);        
        if(is_int($off)) {
            $img = getfrange($this->rawHTML, 'href="', '"', $off, FALSE, ENC_CLASS_DEVIANTART);    
            if(is_string($img)) {
                $this->deviantID['page_profilepicURL'] = $img;
                $this->page_profilepicURL = $img;
            }
        }
        
        if(is_int($off))
            $off = mb_strpos($this->rawHTML, '<img',$off, ENC_CLASS_DEVIANTART);
        if(is_int($off)) {
            $img = getfrange($this->rawHTML, 'src="', '"', $off, FALSE, ENC_CLASS_DEVIANTART);    
            $this->deviantID['img_profilepicURL'] = $img;
            $this->img_profilepicURL = $img;
        }
        
        
        $off = mb_strpos($this->rawHTML, 'gr-deviantid',0, ENC_CLASS_DEVIANTART);
        if(is_int($off)) {
            $img = getfrange($this->rawHTML, '<div class="c pp">', '</div>', $off, FALSE, ENC_CLASS_DEVIANTART);
            if(is_string($img)){
                $doc = qp($this->qp_html5_hack($img));
                $this->deviantID['page_profilepicURL'] = 
                $this->page_profilepicURL = $doc->find('a')->attr('href');
                
                $this->deviantID['img_profilepicURL'] = 
                $this->img_profilepicURL = $doc->find('img')->attr('src');
                
                $this->deviantID['img_profilepic_width'] = $doc->find('img')->attr('width');
                $this->deviantID['img_profilepicURL_height'] = $doc->find('img')->attr('height');
            }
        }
            
        
        
        $off = mb_strpos($this->rawHTML, '<span class="aboutme-realname">',0, ENC_CLASS_DEVIANTART);
        if(is_int($off)) {
            $this->deviantID['realname'] = trim(getfrange($this->rawHTML, '>', '</span>', $off, FALSE, ENC_CLASS_DEVIANTART));
        }
        
        $off = mb_strpos($this->rawHTML, 'id="aboutme-personal-info"',0, ENC_CLASS_DEVIANTART);
        if(is_int($off)) {
            $this->deviantID['personal_info'] = trim(getfrange($this->rawHTML, '>', '</div>', $off, FALSE, ENC_CLASS_DEVIANTART));
        }
        
        $off = mb_strpos($this->rawHTML, 'id="aboutme-bio"',0, ENC_CLASS_DEVIANTART);
        if(is_int($off)) {
            $this->deviantID['bio'] = trim(getfrange($this->rawHTML, '>', '</div>', $off, FALSE, ENC_CLASS_DEVIANTART));
        }        
        
        $off = mb_strpos($this->rawHTML, 'id="aboutme-website"',0, ENC_CLASS_DEVIANTART);
        if(is_int($off)) {
            $this->deviantID['url_website'] = trim(getfrange($this->rawHTML, 'href="', '"', $off, FALSE, ENC_CLASS_DEVIANTART));
        }
        
        
        $interests_label = '';
        $off = mb_strpos($this->rawHTML, '<div class="aboutme-interests-values"',0, ENC_CLASS_DEVIANTART);
        if(is_int($off)) {
            // = 
            $interests = getfrange($this->rawHTML, '>', '</div>', $off, FALSE, ENC_CLASS_DEVIANTART);
            $off=0;
            $label = '';
            $this->deviantID['interests'] = array();
            while(is_string($label)) {
                $label = getfrange($this->rawHTML, '<span class="aboutme-interests-label">', '</span>', $off, FALSE, ENC_CLASS_DEVIANTART);
                $value = getfrange($this->rawHTML, 'interests-value">', '</span>', $off, FALSE, ENC_CLASS_DEVIANTART);
          
                if(is_string($label) && is_string($value)) {
                    if(is_int(strpos($label,'visual'))) $interests_label = 'fav_visual_artists';
                    elseif(is_int(strpos($label,'bands'))) $interests_label = 'fav_musical_artists';
                    elseif(is_int(strpos($label,'movies'))) $interests_label = 'fav_movies';
                    elseif(is_int(strpos($label,'TV shows'))) $interests_label = 'fav_tvshows';
                    elseif(is_int(strpos($label,'books'))) $interests_label = 'fav_books';
                    elseif(is_int(strpos($label,'games'))) $interests_label = 'fav_games';
                    elseif(is_int(strpos($label,'books'))) $interests_label = 'fav_books';
                    elseif(is_int(strpos($label,'gaming platform'))) $interests_label = 'fav_gaming_platform';
                    elseif(is_int(strpos($label,'Tools of the Trade'))) $interests_label = 'fav_tools_of_trade';
                    elseif(is_int(strpos($label,'Other'))) $interests_label = 'fav_other';
                    else $interests_label = $label;
                    $this->deviantID['interests'][$interests_label] = $value;
                }             
            }
        }
        
        
        $off = mb_strpos($this->rawHTML, 'gr-deviantinfo">',0, ENC_CLASS_DEVIANTART);
        if(is_int($off)) {
            $DeviousInfo = getfrange($this->rawHTML, '<ul', '</ul>', $off, TRUE, ENC_CLASS_DEVIANTART);
            if(is_string($DeviousInfo)) {
                $doc = qp($this->qp_html5_hack($DeviousInfo));
                foreach($doc->find('ul>li') as $value) {
                    $label = $value->find('strong')->text();
                    $value = $value->not('strong')->text();
                    
                    if(is_int(strpos($label,'artist'))) $interests_label = 'fav_visual_artists';
                    elseif(is_int(strpos($label,'band or music'))) $interests_label = 'fav_musical_artists';
                    elseif(is_int(strpos($label,'movie'))) $interests_label = 'fav_movies';
                    //elseif(is_int(strpos($label,'TV shows'))) $interests_label = 'fav_tvshows';
                    elseif(is_int(strpos($label,'cartoon character'))) $interests_label = 'fav_cartoon_character';
                    elseif(is_int(strpos($label,'poet or writer'))) $interests_label = 'fav_writer';
                    //elseif(is_int(strpos($label,'books'))) $interests_label = 'fav_books';
                    elseif(is_int(strpos($label,'game'))) $interests_label = 'fav_games';
                    //elseif(is_int(strpos($label,'books'))) $interests_label = 'fav_books';
                    //elseif(is_int(strpos($label,'gaming platform'))) $interests_label = 'fav_gaming_platform';
                    //elseif(is_int(strpos($label,'Tools of the Trade'))) $interests_label = 'fav_tools_of_trade';
                    //elseif(is_int(strpos($label,'Other'))) $interests_label = 'fav_other';
                    else $interests_label = $label; 
                    
                    if(array_key_exists($interests_label, $this->deviantID['interests']))
                        $this->deviantID['interests'][$interests_label] .= '| ' . $value;
                    else
                        $this->deviantID['interests'][$interests_label] = $value;
                }
            } 
        }
        
        
    }
    
    
    /**
     * Frees some memory
     */
    private function init() {
        $this->username = 
        $this->baseURL =
        $this->galleryURL = 
        $this->journalURL = 
        $this->journalRSSURL =
        $this->favoritesURL = 
        $this->printsURL = 
        $this->rawHTML = 
        '';
        
        $this->doc = 
        $this->img_profilepicURL = 
        $this->page_profilepicURL = 
        $this->img_avatarURL = 
        NULL;
    }
    
    /**
     * Validates username
     * 
     * @param string $username
     * @return bool
     */
    private function set_valid_username($username) {

        if(preg_match('/[~*#=`]?[\d\w_-]+/', $username)) {
            $this->username = $username;
            $lowerusername = mb_strtolower($username,ENC_CLASS_DEVIANTART);
            
            $this->baseURL = 'http://'.$username.'.deviantart.com/';
            $this->galleryURL = 'http://'.$username.'.deviantart.com/gallery/';
            $this->galleryRSSURL = 'http://backend.deviantart.com/rss.xml?q=gallery%3A'.$lowerusername.'%2F4830726&type=deviation';
            $this->journalURL = 'http://'.$username.'.deviantart.com/journal/';
            // "application/rss+xml" 
            $this->journalRSSURL = 'http://backend.deviantart.com/rss.xml?q=by%3A'.$lowerusername.'&type=journal&formatted=1';
            $this->favoritesURL = 'http://'.$username.'.deviantart.com/favorites/';
            $this->favoritesRSSURL = 'http://backend.deviantart.com/rss.xml?q=favby%3A'.$lowerusername.'%2F2825750&type=deviation';
            $this->printsURL = 'http://'.$username.'.deviantart.com/prints/'; 
            
            return TRUE;
        }
        else return FALSE;
    }
    
    /**
     *  Helps to make valid xhtml from html5 
     * @param $html_fragment
     */
    private function qp_html5_hack($html_fragment) {
        return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Valid xHTML</title>
</head>
<body>
'.$html_fragment.'
</body>
</html>';   
    }
    
}



/**
 * Helps to retrieve data from remote server
 * @author http://ukj.pri.ee
 *
 */
class curl_get {

    /**
     * cURL request handler
     */
    private $ch = NULL;
    /**
     * @var string $returndata Retrieved data
     */
    public $returndata='';
    
    private $defaults = array( 
            CURLOPT_HEADER => 0, 
            CURLOPT_RETURNTRANSFER => TRUE, 
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => array(
              "Accept-Language: et,en-us,en;q=0.5",
              "Accept-Encoding: identity", 
              "Accept-Charset: ISO-8859-1,UTF-8,ASCII,Latin 1;q=0.7,*;q=0.7",
              "Accept: application/json,text/plain,text/html,application/xhtml+xml,text/xml,application/xml,text/javascript,application/javascript;q=0.9,*/*;q=0.8",
              "Connection: keep-alive" ),
            CURLOPT_QUOTE =>array(),
            CURLOPT_HTTP200ALIASES=>array(),
            CURLOPT_POSTQUOTE=>array(),
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.7; et; rv:1.9.2.18) Gecko/20110614 Firefox/9.1.18',
            CURLOPT_HTTPGET => TRUE
            //CURLOPT_DNS_CACHE_TIMEOUT=>10,
        ); 
        
    /** 
     * Send a GET requst using cURL 
     * @param string $url to request 
     * @return bool sucess
     */ 
    public function __construct($url) 
    {
    
        $this->ch = curl_init($url); 
        curl_setopt_array($this->ch, $this->defaults); 
        $this->returndata = curl_exec($this->ch);
        if( !$this->returndata ) 
        { 
            trigger_error(curl_error($this->ch)); 
            return FALSE;
        } 
        //$info = curl_getinfo($this->ch);
        return TRUE;
    }
    /**
     * Get retrieved data as string
     * @return string
     */
    public function get_data() {
        return $this->returndata;
    }
    /**
     * End session
     */
    public function __destruct() {
        curl_close($this->ch);
    }
}   

?>