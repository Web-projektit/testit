<?php
/* Responsiivinen lomake, jossa on sekä client- että server-validointi bootstrap 4 perustuen. 
   PHP-debuggausrutiineja omassa tiedostossaan. Kenttäkohtaiset
   virheilmoitukset ja palaute lähetetään Ajaxilla, jos saapuvassa
   lomakkeessa on jokin Ajax-kenttä, muuten virheilmoitukset ja
   lomakkeen kenttien syötetyt arvot palautetaan perinteiseen tapaan 
   lomakkeella selaimelle, joka lähettää sen javascript-komennolla edelleen lomakesivulle.
*/
include('debuggeri.php');  

function palaute($ajax,$message){
if ($ajax){
  echo $message;
  }    
else {
  echo "<p>$message</p";   
}    
return;   
}

debuggeri(basename($_SERVER['SCRIPT_NAME']).",post:".var_export($_POST,true));
$error_required = array();
$error_numeric = array();
$kentat = array('title','description','release_year','language_id',
   'rental_duration','rental_rate','length','replacement_cost',
   'rating','special_features');
$numeeriset = array('release_year','rental_duration','rental_rate','length','replacement_cost');			

foreach ($kentat AS $kentta) {
  if (empty($_POST[$kentta]) and $kentta <> 'special_features') 
	$error_required[$kentta] = true;
  if (in_array($kentta,$numeeriset) and !is_numeric(str_replace(",",".",$_POST[$kentta])))
	$error_numeric[$kentta] = true;
  }	
  
$ajax = (isset($_POST['validation']) and !empty($_POST['validation']));  
if ($error_required or $error_numeric){
  if ($ajax){
    $virheet = array_merge($error_required,$error_numeric);
    if ($virheet) {
      echo json_encode($virheet);
      exit;
      }
    }

  elseif ($error_required or $error_numeric){
    echo "<form name=\"error_form\" action=\"tehtava_db_testi.php\" method=\"post\">";
    if ($error_required){
      /* Lomakkeelta puuttuvat kentät palautetaan virheinä lomakkeelle. */    
      foreach ($error_required AS $error_field => $error_value)
      echo "<input type=\"hidden\" value=\"$error_field\" name=\"error_required[]\">";
      }
    if ($error_numeric){
      foreach ($error_numeric AS $error_field => $error_value)
      echo "<input type=\"hidden\" value=\"$error_field\" name=\"error_numeric[]\">";
      }
    foreach ($kentat AS $kentta){
    /* Lomakkeen kenttien jo syötetyt arvot palautetaan valmiiksi lomakkeelle. */    
      $arvo = isset($_POST[$kentta]) ? $_POST[$kentta] : ""; 
      if (is_array($arvo)) {
        $name = $kentta."[]";	
        foreach ($arvo AS $arvo_i){
          echo "<input type=\"hidden\" value=\"$arvo_i\" name=\"$name\">";
	  }
        }	
      elseif (!empty($arvo)) echo "<input type=\"hidden\" value=\"$arvo\" name=\"$kentta\">";
      }
    echo "</form>";
    echo "<script>document.forms['error_form'].submit();</script>";
    }
  }
$db = new mysqli('localhost','root','','sakila');
if (mysqli_connect_errno()){
  palaute($ajax,"Virhe tietokantayhteydessä.");
  exit;
  }  
/*
$query = "SELECT f.title,c.name FROM film f,film_category fc,category c WHERE
          fc.film_id = f.film_id AND c.category_id = fc.category_id AND f.title LIKE 'A%'";
$query = "SELECT f.title,c.name FROM film f INNER JOIN film_category fc 
  ON fc.film_id = f.film_id INNER JOIN category c 
  ON c.category_id = fc.category_id 
  WHERE c.name LIKE 'Horror'";
$result = $db->query($query);
$num_results = $result->num_rows;
echo "Sakila film:$num_results riviä.<br>"; 
while ($row = $result->fetch_assoc()){
  echo $row['title']." ".$row['name']."<br>";	
  }
exit;  */
  
$mysql_arvot = array();  
$kentat = array_intersect($kentat,array_keys($_POST));
foreach ($kentat AS $kentta){
  if (isset($_POST[$kentta]) and is_array($_POST[$kentta])) {
    $mysql_arvot[$kentta] = implode(",",$_POST[$kentta]);  
    }	
  elseif ($kentta <> 'special_features') {
    $arvo = trim(strip_tags($_POST[$kentta])); 	
    if (in_array($kentta,$numeeriset)) $arvo = str_replace(",",".",$arvo);
    //$encoding = mb_detect_encoding($arvo);
    //echo "$arvo,encoding:$encoding<br>";
    $mysql_arvot[$kentta] = $db->real_escape_string($arvo);
    //$mysql_arvot[$kentta] = $arvo;
    }
  }	
$kentat = implode(",",$kentat);
$arvot = implode("','",$mysql_arvot);
$db->set_charset('utf8');
$query = "INSERT INTO film ($kentat) VALUES ('$arvot')";
//echo $query."<br>";
$result = $db->query($query);
if (!$result) {
  debuggeri_backtrace("$query\nvirhe:".$db->error);
  debuggeri("kentat:$kentat");
  debuggeri("arvot:'$arvot'");
  palaute($ajax,"Elokuvan tallennus ei onnistunut.");
  }
else {
  debuggeri(basename(__FILE__).",$query");
  palaute($ajax,"Elokuvan tiedot on tallennettu.");
  }
if (!$ajax){
echo "<form action=\"tehtava_db_testi.php\"><input type=\"submit\" value=\"OK\"></form>";  
}
?>