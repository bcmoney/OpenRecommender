################################################################################
#                         receivemail.class.php  Version: 1.0                   #
#                                                                              #
#                                                                              #
#                          Created by Mitul Koradia                            #
#                                                                              #
#                        Email: mitulkoradia@gmail.com                         #
#                             Cell No +919825273322                           #
################################################################################

This is simple but use full class for the Application Developer

Application :
This class can be used to retrieve mail from pop3/imap mailbox. The class can be used to generate auto response, ticket/post reply via mail etc. The class is able to get the attachment from the mail too. 

--------------------------------------------------------------------------------
System Requirement :

Internet connection 
php with imap library
--------------------------------------------------------------------------------

Description of Functions
----------------------------------------------------------------------------------
function reciveMail($username,$password,$EmailAddress,$mailserver='localhost',$servertype='pop',$port='110')

This is the constructor for this class

Arguments are
 $username                = User name off the mail box
 $password                = Password of mailbox
 $emailAddress            = Email address of that mailbox some time the uname and email 
                            address are identical
 $mailserver              = Ip or name of the POP or IMAP mail server
 $servertype              = if this server is imap or pop default is pop
 $port                    = Server port for pop or imap Default is 110 for pop and 143 for imap
 
------------------------------------------------------------------------------------
function connect()  

This function is useful to connect to the mail box 
------------------------------------------------------------------------------------
function getHeaders($mid)

This function is use full to Get Header info from particular mail

Arguments : 
$mid               = Mail Id of a Mailbox

Return :

Return Associative array with following keys
	subject   => Subject of Mail
	to        => To Address of that mail
	toOth     => Other To address of mail
	toNameOth => To Name of Mail
	from      => From address of mail
	fromName  => Form Name of Mail
	
-------------------------------------------------------------------------------------
getTotalMails()

used to get total unread mail from That mailbox

Return : 
Int Total Mail

-------------------------------------------------------------------------------------
GetAttech($mid,$path)

Save attached file from mail to given path of a particular location

Arguments :
 $mid         = mail id
 $path        = path where to save
 
Return  :
 
 String of filename with coma separated
 like a.gif,pio.jpg etc
 
-------------------------------------------------------------------------------------
getBody($mid)

Get The actual mail content from this mail

Arguments 
  $mid          = Mail id
Return String
-------------------------------------------------------------------------------------
deleteMails($mid)

Delete mail from that mail box

Arguments :
	$mid         = mail Id
-------------------------------------------------------------------------------------
close_mailbox()

Close The Mail Box
------------------------------------------------------------------------------------

################################   End   ###########################################
Thanks.
Mitul Koradia

Please post me bugs and feature requests At: mitulkoradia@gmail.com

	
