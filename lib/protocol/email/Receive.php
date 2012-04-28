<?php
$cfg = array();

######## CONFIG #######
$cfg['upload_dir'] = '../templates_c/import'; //what directory to upload images to
$cfg['mail_server'] = 'mail.bcmoney-mobiletv.com'; //email server
$cfg['mail_port'] = '110'; //email server port

/* email server services as described on http://php.net/imap_open */
$cfg['mail_services'] = '/pop3';
$cfg['mail_username'] = 'upload+bcmoney-mobiletv.com'; //username for the email address to check
$cfg['mail_password'] = 'Kbxf*#nO;gW$'; //password for above user
$cfg['debug'] = false; //true or false for debugging
/* Accepted File Types: if less than four characters, use a dot as the first char. */ 
$cfg['accepted'] = array('.3gp','.3g2','.flv','.mp4','.m4v','.ogg','.ogv','.png','.jpg','.jpe','jpeg','.gif');
######## END CONFIG #########
$pid=2;

$type = array('text', 'multipart', 'message', 'application', 'audio', 'image', 'video', 'other'); 
$encoding = array('7bit', '8bit', 'binary', 'base64', 'quoted-printable', 'other');

if(!is_dir($cfg['upload_dir'])) {
	@mkdir($cfg['upload_dir']) or die('Cannot create directory:'.$cfg['upload_dir'].'! Make sure the parent folder has write permissions'); 
}

// open POP connection
$inbox = @imap_open('{'.$cfg['mail_server'].':'.$cfg['mail_port'].$cfg['mail_services'].'}',$cfg['mail_username'], $cfg['mail_password']) or die('Connection to server failed.');

// parse message body
function parse($structure) {
	global $type;                                                       
	global $encoding;

	// create an array to hold message sections 
	$ret = array();

	// split structure into parts
	$parts = $structure->parts;
	for($x=0; $x<sizeof($parts); $x++) {
		$ret[$x]['pid'] = ($x+1);
		$that = $parts[$x];

		// default to text
		if ($that->type == '') {
			$that->type = 0;
		}
		$ret[$x]['type'] = $type[$that->type] . '/' . strtolower($that->subtype);

		// default to 7bit
		if ($that->encoding == '') {
			$that->encoding = 0;
		}

		$ret[$x]['encoding'] = $encoding[$that->encoding];
		$ret[$x]['size'] = strtolower($that->bytes);
		$ret[$x]['disposition'] = strtolower($that->disposition);

		if ($that->ifparameters == 1) {
			$params = $that->parameters;
			foreach ($params as $p) {
				if($p->attribute == 'NAME') {
					$ret[$x]['name'] = $p->value;
					break;
				}
			}
		}
	}
	return $ret;
}

function get_attachments($arr) {
	for($x=0; $x < sizeof($arr); $x++) {
		if($arr[$x]['disposition'] == 'attachment') {
			$ret[] = $arr[$x];
		}
		return $ret;
	}

	$count = @imap_num_msg($inbox) or die('No Messages in mailbox!');

	// get message headers and structure
	for ($c = 1; $c <= $count; $c++) {
		$id = $c;
		$headers = imap_header($inbox, $id);
		$structure = imap_fetchstructure($inbox, $id);

		// if multipart, parse
		if(sizeof($structure->parts) > 1) {
			$sections = parse($structure);
			$attachments = get_attachments($sections);
		}

		if ($cfg['debug']) {
			echo 'Structure of message: ' . $id . '<br/><pre>';
			print_r($structure);
			echo '</pre><br/>';
			echo 'Sections of message: ' . $id . '<br/><pre>';
			print_r($sections);
			echo '</pre><br/>';
		}

		// look for specified part
		for($x=0; $x<sizeof($sections); $x++) {
			if($sections[$x]['pid'] == $pid) {
				$dtype = $sections[$x]['type'];
				$dencoding = $sections[$x]['encoding'];
				$filename = $sections[$x]['name'];
			}
		}

		if ($cfg['debug']) {
			echo ' type: ' . $dtype . '<br/>';
			echo 'encoding: ' . $dencoding . '<br/>';
			echo 'filename: ' . $filename . '<br/>';
			echo ' id: ' . $id . '<br/><br/>';
		}

		$attachment = imap_fetchbody($inbox, $id, $pid);

		if (!$cfg['debug']) {
		  //if (in_array(substr($filename, -4), $cfg['accepted'])) {
			if ($dencoding == 'base64') {
				// Decode and save attachment to a file
				list($usec, $sec) = explode(' ', microtime());
				$usec = substr($usec,2,3);
				$name = date('Ymd.His');
				$fp=fopen($cfg['upload_dir'].'/'.$name.'_'.$filename,'w');
				fwrite($fp,imap_base64($attachment));
				fclose($fp);
			}
		  //}
		}
	}

	if (!$cfg['debug']) {
		for ($i = 1; $i <= $count; $i++) {
			imap_delete($inbox, $i); // delete all email
		}
		imap_expunge($inbox);
	}
	imap_close($inbox);
	if (!$cfg['debug']) {
		header('Location: '.$cfg['upload_dir']);
	}
}
?>