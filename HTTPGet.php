<?php

class HTTPGet
{
	public $name;
	public $value;
	
	function __construct($getName, $getValue = NULL)
	{
	   
	  $this->name = $getName;
	  
	  // If no value is declared for the HTTGet object, attempt to find it; if never found, null;
	  if ($getValue === NULL)
	  {
	  	 if ( isset($_GET[$this->name]) === TRUE )
		 {
		 	$this->value = strip_tags($_GET[$this->name], ENT_QUOTES); //As of php 5.4 UTF-8 is default
		 }
		 
		 else 
		 {
		    $this->value = NULL;	 
		 }
	  }
	}
	
	// Determines if the $_GET item has been received.
	function exists()
	{
	 if ( isset($_GET[$this->name]) === TRUE)
	 {
	 	return TRUE;
	 }
	 
	 else 
	 {
	    return FALSE;	 
	 }
	}
	
	// Determine if $_GET has a valid value, or determine if it has a specific desired value
	function hasValue($desiredValue = NULL)
	{
		// If no value is desired, we just want to ensure that this $_GET has a valid value
		if ($desiredValue === NULL)
		{
		   if ($this->exists() === TRUE && $this->value !== NULL && trim($this->value) !== "")
		   {
		   return TRUE;
		   }
		   else 
		   {
		   return FALSE;   
		   }
		}
		
		// If a specific value is desired, see if the value of this $_GET is equal to it.
		if ($desiredValue !== NULL)
		{
		   if ($this->exists() === TRUE && $this->value === $desiredValue)
		   {
		   	  return TRUE;
		   }
		}	
	}
	
	function setName($newname)
	{
	  $this->name = $newname;
	}
	
	function getName()
	{
	  return $this->name;
	}
	
	function getValue()
	{
      return $this->value;
	}
	
	// USE WITH CAUTION
	function setValue($newvalue)
	{
	  $this->value = $newvalue;
	}
	
	function isValid()
	{
		if ($this->exists() === TRUE && $this->hasValue() === TRUE)
		{
			return TRUE;
		}
		else 
		{
			return FALSE; 
		}
	}
}


// This function validates a specified array of name strings as valid $_GETs. 
function validateGetGroup($requiredGetNamesArray)
{
	$len = count($requiredGetNamesArray);
	$validcount = 0;
	
	for ($i = 0; $i < $len; $i++)
	{
		$get = new HTTPGet($requiredGetNamesArray[$i]);
		if ($get->exists() === TRUE && $get->hasValue() === TRUE)
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

//This function checks as to whether or not there is any $_GET data detected at all
function getDataExists()
{
	if (count($_GET) === 0)
	{
	  return FALSE;
	}
	else
	{
	  return TRUE;
	}
}


?>
