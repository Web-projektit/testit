<?php
//echo "lomakekasittelija.php<br>";
//var_export($_POST);
//exit;
  
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


if ($error_required or $error_numeric){
  echo "<form name=\"error_form\" action=\"tehtava_db_testi.php\" method=\"post\">";
  if ($error_required){
    foreach ($error_required AS $error_field => $error_value)
    echo "<input type=\"hidden\" value=\"$error_field\" name=\"error_required[]\">";
	}
  if ($error_numeric){
    foreach ($error_numeric AS $error_field => $error_value)
    echo "<input type=\"hidden\" value=\"$error_field\" name=\"error_numeric[]\">";
	}
  foreach ($kentat AS $kentta){
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

$db = new mysqli('localhost','root','','sakila');
if (mysqli_connect_errno()){
  echo "Virhe tietokantayhteydessä.<br>";
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
foreach ($kentat AS $kentta){
  if (isset($_POST[$kentta]) and is_array($_POST[$kentta])) {
	$mysql_arvot[$kentta] = implode(",",$_POST[$kentta]);  
    }	
  elseif ($kentta <> 'special_features') {
    $arvo = trim(strip_tags($_POST[$kentta])); 	
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
$db->query($query);
echo "<p>Elokuvan tiedot on tallennettu.</p><br>";
echo "<form action=\"tehtava_db_testi.php\"><input type=\"submit\" value=\"OK\"></form>"  
?>