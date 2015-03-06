<?php

class HTTPPost
{
  public $name;
  public $value;
  public $type;
  public $error_message;
  
	
  function __construct($postName)
  {
	
    $this->name = $postName;

	//If this $_POST value is a nested array, sanitize it
	if (isset($_POST[$this->name]) === TRUE )
	{
	  if (is_array($_POST[$this->name]) === TRUE)
	  {
	    $num = count($_POST[$this->name]);
	    for ($i = 0; $i < $num; $i++)
	    {
	      $_POST[$this->name][$i] = strip_tags($_POST[$this->name][$i], ENT_QUOTES);	
		}
			  
		$this->value = $_POST[$this->name];
      }
			
	  //If this isn't a nested-array input, but a simple, everyday $_POST input, sanitize as per normal
      else
	  {
        $this->value = strip_tags($_POST[$this->name], ENT_QUOTES);	
	  } 		  
	}
	
	//If the $_POST array element is never found, assign the HTTPPost object's value to NULL
	else 
	{
	  $this->value = NULL;	 
	}
  }//End: Construct
   
	
  // Determines if the POST request has been received.
  function exists()
  {
    if (isset($_POST[$this->name]) === TRUE)
    {
      return TRUE;
	}
	 
	else 
	{
	  return FALSE;	 
	}
  }//end: exists()
	
  // Determine if POST request has a valid value, or determine if it has a specific desired value
  function hasValue($desiredValue = NULL)
  {
    // If no value is desired, we just want to ensure that this $_POST has a valid value
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
		
    // If a specific value is desired, see if the value of this $_POST is equal to it.
	if ($desiredValue !== NULL)
	{
      if ($this->exists() === TRUE && $this->value === $desiredValue)
	  {
	    return TRUE;
	  }
    }	
  }
	
  //Returns the name of this POST data
  function getName()
  {
    return $this->name;
  }//end: getName()
	
	
  //Returns the value of this POST data	
  function getValue()
  {
    return $this->value;
  }//end: getValue()

	
  //Determines the validity of the POST data
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
  
}//end: HTTPPost class




//The HTTPPostGroup allows for validation of entire groups of expected post data
//by validating them as HTTPPost objects
class HTTPPostGroup
{
  public $requireds;
  
  
  //$requiredPostData: (array) An array of strings listing our required <input> elements
  function __construct($requiredPostDataArray)
  {
    $this->requireds = $requiredPostDataArray;
  }
  
  
  //Determines the validity of all required values in this HTTPPostGroup
  function isValid()
  {
	if (isset($this->requireds))
	{
	  $len = count($this->requireds);
	  for ($i = 0; $i < $len; $i++)
	  {
		$post = new HTTPPost($this->requireds[$i]);
		if ($post->exists() === FALSE && $post->hasValue() === FALSE)
		{
          return FALSE;
		}
	  }
   
      //If every required $_POST input is found with correct data, this group is valid
      return TRUE;
    }
	
	//If no array of strings has been included, this is FALSE by default.
	else
	{
	  return FALSE;
	}	
  }  
}


//Detects if there is $_POST data submitted to the page.
class HTTPPostDataDetector
{
  public $post_data_exists;
  
  function __construct()
  {
    if (count($_POST) > 0)
	{
	  $this->post_data_exists = TRUE;
	}
	else
	{
	  $this->post_data_exists = FALSE;	
	}
  }
  
  //Determines whether or not data has been submitted to the page via a $_POST request
  function detectsPostData()
  {
    return $this->post_data_exists;
  }
}



?>
