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
	
	//Determines if the $_GET item has been received.
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
	}//end: exists()
	
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
	}//end: hasValue()
	
    //Returns the input name associated with the GET data
	function getName()
	{
	  return $this->name;
	}
	
	//Returns the value of the GET data
	function getValue()
	{
      return $this->value;
	}
	
	//Determines the validity of the GET data
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
	}//end: isValid()
}


//The HTTPGetGroup allows for validation of entire groups of expected post data
//by validating them as HTTPGet objects
class HTTPGetGroup
{
  public $requireds;
  
  
  //$requiredGetData: (array) An array of strings listing our required <input> elements or querystring elements
  function __construct($requiredGetDataArray)
  {
    $this->requireds = $requiredGetDataArray;
  }
  
  
  //Determines the validity of all required values in this HTTPGetGroup
  function isValid()
  {
	if (isset($this->requireds))
	{
	  $len = count($this->requireds);
	  for ($i = 0; $i < $len; $i++)
	  {
		$get = new HTTPGet($this->requireds[$i]);
		if ($get->exists() === FALSE && $get->hasValue() === FALSE)
		{
          return FALSE;
		}
	  }
   
      //If every required $_GET input is found with correct data, this group is valid
      return TRUE;
    }
	
	//If no array of strings has been included, this is FALSE by default.
	else
	{
	  return FALSE;
	}	
  }  
}


//Detects if there is $_GET data submitted to the page.
class HTTPGetDataDetector
{
  public $get_data_exists;
  
  function __construct()
  {
    if (count($_GET) > 0)
	{
	  $this->get_data_exists = TRUE;
	}
	else
	{
	  $this->get_data_exists = FALSE;	
	}
  }
  
  //Determines whether or not data has been submitted to the page via a $_POST request
  function detectsGetData()
  {
    return $this->get_data_exists;
  }//end: detectsGetData()
}

?>