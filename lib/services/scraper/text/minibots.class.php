<?
/* ---------------------------------------------------------- */
/* minibots.class.php Ver.1.9e                                */
/* ---------------------------------------------------------- */
/* Mini Bots class is a small php class that allows you to    */
/* use some free web seriveces online to retrive usefull data */
/* and infos. This version includes:                          */
/* smtp validation, check spelling, meteo, exchange rates,    */
/* shorten urls, and geo referencing with IP address and more */
/* Feel free to use in your applications, but link my blog:   */
/* http://www.barattalo.it                                    */
/* Giulio Pons                                                */
/* ---------------------------------------------------------- */

Class Minibots 
{
	private $file_size = 0;
	private $max_file_size = 5000;
	private $file_downloaded = "";

	public function __construct () {
		
	}

	public function getIP() {
		$ip="";
		if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
		else if(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
		else if(getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
		else $ip = "";
		return $ip;
	}


	private function dayadd($days,$date=null , $format="d/m/Y"){
		// add days to a date
		return date($format,strtotime($days." days",strtotime( $date ? $date : date($format) )));
	}


	private function attr($s,$attrname) {
		//return html attribute
		preg_match_all('#\s*('.$attrname.')\s*=\s*["|\']([^"\']*)["|\']\s*#i', $s, $x); 
		if (count($x)>=3) return isset($x[2][0]) ? $x[2][0] : "";
		return "";
	}

	private function makeabsolute($url,$link) {
		$p = parse_url($url);
		if (strpos( $link,"http://")===0 ) return $link;
		if($p['scheme']."://".$p['host']==$url && $link[0]!="/" && $link!=$url) return $p['scheme']."://".$p['host']."/".$link;
		if (strpos( $link, "/")===0) return "http://".$p['host'].$link;
		return str_replace(substr(strrchr($url, "/"), 1),"",$url).$link;
	}

	function on_curl_header($ch, $header) {	// to handle file size check and prevent downloading too much
		$trimmed = rtrim($header);   
		if (preg_match('/^Content-Length: (\d+)$/i', $trimmed, $matches)) {
			$file_size = (float)$matches[1];
			if ($file_size > $this->max_file_size) {
				// stop if bigger
				return -1;
			}
		}
		return strlen($header);
	}

	function on_curl_write($ch, $data) {	// to handle file size check and prevent downloading too much
		$bytes = strlen($data);
		$this->file_size += $bytes;
		$this->file_downloaded .= $data;
		if ($this->file_size > $this->max_file_size) {
			// stop if bigger
			return -1;
		}
		return $bytes;
	}


	private function getRemoteFileSize($url) {
		if (substr($url,0,4)=='http') {
			$x = array_change_key_case(get_headers($url, 1),CASE_LOWER);
			if ( strcasecmp($x[0], 'HTTP/1.1 200 OK') != 0 ) { $x = $x['content-length'][1]; }
			else { $x = $x['content-length']; }
		}
		else { $x = @filesize($url); }
		return $x;
	} 


	public function doSpelling($q) {
		// (thanks to google)
		// grab google page with search
		$web_page = file_get_contents( "http://www.google.it/search?q=" . urlencode($q) );
		// put anchors tag in an array
		preg_match_all('#<a([^>]*)?>(.*)</a>#Us', $web_page, $a_array);
		for($j=0;$j<count($a_array[0]);$j++) {
			// find link with spell suggestion and return it
			if(stristr($a_array[0][$j],"class=spell")) return strip_tags($a_array[0][$j]);
		}
		return $q;	//if no results returns the q value
	}


	public function doExchangeRate($m,$d) {
		// (thanks to bank of italy)
		// grab exchange rates
		$dar = explode("-" , $this->dayadd(-1,$d,"Y-m-d") );
		$web_page = file_get_contents( "http://uif.bancaditalia.it/UICFEWebroot/QueryOneDateAllCur?lang=en&rate=0&initDay=".$dar[2]."&initMonth=".$dar[1]."&initYear=".$dar[0]."&refCur=euro&R1=csv");
		// parse csv results
		$lines = explode("\n",$web_page);
		for($j=0;$j<count($lines);$j++) {
			$fields = explode(",",$lines[$j]);
			if ($fields[2]==$m) return $fields[4];
		}
		return "";
	}


	public function doMeteo($q,$thedate="") {
		//(thanks to google)
		if (!$thedate) $date = date("Y-m-d");	//today
			else $date = $thedate;
		if ($date>$this->dayadd(3,date("Y-m-d"),"Y-m-d"))return "";
		// grab google page with meteo query
		$web_page = file_get_contents( "http://www.google.it/search?q=meteo+" . urlencode($q) );
		//parse to find data
		preg_match_all('#<div class=e>(.*)</table>#Us', $web_page, $m);
		if (count($m)>0) {
			
			$p = array();
			preg_match_all('#<img([^>]*)?>#Us', $m[0][0], $img);
			for ($i=0;$i<count($img[0]);$i++) {
				$tag = str_replace("src=\"/","src=\"http://www.google.it/",$img[0][$i]);
				$p[$i]["date"]=$this->dayadd($i,date("Y-m-d"),"Y-m-d");
				$p[$i]["title"] = $this->attr($tag,"title");
				$p[$i]["img"] = $this->attr($tag,"src");
			}
			preg_match_all('#<nobr>(.*)</nobr>#Uis', $m[0][0], $nobr);
			for ($i=0;$i<count($nobr[1]);$i++) {
				$temp= explode("|",$nobr[1][$i]);
				$p[$i]["min"] = trim($temp[1]) ;
				$p[$i]["max"] = trim($temp[0]) ;
			}
			return (!$thedate?$p:$p[$date]);
		}
		return array();
	}


	public function doShortURL($ToConvert) {
		//(thanks to tinyurl.com)
		$short_url= file_get_contents('http://tinyurl.com/api-create.php?url=' . $ToConvert);
		return $short_url;
	}

	public function doShortURLDecode($url) {
		if (!function_exists("curl_init")) die("doShortURLDecode needs CURL module, please install CURL on your php.");
		$ch = @curl_init($url);
		@curl_setopt($ch, CURLOPT_HEADER, TRUE);
		@curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = @curl_exec($ch);
		preg_match('/Location: (.*)\n/', $response, $a);
		if (!isset($a[1])) return $url;
		return $a[1];
	}

	
	private function getHttpResponseCode($url) {
		if (!function_exists("curl_init")) die("getHttpResponseCode needs CURL module, please install CURL on your php.");
		// 404 not found, 403 forbidden...
		// for a full list: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
		$ch = @curl_init($url);
		@curl_setopt($ch, CURLOPT_HEADER, TRUE);
		@curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$status = array();
		preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($ch) , $status);
		return $status[1];
	}

	//like file_exists, but for remote urls.
	public function url_exists($url) {
		return ($this->getHttpResponseCode($url) == 200);
	}

	public function doGeoIp($ip="") {
		//(thanks to geoiptool)
		// -----------------------------------------------------------------------------------
		// better use this free api: http://ipinfodb.com/ip_query.php?ip=62.149.150.92
		// that returns clean xml code.
		if (!$ip) $ip = $this->getIP();
		$ar = array();
		$web_page = file_get_contents( "http://www.geoiptool.com/en/?IP=".$ip );
		preg_match_all('#<table([^>]*)tbl_style([^>]*)?>(.*)</table>#Us', $web_page, $t_array);
		for($j=0;$j<count($t_array[0]);$j++) {
			//find table with data
			if (stristr($t_array[0][$j],"IP Address")) {
				//parse data
				$table = $t_array[0][$j];
				preg_match_all('#<tr([^>]*)?>(.*)</tr>#Us', $table, $tr_array);
				for($i=0;$i<count($tr_array[0]);$i++) {
					$tar = explode(":", strip_tags ( $tr_array[0][$i] ) );
					$ar[ trim($tar[0]) ] = trim($tar[1]);
				}
			}
		}
		return $ar;
	}

	function doSMTPValidation($email, $probe_address="", $debug=false) {
		# --------------------------------
		# function to validate email address 
		# through a smtp connection with the 
		# mail server. returns an true when ok
		# or an array (msg, error code) when fails.
		# --------------------------------
		$output = "";
		# --------------------------------
		# Check syntax with regular expression
		# --------------------------------
		if (!$probe_address) $probe_address = $_SERVER["SERVER_ADMIN"];
		if (preg_match('/^([a-zA-Z0-9\._\+-]+)\@((\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?))$/', $email, $matches)) {
			$user = $matches[1];
			$domain = $matches[2];
			# --------------------------------
			# Check availability of DNS MX records
			# --------------------------------
			if (function_exists('checkdnsrr')) {
				# --------------------------------
				# Construct array of available mailservers
				# --------------------------------
				if(getmxrr($domain, $mxhosts, $mxweight)) {
					for($i=0;$i<count($mxhosts);$i++){
						$mxs[$mxhosts[$i]] = $mxweight[$i];
					}
					asort($mxs);
					$mailers = array_keys($mxs);
				} elseif(checkdnsrr($domain, 'A')) {
					$mailers[0] = gethostbyname($domain);
				} else {
					$mailers=array();
				}
				$total = count($mailers);
				# --------------------------------
				# Query each mailserver
				# --------------------------------
				if($total > 0) {
					# --------------------------------
					# Check if mailers accept mail
					# --------------------------------
					for($n=0; $n < $total; $n++) {
						# --------------------------------
						# Check if socket can be opened
						# --------------------------------
						if($debug) { $output .= "Checking server $mailers[$n]...\n";}
						$connect_timeout = 2;
						$errno = 0;
						$errstr = 0;
						# --------------------------------
						# controllo probe address
						# --------------------------------
						if (preg_match('/^([a-zA-Z0-9\._\+-]+)\@((\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?))$/', $probe_address,$fakematches)) {
							$probe_domain = str_replace("@","",strstr($probe_address, '@'));

							# --------------------------------
							# Try to open up socket
							# --------------------------------
							if($sock = @fsockopen($mailers[$n], 25, $errno , $errstr, $connect_timeout)) {
								$response = fgets($sock);
								if($debug) {$output .= "Opening up socket to $mailers[$n]... Success!\n";}
								stream_set_timeout($sock, 5);
								$meta = stream_get_meta_data($sock);
								if($debug) { $output .= "$mailers[$n] replied: $response\n";}
								# --------------------------------
								# Be sure to set this correctly!
								# --------------------------------
								$cmds = array(
									"HELO $probe_domain",
									"MAIL FROM: <$probe_address>",
									"RCPT TO: <$email>",
									"QUIT",
								);
								# --------------------------------
								# Hard error on connect -> break out
								# --------------------------------
								if(!$meta['timed_out'] && !preg_match('/^2\d\d[ -]/', $response)) {
									$codice = trim(substr(trim($response),0,3));
									if ($codice=="421") {
										//421 #4.4.5 Too many connections to this host.
										$error = $response;
										break;
									} else {
										if($response=="" || $codice=="") {
											//c'è stato un errore ma la situazione è poco chiara
											$codice = "0";
										}
										$error = "Error: $mailers[$n] said: $response\n";
										break;
									}
									break;
								}
								foreach($cmds as $cmd) {
									$before = microtime(true);
									fputs($sock, "$cmd\r\n");
									$response = fgets($sock, 4096);
									$t = 1000*(microtime(true)-$before);
									if($debug) {$output .= "$cmd\n$response" . "(" . sprintf('%.2f', $t) . " ms)\n";}
									if(!$meta['timed_out'] && preg_match('/^5\d\d[ -]/', $response)) {
										$codice = trim(substr(trim($response),0,3));
										if ($codice<>"552") {
											$error = "Unverified address: $mailers[$n] said: $response";
											break 2;
										} else {
											$error = $response;
											break 2;
										}
										# --------------------------------
										// il 554 e il 552 sono quota
										// 554 Recipient address rejected: mailbox overquota
										// 552 RCPT TO: Mailbox disk quota exceeded
										# --------------------------------
									}
								}
								fclose($sock);
								if($debug) { $output .= "Succesful communication with $mailers[$n], no hard errors, assuming OK\n";}
								break;
							} elseif($n == $total-1) {
								$error = "None of the mailservers listed for $domain could be contacted";
								$codice = "0";
							}
						} else {
							$error = "Il probe_address non è una mail valida.";
						}
					}
				} elseif($total <= 0) {
					$error = "No usable DNS records found for domain '$domain'";
				}
			}
		} else {
			$error = 'Address syntax not correct';
		}
		if($debug) {
			print nl2br(htmlentities($output));
		}
		if(!isset($codice)) {$codice="n.a.";}
		if(isset($error)) return array($error,$codice); else return true;
	}

	public function getUrlInfo($url,$maximages=5,$maxkbimg=10) {
		if (!function_exists("curl_init")) die("getUrlInfo needs CURL module, please install CURL on your php.");
		// this bot retrieves info about a url:
		// keywords, title, description,favicon and images
		// 
		$url = $this->makeabsolute($url, $this->doShortURLDecode($url));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);       // Fail on errors
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);     // return into a variable
		curl_setopt($ch, CURLOPT_PORT, 80);             //Set the port number
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);          // times out after 15s
		if($maximages==0) {
			// if you don't want images from html 
			// use only first 5 kb to reduce band used and time
			$this->max_file_size = 5000;
			curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'on_curl_header'));
			curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, 'on_curl_write'));
		}
		$web_page = curl_exec($ch);
		if(strlen($web_page) <= 1 && $maximages==0) {
			$web_page = $this->file_downloaded;
		}

		//$web_page = file_get_contents($url);
		$data['keywords']="";
		$data['description']="";
		$data['title']="";
		$data['favicon']="";
		$data['images']=array();
		$data['thumbsite']="http://open.thumbshots.org/image.pxf?url=".urlencode($url);
		//search title
		preg_match_all('#<title([^>]*)?>(.*)</title>#Uis', $web_page, $title_array);
		$data['title'] = $title_array[2][0];
		//search keywords and description
		preg_match_all('#<meta([^>]*)(.*)>#Uis', $web_page, $meta_array);
		//print_r($meta_array);
		for($i=0;$i<count($meta_array[0]);$i++) {
			if (strtolower($this->attr($meta_array[0][$i],"name"))=='description') $data['description'] = $this->attr($meta_array[0][$i],"content");
			if (strtolower($this->attr($meta_array[0][$i],"name"))=='keywords') $data['keywords'] = $this->attr($meta_array[0][$i],"content");
		}
		//search favicon
		preg_match_all('#<link([^>]*)(.*)>#Uis', $web_page, $link_array);
		for($i=0;$i<count($link_array[0]);$i++) {
			if (strtolower($this->attr($link_array[0][$i],"rel"))=='shortcut icon') $data['favicon'] = $this->makeabsolute($url,$this->attr($link_array[0][$i],"href"));
		}
		// search images big enough
		preg_match_all('#<img([^>]*)(.*)/?>#Uis', $web_page, $imgs_array);
		$imgs = array();
		for($i=0;$i<count($imgs_array[0]);$i++) {
			if ($src = $this->attr($imgs_array[0][$i],"src")) {
				$src = $this->makeabsolute($url,$src);
				if(!in_array($src,$imgs) && $this->getRemoteFileSize($src)>$maxkbimg*1000) array_push($imgs,$src);
			}
			if (count($imgs)>$maximages-1) break;
		}
		$data['images']=$imgs;

		return $data;
	}

	public function getUrlInfoFast($url,$maxbytes=4096) {
		if (!function_exists("curl_init")) die("getUrlInfo needs CURL module, please install CURL on your php.");
		$url = $this->makeabsolute($url, $this->doShortURLDecode($url));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);       // Fail on errors
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);     // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);          // times out after 15s
		$this->max_file_size = $maxbytes;
		//curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'on_curl_header'));
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, 'on_curl_write'));
		$web_page = curl_exec($ch);
		if(strlen($web_page) <= 1) $web_page = $this->file_downloaded;
		$data['d']="";
		$data['t']="";
		$data['f']="";
		$data['e']=""; // per mettere automaticamente embed di player video youtube e mp3
		$data['g']=""; // trigger per visualizzare embed
		//search title
		preg_match_all('#<title([^>]*)?>(.*)</title>#Uis', $web_page, $title_array);
		$data['t'] = isset($title_array[2][0]) ? trim(preg_replace('/ +/', ' ', $title_array[2][0])) : "senza titolo";
		//search keywords and description
		preg_match_all('#<meta([^>]*)(.*)>#Uis', $web_page, $meta_array);
		//print_r($meta_array);
		for($i=0;$i<count($meta_array[0]);$i++) if (strtolower($this->attr($meta_array[0][$i],"name"))=='description') $data['d'] = $this->attr($meta_array[0][$i],"content");
		//search favicon
		preg_match_all('#<link([^>]*)(.*)>#Uis', $web_page, $link_array);
		for($i=0;$i<count($link_array[0]);$i++) {
			if (strtolower($this->attr($link_array[0][$i],"rel"))=='shortcut icon') $data['f'] = $this->makeabsolute($url,$this->attr($link_array[0][$i],"href"));
		}
		$trigger = "<img src='http://open.thumbshots.org/image.pxf?url=".urlencode($url)."' />";
		$embed = "";

		//http://www.youtube.com/v/Md1E_Rg4MGQ&hl=en&fs=1&
		//http://www.youtube.com/watch?v=Md1E_Rg4MGQ&feature=aso
		preg_match_all('/^http:\/\/www.youtube.com\/(v\/|watch\?v=)([^&]*)(.*)$/', $url, $yarr);
		if(isset($yarr[2][0])) {
			$trigger = "<img src='http://img.youtube.com/vi/".$yarr[2][0]."/1.jpg'/>";
			$embed = $this->resizeEmbed(
			'<object width="480" height="385"><param name="movie" value="http://www.youtube.com/v/'.$yarr[2][0].'?fs=1&amp;hl=it_IT"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$yarr[2][0].'?fs=1&amp;hl=it_IT" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed></object>', 400);
		}

		//http://vimeo.com/17116744
		preg_match_all('/^http:\/\/vimeo.com\/([0-9]*)$/', $url, $varr);
		if(isset($varr[1][0])) {
			$trigger = "<img src='".$this->getVimeoInfo($varr[1][0],"thumbnail_small")."'/>";
			$embed = $this->resizeEmbed(
			'<iframe src="http://player.vimeo.com/video/'.$varr[1][0].'" width="400" height="225" frameborder="0"></iframe>' , 400);
		}
		
		$data["e"] = $embed;
		$data["g"] = $trigger;
		return $data;
	}

	//
	// copy remote file to server
	private function copyFile($url,$filename){
		$file = fopen ($url, "rb");
		if (!$file) return false; else {
			$fc = fopen($filename, "wb");
			while (!feof ($file)) {
				$line = fread ($file, 1028);
				fwrite($fc,$line);
			}
			fclose($fc);
			return true;
		}
	}

	//
	// save a url to a local pdf using pdfmyurl.com service
	public function url2pdf($url,$pdffilename) {
		return $this->copyFile("http://pdfmyurl.com?url=".urlencode( str_replace("http://","",$url) ), $pdffilename);
	}

	//
	// save a url to a local jpg thumb using open.thumbshots.com service
	public function url2thumb($url,$thumbfilename) {
		return $this->copyFile("http://open.thumbshots.org/image.pxf?url=".urlencode( $url ), $thumbfilename);
	}

	// 
	// return text from a url
	// thanks to php.net
	public function webpage2txt($url) {
		if (!function_exists("curl_init")) die("webpage2txt needs CURL module, please install CURL on your php.");
		$user_agent = "Mozilla/5.0 (Windows; U; Windows NT 6.0; it; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8";

		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);              // Fail on errors
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_PORT, 80);            //Set the port number
		curl_setopt($ch, CURLOPT_TIMEOUT, 15); // times out after 15s

		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

		$document = curl_exec($ch);

		$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
			'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			'@<![\s\S]*?–[ \t\n\r]*>@',         // Strip multi-line comments including CDATA
			'/\s{2,}/',
		);

		$text = preg_replace($search, " ", html_entity_decode($document));

		$pat[0] = "/^\s+/";
		$pat[2] = "/\s+\$/";
		$rep[0] = "";
		$rep[2] = " ";

		$text = preg_replace($pat, $rep, trim($text));

		return $text;
	}

	public function twitterSetStatus($user,$pwd,$status) {
		if (!function_exists("curl_init")) die("twitterSetStatus needs CURL module, please install CURL on your php.");
		$ch = curl_init();

		// -------------------------------------------------------
		// get login form and parse it
		curl_setopt($ch, CURLOPT_URL, "https://mobile.twitter.com/session/new");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543a Safari/419.3 ");
		$page = curl_exec($ch);
		$page = stristr($page, "<div class='signup-body'>");
		preg_match("/form action=\"(.*?)\"/", $page, $action);
		preg_match("/input name=\"authenticity_token\" type=\"hidden\" value=\"(.*?)\"/", $page, $authenticity_token);

		// -------------------------------------------------------
		// make login and get home page
		$strpost = "authenticity_token=".urlencode($authenticity_token[1])."&username=".urlencode($user)."&password=".urlencode($pwd);
		curl_setopt($ch, CURLOPT_URL, $action[1]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $strpost);
		$page = curl_exec($ch);
		// check if login was ok
		preg_match("/\<div class=\"warning\"\>(.*?)\<\/div\>/", $page, $warning);
		if (isset($warning[1])) return $warning[1];
		$page = stristr($page,"<div class='tweetbox'>");
		preg_match("/form action=\"(.*?)\"/", $page, $action);
		preg_match("/input name=\"authenticity_token\" type=\"hidden\" value=\"(.*?)\"/", $page, $authenticity_token);

		// -------------------------------------------------------
		// send status update
		$strpost = "authenticity_token=".urlencode($authenticity_token[1]);
		$tweet['display_coordinates']='';
		$tweet['in_reply_to_status_id']='';
		$tweet['lat']='';
		$tweet['long']='';
		$tweet['place_id']='';
		$tweet['text']=$status;
		$ar = array("authenticity_token" => $authenticity_token[1], "tweet"=>$tweet);
		$data = http_build_query($ar);
		curl_setopt($ch, CURLOPT_URL, $action[1]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$page = curl_exec($ch);

		return true;
	}

	//
	// get twitter infos from nickname
	// and get avatar url, parse data from page
	public function twitterInfo($nick) {
		if (!function_exists("curl_init")) die("twitterInfo needs CURL module, please install CURL on your php.");
		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL, "http://twitter.com/$nick"); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);		// Fail on errors
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);	// allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);		// return into a variable
		curl_setopt($ch, CURLOPT_PORT, 80);				// Set the port number
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);			// times out after 15s
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		$document = curl_exec($ch);
		preg_match_all('#<div class="stats">(.*)</div>#Uis', $document, $stats);
		preg_match_all('#<span[^>]*?>(.*)</span>#Uis', $stats[1][0], $spans);
		$o = array();
		for ($i=0;$i<count($spans[0]);$i++) {
			if ($this->attr($spans[0][$i],"id")=="following_count") $o['following'] = $spans[1][$i];
			if ($this->attr($spans[0][$i],"id")=="follower_count") $o['follower'] = $spans[1][$i];
			if ($this->attr($spans[0][$i],"id")=="lists_count") $o['lists'] = $spans[1][$i];
		}
		$o['avatar'] = "";
		preg_match_all('#<img [^>]*?>#Uis', $document, $t);
		for ($i=0;$i<count($t[0]);$i++) if ($this->attr($t[0][$i],"id")=="profile-image") $o['avatar'] = $this->attr($t[0][$i],"src");
		return $o;
	}

	//
	// get twitter infos from nickname
	// and get avatar url, parse data from api xml response
	public function twitterInfoApi($nick) {
		if (!function_exists("curl_init")) die("twitterInfoApi needs CURL module, please install CURL on your php.");
		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL, "http://api.twitter.com/1/users/show.xml?screen_name=$nick"); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		$obj = simplexml_load_string( curl_exec($ch) );
		if(is_object($obj)) {
			return array(
					"name" => (string)$obj->name,
					"description" => (string)$obj->description,
					"avatar" => (string)$obj->profile_image_url,
					"followers" => (string)$obj->followers_count,
					"following" => (string)$obj->friends_count,
					"status" => date("Y-m-d H:i:s", strtotime( (string)$obj->status->created_at ))." - ".(string)$obj->status->text
				);
		} else {
			return null;
		}
	}

	// 
	// update twitter status (old function, no longer valid, with basic authentication)
	private function twitterSetStatus__OLD__($user,$pwd,$status) {
		if (!function_exists("curl_init")) die("twitterSetStatus needs CURL module, please install CURL on your php.");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.twitter.com/1/statuses/update.xml");
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'status=' . urlencode($status));
		curl_setopt($ch, CURLOPT_USERPWD, "$user:$pwd");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		$document = curl_exec($ch);
		if ($document) return true; else return false;
	}

	//
	// elenco aggiornamenti di status
	public function twitterGetStatusList($nick) {
		if (!function_exists("curl_init")) die("twitterGetStatusList needs CURL module, please install CURL on your php.");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.twitter.com/1/statuses/user_timeline.xml?screen_name=$nick");
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		$obj = simplexml_load_string( curl_exec($ch) );
		$a= array();
		if ($obj) {
			for ($i=0;$i<count($obj->status);$i++) {
				$s = date("Y-m-d H:i:s", strtotime( (string)$obj->status[$i]->created_at ))." - ".(string)$obj->status[$i]->text;
				array_push($a,$s);
			}
		}
		return $a;
	}

	//
	// change Facebook status with curl
	// Thanks to Alste (curl stuff inspired by nexdot.net/blog)
	// This function executes the status update if $pagina == "home.php".
	// If you provide a different fan page url, this function will post
	// on the page wall (if the user can).
	private function setFacebookStatusOrPostToWall($status, $login_email, $login_pass, $pagina = "home.php", $debug=false) {
		if (!function_exists("curl_init")) die("setFacebookStatusOrPostToWall needs CURL module, please install CURL on your php.");
		//CURL stuff
		//This executes the login procedure
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://login.facebook.com/login.php?m&amp;next=http%3A%2F%2Fm.facebook.com%2Fhome.php');
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'email=' . urlencode($login_email) . '&pass=' . urlencode($login_pass) . '&login=' . urlencode("Log in"));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
		curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//make sure you put a popular web browser here (signature for your web browser can be retrieved with 'echo $_SERVER['HTTP_USER_AGENT'];' 
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.12) Gecko/2009070611 Firefox/3.0.12");
		curl_exec($ch);

		// post status or on a fan page
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_URL, 'http://m.facebook.com/'.$pagina);
		//echo 'http://m.facebook.com/'.$pagina;
		//die;
		$page = curl_exec($ch);

		if($pagina == "home.php" ) {
			// update status
			if ($debug) {
				//show information regarding the request
				print_r(curl_getinfo($ch));
				echo curl_errno($ch) . '-' . curl_error($ch);
				echo "<hr>";
				echo (htmlspecialchars($page));
				echo "<br><br>Your Facebook status seems to have been updated.";
			}
			curl_setopt($ch, CURLOPT_POST, 1);
			//this gets the post_form_id value
			preg_match("/input type=\"hidden\" name=\"post_form_id\" value=\"(.*?)\"/", $page, $form_id);
			preg_match("/input type=\"hidden\" name=\"fb_dtsg\" value=\"(.*?)\"/", $page, $fb_dtsg);
			preg_match("/input type=\"hidden\" name=\"charset_test\" value=\"(.*?)\"/", $page, $charset_test);
			preg_match("/input type=\"submit\" class=\"button\" name=\"update\" value=\"(.*?)\"/", $page, $update);
			//we'll also need the exact name of the form processor page
			//preg_match("/form action=\"(.*?)\"/", $page, $form_num);
			//sometimes doesn't work so we search the correct form action to use
			//since there could be more than one form in the page.
			preg_match_all("#<form([^>]*)>(.*)</form>#Ui", $page, $form_ar);
			for($i=0;$i<count($form_ar[0]);$i++) if(stristr($form_ar[0][$i],"post_form_id")) preg_match("/form action=\"(.*?)\"/", $page, $form_num);


			$strpost = 'post_form_id=' . $form_id[1] . '&status=' . urlencode($status) . '&update=' . urlencode($update[1]) . '&charset_test=' . urlencode($charset_test[1]) . '&fb_dtsg=' . urlencode($fb_dtsg[1]);
			if($debug) {
				echo "Parameters sent: ".$strpost."<hr>";
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $strpost );

			//set url to form processor page
			curl_setopt($ch, CURLOPT_URL, 'http://m.facebook.com' . $form_num[1]);
			curl_exec($ch);
			if ($debug) {
				//show information regarding the request
				print_r(curl_getinfo($ch));
				echo curl_errno($ch) . '-' . curl_error($ch);
				echo "<br><br>Your Facebook status seems to have been updated.";
			}
			//close the connection
			curl_close($ch);
		} else {
			// post on facebook page
			if ($debug) {
				//show information regarding the request
				print_r(curl_getinfo($ch));
				echo curl_errno($ch) . '-' . curl_error($ch);
				echo "<hr>";
				echo (htmlspecialchars($page)); 
			}
			curl_setopt($ch, CURLOPT_POST, 1);
			//this gets the post_form_id value
			preg_match("/form action=\"(.*?)\" method=\"post\"/", $page, $action);
			preg_match("/input type=\"hidden\" name=\"post_form_id\" value=\"(.*?)\"/", $page, $form_id);
			preg_match("/input type=\"hidden\" name=\"fb_dtsg\" value=\"(.*?)\"/", $page, $fb_dtsg);
			preg_match("/input type=\"hidden\" name=\"charset_test\" value=\"(.*?)\"/", $page, $charset_test);
			preg_match("/input type=\"submit\" class=\"button\" name=\"post\" value=\"(.*?)\"/", $page, $post);
			//we'll also need the exact name of the form processor page
			preg_match("/form action=\"(.*?)\"/", $page, $form_num);

			$strpost = 'post_form_id=' . $form_id[1] . '&message=' . urlencode($status) . '&post=' . urlencode($post[1]) . '&charset_test=' . urlencode($charset_test[1]) . '&fb_dtsg=' . urlencode($fb_dtsg[1]);
			if($debug) {
				echo "Parameters sent: ".$strpost."<hr>";
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $strpost );

			//set url to form processor page
			curl_setopt($ch, CURLOPT_URL, 'http://m.facebook.com' . $action[1]);
			curl_exec($ch);
			if ($debug) {
				//show information regarding the request
				print_r(curl_getinfo($ch));
				echo curl_errno($ch) . '-' . curl_error($ch);
				echo "<br><br>Your Facebook page wall have been updated.";
			}
			//close the connection
			curl_close($ch);

		}


	}

	public function setFacebookStatus($status, $login_email, $login_pass, $debug=false) {
		if (!function_exists("curl_init")) die("setFacebookStatus needs CURL module, please install CURL on your php.");
		$this->setFacebookStatusOrPostToWall($status, $login_email, $login_pass, "home.php", $debug);
	}

	public function postToFacebookPage($msg, $pagina, $login_email, $login_pass, $debug=false) {
		if (!function_exists("curl_init")) die("postToFacebookPage needs CURL module, please install CURL on your php.");
		//http://www.facebook.com/#!/pages/Favignana/38995680998?ref=ts
		// $pagina = "/pages/Favignana/38995680998";
		$this->setFacebookStatusOrPostToWall($msg, $login_email, $login_pass, $pagina, $debug);
	}


	//
	// get list of images from google images
	public function googleGetImages($k) {
		$url = "http://images.google.it/images?as_q=##query##&hl=it&imgtbs=z&btnG=Cerca+con+Google&as_epq=&as_oq=&as_eq=&imgtype=&imgsz=m&imgw=&imgh=&imgar=&as_filetype=&imgc=&as_sitesearch=&as_rights=&safe=images&as_st=y";
		$web_page = file_get_contents( str_replace("##query##",urlencode($k), $url ));

		$tieni = stristr($web_page,"dyn.setResults(");
		$tieni = str_replace( "dyn.setResults(","", str_replace(stristr($tieni,");"),"",$tieni) );
		$tieni = str_replace("[]","",$tieni);
		$m = preg_split("/[\[\]]/",$tieni);
		$x = array();
		for($i=0;$i<count($m);$i++) {
			$m[$i] = str_replace("/imgres?imgurl\\x3d","",$m[$i]);
			$m[$i] = str_replace(stristr($m[$i],"\\x26imgrefurl"),"",$m[$i]);
			$m[$i] = preg_replace("/^\"/i","",$m[$i]);
			$m[$i] = preg_replace("/^,/i","",$m[$i]);
			if ($m[$i]!="") array_push($x,$m[$i]);
		}
		return $x;
	}

	//
	// get users video from youtube
	public function youtubeGetVideos($user) {
		// you can retrieve thumbs from here: http://www.reelseo.com/youtube-thumbnail-image/
		// youtube thumbs here: http://img.youtube.com/vi/####VIDEOID#####/1.jpg
		$web_page = file_get_contents( "http://m.youtube.com/".urlencode($user) );
		preg_match_all('#<a (.*)</a>#Uis', $web_page, $links);

		$x = array();
		for ($i=0;$i<count($links[1]);$i++) {
			if (stristr($links[1][$i],"/watch")) {
				$m = preg_split("/[&=\?]/", str_replace("&amp;", "&", $this->attr($links[1][$i],"href")));
				for ($j=0;$j<count($m);$j++) if ($m[$j]=="v") { array_push($x,$m[$j+1]); break; }
			}
		}
		return $x;
	}

	private function decodeFlickrUsername($n) {
		$s = @file_get_contents("http://www.flickr.com/photos/".$n);
		preg_match_all('#<a([^>]*)?>(.*)</a>#Us', $s, $a_array);
		for($i=0;$i<count($a_array[0]);$i++) {
			//echo htmlspecialchars($a_array[0][$i])."<hr>";
			if(stristr($a_array[0][$i],"http://api.flickr.com/services/feeds")) {
				$m = preg_split("/[&=\?]/", $this->attr($a_array[0][$i],"href"));
				for ($j=0;$j<count($m);$j++) if ($m[$j]=="id") return $m[$j+1];
			}
		}
		return "";

	}
	public function parseFlickrFeed($user,$n) {
		$id = $this->decodeFlickrUsername($user);
		if(!$id) return array();
		// $id = 16664181@N00
		$url = "http://api.flickr.com/services/feeds/photos_public.gne?id={$id}&lang=it-it&format=rss_200";
		$s = file_get_contents($url);
		preg_match_all('#<item>(.*)</item>#Us', $s, $items);
		$ar = array();
		for($i=0;$i<count($items[1]);$i++) {
			if($i>=$n) return $out;
			$item = $items[1][$i];
			preg_match_all('#<link>(.*)</link>#Us', $item, $temp);
			$link = $temp[1][0];
			preg_match_all('#<title>(.*)</title>#Us', $item, $temp);
			$title = $temp[1][0];
			preg_match_all('#<media:thumbnail([^>]*)>#Us', $item, $temp);
			$thumb = $this->attr($temp[0][0],"url");
			$ar['images'][$i] = $thumb;
			$ar['link'][$i] = $link;
			$ar['title'][$i] = $title;
		}
		return $ar;
	}

	public function pushMeTo($widgeturl,$text,$signature) {
		if (!function_exists("curl_init")) die("pushMeTo needs CURL module, please install CURL on your php.");
		//$widgeturl = "http://pushme.to/q/widget/export/?hash=51ff0b6e3c1ce3a7a7e473198e1b6d9a";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $widgeturl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.12) Gecko/2009070611 Firefox/3.0.12");
		$page = curl_exec($ch);
		//this gets the post_form_id value
		preg_match("/form action=\"(.*?)\"/", $page, $form_action);
		preg_match("/textarea name=\"(.*?)\"/", $page, $message_field);
		preg_match("/input type=\"text\" name=\"(.*?)\"/", $page, $signature_field);

		$ch = curl_init();
		$strpost = $message_field[1].'=' . urlencode($text) . '&'.$signature_field[1].'=' . urlencode($signature);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $strpost );
		curl_setopt($ch, CURLOPT_URL, $form_action[1]);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.12) Gecko/2009070611 Firefox/3.0.12");
		$page = curl_exec($ch);
	}

	public function googleSuggestKeywords($k) {
		if (!function_exists("curl_init")) die("googleSuggestKeywords needs CURL module, please install CURL on your php.");
		// Get all the related keywords from Google Suggest (JUST ONE WORD ALLOWED)
		// http://www.labnol.org/internet/tutorial-create-bot-for-gtalk-yahoo-messenger/4354/
		$k = explode(" ",$k); $k = $k[0];
		$u = "http://google.com/complete/search?output=toolbar&q=" . $k;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $u);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$xml = simplexml_load_string(curl_exec($ch));
		curl_close($ch);
		// Parse the keywords 
		$result = $xml->xpath('//@data');
		$ar = array();
		while (list($key, $value) = each($result)) $ar[] = (string)$value;
		return $ar;
	}

	// from an address to a couple (lat,long) coordinates
	public function getLatLong($address){
		if (!is_string($address))die("All Addresses must be passed as a string");
		$_url = sprintf('http://maps.google.com/maps?output=js&q=%s',rawurlencode($address));
		$_result = false;
		if($_result = file_get_contents($_url)) {
			if(strpos($_result,'errortips') > 1 || strpos($_result,'Did you mean:') !== false) return false;
			preg_match('!center:\s*{lat:\s*(-?\d+\.\d+),lng:\s*(-?\d+\.\d+)}!U', $_result, $_match);
			$_coords['lat'] = $_match[1];
			$_coords['long'] = $_match[2];
		}
		return $_coords;
	}


	public function wikiDefinition($s) {
		//http://it.wikipedia.org/w/api.php?action=opensearch&search=subsonica&format=xml&limit=1
		$url = "http://en.wikipedia.org/w/api.php?action=opensearch&search=".urlencode($s)."&format=xml&limit=1";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);   // Include head as needed
		curl_setopt($ch, CURLOPT_NOBODY, FALSE);        // Return body
		curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs
		curl_setopt($ch, CURLOPT_REFERER, "");            // Referer value
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
		curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; he; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8");   // Webbot name
		$page = curl_exec($ch);
		$xml = simplexml_load_string($page);
		if((string)$xml->Section->Item->Description) {
			return array((string)$xml->Section->Item->Text, (string)$xml->Section->Item->Description, (string)$xml->Section->Item->Url);
		} else {
			return "";
		}
	}
	
	public function myspaceConcerts($user) {
		$ch = curl_init("http://www.myspace.com/".$user."/shows");
		curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);   // Include head as needed
		curl_setopt($ch, CURLOPT_NOBODY, FALSE);        // Return body
		curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs
		curl_setopt($ch, CURLOPT_REFERER, "");            // Referer value
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
		curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; he; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8");
		$page = curl_exec($ch);
		preg_match_all("#<a class=\"userLink\" href=\"/".$user."\">(.*)</a>#Us", $page, $a);
		$band = trim(strip_tags($a[1][0]));
		// months array is in italian because of from my web servers pages returns in italian
		// probably you have to change this array
		$months = array("gen"=>"01","feb"=>"02","mar"=>"03","apr"=>"04","mag"=>"05","giu"=>"06","lug"=>"07","ago"=>"08","set"=>"09","ott"=>"10","nov"=>"11","dic"=>"12");
		$out = array();
		$c=0;
		$li = preg_split("/<li class=\"moduleItem event( odd| even)?( first| last)? vevent\" ?>/i",$page);
		for($i=0;$i<count($li);$i++) {
			if(stristr($li[$i],"<div class=\"entryDate\">")) {
				//echo strip_tags($li[$i])."<hr>";
				//<span class="month"> ott</span>
				preg_match_all("#<span class=\"month\">(.*)</span>#Us", $li[$i], $temp);
				$month = $months[strip_tags(trim($temp[1][0]))];
				preg_match_all("#<span class=\"day\">(.*)</span>#Us", $li[$i], $temp);
				$day = str_pad( strip_tags(trim($temp[1][0])), 2, "0", STR_PAD_LEFT);
				$year = date("Y");
				$data = $year."-".$month."-".$day;
				if($data<date("Y-m-d")) { $data = (date("Y")+1)."-".$month."-".$day; }
				preg_match_all("#<h4>(.*)</h4>#Us", $li[$i], $temp);
				$posto = strip_tags(trim($temp[1][0]));
				preg_match_all("#<span class=\"locality\">(.*)</span>#Us", $li[$i], $temp);
				$citta = strip_tags(trim($temp[1][0]));
				preg_match_all("#<span class=\"region\">(.*)</span>#Us", $li[$i], $temp);
				$region = strip_tags(trim($temp[1][0]));
				preg_match_all("#<span class=\"country-name\">(.*)</span>#Us", $li[$i], $temp);
				$stato = strip_tags(trim($temp[1][0]));
				$out[$c]["band"] = $band;
				$out[$c]["date"] = $data;
				//$out[$c]["time"] = ""; not parsed
				$out[$c]["venue"] = $posto;
				//$out[$c]["url"] = ""; not parsed
				$out[$c]["where"] = $citta.",".$region.",".$stato;
				$c++;
			}
		}
		return $out;
	}

	public function getVimeoInfo($id, $info = 'thumbnail_medium') {
		// http://www.soapboxdave.com/2010/04/getting-the-vimeo-thumbnail/
		if (!function_exists('curl_init')) die('CURL is not installed!');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://vimeo.com/api/v2/video/$id.php");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$output = unserialize(curl_exec($ch));
		$output = $output[0][$info];
		curl_close($ch);
		return $output;
	}

	// resize video embed and iframes
	public function resizeEmbed($video,$new_width='') {
		preg_match("/width=\"([^\"]*)\"/i",$video,$w); $w = (integer)$w[1];
		preg_match("/height=\"([^\"]*)\"/i",$video,$h); $h = (integer)$h[1];
			if (!$new_width) $new_width = $w;
		$w2 = $new_width;
		$ratio = (float)($w2/$w);
		$h2 = (integer)($h * $ratio);
		$video = str_replace("width=\"$w\"","width=\"$w2\"",$video);
		$video = str_replace("height=\"$h\"","height=\"$h2\"",$video);
		//return array("embed"=>$video,"w"=>$w2,"h"=>$h2,"w0"=>$w,"h0"=>$h);
		return $video;
	}
	


}

?>