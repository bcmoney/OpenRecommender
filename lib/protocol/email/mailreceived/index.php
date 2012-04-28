<?
/*
 * File: example.php
 * Description: Received Mail Example
 * Created: 01-03-2006
 * Author: Mitul Koradia
 * Email: mitulkoradia@gmail.com
 * Cell : +91 9825273322
 */
include("receivemail.class.php");
// Creating a object of reciveMail Class
$obj= new receiveMail('upload','Kbxf*#nO;gW$','upload@bcmoney-mobiletv.com','mail.bcmoney-mobiletv.com','pop3','110',false);

//Connect to the Mail Box
$obj->connect();         //If connection fails give error message and exit

// Get Total Number of Unread Email in mail box
$tot=$obj->getTotalMails(); //Total Mails in Inbox Return integer value

echo "Total Mails:: $tot<br>";

for($i=$tot;$i>0;$i--)
{
	$head=$obj->getHeaders($i);  // Get Header Info Return Array Of Headers **Array Keys are (subject,to,toOth,toNameOth,from,fromName)
	echo "Subjects :: ".$head['subject']."<br>";
	echo "TO :: ".$head['to']."<br>";
	echo "To Other :: ".$head['toOth']."<br>";
	echo "ToName Other :: ".$head['toNameOth']."<br>";
	echo "From :: ".$head['from']."<br>";
	echo "FromName :: ".$head['fromName']."<br>";
	echo "<br><br>";
	echo "<br>*******************************************************************************************<BR>";
	echo $obj->getBody($i);  // Get Body Of Mail number Return String Get Mail id in interger
	
	$str=$obj->GetAttach($i,"./"); // Get attached File from Mail Return name of file in comma separated string  args. (mailid, Path to store file)
	$ar=explode(",",$str);
	foreach($ar as $key=>$value)
		echo ($value=="")?"":"Atteched File :: ".$value."<br>";
	echo "<br>------------------------------------------------------------------------------------------<BR>";
	
	//$obj->deleteMails($i); // Delete Mail from Mail box
}
$obj->close_mailbox();   //Close Mail Box

?>