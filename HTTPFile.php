<?php

class HTTPFile
{
	public $inputname;
	public $filename;
	public $servername;
	public $type;
	public $size;
	public $error; //May need to work with more
	
	public $validextensions;
	
	//Note: filevalue is the name and all array components of a normal "_FILES" object array
	function __construct($inputname, $validExtensions = NULL)
	{
	  $this->inputname = $inputname; //We'll always know the name 
	 
	  //Name and values array construction
	  if ( isset($_FILES[$this->inputname]) === TRUE )
	  {
	  	$this->filename = strip_tags($_FILES[$this->inputname]["name"], ENT_QUOTES); //As of php 5.4 UTF-8 is default
	    $this->servername = $_FILES[$this->inputname]["tmp_name"]; //As of php 5.4 UTF-8 is default
	    $this->type = $_FILES[$this->inputname]["type"];
		$this->error = $_FILES[$this->inputname]["error"];
		$this->size = $_FILES[$this->inputname]["size"];
	  }
		 
	  else 
	  {
	  	$this->filename  = NULL;
	    $this->servername = NULL;
		$this->type = NULL;
		$this->size = NULL;
		$this->error = "HTTPFile not found";
	  }	
	  
	  
	  //Valid types construction
	  $extensions = array();
	  
	  //If no extensions are given, set the default accepted types to the following array of document extensions
	  if ($validExtensions === NULL)
	  {
	    array_push($extensions, array("txt", "doc", "docx", "xls", "xlsx", "pdf", "csv", "ppt", "pptx"));
		$this->validextensions = $extensions;
	  }
	  
	  //If extensions are given, cycle through each given value to ensure that the "." is removed, if given, for easier type checks
	  else
	  {
	    for ($i = 0; $i < count($validExtensions); $i++)
		{
		  $element = $validExtensions[$i];
		  
		  if (strpos($element, ".") !== FALSE)
		  {
		  	 $pieces = explode(".", $element);
			 $extension_without_dot = $pieces[count($pieces) - 1];
			 
			 //Set the element's new value: The same value, but without the "." prefix before the extension
			 $validExtensions[$i] = $extension_without_dot;
		  }
		}
		
		//Push the now-cleansed extensions onto the $extensions array
		array_push($extensions, $validExtensions);
        $this->validextensions = $extensions;		
	  }
	  
	}//End: __construct()
	
	function getAsAttachment()
	{
	  return array($this->getServerName(), $this->getFileName());
	}
	
	
	function getInputName()
	{
		return $this->inputname;
	}
	
	function setFileName($newname)
	{
		$this->filename = $newname;
	}
	
	
	function getFileName()
	{
		//If filename is NULL from non-existance, echo the string NULL	
		if ($this->filename === NULL)
		{
	      return NULL;
		}
		else
		{
		  return $this->filename;
		}
	}
	
	
	function getServerName()
	{
		return $this->servername;
	}
	
	function getType()
	{
		return $this->type;
	}
	
	
	function getSize()
	{
		return $this->size;
	}
	
	function getError($returnAsJSONBool = NULL)
	{
		if ($returnAsJSONBool === NULL || $returnAsJSONBool === FALSE)
		{
			return $this->error;
		}
		
		if ($returnAsJSONBool === TRUE)
		{
			return new TurtlePieException($this->error); //return the error as a server-default JSON exception;
		}
	}
	

	//Checks to see if the $_FILE exists
	function exists()
	{
		if ($this->servername !== NULL)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	//After sanitizing against "."s, 
	function setValidExtensions($validExtensions)
	{
		$extensions = array();
		for ($i = 0; $i < count($validExtensions); $i++)
		{
		  $element = $validExtensions[$i];
		  
		  if (strpos($element, ".") === TRUE)
		  {
		  	 $pieces = explode(".", $element);
			 
			 //Set the element's new value: The same value, but without the "." prefix before the extension
			 $validExtensions[$i] = $pieces[count($pieces) - 1];
		  }
		}
		
		//Push the now-cleansed extensions onto the $extensions array
		array_push($extensions, $validExtensions);
        $this->validextensions = $extensions;		
	}
	
	//Returns the array that houses the array of valid document extensions
	function getValidExtensions()
	{
		return $this->validextensions;
	}
	
	//Returns the given file extension based off of the user's file's given name
	function getFileExtension()
	{
		$filepieces = explode(".", $this->filename);
		return $filepieces[count($filepieces) - 1];
	}
	
	//Checks if given file is of a valid extension type
	function isValidExtension($extension = NULL)
	{
		if ($extension === NULL)
		{
		  $extension = $this->getFileExtension();
		}
		
		$validsarray = $this->validextensions;
		$valids = $validsarray[0];
		
		//If there are no valid types, return false;
		if (count($valids) === 0)
		{
		  return FALSE;
		}
		
		//If the file extension for this given user document is valid, return TRUE;
		for ($i = 0; $i < count($valids); $i++)
		{
		  if ($extension === $valids[$i])
		  {
		    return TRUE;  
		  }
		}
		
	  return FALSE; 
	}
	
	//Adds an additional type of document extensions to be added to the list of accepted document extensions/types
	function addValidExtension($extension)
	{
	  //Grab array housing the array of already-approved and accepted file extensions, and push the new extension
	  
	  //If a "." is present, remove the "." from the extension
	  if (strpos($extension, ".") !== FALSE)
	  {
	  	$pieces = explode(".", $extension);
		$extension = $pieces[count($pieces) - 1];
	  }
	  
	  //Search the existing array of valid file types; if already exists, don't perform the addtion for the new extension;
	  $validsarray = $this->getValidExtensions();
	  $valids = $validsarray[0];
	  
	  for ($i = 0; $i < count($valids); $i++)
	  {
	    if ($extension === $valids[$i])
		{
			return; //Simply return without adding anything, given that the desired additional extension is already present.
		}	
	  }
	  
	  //If a match is never found for that extension, it is a new extension to now be accepted as valid; push to the accepted array
	  array_push($valids, $extension);
	  $this->validextensions = array($valids);
	}
	
	//Removes a chosen file extension from the list of accepted file extensions
	function removeValidExtension($extension)
	{ 
	  //Remove the "." from the extension
	  if (strpos($extension, ".") !== FALSE)
	  {
	  	$pieces = explode(".", $extension);
		$extension = $pieces[count($pieces) - 1];
	  }
	  
	  $validsarray = $this->getValidExtensions();
	  $valids = $validsarray[0];
	   
	  for ($i = 0; $i < count($valids); $i++)
	  {
	    if ($extension === $valids[$i])
		{
		  unset($valids[$i]);
		  
		  //Use array_values to re-cast the integer with properly-indexed (AKA: in proper numerical order) array values
		  $this->validextensions = array(array_values($valids));
		} 
	  }
	}
	
}//End: HTTPFile Class

// This function validates a specified array of name strings as valid $_FILEs. 
function validateFileGroup($requiredFileNamesArray)
{
	$len = count($requiredFileNamesArray);
	$validcount = 0;
	
	for ($i = 0; $i < $len; $i++)
	{
		$file = new HTTPFile($requiredFileNamesArray[$i]);
		if ($file->exists() === TRUE)
		{
		  $validcount = $validcount + 1;	
		}
	}

	// If the number of valid items equals that of the number of items we originally specified, return true;
	if ($validcount === $len)
	{
	   return TRUE;
	}
	
	else
	{
	   return FALSE;
	}
}









?>