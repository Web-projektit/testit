<?php
/* $.post( "test.php", {func:"getNameAndTime",lomake:$("#testform").serialize()},function(data){}); */

$funktio = $_POST['func'];
$lomake = $_POST['lomake'];

$name = $lomake['name'];
$address = $lomake['address'];

if ($funktio == "getNameAndTime"){
  $array = array("name" => 'John',"time" => "2pm");  
  $tulos = json_encode($array);  
  echo $tulos;  
  }
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

