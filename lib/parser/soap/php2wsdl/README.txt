       ___________________________________________________________

                   PHP WSDL Generator - Version 1.3.1
                               April 2009
       ___________________________________________________________

                   Copyright (c) 2009 Dragos Protung

_______________________

CONTENTS
_______________________

1. Description
2. System Requirements
3. How to use
4. Contact

_______________________

1. Product Description
_______________________

This class can be used to generate a Web Services Definition Language (WSDL) document from the code of a class.
It parses the code of a given PHP class script files and generates a WSDL definition from the list of the class functions.
Certain functions may be skipped to not be included in the WSDL definition based on the function access type.
The generated WSDL may be saved to a given file, returned as a string or served for download.
_______________________

2. System Requirements
_______________________

PHP 5.x

______________

3. How to use
______________


3.1 - Begin
	Instantiate the class providing a name and an URL for the WSDL: new WSDLCreator("WSDLExample1", "http://www.yousite.com/wsdl");

3.2 - Adding files
	Then you need to add some files that contain PHP classes to generate the WSDL from by using the addFile() method.
	Ex.: $WSDLCreator->addFile("path/to/your/file/file.ext);
	
	You can have any other code in the files and anywhere in the file as long as the file will not generate any errors (at run time).
	If a file will not contain a class will not generate an error.
	You can add as many files you want.

3.3 - Ignoring
	If a file contains a class that you do not want to include in your WSDL you can just call the ignoreClass() method
	providing the name of the class (case sensitive).
	
	If you want to exclude more classes you can use the ignoreClasses() method
	providing an array with the names of the classes like array("class1", "class2", ...).

	If a class contains a method that you do not want to include in your WSDL call the ignoreMethod() method providing
	the class name and the method name in an array like array("class"=>"method").
	
	To exclude multiple classes call ignoreMethod() providing an array like array("class"=>"method", "class2"=>"method2")

	If you want to ignore one or more types of methods the you can use:
		- ignorePublic() - ignore public functions
		- ignoreProtected() - ignore protected methods
		- ignorePrivate() - ignore private methods
		- ignoreStatic() - ignore static methods
	
	!!!!! WARNING !!!!!
	By default protected and private methods are ignored
	!!!!! WARNING !!!!!
		
3.4 - Seting URLs
	It is best to have your code documented so the script will know the types of the parameters of a method (if a method
	returns a value, the type of that value, etc.)

	You need to provide an URL to all the classes defined. To do this call addURLToClass() providing the name of the class
	and the URL like addURLToClass("class1", "http://protung.ro")
	If you do not want to individual provide an URL call the setClassesGeneralURL() method. This will set an URL for all
	the classes that do not have an URL manually set.
	If you do not do this a fatal error will be raised and you can not generate the WSDL.
	
	The same rule applies to parameters of a method. If for example, a parameter of a class method is another class
	(and you describe it in a comment), and that class (parameter type) is not defined in all the files you include,
	then you need to provide an URL for that class. If you do not do this a fatal error will be raised and you can not 
	generate the WSDL. To to this just call addURLToTypens() providing the type of the variable (class name) and URL
	as a parameter like addURLToTypens("XMLCreator", "http://protung.ro")

3.5 - Others
	You can choose to include or not documentation of each method included in the WSDL by calling the
	$WSDLCreator->includeMethodsDocumentation(true/false); method.
	
3.6 - Get the result
	In the end to create the WSDL just call the createWSDL() method and then by using:
		getWSDL() - will return the WSDL as a string so you can save it in a variable
		printWSDL() - will print the WSDL (use it when there is no other output)
		saveWSDL() - will save the WSDL to a file you specify
				   - if the file exists and overwrite is false then the WSDL will be downloaded
		downloadWSDL() - will force you to download the WSDL


______________

3. Contact
______________

Please send your suggestions, bug reports and general feedback to dragos@protung.ro
Also visit http://www.protung.ro


Out for now ;)