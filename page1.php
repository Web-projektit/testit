<?php
/* Kommentti 3.12.2019 klo 13:41*/
  session_start();
  
  $_SESSION['sess_var'] = "Hello world, terve maailma!";

  echo 'The content of $_SESSION[\'sess_var\'] is '
        .$_SESSION['sess_var'].'<br />';
?>
<a href="page2.php">Next page</a> 
