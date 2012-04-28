<?php
// Main ReciveMail Class File - Version 1.1 (02-06-2009)
/*
 * File: recivemail.class.php
 * Description: Reciving mail With Attechment
 * Version: 1.1
 * Created: 01-03-2006
 * Modified: 02-06-2009
 * Author: Mitul Koradia
 * Email: mitulkoradia@gmail.com
 * Cell : +91 9825273322
 */
 
/***************** Changes *********************
*
* 1) Added feature to retrive embedded attachment - Changes provided by. Antti <anttiantti83@gmail.com>
* 2) Added SSL Supported mailbox.
*
**************************************************/

class receiveMail
{
	var $server='';
	var $username='';
	var $password='';
	
	var $marubox='';					
	
	var $email='';			
	
	function receiveMail($username,$password,$EmailAddress,$mailserver='localhost',$servertype='pop',$port='110',$ssl = false) //Constructure
	{
		if($servertype=='imap')
		{
			if($port=='') $port='143'; 
			$strConnect='{'.$mailserver.':'.$port. '}INBOX'; 
		}
		else
		{
			$strConnect='{'.$mailserver.':'.$port. '/pop3'.($ssl ? "/ssl" : "").'}INBOX'; 
		}
		$this->server			=	$strConnect;
		$this->username			=	$username;
		$this->password			=	$password;
		$this->email			=	$EmailAddress;
	}
	function connect() //Connect To the Mail Box
	{
		$this->marubox=@imap_open($this->server,$this->username,$this->password);
		
		if(!$this->marubox)
		{
			echo "Error: Connecting to mail server";
			exit;
		}
	}
	function getHeaders($mid) // Get Header info
	{
		if(!$this->marubox)
			return false;

		$mail_header=imap_header($this->marubox,$mid);
		$sender=$mail_header->from[0];
		$sender_replyto=$mail_header->reply_to[0];
		if(strtolower($sender->mailbox)!='mailer-daemon' && strtolower($sender->mailbox)!='postmaster')
		{
			$mail_details=array(
					'from'=>strtolower($sender->mailbox).'@'.$sender->host,
					'fromName'=>$sender->personal,
					'toOth'=>strtolower($sender_replyto->mailbox).'@'.$sender_replyto->host,
					'toNameOth'=>$sender_replyto->personal,
					'subject'=>$mail_header->subject,
					'to'=>strtolower($mail_header->toaddress)
				);
		}
		return $mail_details;
	}
	function get_mime_type(&$structure) //Get Mime type Internal Private Use
	{ 
		$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"); 
		
		if($structure->subtype) { 
			return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype; 
		} 
		return "TEXT/PLAIN"; 
	} 
	function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) //Get Part Of Message Internal Private Use
	{ 
		if(!$structure) { 
			$structure = imap_fetchstructure($stream, $msg_number); 
		} 
		if($structure) { 
			if($mime_type == $this->get_mime_type($structure))
			{ 
				if(!$part_number) 
				{ 
					$part_number = "1"; 
				} 
				$text = imap_fetchbody($stream, $msg_number, $part_number); 
				if($structure->encoding == 3) 
				{ 
					return imap_base64($text); 
				} 
				else if($structure->encoding == 4) 
				{ 
					return imap_qprint($text); 
				} 
				else
				{ 
					return $text; 
				} 
			} 
			if($structure->type == 1) /* multipart */ 
			{ 
				while(list($index, $sub_structure) = each($structure->parts))
				{ 
					if($part_number)
					{ 
						$prefix = $part_number . '.'; 
					} 
					$data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1)); 
					if($data)
					{ 
						return $data; 
					} 
				} 
			} 
		} 
		return false; 
	} 
	function getTotalMails() //Get Total Number off Unread Email In Mailbox
	{
		if(!$this->marubox)
			return false;

		$headers=imap_headers($this->marubox);
		return count($headers);
	}
	function GetAttach($mid,$path) // Get Atteced File from Mail
	{
		if(!$this->marubox)
			return false;

		$struckture = imap_fetchstructure($this->marubox,$mid);
		$ar="";
		if($struckture->parts)
        {
			foreach($struckture->parts as $key => $value)
			{
				$enc=$struckture->parts[$key]->encoding;
				if($struckture->parts[$key]->ifdparameters)
				{
					$name=$struckture->parts[$key]->dparameters[0]->value;
					$message = imap_fetchbody($this->marubox,$mid,$key+1);
					if ($enc == 0)
						$message = imap_8bit($message);
					if ($enc == 1)
						$message = imap_8bit ($message);
					if ($enc == 2)
						$message = imap_binary ($message);
					if ($enc == 3)
						$message = imap_base64 ($message); 
					if ($enc == 4)
						$message = quoted_printable_decode($message);
					if ($enc == 5)
						$message = $message;
					$fp=fopen($path.$name,"w");
					fwrite($fp,$message);
					fclose($fp);
					$ar=$ar.$name.",";
				}
				// Support for embedded attachments starts here
				if($struckture->parts[$key]->parts)
				{
					foreach($struckture->parts[$key]->parts as $keyb => $valueb)
					{
						$enc=$struckture->parts[$key]->parts[$keyb]->encoding;
						if($struckture->parts[$key]->parts[$keyb]->ifdparameters)
						{
							$name=$struckture->parts[$key]->parts[$keyb]->dparameters[0]->value;
							$partnro = ($key+1).".".($keyb+1);
							$message = imap_fetchbody($this->marubox,$mid,$partnro);
							if ($enc == 0)
								   $message = imap_8bit($message);
							if ($enc == 1)
								   $message = imap_8bit ($message);
							if ($enc == 2)
								   $message = imap_binary ($message);
							if ($enc == 3)
								   $message = imap_base64 ($message);
							if ($enc == 4)
								   $message = quoted_printable_decode($message);
							if ($enc == 5)
								   $message = $message;
							$fp=fopen($path.$name,"w");
							fwrite($fp,$message);
							fclose($fp);
							$ar=$ar.$name.",";
						}
					}
				}				
			}
		}
		$ar=substr($ar,0,(strlen($ar)-1));
		return $ar;
	}
	function getBody($mid) // Get Message Body
	{
		if(!$this->marubox)
			return false;

		$body = $this->get_part($this->marubox, $mid, "TEXT/HTML");
		if ($body == "")
			$body = $this->get_part($this->marubox, $mid, "TEXT/PLAIN");
		if ($body == "") { 
			return "";
		}
		return $body;
	}
	function deleteMails($mid) // Delete That Mail
	{
		if(!$this->marubox)
			return false;
	
		imap_delete($this->marubox,$mid);
	}
	function close_mailbox() //Close Mail Box
	{
		if(!$this->marubox)
			return false;

		imap_close($this->marubox,CL_EXPUNGE);
	}
}
?>