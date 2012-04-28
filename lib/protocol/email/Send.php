<?php

class AttachmentEmail {
	private $from = 'yours@email.com';
	private $from_name = 'Your Name';
	private $reply_to = 'yours@email.com';
	private $to = '';
	private $subject = '';
	private $message = '';
	private $attachment = '';
	private $attachment_filename = '';

	public function __construct($to, $subject, $message, $attachment = '', $attachment_filename = '') {
		$this -> to = $to;
		$this -> subject = $subject;
		$this -> message = $message;
		$this -> attachment = $attachment;
		$this -> attachment_filename = $attachment_filename;
	}

	public function mail() {
		if (!empty($this -> attachment)) {
			$filename = empty($this -> attachment_filename) ? basename($this -> attachment) : $this -> attachment_filename ;
			$path = dirname($this -> attachment);
			$mailto = $this -> to;
			$from_mail = $this -> from;
			$from_name = $this -> from_name;
			$replyto = $this -> reply_to;
			$subject = $this -> subject;
			$message = $this -> message;

			$file = $path.'/'.$filename;
			$file_size = filesize($file);
			$handle = fopen($file, "r");
			$content = fread($handle, $file_size);
			fclose($handle);
			$content = chunk_split(base64_encode($content));
			$uid = md5(uniqid(time()));
			$name = basename($file);
			$header = "From: ".$from_name." <".$from_mail.">\r\n";
			$header .= "Reply-To: ".$replyto."\r\n";
			$header .= "MIME-Version: 1.0\r\n";
			$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
			$header .= "This is a multi-part message in MIME format.\r\n";
			$header .= "--".$uid."\r\n";
			$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
			$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$header .= $message."\r\n\r\n";
			$header .= "--".$uid."\r\n";
			$header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use diff. tyoes here
			$header .= "Content-Transfer-Encoding: base64\r\n";
			$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
			$header .= $content."\r\n\r\n";
			$header .= "--".$uid."--";

			if (mail($mailto, $subject, "", $header)) {
				return true;
			} else {
				return false;
			}
		} else {
			$header = "From: ".($this -> from_name)." <".($this -> from).">\r\n";
			$header .= "Reply-To: ".($this -> reply_to)."\r\n";

			if (mail($this -> to, $this -> subject, $this -> message, $header)) {
				return true;
			} else {
				return false;
			}

		}
	}
}

?>