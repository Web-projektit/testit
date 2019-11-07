<?php
/* Kommentti */
  session_start();
  
  $_SESSION['sess_var'] = "Hello world, maailma!";

  echo 'The content of $_SESSION[\'sess_var\'] is '
        .$_SESSION['sess_var'].'<br />';
?>
<a href="page2.php">Next page</a> 
