<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 */
function debug_error_handler($errno,$errstr,$errfile,$errline){
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        return false;
        }

    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        //echo "System generated error: [$errno] $errstr<br />\n";
        return false;
        break;
    }
    /* Don't execute PHP internal error handler */
    return true;
}


function debuggeri_filter($n){
  $pop = array_shift($n);
  $args = implode(",",$n['args']);
  $m = $n['file'].",rivi ".$n['line'].", ".$n['function']."($args)";  
  return $m; 
  }


function debuggeri_backtrace($errorMsg){
  if (defined('DEBUG') and !DEBUG) return;  
  $backtrace = debug_backtrace();   
  array_shift($backtrace);
  $backtrace = array_reverse($backtrace);
  $backtrace = array_map('debuggeri_filter', $backtrace);
  $msg = date("Y-m-d H:i:s")." ".$errorMsg;
  file_put_contents("debug_log.txt", $msg."\n".implode("\n",$backtrace)."\n", FILE_APPEND);
  }


function debuggeri_shutdown($parametrit = ""){
  if (defined('DEBUG') and !DEBUG) return;    
  $error = error_get_last();
  //var_export($error);
  if ($error and $error['type'] === E_ERROR){
    $type = ($error) ? $error['type'] : "";   
    $msg = date("Y-m-d H:i:s")." Ohjelman suoritus päättyi.";
    $msg.= " Tappava virhe $type rivillä ".$error['line'].",tiedostossa ".$error['file'];     
    $msg = utf8_decode($msg);
    $path = $_SERVER['DOCUMENT_ROOT']."/debug_shutdown.txt";
    file_put_contents($path,$msg."\n", FILE_APPEND);
    }    
  }  

?>

