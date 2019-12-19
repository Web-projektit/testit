<?php
/* Kommentti NetBeans 3.12.2019 klo 13:45*/
  session_start();
  //$a = "1,1";
  //echo "a: ".($a + 0)."<br>";
  $_SESSION['sess_var'] = "Hello world, terve maailma!";
  //date_default_timezone_set("Europe/Helsinki");
  $ajankohta = date("Y-m-d H:i:s");
  //echo ($x + 1):
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
 file_put_contents("debug_curl.txt", $output."\n", FILE_APPEND);
 echo $output;
?>
<a href="page2.php">Next page</a> 
