<?php
/* Kommentti 3.12.2019 klo 13:43*/
  session_start();
  
  $_SESSION['sess_var'] = "Hello world, terve maailma!";
  //date_default_timezone_set("Europe/Helsinki");
  $ajankohta = date("Y-m-d H:i:s");

  echo $ajankohta.': The content of $_SESSION[\'sess_var\'] is '
        .$_SESSION['sess_var'].'<br />';
  
  // create curl resource 
  $ch = curl_init(); 

  // set url 
  curl_setopt($ch, CURLOPT_URL, "https://www.is.fi"); 

  //return the transfer as a string 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

  // $output contains the output string 
  $output = curl_exec($ch); 

 // close curl resource to free up system resources 
 curl_close($ch);
 $haku = stristr($output, "ravi");
 echo $haku;
?>
<a href="page2.php">Next page</a> 
