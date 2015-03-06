# SimpleHTTP
Simple validation and sanitation for HTTP request methods

<h2>Overview</h2>
<strong>Disclaimer</strong>: HTTP request method validation and sanitation are not adequate measures to fully prevent SQL injection. Please refer to: http://php.net/manual/en/pdo.prepared-statements.php for documentation concerning sanitation through PDO-based prepared statements.

<h4>What is request validation?</h4>
Request validation is the act of defining specifically what data you expect to receive from the user, as well as the means by which you're receiving that data.

When allowing user input from a website or other source to your server, server-side validation of input and proper handling of that data is paramount in ensuring server and service security.

As a first line of server-side defense, SimpleHTTP aids in easily:
<ul>
<li>Detecting data by request type</li>
<li>Validating data by request type</li>
<li>Handling and sanitizing data by request type</li>
</ul>


<h3>Coverage</h3>
Currently, the SimpleHTTP package allows for the detection, handling, and sanitation of the following methods:
<ul>
<li>GET</li>
<li>POST</li>
<li>and has also been extended for use in defining and sanitizing PHP <i>$_FILES</i> input</li>
</ul>


<h3>HTTPPost</h3>
HTTPPost defines three easy-to-use classes:
<ul>
<li>HTTPPostGroup: Allows for all-at-once validation of POST data</li>
<li>HTTPPost: (Core class) Handles validation and sanitation for individual POST data inputs </li>
<li>HTTPDataDetector: Ensures only POST data is received by the server</li>
</ul>

<h5>Breakdown and Example</h5>
```PHP
<?php
  $post_data = new HTTPPost("input_field_name"); //Set up our expected data via HTTPPost object
  
  //In defining this object, we are:
  1.) Eliminating all HTML tags included within the data received
  2.) Converting all single and double quotes
  
  //If we choose, we can validate each input individually prior to working with its values:
  if ($post_data->isValid() === TRUE)
  {
    $post_data_value = $post_data->getValue();
    
    //Perform our custom logic with our POST data
  }
?>
```

<h3>Example: Accepting user POST data</h3>
Let's say we're managing a webpage that's keeping track of folks signing up for a karate tournament. Here's our HTML form we've set up to capture sign-up data from participants:

Our user-facing webpage's directory: <strong>SuperCoolKarateSite/Signup/index.php</strong>
```HTML
<form name = "karate-signup-form" method = "POST" action = "path/to/my/php/script/karate-signup-logic.php>
  Your First Name: <input type = "text" name = "first-name"></input>
  Your Last Name: <input type = "text" name = "last-name"></input>
  Your Current Belt Level: 
    <select name = "belt-level">
      <option>White Belt</option>
      <option>Red Belt</option>
      <option>Black Belt</option>
    </select>
  <button type = "submit" name = "submit">Sign Up<button>
</form>
```

If we look at the <strong>method</strong> and <strong>action</strong> of our HTML form, we're shooting off the data the users submit to our logic sitting in the <i>path/to/my/php/script/karate-signup-logic.php</i> page via a POST request.

Our PHP script to handle the data received from our signup form: <strong>karate-signup-logic.php</strong>
```PHP
<?php
  require("path/to/SimpleHTTP/SimpleHTTP.php"); //Include our SimpleHTTP files
  
  //First, let's make sure we're receiving purely POST ($_POST) data on this page
  $detector = new HTTPPostDetector();
  if ($detector->detectsPostData() === TRUE)
  {
    //Define via an array the POST data, by <input name = ...>, that we're expecting to receive
    $required_fields = array("first-name", "last-name", "belt-level");
    
    //Next, ensure that our group of POST data is valid
    $data = new HTTPPostGroup($required_fields);
    
    if ($data->isValid() === TRUE)
    {
      //With our data validated, we'll set up our HTTPPost objects
      //Sanitation is performed on object construction
      $first_name = new HTTPPost("first-name");
      $last_name = new HTTPPost("last_name");
      $belt_level = new HTTPPost("belt_level");
      
      //Perform custom logic here...
      
      
      //Return a kind message thanking the user for their signup.
      echo '<p>Thanks for signing up, ' . $first_name->getValue().' '.$last_name->getValue().'!</p> 
      <p>We hope to see you at the '.$belt_level->getValue().' competition!</p>';
      
      
    }
    else
    {
      echo "Whoops -- it looks like there's data missing!";
    }
    
  }
  else
  {
    header("Location: ../404/"); //Redirect the user visiting this page without POST data to the 404 page
  }?>
```

<h3>HTTPGet</h3>
HTTPPost defines three easy-to-use classes:
<ul>
<li>HTTPGetGroup: Allows for all-at-once validation of GET data</li>
<li>HTTPGet: (Core class) Handles validation and sanitation for individual GET data from the querystring or input</li>
<li>HTTPGetDetector: Ensures only GET data is received by the server</li>
</ul>

<h5>Breakdown and Example</h5>
```PHP
<?php
  $get_data = new HTTPGet("get_querystring_name_or_input_name"); //Set up our expected data via HTTPGet object
  
  //In defining this object, we are:
  1.) Eliminating all HTML tags included within the data received
  2.) Converting all single and double quotes
  
  //If we choose, we can validate each input individually prior to working with its values:
  if ($get_data->isValid() === TRUE)
  {
    $get_data_value = $get_data->getValue();
    
    //Perform our custom logic with our GET data
  }
?>
```

<h3>HTTPFile</h3>
HTTPFile defines two easy-to-use classes:
<ul>
<li>HTTPFileGroup: Allows for all-at-once validation of files</li>
<li>HTTPFile: (Core class) Handles validation and sanitation for individual files</li>
</ul>

<h5>Breakdown and Example</h5>
```PHP
<?php

  //In defining this object, we are:
  1.) Eliminating all HTML tags included within the name of the file received
  2.) Converting all single and double quotes within the name of the file received
 
  $file = new HTTPFile("user-file"); //Set up our expected data via HTTPPost object
  
  //I want to accept Micrsoft Word, PDF, and normal text documents for this particular input
  $extensions_to_allow = array(".txt", ".pdf", ".docx");
  $file->setValidExtensions($extensions_to_allow);
  
  //Validate the file, ensuring it exists and is of the correct file extension
  if ($file->isValid() === TRUE)
  {
    echo "Received file: " . $file->getFileName(). "!";
  }
?>
```
